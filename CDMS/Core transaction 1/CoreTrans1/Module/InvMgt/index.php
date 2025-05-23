<?php
// THIS MUST BE THE VERY FIRST THING IN THE FILE.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$basePath = '/cr1/'; // Define basePath for includes

// Authentication check
if (!isset($_SESSION['user_id'])) { // Or use $_SESSION['user_name']
    header('Location: ' . rtrim($basePath, '/') . '/login.php'); // Redirect to login
    exit();
}
require_once '../../includes/Database.php';
include_once('../../includes/head.php'); 

try {
    $db = new Database();
    $conn = $db->connect('inventory'); // Connects to cr1_inventory_management as per Database.php

    $action = isset($_GET['action']) ? $_GET['action'] : 'view';
    $inventoryId = isset($_GET['id']) ? (int)$_GET['id'] : null;

    // Function to fetch item details
    function getItemDetails($conn, $inventoryId) {
        $stmt = $conn->prepare("SELECT i.InventoryID, i.ItemName, i.StockLevel, r.ReorderLevel 
            FROM inventory i 
            LEFT JOIN reorder_levels r ON i.InventoryID = r.InventoryID 
            WHERE i.InventoryID = ?");
        $stmt->bind_param("i", $inventoryId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Function to fetch stock movements for a specific item
    function getStockMovementsForItem($conn, $inventoryId) {
        $stmt = $conn->prepare("SELECT sm.movementID, sm.InventoryID, i.ItemName, sm.MovementType, sm.Quantity, sm.MovementDate 
            FROM stock_movements sm 
            JOIN inventory i ON sm.InventoryID = i.InventoryID 
            WHERE sm.InventoryID = ? 
            ORDER BY sm.MovementDate DESC");
        $stmt->bind_param("i", $inventoryId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Function to update item
    function updateItem($conn, $inventoryId, $itemName, $stockLevel, $reorderLevel) {
        // Verify item exists
        $stmt = $conn->prepare("SELECT InventoryID FROM inventory WHERE InventoryID = ?");
        $stmt->bind_param("i", $inventoryId);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            throw new Exception("Item with InventoryID $inventoryId does not exist.");
        }

        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("UPDATE inventory SET ItemName = ?, StockLevel = ? WHERE InventoryID = ?");
            $stmt->bind_param("sii", $itemName, $stockLevel, $inventoryId);
            $stmt->execute();

            // Update reorder_levels only if reorderLevel is provided (safeguard)
            if ($reorderLevel !== null) {
                $stmt = $conn->prepare("INSERT INTO reorder_levels (InventoryID, ReorderLevel) VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE ReorderLevel = ?");
                $stmt->bind_param("iii", $inventoryId, $reorderLevel, $reorderLevel);
                $stmt->execute();
            }

            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
    }

    // Function to delete item
    function deleteItem($conn, $inventoryId) {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("DELETE FROM stock_movements WHERE InventoryID = ?");
            $stmt->bind_param("i", $inventoryId);
            if (!$stmt->execute()) {
                throw new Exception("Failed to delete stock movements: " . $conn->error);
            }

            $stmt = $conn->prepare("DELETE FROM reorder_levels WHERE InventoryID = ?");
            $stmt->bind_param("i", $inventoryId);
            if (!$stmt->execute()) {
                throw new Exception("Failed to delete reorder levels: " . $conn->error);
            }

            $stmt = $conn->prepare("DELETE FROM inventory WHERE InventoryID = ?");
            $stmt->bind_param("i", $inventoryId);
            if (!$stmt->execute()) {
                throw new Exception("Failed to delete inventory: " . $conn->error);
            }

            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
    }

    // Handle JSON endpoints first to prevent HTML rendering
    if ($action === 'view_stock' && $inventoryId) {
        try {
            $itemDetails = getItemDetails($conn, $inventoryId);
            $itemStockMovements = getStockMovementsForItem($conn, $inventoryId);
            if (!$itemDetails) {
                throw new Exception('Item not found.');
            }
            $response = [
                'status' => 'success',
                'item' => $itemDetails,
                'movements' => $itemStockMovements
            ];
        } catch (Exception $e) {
            $response = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
        header('Content-Type: application/json');
        ob_end_clean();
        echo json_encode($response);
        exit;
    }

    if ($action === 'edit' && $inventoryId) {
        try {
            $itemDetails = getItemDetails($conn, $inventoryId);
            if (!$itemDetails) {
                throw new Exception('Item not found.');
            }
            $response = [
                'status' => 'success',
                'item' => $itemDetails
            ];
        } catch (Exception $e) {
            $response = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
        header('Content-Type: application/json');
        ob_end_clean();
        echo json_encode($response);
        exit;
    }

    if ($action === 'delete' && $inventoryId && isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
        try {
            if (deleteItem($conn, $inventoryId)) {
                $response = [
                    'status' => 'success',
                    'message' => 'Item deleted successfully!'
                ];
            } else {
                throw new Exception('Deletion failed.');
            }
        } catch (Exception $e) {
            $response = [
                'status' => 'error',
                'message' => 'Failed to delete item: ' . $e->getMessage()
            ];
        }
        header('Content-Type: application/json');
        ob_end_clean();
        echo json_encode($response);
        exit;
    }

    // Handle form submissions and actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($action === 'add_item') {
            $itemName = $_POST['item_name'];
            $stockLevel = (int)$_POST['stock_level'];
            $reorderLevel = (int)$_POST['reorder_level'];

            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare("INSERT INTO inventory (ItemName, StockLevel) VALUES (?, ?)");
                $stmt->bind_param("si", $itemName, $stockLevel);
                $stmt->execute();
                $inventoryId = $conn->insert_id;

                $stmt = $conn->prepare("INSERT INTO reorder_levels (InventoryID, ReorderLevel) VALUES (?, ?)");
                $stmt->bind_param("ii", $inventoryId, $reorderLevel);
                $stmt->execute();

                $stmt = $conn->prepare("INSERT INTO stock_movements (InventoryID, MovementType, Quantity) VALUES (?, 'IN', ?)");
                $stmt->bind_param("ii", $inventoryId, $stockLevel);
                $stmt->execute();

                $conn->commit();
                $response = ['status' => 'success', 'message' => 'Item added successfully!'];
            } catch (Exception $e) {
                $conn->rollback();
                $response = ['status' => 'error', 'message' => 'Failed to add item: ' . $e->getMessage()];
            }
            header('Content-Type: application/json');
            ob_end_clean();
            echo json_encode($response);
            exit;
        } elseif ($action === 'edit_item' && $inventoryId) {
            $itemName = $_POST['item_name'];
            $stockLevel = (int)$_POST['stock_level'];
            $reorderLevel = isset($_POST['reorder_level']) ? (int)$_POST['reorder_level'] : null;
            try {
                if (updateItem($conn, $inventoryId, $itemName, $stockLevel, $reorderLevel)) {
                    $response = ['status' => 'success', 'message' => 'Item updated successfully!'];
                } else {
                    throw new Exception('Update failed.');
                }
            } catch (Exception $e) {
                $response = ['status' => 'error', 'message' => 'Failed to update item: ' . $e->getMessage()];
            }
            header('Content-Type: application/json');
            ob_end_clean();
            echo json_encode($response);
            exit;
        }
    }

    // Fetch inventory data (ensure unique InventoryID)
    $inventoryResult = $conn->query("SELECT i.InventoryID, i.ItemName, i.StockLevel, r.ReorderLevel 
        FROM inventory i 
        LEFT JOIN reorder_levels r ON i.InventoryID = r.InventoryID 
        GROUP BY i.InventoryID");
    $inventoryItems = $inventoryResult->fetch_all(MYSQLI_ASSOC);
    error_log('Fetched inventory items: ' . print_r($inventoryItems, true));

    // Fetch reorder data
    $reorderResult = $conn->query("SELECT i.InventoryID, i.ItemName, i.StockLevel, r.ReorderLevel 
        FROM reorder_levels r 
        JOIN inventory i ON r.InventoryID = i.InventoryID 
        GROUP BY r.InventoryID");
    $reorderItems = $reorderResult->fetch_all(MYSQLI_ASSOC);
    error_log('Fetched reorder items: ' . print_r($reorderItems, true));

    // Fetch stock movements (for view action)
    $movementsResult = $conn->query("SELECT sm.movementID, sm.InventoryID, i.ItemName, sm.MovementType, sm.Quantity, sm.MovementDate 
        FROM stock_movements sm 
        JOIN inventory i ON sm.InventoryID = i.InventoryID 
        ORDER BY sm.MovementDate DESC");
    $stockMovements = $movementsResult->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        ob_end_clean();
        echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
        exit;
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Database connection failed: " . $e->getMessage() . "',
                confirmButtonText: 'OK'
            });
        </script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        deepbrown: '#594423',
                        champagnelbeige: '#F7E6CA',
                        softpillow: '#FFF6E8',
                    },
                    boxShadow: {
                        subtle: '0px 0px 4px 8px rgba(0,0,0,0.01)',
                    },
                    borderRadius: {
                        btn: '8px',
                        container: '16px',
                        large: '24px',
                    },
                    fontFamily: {
                        cinzel: ['Cinzel', 'serif'],
                        georgia: ['Georgia', 'serif']
                    }
                }
            }
        }

        // Base URL for fetch requests
        const BASE_URL = window.location.pathname.includes('index.php') ? window.location.pathname : window.location.pathname + 'index.php';

        // View Stock Modal
        function showViewStockModal(inventoryId) {
            fetch(`${BASE_URL}?action=view_stock&id=${inventoryId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
            })
            .then(response => {
                console.log('View Stock Response:', response);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                console.log('View Stock Data:', data);
                if (data.status === 'error') {
                    throw new Error(data.message);
                }
                let item = data.item;
                let movements = data.movements;
                let movementsHtml = movements.length > 0 ? movements.map(movement => `
                    <tr class="hover:bg-champagnelbeige">
                        <td>${movement.movementID}</td>
                        <td>${movement.ItemName}</td>
                        <td>${movement.MovementType}</td>
                        <td>${movement.Quantity}</td>
                        <td>${movement.MovementDate}</td>
                    </tr>
                `).join('') : '<tr><td colspan="5" class="text-center py-4">No movements found.</td></tr>';
                Swal.fire({
                    title: `Stock Details for ${item.ItemName}`,
                    html: `
                        <div class="text-deepbrown p-4">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <p><strong>Item ID:</strong> ${item.InventoryID}</p>
                                <p><strong>Stock Level:</strong> ${item.StockLevel}</p>
                                <p><strong>Reorder Level:</strong> ${item.ReorderLevel || 'N/A'}</p>
                                <p><strong>Status:</strong> 
                                    <span class="${item.StockLevel <= (item.ReorderLevel || Infinity) ? 'text-red-600' : 'text-green-600'}">
                                        ${item.StockLevel <= (item.ReorderLevel || Infinity) ? 'Low Stock' : 'Sufficient'}
                                    </span>
                                </p>
                            </div>
                            <h3 class="text-lg font-semibold mt-4 mb-2">Stock Movements</h3>
                            <div class="table-container max-h-60 overflow-y-auto rounded-lg border border-champagnelbeige">
                                <table class="w-full bg-white">
                                    <thead class="bg-champagnelbeige">
                                        <tr>
                                            <th class="py-3">Movement ID</th>
                                            <th class="py-3">Item Name</th>
                                            <th class="py-3">Type</th>
                                            <th class="py-3">Quantity</th>
                                            <th class="py-3">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>${movementsHtml}</tbody>
                                </table>
                            </div>
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonText: 'Close',
                    confirmButtonColor: '#594423',
                    customClass: {
                        popup: 'bg-softpillow text-deepbrown rounded-xl',
                        title: 'text-2xl font-cinzel mb-4',
                        htmlContainer: 'p-6',
                        confirmButton: 'px-6 py-2 rounded-lg'
                    },
                    didOpen: () => {
                        const container = Swal.getHtmlContainer();
                        container.querySelectorAll('table th, table td').forEach(cell => {
                            cell.style.padding = '12px';
                            cell.style.textAlign = 'center';
                            cell.style.borderBottom = '1px solid #e5e7eb';
                        });
                        container.querySelector('thead').style.position = 'sticky';
                        container.querySelector('thead').style.top = '0';
                    }
                });
            })
            .catch(error => {
                console.error('View Stock Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load stock details: ' + error.message,
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'bg-softpillow text-deepbrown rounded-xl',
                        title: 'text-2xl font-cinzel',
                        confirmButton: 'px-6 py-2 rounded-lg'
                    }
                });
            });
        }

        // Edit Modal
        function showEditModal(inventoryId) {
            let isSubmitting = false; // Prevent multiple submissions
            fetch(`${BASE_URL}?action=edit&id=${inventoryId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
            })
            .then(response => {
                console.log('Edit Response:', response);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                console.log('Edit Data:', data);
                if (data.status === 'error') {
                    throw new Error(data.message);
                }
                let item = data.item;
                Swal.fire({
                    title: `Edit ${item.ItemName}`,
                    html: `
                        <form id="editForm" class="text-deepbrown p-4">
                            <input type="hidden" name="inventory_id" value="${item.InventoryID}">
                            <div class="mb-4">
                                <label class="block mb-2 text-sm font-semibold">Item Name</label>
                                <input type="text" name="item_name" value="${item.ItemName}" class="w-full p-3 border border-champagnelbeige rounded-lg focus:outline-none focus:ring-2 focus:ring-deepbrown" required>
                            </div>
                            <div class="mb-4">
                                <label class="block mb-2 text-sm font-semibold">Stock Level</label>
                                <input type="number" name="stock_level" value="${item.StockLevel}" class="w-full p-3 border border-champagnelbeige rounded-lg focus:outline-none focus:ring-2 focus:ring-deepbrown" required min="0">
                            </div>
                            <div class="mb-4">
                                <label class="block mb-2 text-sm font-semibold">Reorder Level</label>
                                <input type="number" name="reorder_level" value="${item.ReorderLevel || 0}" class="w-full p-3 border border-champagnelbeige rounded-lg bg-gray-100" readonly>
                            </div>
                        </form>
                    `,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#594423',
                    cancelButtonColor: '#d33',
                    customClass: {
                        popup: 'bg-softpillow text-deepbrown rounded-xl',
                        title: 'text-2xl font-cinzel mb-4',
                        htmlContainer: 'p-6',
                        confirmButton: 'px-6 py-2 rounded-lg',
                        cancelButton: 'px-6 py-2 rounded-lg'
                    },
                    didOpen: () => {
                        const confirmButton = Swal.getConfirmButton();
                        confirmButton.id = 'editConfirmButton';
                    },
                    preConfirm: () => {
                        if (isSubmitting) {
                            return false; // Prevent multiple submissions
                        }
                        isSubmitting = true;
                        const confirmButton = document.getElementById('editConfirmButton');
                        confirmButton.disabled = true; // Disable button to prevent double clicks
                        console.log('Submitting edit form for InventoryID:', inventoryId);
                        const form = Swal.getHtmlContainer().querySelector('#editForm');
                        const formData = new FormData(form);
                        return fetch(`${BASE_URL}?action=edit_item&id=${inventoryId}`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                        })
                        .then(response => {
                            console.log('Edit Submit Response:', response);
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error('Network response was not ok: ' + response.statusText + ' - Response: ' + text);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Edit Submit Data:', data);
                            if (data.status === 'error') {
                                throw new Error(data.message);
                            }
                            return data;
                        })
                        .catch(error => {
                            console.error('Edit Submit Error:', error);
                            isSubmitting = false;
                            confirmButton.disabled = false;
                            Swal.showValidationMessage(`Request failed: ${error.message}`);
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: result.value.message,
                            showConfirmButton: false,
                            timer: 1500,
                            customClass: {
                                popup: 'bg-softpillow text-deepbrown rounded-xl',
                                title: 'text-2xl font-cinzel'
                            }
                        }).then(() => {
                            location.reload();
                        });
                    }
                });
            })
            .catch(error => {
                console.error('Edit Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load edit form: ' + error.message,
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'bg-softpillow text-deepbrown rounded-xl',
                        title: 'text-2xl font-cinzel',
                        confirmButton: 'px-6 py-2 rounded-lg'
                    }
                });
            });
        }

        // Delete Confirmation Modal
        function showDeleteModal(inventoryId) {
            Swal.fire({
                title: 'Delete Item',
                text: 'Are you sure you want to delete this item? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel',
                customClass: {
                    popup: 'bg-softpillow text-deepbrown rounded-xl',
                    title: 'text-2xl font-cinzel mb-4',
                    confirmButton: 'px-6 py-2 rounded-lg',
                    cancelButton: 'px-6 py-2 rounded-lg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`${BASE_URL}?action=delete&id=${inventoryId}&confirm=yes`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                    })
                    .then(response => {
                        console.log('Delete Response:', response);
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Delete Data:', data);
                        if (data.status === 'error') {
                            throw new Error(data.message);
                        }
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500,
                            customClass: {
                                popup: 'bg-softpillow text-deepbrown rounded-xl',
                                title: 'text-2xl font-cinzel'
                            }
                        }).then(() => {
                            location.reload();
                        });
                    })
                    .catch(error => {
                        console.error('Delete Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to delete item: ' + error.message,
                            confirmButtonText: 'OK',
                            customClass: {
                                popup: 'bg-softpillow text-deepbrown rounded-xl',
                                title: 'text-2xl font-cinzel',
                                confirmButton: 'px-6 py-2 rounded-lg'
                            }
                        });
                    });
                }
            });
        }

        // Add Item Form Submission
        document.addEventListener('DOMContentLoaded', () => {
            const addForm = document.querySelector('form[action="?action=add_item"]');
            if (addForm) {
                addForm.setAttribute('action', `${BASE_URL}?action=add_item`);
                addForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const formData = new FormData(addForm);
                    try {
                        const response = await fetch(`${BASE_URL}?action=add_item`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                        });
                        console.log('Add Item Response:', response);
                        if (!response.ok) {
                            const text = await response.text();
                            throw new Error('Network response was not ok: ' + response.statusText + ' - Response: ' + text);
                        }
                        const data = await response.json();
                        console.log('Add Item Data:', data);
                        if (data.status === 'error') {
                            throw new Error(data.message);
                        }
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500,
                            customClass: {
                                popup: 'bg-softpillow text-deepbrown rounded-xl',
                                title: 'text-2xl font-cinzel'
                            }
                        }).then(() => {
                            location.href = `${BASE_URL}?action=view`;
                        });
                    } catch (error) {
                        console.error('Add Item Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to add item: ' + error.message,
                            confirmButtonText: 'OK',
                            customClass: {
                                popup: 'bg-softpillow text-deepbrown rounded-xl',
                                title: 'text-2xl font-cinzel',
                                confirmButton: 'px-6 py-2 rounded-lg'
                            }
                        });
                    }
                });
            }
        });
    </script>
    <style>
        .table-container {
            max-height: 400px;
            overflow-y: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background-color: #F7E6CA;
            font-weight: 600;
        }
        .action-btn {
            padding: 8px;
            margin: 0 4px;
            transition: transform 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            min-width: 36px;
            height: 36px;
        }
        .action-btn:hover {
            transform: scale(1.05);
        }
        .action-btn i {
            font-size: 18px;
        }
        .tooltip {
            position: relative;
            display: inline-block;
        }
        .tooltip .tooltiptext {
            visibility: hidden;
            width: 120px;
            background-color: #594423;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px 0;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -60px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }
    </style>
</head>

<body class="flex min-h-screen w-full bg-softpillow font-georgia text-deepbrown">
<div class="flex min-h-screen w-full bg-softpillow font-georgia text-deepbrown">
    <?php include('../../includes/sidebar.php'); ?>
    <div class="main w-full md:ml-[320px]">
        <?php include('../../includes/navbar.php'); ?>

        <main class="main-content w-full px-4 py-8">
            <div class="max-w-6xl mx-auto">
                <h1 class="text-2xl font-bold text-deepbrown mb-6">Inventory Management</h1>

                <?php if ($action === 'view'): ?>
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-semibold text-deepbrown">Inventory Items</h2>
                            <a href="?action=add" class="bg-deepbrown text-white px-4 py-2 rounded-lg hover:bg-[#4E3B2A] action-btn">
                                <i class='bx bx-plus'> Add Items Stock</i>
                            </a>
                        </div>
                        <div class="table-container">
                            <table class="w-full bg-white rounded-lg">
                                <thead class="bg-champagnelbeige sticky top-0">
                                    <tr>
                                        <th>ID</th>
                                        <th>Item Name</th>
                                        <th>Stock Level</th>
                                        <th>Reorder Level</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $renderedIds = [];
                                    foreach ($inventoryItems as $item): 
                                        if (in_array($item['InventoryID'], $renderedIds)) {
                                            error_log('Duplicate InventoryID detected in inventory table: ' . $item['InventoryID']);
                                            continue;
                                        }
                                        $renderedIds[] = $item['InventoryID'];
                                    ?>
                                        <tr>
                                            <td><?php echo $item['InventoryID']; ?></td>
                                            <td><?php echo htmlspecialchars($item['ItemName']); ?></td>
                                            <td><?php echo $item['StockLevel']; ?></td>
                                            <td><?php echo $item['ReorderLevel'] ?? 'N/A'; ?></td>
                                            <td>
                                                <?php echo isset($item['ReorderLevel']) && $item['StockLevel'] <= $item['ReorderLevel'] ? 
                                                    '<span class="text-red-600">Low Stock</span>' : 
                                                    '<span class="text-green-600">Sufficient</span>'; ?>
                                            </td>
                                            <td>
                                                <div class="flex justify-center space-x-2">
                                                    <div class="tooltip">
                                                        <button onclick="showViewStockModal(<?php echo $item['InventoryID']; ?>)" 
                                                                class="action-btn bg-green-500 text-white rounded">
                                                            <i class='bx bx-show'></i>
                                                        </button>
                                                        <span class="tooltiptext">View Stock</span>
                                                    </div>
                                                    <div class="tooltip">
                                                        <button onclick="showEditModal(<?php echo $item['InventoryID']; ?>)" 
                                                                class="action-btn bg-blue-500 text-white rounded">
                                                            <i class='bx bx-edit'></i>
                                                        </button>
                                                        <span class="tooltiptext">Edit</span>
                                                    </div>
                                                    <div class="tooltip">
                                                        <button onclick="showDeleteModal(<?php echo $item['InventoryID']; ?>)" 
                                                                class="action-btn bg-red-500 text-white rounded">
                                                            <i class='bx bx-trash'></i>
                                                        </button>
                                                        <span class="tooltiptext">Delete</span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($inventoryItems)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4">No inventory items found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-deepbrown mb-4">Stock Movements</h2>
                        <div class="table-container">
                            <table class="w-full bg-white rounded-lg">
                                <thead class="bg-champagnelbeige sticky top-0">
                                    <tr>
                                        <th>Movement ID</th>
                                        <th>Item Name</th>
                                        <th>Type</th>
                                        <th>Quantity</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stockMovements as $movement): ?>
                                        <tr>
                                            <td><?php echo $movement['movementID']; ?></td>
                                            <td><?php echo htmlspecialchars($movement['ItemName']); ?></td>
                                            <td><?php echo $movement['MovementType']; ?></td>
                                            <td><?php echo $movement['Quantity']; ?></td>
                                            <td><?php echo $movement['MovementDate']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($stockMovements)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4">No stock movements found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                <?php elseif ($action === 'add'): ?>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-semibold text-deepbrown mb-4">Add New Item</h2>
                        <form method="POST" action="?action=add_item">
                            <div class="mb-4">
                                <label class="block text-deepbrown mb-2 text-sm font-semibold">Item Name</label>
                                <input type="text" name="item_name" class="w-full p-3 border border-champagnelbeige rounded-lg focus:outline-none focus:ring-2 focus:ring-deepbrown" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-deepbrown mb-2 text-sm font-semibold">Stock Level</label>
                                <input type="number" name="stock_level" class="w-full p-3 border border-champagnelbeige rounded-lg focus:outline-none focus:ring-2 focus:ring-deepbrown" required min="0">
                            </div>
                            <div class="mb-4">
                                <label class="block text-deepbrown mb-2 text-sm font-semibold">Reorder Level</label>
                                <input type="number" name="reorder_level" class="w-full p-3 border border-champagnelbeige rounded-lg focus:outline-none focus:ring-2 focus:ring-deepbrown" required min="0">
                            </div>
                            <div class="flex space-x-4">
                                <button type="submit" class="bg-deepbrown text-white px-6 py-2 rounded-lg hover:bg-[#4E3B2A] action-btn">
                                    <i class='bx bx-save mr-2'></i> Save
                                </button>
                                <a href="?action=view" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 action-btn">
                                    <i class='bx bx-x mr-2'></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="../../assets/scripts.js"></script>
</body>
</html>