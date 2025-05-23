<?php
require '../partials/admin/head.php';
$conn = require '../config.php';
require '../Database.php';
$db = new Database($conn['database']);

// Join menuitems with recipes table
function dd($value)
{
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
    die();
}
// $results = $db->query("SELECT m.*, r.RecipeID, r.Recipe, i.ingredients, i.quantity, i.IngredientID, f.cost
//                       FROM menuitems m
//                       INNER JOIN recipes r ON m.MenuItemID = r.MenuItemID
//                       INNER JOIN ingredients i ON r.RecipeID=i.RecipeID
//                       INNER JOIN foodcosting f ON f.MenuItemID=m.MenuItemID")->fetchAll();
// dd($results);


$results = $db->query("SELECT i.*, r.*,m.*,f.*
FROM ingredients i INNER JOIN recipes r ON i.RecipeID=r.RecipeID
INNER JOIN menuitems m ON r.MenuItemID=m.MenuItemID
INNER JOIN foodcosting f ON f.IngredientID=i.IngredientID")->fetchAll();
// dd($results);
?>

<div class="flex min-h-screen w-full">
    <?php
    require 'partials/sidebar.php';
    require 'partials/navbar.php';
    ?>
    <main class="flex-1 p-5">
        <!-- Add Item Button -->
        <button class="bg-green-600 text-white px-4 py-2 rounded mb-4" onclick="openModal('addModal')">Add Item</button>

        <!-- Items Table -->
        <table class="w-full text-justify bg-white text-[#4E3B2A] border">
            <thead class="border">
                <tr>
                    <th>MenuItemID</th>
                    <th>ItemName</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $result): ?>
                    <tr class="border">
                        <td><?= $result['MenuItemID'] ?></td>
                        <td><?= $result['ItemName'] ?></td>
                        <td><?= $result['Category'] ?></td>
                        <td><?= $result['Price'] ?></td>
                        <td class="space-x-2 flex   ">
                            <button class="bg-blue-500 text-white px-2 py-1 rounded" onclick='openViewModal(<?= json_encode($result) ?>)'>View</button>
                            <button class="bg-yellow-500 text-white px-2 py-1 rounded" onclick='openEditModal(<?= json_encode($result) ?>)'>Edit</button>
                            <form action="menu_crud.php" method="POST" class="inline">
                                <input type="hidden" name="delete" value="true">
                                <input type="hidden" name="MenuItemID" value="<?= $result['MenuItemID'] ?>">
                                <input type="hidden" name="RecipeID" value="<?= $result['RecipeID'] ?>">
                                <input type="hidden" name="IngredientID" value="<?= $result['IngredientID'] ?>">
                                <input type="hidden" name="CostingID" value="<?= $result['CostingID'] ?>">
                                <button type="submit" class="bg-red-600 text-white px-2 py-1 rounded" onclick="return confirm('Delete this item?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>

        <!-- View Modal -->
        <div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded w-96">
                <h2 class="text-xl mb-4 font-bold">View Item</h2>
                <p><strong>Item Name:</strong> <span id="viewItemName"></span></p>
                <p><strong>Category:</strong> <span id="viewCategory"></span></p>
                <p><strong>Price:</strong> <span id="viewPrice"></span></p>
                <p><strong>Recipe:</strong> <span id="viewRecipe"></span></p>
                <p><strong>Ingredients:</strong> <span id="viewIngredients"></span></p>
                <p><strong>FoodCost:</strong> <span id="viewCost"></span></p>
                <p><strong>Quantity:</strong> <span id="viewQuantity"></span></p>
                <button onclick="closeModal('viewModal')" class="mt-4 bg-gray-600 text-white px-4 py-2 rounded">Close</button>
            </div>
        </div>

        <!-- Edit Modal -->
        <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded w-96">
                <h2 class="text-xl mb-4 font-bold">Edit Item</h2>
                <form action="menu_crud.php" method="POST">
                    <input type="hidden" name="edit" value="true">
                    <input type="hidden" name="menu_id" id="editMenuID">
                    <label class="block mb-2">Item Name</label>
                    <input type="text" name="item_name" id="editItemName" class="w-full border p-2 mb-2">
                    <label class="block mb-2">Category</label>
                    <select name="Category" id="editCategory" class="w-full p-3 mb-3">
                        <option value="main-course">Main Course</option>
                        <option value="appetizers">Appetizers</option>
                        <option value="drinks">Drinks</option>
                        <option value="desserts">Desserts</option>
                    </select>
                    <label class="block mb-2">Price</label>
                    <input type="number" step="0.01" name="price" id="editPrice" class="w-full border p-2 mb-2">
                    <label class="block mb-2">Recipe</label>
                    <input type="text" name="recipe" id="editRecipe" class="w-full border p-2 mb-2">
                    <label class="block mb-2">Ingredients</label>
                    <input name="ingredients" id="editIngredients" class="w-full border p-2 mb-4"></input>
                    <label class="block mb-2">FoodCost</label>
                    <input name="foodcost" id="editFoodCost" class="w-full border p-2 mb-4"></input>
                    <label class="block mb-2">Quantity</label>
                    <input name="quantity" id="editQuantity" class="w-full border p-2 mb-4"></input>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeModal('editModal')" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                        <button type="submit" name="update" class="bg-yellow-500 text-white px-4 py-2 rounded">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Modal -->
        <div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded w-96">
                <h2 class="text-xl mb-4 font-bold">Add Item</h2>
                <form action="menu_crud.php" method="POST">
                    <input type="text" name="add" value="true">

                    <label class="block mb-2">Item Name</label>
                    <input type="text" name="ItemName" class="w-full border p-2 mb-2">

                    <label class="block mb-2">Category</label>
                    <select name="Category" id="" class="w-full p-3 mb-3">
                        <option value="main-course">Main Course</option>
                        <option value="appetizers">Appetizers</option>
                        <option value="drinks">Drinks</option>
                        <option value="desserts">Desserts</option>
                    </select>

                    <label class="block mb-2">Price</label>
                    <input type="number" step="0.01" name="Price" class="w-full border p-2 mb-2">

                    <label class="block mb-2">Recipe</label>
                    <input type="text" name="Recipe" class="w-full border p-2 mb-2">

                    <label class="block mb-2">Ingredients</label>
                    <input type="text" name="ingredients" class="w-full border p-2 mb-2">

                    <label class="block mb-2">FoodCosting</label>
                    <input type="text" name="Cost" class="w-full border p-2 mb-2">

                    <label class="block mb-2">Quantity</label>
                    <input type="number" name="quantity" class="w-full border p-2 mb-2">

                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeModal('addModal')" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Add</button>
                    </div>
                </form>
            </div>
        </div>

        <?php
        if (count($results) < 1):
        ?>
            <div class="text-center text-red-500 mt-7 text-xl">NO MENU ITEM FOUND!</div>
        <?php
        endif
        ?>

    </main>
</div>

<!-- JS Modal Logic -->
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function openViewModal(data) {
        document.getElementById('viewItemName').textContent = data.ItemName;
        document.getElementById('viewCategory').textContent = data.Category;
        document.getElementById('viewPrice').textContent = data.Price;
        document.getElementById('viewRecipe').textContent = data.Recipe;
        document.getElementById('viewIngredients').textContent = data.Ingredients;
        document.getElementById('viewCost').textContent = data.Cost;
        document.getElementById('viewQuantity').textContent = data.Quantity;
        openModal('viewModal');
    }

    function openEditModal(data) {
        document.getElementById('editMenuID').value = data.MenuItemID;
        document.getElementById('editItemName').value = data.ItemName;
        document.getElementById('editCategory').value = data.Category;
        document.getElementById('editPrice').value = data.Price;
        document.getElementById('editRecipe').value = data.Recipe;
        document.getElementById('editIngredients').value = data.Ingredients;
        document.getElementById('editFoodCost').value = data.Cost;
        document.getElementById('editQuantity').value = data.Quantity;
        openModal('editModal');
    }
</script>

<?php
require 'partials/footer.php';
?>