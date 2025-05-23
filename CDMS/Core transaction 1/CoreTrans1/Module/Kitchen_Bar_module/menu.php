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
$db = new Database();
$conn = $db->connect('kitchen');



if (!$conn) {
    error_log("Database connection failed: " . $db->getError());
    die("Database connection failed. Please try again later.");
}

$stmt = $conn->prepare("SELECT MenuItemID, ItemName, Category, Price, Qty, ImagePath FROM menuitems");
$stmt->execute();
$result = $stmt->get_result();
$menuItems = [];
if ($result) {
    $menuItems = $result->fetch_all(MYSQLI_ASSOC);
    foreach ($menuItems as $item) {
        if (empty($item['MenuItemID']) || !is_numeric($item['MenuItemID']) || $item['MenuItemID'] <= 0) {
            error_log("Invalid MenuItemID in database: " . json_encode($item));
        }
    }
} else {
    error_log("Failed to fetch menu items: " . $conn->error);
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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
    </script>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 16px;
            text-align: center;
            border-bottom: 1px solid #F7E6CA;
        }
        th {
            background-color: #F7E6CA;
            font-weight: 600;
        }
        .action-btn {
            padding: 8px 16px;
            transition: background-color 0.2s;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            font-size: 14px;
        }
        .action-btn i {
            margin-right: 4px;
        }
        .action-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
<div class="flex min-h-screen w-full bg-softpillow font-georgia text-deepbrown">
    <?php include('../../includes/sidebar.php'); ?>
    <div class="main w-full md:ml-[320px]">
        <?php include('../../includes/navbar.php'); ?>
        <div class="max-w-6xl mx-auto px-4 py-8">
            <h1 class="text-3xl font-cinzel font-bold mb-6">Menu Management</h1>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
                <button onclick="openModal()" class="bg-green-600 text-white px-6 py-3 rounded-btn hover:bg-green-700 transition duration-200 shadow-subtle flex items-center">
                    <i class="fas fa-plus mr-2"></i>Add Menu Item
                </button>
                <div class="relative w-full md:w-1/3">
                    <input 
                        type="text" 
                        id="searchInput" 
                        oninput="filterMenu()" 
                        placeholder="Search menu item..." 
                        class="w-full pl-10 pr-4 py-2 border border-champagnelbeige rounded-btn shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-300 transition duration-200"
                    />
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-deepbrown/60"></i>
                </div>
            </div>
            <div class="bg-white shadow-subtle rounded-container overflow-hidden">
                <div class="overflow-x-auto">
                    <table id="menuTable" class="min-w-full text-center text-sm">
                        <thead class="bg-champagnelbeige text-deepbrown">
                            <tr>
                                <th class="p-4 font-semibold">ID</th>
                                <th class="p-4 font-semibold">Image</th>
                                <th class="p-4 font-semibold">Name</th>
                                <th class="p-4 font-semibold">Category</th>
                                <th class="p-4 font-semibold">Price</th>
                                <th class="p-4 font-semibold">Quantity</th>
                                <th class="p-4 font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($menuItems as $item): ?>
                                <?php 
                                $isValidId = !empty($item['MenuItemID']) && is_numeric($item['MenuItemID']) && $item['MenuItemID'] > 0;
                                ?>
                                <tr class="border-b border-champagnelbeige hover:bg-softpillow transition duration-150" id="menuItem-<?= htmlspecialchars($item['MenuItemID'] ?? 'invalid') ?>">
                                    <td class="p-4"><?= htmlspecialchars($item['MenuItemID'] ?? 'N/A') ?></td>
                                    <td class="p-4">
                                        <img src="<?= htmlspecialchars($item['ImagePath'] ?? '/images/placeholder.jpg') ?>" alt="Image" class="w-16 h-16 object-cover rounded mx-auto">
                                    </td>
                                    <td class="p-4"><?= htmlspecialchars($item['ItemName'] ?? 'No Name') ?></td>
                                    <td class="p-4"><?= htmlspecialchars($item['Category'] ?? 'N/A') ?></td>
                                    <td class="p-4">₱<?= number_format($item['Price'] ?? 0, 2) ?></td>
                                    <td class="p-4"><?= htmlspecialchars($item['Qty'] ?? 0) ?></td>
                                    <td class="p-4 flex justify-center space-x-2">
                                        <button 
                                            data-id="<?= htmlspecialchars($item['MenuItemID'] ?? '') ?>" 
                                            data-item='<?= json_encode($item, JSON_HEX_QUOT | JSON_HEX_APOS) ?>' 
                                            class="view-btn action-btn bg-yellow-500 text-white hover:bg-yellow-600 <?= $isValidId ? '' : 'opacity-50 cursor-not-allowed' ?>" 
                                            <?= $isValidId ? '' : 'disabled' ?>
                                        >
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button 
                                            data-item='<?= json_encode($item, JSON_HEX_QUOT | JSON_HEX_APOS) ?>' 
                                            class="edit-btn action-btn bg-blue-500 text-white hover:bg-blue-600"
                                        >
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button 
                                            data-id="<?= htmlspecialchars($item['MenuItemID'] ?? 0) ?>" 
                                            data-name="<?= htmlspecialchars($item['ItemName'] ?? 'Unknown Item') ?>" 
                                            class="delete-btn action-btn bg-red-500 text-white hover:bg-red-600"
                                        >
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Item Modal -->
<div id="viewItemModal" class="fixed inset-0 bg-deepbrown bg-opacity-75 flex items-center justify-center z-50 hidden transition-opacity duration-300">
    <div class="bg-white p-8 rounded-container shadow-subtle w-full max-w-2xl max-h-[70vh] overflow-y-auto transform transition-transform duration-200 scale-100">
        <h2 class="text-2xl font-cinzel font-bold mb-6 text-deepbrown">Menu Item Details</h2>
        <div id="viewItemContent" class="space-y-4"></div>
        <div class="flex justify-end mt-6">
            <button onclick="closeViewModal()" class="bg-gray-300 text-deepbrown px-6 py-3 rounded-btn hover:bg-gray-400 transition duration-200 w-32">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Add/Edit Menu Item Modal -->
