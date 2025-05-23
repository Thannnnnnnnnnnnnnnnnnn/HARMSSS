<?php
header('Content-Type: application/json');
require_once '../../includes/Database.php';

$db = new Database();
$conn = $db->connect('kitchen');
$conn_order = $db->connect('orders');

$response = ['success' => false, 'error' => '', 'item' => null];

// Enable error logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', $_SERVER['DOCUMENT_ROOT'] . '/logs/php_error.log');

// Check database connections
if (!$conn || !$conn_order) {
    $error = $db->getError();
    error_log("Database connection failed: $error");
    $response['error'] = 'Database connection failed: ' . $error;
    echo json_encode($response);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method. Only POST is allowed.');
    }

    $action = isset($_POST['action']) ? trim($_POST['action']) : '';
    error_log("Action received: '$action'");

    $valid_actions = ['view', 'delete', 'add', 'edit'];
    if (empty($action) || !in_array($action, $valid_actions)) {
        throw new Exception('Invalid or missing action parameter. Expected: ' . implode(', ', $valid_actions));
    }

    if ($action === 'view') {
        $menuItemId = isset($_POST['MenuItemID']) && is_numeric($_POST['MenuItemID']) && $_POST['MenuItemID'] > 0 ? (int)$_POST['MenuItemID'] : null;
        if (!$menuItemId) {
            throw new Exception('Invalid Menu Item ID.');
        }
        $stmt = $conn->prepare("SELECT MenuItemID, ItemName, Category, Price, Qty, ImagePath FROM menuitems WHERE MenuItemID = ?");
        if (!$stmt) {
            throw new Exception('Prepare failed for menuitems selection: ' . $conn->error);
        }
        $stmt->bind_param("i", $menuItemId);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed for menuitems selection: ' . $stmt->error);
        }
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();
        $stmt->close();
        if (!$item) {
            throw new Exception('Menu item not found for MenuItemID: ' . $menuItemId);
        }
        $stmt = $conn->prepare("SELECT RecipeID, RecipeName, RecipeDetails FROM recipes WHERE MenuItemID = ?");
        if (!$stmt) {
            throw new Exception('Prepare failed for recipes selection: ' . $conn->error);
        }
        $stmt->bind_param("i", $menuItemId);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed for recipes selection: ' . $stmt->error);
        }
        $result = $stmt->get_result();
        $item['recipe'] = $result->fetch_assoc() ?: null;
        $stmt->close();
        $recipeId = $item['recipe']['RecipeID'] ?? 0;
        $stmt = $conn->prepare("SELECT IngredientID, IngredientName, Quantity FROM ingredients WHERE RecipeID = ?");
        if (!$stmt) {
            throw new Exception('Prepare failed for ingredients selection: ' . $conn->error);
        }
        $stmt->bind_param("i", $recipeId);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed for ingredients selection: ' . $stmt->error);
        }
        $result = $stmt->get_result();
        $item['ingredients'] = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $response['success'] = true;
        $response['item'] = $item;
        error_log("Successfully fetched item for MenuItemID: $menuItemId");
    } elseif ($action === 'delete') {
        $menuItemId = isset($_POST['MenuItemID']) && is_numeric($_POST['MenuItemID']) && $_POST['MenuItemID'] > 0 ? (int)$_POST['MenuItemID'] : null;
        if (!$menuItemId) {
            throw new Exception('Invalid Menu Item ID: ' . ($_POST['MenuItemID'] ?? 'null'));
        }

        $conn->begin_transaction();
        $conn_order->begin_transaction();

        try {
            // Fetch image path
            $stmt = $conn->prepare("SELECT ImagePath FROM menuitems WHERE MenuItemID = ?");
            if (!$stmt) {
                throw new Exception('Prepare failed for menuitems image path selection: ' . $conn->error);
            }
            $stmt->bind_param("i", $menuItemId);
            if (!$stmt->execute()) {
                throw new Exception('Execute failed for menuitems image path selection: ' . $stmt->error);
            }
            $result = $stmt->get_result();
            $item = $result->fetch_assoc();
            $imagePath = $item['ImagePath'] ?? null;
            $stmt->close();

            // Delete related ingredients
            $stmt = $conn->prepare("SELECT RecipeID FROM recipes WHERE MenuItemID = ?");
            if (!$stmt) {
                throw new Exception('Prepare failed for recipe selection: ' . $conn->error);
            }
            $stmt->bind_param("i", $menuItemId);
            if (!$stmt->execute()) {
                throw new Exception('Execute failed for recipe selection: ' . $stmt->error);
            }
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $recipeId = $row['RecipeID'];
                $stmt2 = $conn->prepare("DELETE FROM ingredients WHERE RecipeID = ?");
                if (!$stmt2) {
                    throw new Exception('Prepare failed for ingredients deletion: ' . $conn->error);
                }
                $stmt2->bind_param("i", $recipeId);
                if (!$stmt2->execute()) {
                    throw new Exception('Execute failed for ingredients deletion: ' . $stmt2->error);
                }
                error_log("Deleted ingredients for RecipeID: $recipeId");
                $stmt2->close();
            }
            $stmt->close();

            // Delete related recipes
            $stmt = $conn->prepare("DELETE FROM recipes WHERE MenuItemID = ?");
            if (!$stmt) {
                throw new Exception('Prepare failed for recipes deletion: ' . $conn->error);
            }
            $stmt->bind_param("i", $menuItemId);
            if (!$stmt->execute()) {
                throw new Exception('Execute failed for recipes deletion: ' . $stmt->error);
            }
            error_log("Deleted recipes for MenuItemID: $menuItemId");
            $stmt->close();

            // Delete related order items
            $stmt = $conn_order->prepare("DELETE FROM orderitems WHERE MenuItemID = ?");
            if (!$stmt) {
                throw new Exception('Prepare failed for orderitems deletion: ' . $conn_order->error);
            }
            $stmt->bind_param("i", $menuItemId);
            if (!$stmt->execute()) {
                throw new Exception('Execute failed for orderitems deletion: ' . $stmt->error);
            }
            error_log("Deleted orderitems for MenuItemID: $menuItemId");
            $stmt->close();

            // Delete menu item
            $stmt = $conn->prepare("DELETE FROM menuitems WHERE MenuItemID = ?");
            if (!$stmt) {
                throw new Exception('Prepare failed for menuitems deletion: ' . $conn->error);
            }
            $stmt->bind_param("i", $menuItemId);
            if (!$stmt->execute()) {
                throw new Exception('Execute failed for menuitems deletion: ' . $stmt->error);
            }
            if ($stmt->affected_rows === 0) {
                throw new Exception('Menu item not found or could not be deleted for MenuItemID: ' . $menuItemId);
            }
            $stmt->close();

            // Delete image file
            if ($imagePath && file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                if (!unlink($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                    error_log("Failed to delete image file: $imagePath");
                } else {
                    error_log("Deleted image file: $imagePath");
                }
            }

            $conn->commit();
            $conn_order->commit();
            $response['success'] = true;
            error_log("Successfully deleted MenuItemID: $menuItemId");
        } catch (Exception $e) {
            $conn->rollback();
            $conn_order->rollback();
            error_log("Delete transaction failed for MenuItemID $menuItemId: " . $e->getMessage());
            throw $e;
        }
    } elseif ($action === 'add' || $action === 'edit') {
        $menuItemId = isset($_POST['MenuItemID']) && is_numeric($_POST['MenuItemID']) && $_POST['MenuItemID'] > 0 ? (int)$_POST['MenuItemID'] : null;
        $menuName = trim($_POST['ItemName'] ?? '');
        $category = trim($_POST['Category'] ?? '');
        $price = isset($_POST['Price']) && is_numeric($_POST['Price']) && $_POST['Price'] > 0 ? (float)$_POST['Price'] : 0;
        $qty = isset($_POST['Qty']) && is_numeric($_POST['Qty']) && $_POST['Qty'] >= 0 ? (int)$_POST['Qty'] : -1;
        $recipeName = trim($_POST['RecipeName'] ?? '');
        $recipeDetails = trim($_POST['RecipeDetails'] ?? '');
        $ingredientNames = isset($_POST['IngredientName']) && is_array($_POST['IngredientName']) ? array_map('trim', $_POST['IngredientName']) : [];
        $ingredientQuantities = isset($_POST['IngredientQuantity']) && is_array($_POST['IngredientQuantity']) ? array_map('floatval', $_POST['IngredientQuantity']) : [];

        error_log("Input data for $action: " . json_encode([
            'MenuItemID' => $menuItemId,
            'ItemName' => $menuName,
            'Category' => $category,
            'Price' => $price,
            'Qty' => $qty,
            'RecipeName' => $recipeName,
            'RecipeDetails' => $recipeDetails,
            'IngredientNames' => $ingredientNames,
            'IngredientQuantities' => $ingredientQuantities
        ]));

        // Validation
        if (empty($menuName)) {
            throw new Exception("Item Name is required.");
        }
        if (!in_array($category, ['Food', 'Beverage'])) {
            throw new Exception("Invalid Category. Must be 'Food' or 'Beverage'.");
        }
        if ($price <= 0) {
            throw new Exception("Price must be a positive number.");
        }
        if ($qty < 0) {
            throw new Exception("Quantity must be non-negative.");
        }
        if ($action === 'edit' && !$menuItemId) {
            throw new Exception("Valid Menu Item ID is required for editing.");
        }
        if (($recipeName && !$recipeDetails) || (!$recipeName && $recipeDetails)) {
            throw new Exception("Both Recipe Name and Recipe Details are required if one is provided.");
        }

        // Handle image upload
        $imagePath = null;
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/images/';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new Exception("Failed to create upload directory: $uploadDir");
            }
        }
        if (!is_writable($uploadDir)) {
            throw new Exception("Upload directory is not writable: $uploadDir");
        }

        if (isset($_FILES['Image']) && $_FILES['Image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['Image'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = mime_content_type($file['tmp_name']);
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception("Invalid file type. Only JPEG, PNG, and GIF are allowed.");
            }
            $maxSize = 5 * 1024 * 1024;
            if ($file['size'] > $maxSize) {
                throw new Exception("File size exceeds 5MB limit.");
            }
            $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
            $uniqueName = uniqid('menu_item_') . '.' . $fileExt;
            $imagePath = '/images/' . $uniqueName;
            $filePath = $uploadDir . $uniqueName;
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                throw new Exception("Failed to upload the image.");
            }
            error_log("Uploaded image: $imagePath");
        } else {
            $imagePath = $_POST['ImagePath'] ?? null;
        }

        $conn->begin_transaction();

        try {
            if ($action === 'edit' && $menuItemId) {
                $stmt = $conn->prepare("SELECT MenuItemID FROM menuitems WHERE MenuItemID = ?");
                if (!$stmt) {
                    throw new Exception('Prepare failed for menuitems existence check: ' . $conn->error);
                }
                $stmt->bind_param("i", $menuItemId);
                if (!$stmt->execute()) {
                    throw new Exception('Execute failed for menuitems existence check: ' . $stmt->error);
                }
                $result = $stmt->get_result();
                if ($result->num_rows === 0) {
                    throw new Exception("Menu Item ID $menuItemId does not exist.");
                }
                $stmt->close();

                $stmt = $conn->prepare("UPDATE menuitems SET ItemName = ?, Category = ?, Price = ?, Qty = ?, ImagePath = ? WHERE MenuItemID = ?");
                if (!$stmt) {
                    throw new Exception('Prepare failed for menuitems update: ' . $conn->error);
                }
                $stmt->bind_param("ssdssi", $menuName, $category, $price, $qty, $imagePath, $menuItemId);
                if (!$stmt->execute()) {
                    throw new Exception('Execute failed for menuitems update: ' . $stmt->error);
                }
                error_log("Updated menuitems for MenuItemID: $menuItemId");
                $stmt->close();

                $stmt = $conn->prepare("SELECT RecipeID FROM recipes WHERE MenuItemID = ?");
                if (!$stmt) {
                    throw new Exception('Prepare failed for recipes selection: ' . $conn->error);
                }
                $stmt->bind_param("i", $menuItemId);
                if (!$stmt->execute()) {
                    throw new Exception('Execute failed for recipes selection: ' . $stmt->error);
                }
                $result = $stmt->get_result();
                $recipe = $result->fetch_assoc();
                $stmt->close();

                if ($recipe) {
                    if ($recipeName && $recipeDetails) {
                        $stmt = $conn->prepare("UPDATE recipes SET RecipeName = ?, RecipeDetails = ? WHERE RecipeID = ?");
                        if (!$stmt) {
                            throw new Exception('Prepare failed for recipes update: ' . $conn->error);
                        }
                        $stmt->bind_param("ssi", $recipeName, $recipeDetails, $recipe['RecipeID']);
                        if (!$stmt->execute()) {
                            throw new Exception('Execute failed for recipes update: ' . $stmt->error);
                        }
                        error_log("Updated recipe for RecipeID: " . $recipe['RecipeID']);
                        $stmt->close();
                    } else {
                        $stmt = $conn->prepare("DELETE FROM ingredients WHERE RecipeID = ?");
                        if (!$stmt) {
                            throw new Exception('Prepare failed for ingredients deletion: ' . $conn->error);
                        }
                        $stmt->bind_param("i", $recipe['RecipeID']);
                        if (!$stmt->execute()) {
                            throw new Exception('Execute failed for ingredients deletion: ' . $stmt->error);
                        }
                        error_log("Deleted ingredients for RecipeID: " . $recipe['RecipeID']);
                        $stmt->close();

                        $stmt = $conn->prepare("DELETE FROM recipes WHERE RecipeID = ?");
                        if (!$stmt) {
                            throw new Exception('Prepare failed for recipes deletion: ' . $conn->error);
                        }
                        $stmt->bind_param("i", $recipe['RecipeID']);
                        if (!$stmt->execute()) {
                            throw new Exception('Execute failed for recipes deletion: ' . $stmt->error);
                        }
                        error_log("Deleted recipe for RecipeID: " . $recipe['RecipeID']);
                        $stmt->close();
                        $recipe = null;
                    }
                } elseif ($recipeName && $recipeDetails) {
                    $stmt = $conn->prepare("INSERT INTO recipes (MenuItemID, RecipeName, RecipeDetails) VALUES (?, ?, ?)");
                    if (!$stmt) {
                        throw new Exception('Prepare failed for recipes insert: ' . $conn->error);
                    }
                    $stmt->bind_param("iss", $menuItemId, $recipeName, $recipeDetails);
                    if (!$stmt->execute()) {
                        throw new Exception('Execute failed for recipes insert: ' . $stmt->error);
                    }
                    $recipeId = $conn->insert_id;
                    $recipe = ['RecipeID' => $recipeId];
                    error_log("Inserted recipe with RecipeID: $recipeId for MenuItemID: $menuItemId");
                    $stmt->close();
                }

                if ($recipe && $recipeName && $recipeDetails) {
                    $recipeId = $recipe['RecipeID'];
                    $stmt = $conn->prepare("DELETE FROM ingredients WHERE RecipeID = ?");
                    if (!$stmt) {
                        throw new Exception('Prepare failed for ingredients deletion: ' . $conn->error);
                    }
                    $stmt->bind_param("i", $recipeId);
                    if (!$stmt->execute()) {
                        throw new Exception('Execute failed for ingredients deletion: ' . $stmt->error);
                    }
                    error_log("Deleted existing ingredients for RecipeID: $recipeId");
                    $stmt->close();

                    for ($i = 0; $i < count($ingredientNames); $i++) {
                        if (!empty($ingredientNames[$i]) && is_numeric($ingredientQuantities[$i]) && $ingredientQuantities[$i] >= 0) {
                            $stmt = $conn->prepare("INSERT INTO ingredients (RecipeID, IngredientName, Quantity) VALUES (?, ?, ?)");
                            if (!$stmt) {
                                throw new Exception('Prepare failed for ingredients insert: ' . $conn->error);
                            }
                            $stmt->bind_param("isd", $recipeId, $ingredientNames[$i], $ingredientQuantities[$i]);
                            if (!$stmt->execute()) {
                                throw new Exception('Execute failed for ingredients insert: ' . $stmt->error);
                            }
                            error_log("Inserted ingredient: {$ingredientNames[$i]} for RecipeID: $recipeId");
                            $stmt->close();
                        }
                    }
                }
            } else {
                $stmt = $conn->prepare("INSERT INTO menuitems (ItemName, Category, Price, Qty, ImagePath) VALUES (?, ?, ?, ?, ?)");
                if (!$stmt) {
                    throw new Exception('Prepare failed for menuitems insert: ' . $conn->error);
                }
                $stmt->bind_param("ssdss", $menuName, $category, $price, $qty, $imagePath);
                if (!$stmt->execute()) {
                    throw new Exception('Execute failed for menuitems insert: ' . $stmt->error);
                }
                $menuItemId = $conn->insert_id;
                if (!$menuItemId) {
                    throw new Exception('Failed to insert menu item: no ID returned');
                }
                error_log("Inserted menu item with ID: $menuItemId");
                $stmt->close();

                if ($recipeName && $recipeDetails) {
                    $stmt = $conn->prepare("INSERT INTO recipes (MenuItemID, RecipeName, RecipeDetails) VALUES (?, ?, ?)");
                    if (!$stmt) {
                        throw new Exception('Prepare failed for recipes insert: ' . $conn->error);
                    }
                    $stmt->bind_param("iss", $menuItemId, $recipeName, $recipeDetails);
                    if (!$stmt->execute()) {
                        throw new Exception('Execute failed for recipes insert: ' . $stmt->error);
                    }
                    $recipeId = $conn->insert_id;
                    error_log("Inserted recipe with ID: $recipeId");
                    $stmt->close();

                    for ($i = 0; $i < count($ingredientNames); $i++) {
                        if (!empty($ingredientNames[$i]) && is_numeric($ingredientQuantities[$i]) && $ingredientQuantities[$i] >= 0) {
                            $stmt = $conn->prepare("INSERT INTO ingredients (RecipeID, IngredientName, Quantity) VALUES (?, ?, ?)");
                            if (!$stmt) {
                                throw new Exception('Prepare failed for ingredients insert: ' . $conn->error);
                            }
                            $stmt->bind_param("isd", $recipeId, $ingredientNames[$i], $ingredientQuantities[$i]);
                            if (!$stmt->execute()) {
                                throw new Exception('Execute failed for ingredients insert: ' . $stmt->error);
                            }
                            error_log("Inserted ingredient: {$ingredientNames[$i]} for RecipeID: $recipeId");
                            $stmt->close();
                        }
                    }
                }
            }

            // Fetch the complete item
            $stmt = $conn->prepare("SELECT MenuItemID, ItemName, Category, Price, Qty, ImagePath FROM menuitems WHERE MenuItemID = ?");
            if (!$stmt) {
                throw new Exception('Prepare failed for menuitems fetch: ' . $conn->error);
            }
            $stmt->bind_param("i", $menuItemId);
            if (!$stmt->execute()) {
                throw new Exception('Execute failed for menuitems fetch: ' . $stmt->error);
            }
            $result = $stmt->get_result();
            $item = $result->fetch_assoc();
            $stmt->close();

            if (!$item) {
                throw new Exception("Failed to fetch item after operation for MenuItemID: $menuItemId");
            }

            $stmt = $conn->prepare("SELECT RecipeID, RecipeName, RecipeDetails FROM recipes WHERE MenuItemID = ?");
            if (!$stmt) {
                throw new Exception('Prepare failed for recipes fetch: ' . $conn->error);
            }
            $stmt->bind_param("i", $menuItemId);
            if (!$stmt->execute()) {
                throw new Exception('Execute failed for recipes fetch: ' . $stmt->error);
            }
            $result = $stmt->get_result();
            $item['recipe'] = $result->fetch_assoc() ?: null;
            $stmt->close();

            $recipeId = $item['recipe']['RecipeID'] ?? 0;
            $stmt = $conn->prepare("SELECT IngredientID, IngredientName, Quantity FROM ingredients WHERE RecipeID = ?");
            if (!$stmt) {
                throw new Exception('Prepare failed for ingredients fetch: ' . $conn->error);
            }
            $stmt->bind_param("i", $recipeId);
            if (!$stmt->execute()) {
                throw new Exception('Execute failed for ingredients fetch: ' . $stmt->error);
            }
            $result = $stmt->get_result();
            $item['ingredients'] = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            if (!isset($item['MenuItemID']) || $item['MenuItemID'] <= 0) {
                throw new Exception("Invalid MenuItemID fetched: " . ($item['MenuItemID'] ?? 'null'));
            }

            $conn->commit();
            $response['success'] = true;
            $response['item'] = $item;
            error_log("Successfully saved item for MenuItemID: $menuItemId");
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Transaction failed for $action, MenuItemID: " . ($menuItemId ?? 'null') . ": " . $e->getMessage());
            throw $e;
        }
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    error_log("Error in menuitem_handler for action '$action': " . $e->getMessage());
}

echo json_encode($response);
$conn->close();
$conn_order->close();
?>