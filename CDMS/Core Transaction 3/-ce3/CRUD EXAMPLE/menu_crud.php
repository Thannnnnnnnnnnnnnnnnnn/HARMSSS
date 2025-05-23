<?php
require 'partials/head.php';
$conn = require 'config.php';
require 'Database.php';
function dd($value)
{
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
    die();
}

$db = new Database($conn['database']);
$kitchen_bar_database = new Database($conn['kitchen_bar_database']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // dd($_POST);
    if ($_POST['add'] ?? '' === true) {
        // dd($_POST);
        $kitchen_bar_database->query("INSERT INTO menuitems( ItemName, Category, Price)
    VALUES(:ItemName, :Category, :Price)", [
            ':ItemName' => $_POST['ItemName'],
            ':Category' => $_POST['Category'],
            ':Price' => $_POST['Price']
        ]);
        $MenuItemID = $kitchen_bar_database->pdo->lastInsertId();
        // dd($MenuItemID);
        $kitchen_bar_database->query("INSERT INTO recipes( MenuItemID, ItemName, Recipe)
    VALUES(:MenuItemID, :ItemName, :Recipe)", [
            ':MenuItemID' => $MenuItemID,
            ':ItemName' => $_POST['ItemName'],
            ':Recipe' => $_POST['Recipe']
        ]);
        $RecipeID = $kitchen_bar_database->pdo->lastInsertId();
        // dd($RecipeID);
        $kitchen_bar_database->query("INSERT INTO ingredients( RecipeID, Ingredients, Quantity)
    VALUES(:RecipeID, :Ingredients, :Quantity)", [
            ':RecipeID' => $RecipeID,
            ':Ingredients' => $_POST['ingredients'],
            ':Quantity' => $_POST['quantity']
        ]);
        $IngredientID = $kitchen_bar_database->pdo->lastInsertId();
        // dd($MenuItemID);
        $kitchen_bar_database->query("INSERT INTO foodcosting( IngredientID, Cost)
    VALUES(:IngredientID, :Cost)", [
            ':IngredientID' => $IngredientID,
            ':Cost' => $_POST['Cost']
        ]);
        header('location: menuitems.php');
        exit();
    }
    if ($_POST['edit'] ?? '' === true) {
        // Update menuitems
        $kitchen_bar_database->query('UPDATE menuitems 
        SET ItemName = :ItemName, Category = :Category, Price = :Price
        WHERE MenuItemID = :MenuItemID', [
            ':ItemName' => $_POST['item_name'],
            ':Category' => $_POST['Category'],
            ':Price' => $_POST['price'],
            ':MenuItemID' => $_POST['menu_id']
        ]);

        // Update recipes
        $kitchen_bar_database->query('UPDATE recipes 
        SET Recipe = :Recipe
        WHERE MenuItemID = :MenuItemID', [
            ':Recipe' => $_POST['recipe'],
            ':MenuItemID' => $_POST['menu_id']
        ]);

        // Update ingredients
        $recipeResult = $kitchen_bar_database->query('SELECT RecipeID FROM recipes WHERE MenuItemID = :MenuItemID', [
            ':MenuItemID' => $_POST['menu_id']
        ])->fetch();

        if ($recipeResult) {
            $RecipeID = $recipeResult['RecipeID'];
            $kitchen_bar_database->query('UPDATE ingredients 
            SET Ingredients = :Ingredients, Quantity = :Quantity
            WHERE RecipeID = :RecipeID', [
                ':Ingredients' => $_POST['ingredients'],
                ':Quantity' => $_POST['quantity'],
                ':RecipeID' => $RecipeID
            ]);

            // Update foodcosting
            $ingredientResult = $kitchen_bar_database->query('SELECT IngredientID FROM ingredients WHERE RecipeID = :RecipeID', [
                ':RecipeID' => $RecipeID
            ])->fetch();

            if ($ingredientResult) {
                $IngredientID = $ingredientResult['IngredientID'];
                $kitchen_bar_database->query('UPDATE foodcosting 
                SET Cost = :Cost
                WHERE IngredientID = :IngredientID', [
                    ':Cost' => $_POST['foodcost'],
                    ':IngredientID' => $IngredientID
                ]);
            }
        }

        header('location: menuitems.php');
        exit();
    }
    if ($_POST['delete'] ?? '' === true) {
        // dd($_POST);
        $kitchen_bar_database->query('DELETE FROM foodcosting 
        WHERE CostingID = :CostingID', [
            ':CostingID' => $_POST['CostingID']
        ]);
        $kitchen_bar_database->query('DELETE FROM ingredients 
        WHERE IngredientID = :IngredientID', [
            ':IngredientID' => $_POST['IngredientID']
        ]);
        $kitchen_bar_database->query('DELETE FROM recipes 
        WHERE RecipeID = :RecipeID', [
            ':RecipeID' => $_POST['RecipeID']
        ]);
        $kitchen_bar_database->query('DELETE FROM menuitems 
        WHERE MenuItemID = :MenuItemID', [
            ':MenuItemID' => $_POST['MenuItemID']
        ]);
        $kitchen_bar_database->query('DELETE FROM foodcosting 
        WHERE CostingID = :CostingID', [
            ':CostingID' => $_POST['CostingID']
        ]);
        header('location: menuitems.php');
        exit();
        // dd($_POST);
    }
}