<div id="menuItemModal" class="fixed inset-0 bg-deepbrown bg-opacity-75 flex items-center justify-center z-50 hidden transition-opacity duration-300">
    <div class="bg-white p-8 rounded-container shadow-subtle w-full max-w-4xl max-h-[70vh] overflow-y-auto transform transition-transform duration-200 scale-100">
        <h2 class="text-2xl font-cinzel font-bold mb-6 text-deepbrown text-center" id="modalTitle">Add Menu Item</h2>
        <form id="menuItemForm" onsubmit="handleFormSubmit(event)" enctype="multipart/form-data" data-operation="add">
            <input type="hidden" id="menuItemId" name="MenuItemID">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div id="menuItemIdDisplay" class="hidden">
                        <label for="menuItemId" class="block text-deepbrown font-semibold mb-1">Menu Item ID</label>
                        <input type="text" id="menuItemIdReadonly" class="w-full p-2 border border-champagnelbeige rounded-btn bg-gray-100" readonly>
                    </div>
                    <div>
                        <label for="itemName" class="block text-deepbrown font-semibold mb-1">Item Name *</label>
                        <input type="text" id="itemName" name="ItemName" class="w-full p-2 border border-champagnelbeige rounded-btn focus:outline-none focus:ring-2 focus:ring-blue-300 transition duration-200" required>
                    </div>
                    <div>
                        <label for="category" class="block text-deepbrown font-semibold mb-1">Category *</label>
                        <select id="category" name="Category" class="w-full p-2 border border-champagnelbeige rounded-btn focus:outline-none focus:ring-2 focus:ring-blue-300 transition duration-200" required>
                            <option value="Food">Food</option>
                            <option value="Beverage">Beverage</option>
                        </select>
                    </div>
                    <div>
                        <label for="price" class="block text-deepbrown font-semibold mb-1">Price (₱) *</label>
                        <input type="number" step="0.01" id="price" name="Price" class="w-full p-2 border border-champagnelbeige rounded-btn focus:outline-none focus:ring-2 focus:ring-blue-300 transition duration-200" required min="0.01">
                    </div>
                    <div>
                        <label for="qty" class="block text-deepbrown font-semibold mb-1">Quantity *</label>
                        <input type="number" id="qty" name="Qty" class="w-full p-2 border border-champagnelbeige rounded-btn focus:outline-none focus:ring-2 focus:ring-blue-300 transition duration-200" required min="0">
                    </div>
                    <div>
                        <label for="image" class="block text-deepbrown font-semibold mb-1">Image</label>
                        <input type="file" id="image" name="Image" accept="image/*" class="w-full p-2 border border-champagnelbeige rounded-btn focus:outline-none focus:ring-2 focus:ring-blue-300 transition duration-200">
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <label for="recipeName" class="block text-deepbrown font-semibold mb-1">Recipe Name</label>
                        <input type="text" id="recipeName" name="RecipeName" class="w-full p-2 border border-champagnelbeige rounded-btn focus:outline-none focus:ring-2 focus:ring-blue-300 transition duration-200">
                    </div>
                    <div>
                        <label for="recipeDetails" class="block text-deepbrown font-semibold mb-1">Recipe Details</label>
                        <textarea id="recipeDetails" name="RecipeDetails" class="w-full p-2 border border-champagnelbeige rounded-btn focus:outline-none focus:ring-2 focus:ring-blue-300 transition duration-200" rows="4"></textarea>
                    </div>
                    <div>
                        <label class="block text-deepbrown font-semibold mb-1">Ingredients</label>
                        <div id="ingredientList" class="space-y-2"></div>
                        <button type="button" onclick="addIngredientField()" class="mt-2 bg-green-500 text-white px-4 py-2 rounded-btn hover:bg-green-600 transition duration-200 flex items-center">
                            <i class="fas fa-plus mr-1"></i>Add Ingredient
                        </button>
                    </div>
                </div>
            </div>
            <input type="hidden" id="existingImagePath" name="ImagePath">
            <div class="flex justify-between mt-6">
                <button type="button" onclick="closeModal这两个() ?>" class="text-deepbrown hover:text-deepbrown/80 px-4 py-2 transition duration-200">
                    Close
                </button>
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-btn hover:bg-blue-600 transition duration-200">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Success Message Modal -->
<div id="successMessage" class="fixed inset-0 bg-deepbrown bg-opacity-75 flex items-center justify-center z-50 hidden transition-opacity duration-300">
    <div class="bg-white p-6 rounded-container shadow-subtle w-full max-w-md transform transition-transform duration-200 scale-100">
        <h2 class="text-2xl font-cinzel font-bold text-green-500 text-center mb-4">Success!</h2>
        <p class="text-center text-deepbrown">Menu Item has been saved successfully.</p>
        <div class="flex justify-center mt-6">
            <button onclick="closeSuccessPopup()" class="bg-blue-500 text-white px-6 py-2 rounded-btn hover:bg-blue-600 transition duration-200">
                Close
            </button>
        </div>
    </div>
</div>

<script src="../../assets/menumanagement.js"></script>
<script src="../../assets/scripts.js"></script>
</body>
</html>