//--------------------------------------------------------------------------------------//--------------------------------------------------------------------------------------

$reserve = new Database($conn['database']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // dd($_POST);
    if ($_POST['add'] ?? '' === true) {
        // dd($_POST);
        $reserve->query("INSERT INTO reservationstatus( StatusID, ReservationID, `Status`)
    VALUES(:StatusID, :ReservationID, :`Status`)", [
            ':StatusID' => $_POST['status_id'],
            ':ReservationID' => $_POST['reservation_id'],
            ':Status' => $_POST['status']
        ]);
        $StatusID = $reserve->pdo->lastInsertId();
        // dd($MenuItemID);
        $reserve->query("INSERT INTO reservationstatus( StatusID, ReservationID, `Status`)
    VALUES(:StatusID, :ReservationID, :`Status`)", [
            ':StatusID' => $StatusID,
            ':ReservationID' => $_POST['ReservationID'],
            ':Status' => $_POST['Status']
        ]);
        $ReservationID = $kitchen_bar_database->pdo->lastInsertId();
        // dd($RecipeID);
        $reserve->query("INSERT INTO reservationstatus( StatusID, ReservationID, `Status`)
    VALUES(:StatusID, :ReservationID, :`Status`)", [
            ':StatusID' => $_POST['StatusID'],
            ':ReservationID' => $ReservationID,
            ':Status' => $_POST['Status']
        ]);
        $Status = $kitchen_bar_database->pdo->lastInsertId();
        // dd($MenuItemID);
        $reserve->query("INSERT INTO reservationstatus( StatusID, ReservationID, `Status`)
    VALUES(:StatusID, :ReservationID, :`Status`)", [
            ':StatusID' => $_POST['StatusID'],
            ':ReservationID' => $_POST['ReservationID'],
            ':Status' => $Status,
        ]);
        header('location: rs.php');
        exit();
    }
    if ($_POST['edit'] ?? '' === true) {
        // Update menuitems
        $kitchen_bar_database->query('UPDATE menuitems 
        SET ItemName = :ItemName, Category = :Category, Price = :Price
        WHERE MenuItemID = :MenuItemID', [
            ':ItemName' => $_POST['item_name'],
            ':Category' => $_POST['Category'],
            ':Price' => $_POST['price'],
            ':MenuItemID' => $_POST['menu_id']
        ]);

        // Update recipes
        $kitchen_bar_database->query('UPDATE recipes 
        SET Recipe = :Recipe
        WHERE MenuItemID = :MenuItemID', [
            ':Recipe' => $_POST['recipe'],
            ':MenuItemID' => $_POST['menu_id']
        ]);

        // Update ingredients
        $recipeResult = $kitchen_bar_database->query('SELECT RecipeID FROM recipes WHERE MenuItemID = :MenuItemID', [
            ':MenuItemID' => $_POST['menu_id']
        ])->fetch();

        if ($recipeResult) {
            $RecipeID = $recipeResult['RecipeID'];
            $kitchen_bar_database->query('UPDATE ingredients 
            SET Ingredients = :Ingredients, Quantity = :Quantity
            WHERE RecipeID = :RecipeID', [
                ':Ingredients' => $_POST['ingredients'],
                ':Quantity' => $_POST['quantity'],
                ':RecipeID' => $RecipeID
            ]);

            // Update foodcosting
            $ingredientResult = $kitchen_bar_database->query('SELECT IngredientID FROM ingredients WHERE RecipeID = :RecipeID', [
                ':RecipeID' => $RecipeID
            ])->fetch();

            if ($ingredientResult) {
                $IngredientID = $ingredientResult['IngredientID'];
                $kitchen_bar_database->query('UPDATE foodcosting 
                SET Cost = :Cost
                WHERE IngredientID = :IngredientID', [
                    ':Cost' => $_POST['foodcost'],
                    ':IngredientID' => $IngredientID
                ]);
            }
        }

        header('location: menuitems.php');
        exit();
    }
    if ($_POST['delete'] ?? '' === true) {
        // dd($_POST);
        $kitchen_bar_database->query('DELETE FROM foodcosting 
        WHERE CostingID = :CostingID', [
            ':CostingID' => $_POST['CostingID']
        ]);
        $kitchen_bar_database->query('DELETE FROM ingredients 
        WHERE IngredientID = :IngredientID', [
            ':IngredientID' => $_POST['IngredientID']
        ]);
        $kitchen_bar_database->query('DELETE FROM recipes 
        WHERE RecipeID = :RecipeID', [
            ':RecipeID' => $_POST['RecipeID']
        ]);
        $kitchen_bar_database->query('DELETE FROM menuitems 
        WHERE MenuItemID = :MenuItemID', [
            ':MenuItemID' => $_POST['MenuItemID']
        ]);
        $kitchen_bar_database->query('DELETE FROM foodcosting 
        WHERE CostingID = :CostingID', [
            ':CostingID' => $_POST['CostingID']
        ]);
        header('location: menuitems.php');
        exit();
        // dd($_POST);
    }
}
