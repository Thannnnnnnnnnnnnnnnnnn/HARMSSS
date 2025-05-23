function openModal() {
    console.log('openModal called');
    const form = document.getElementById('menuItemForm');
    form.reset();
    form.dataset.operation = 'add';
    document.getElementById('modalTitle').textContent = 'Add Menu Item';
    document.getElementById('menuItemIdDisplay').classList.add('hidden');
    document.getElementById('menuItemId').value = '';
    document.getElementById('existingImagePath').value = '';
    document.getElementById('recipeName').value = '';
    document.getElementById('recipeDetails').value = '';
    document.getElementById('ingredientList').innerHTML = '';
    addIngredientField();
    document.getElementById('menuItemModal').classList.remove('hidden');
}

function addIngredientField(ingredient = { IngredientName: '', Quantity: '' }) {
    console.log('addIngredientField called', ingredient);
    const ingredientList = document.getElementById('ingredientList');
    const div = document.createElement('div');
    div.className = 'ingredient-row flex space-x-2 items-center';
    div.innerHTML = `
        <input type="text" name="IngredientName[]" class="flex-1 p-2 border border-champagnelbeige rounded-btn focus:outline-none focus:ring-2 focus:ring-blue-300 transition duration-200" placeholder="Ingredient Name" value="${ingredient.IngredientName || ''}">
        <input type="number" step="0.01" name="IngredientQuantity[]" class="w-24 p-2 border border-champagnelbeige rounded-btn focus:outline-none focus:ring-2 focus:ring-blue-300 transition duration-200" placeholder="Quantity" value="${ingredient.Quantity || ''}">
        <button type="button" onclick="this.parentElement.remove()" class="bg-red-500 text-white px-2 py-1 rounded-btn hover:bg-red-600 transition duration-200">
            <i class="fas fa-trash"></i>
        </button>
    `;
    ingredientList.appendChild(div);
}

function viewItemModal(menuItemId) {
    console.log('viewItemModal called with menuItemId:', menuItemId, 'type:', typeof menuItemId);
    if (!menuItemId || isNaN(menuItemId) || parseInt(menuItemId) <= 0) {
        console.error('Invalid MenuItemID:', menuItemId);
        Swal.fire({
            title: 'Error!',
            text: 'Invalid Menu Item ID. Please try again.',
            icon: 'error'
        });
        return;
    }

    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main');
    if (sidebar) sidebar.classList.add('hidden');
    if (mainContent) mainContent.classList.add('md:ml-0');

    const formData = new FormData();
    formData.append('action', 'view');
    formData.append('MenuItemID', menuItemId);

    fetch('menuitem_handler.php', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.item) {
                const item = data.item;
                console.log('Received item data:', item);
                const ingredientsList = item.ingredients && item.ingredients.length > 0 
                    ? item.ingredients.map(ing => `<li>${ing.IngredientName}: ${ing.Quantity}</li>`).join('')
                    : '<li>No ingredients available</li>';
                const viewContent = `
                    <div class="mb-4">
                        <strong class="text-deepbrown">Menu Item ID:</strong> ${item.MenuItemID}
                    </div>
                    <div class="mb-4">
                        <strong class="text-deepbrown">Item Name:</strong> ${item.ItemName}
                    </div>
                    <div class="mb-4">
                        <strong class="text-deepbrown">Category:</strong> ${item.Category}
                    </div>
                    <div class="mb-4">
                        <strong class="text-deepbrown">Price:</strong> ₱${Number(item.Price).toFixed(2)}
                    </div>
                    <div class="mb-4">
                        <strong class="text-deepbrown">Quantity:</strong> ${item.Qty}
                    </div>
                    <div class="mb-4">
                        <strong class="text-deepbrown">Recipe Name:</strong> ${item.recipe?.RecipeName || 'N/A'}
                    </div>
                    <div class="mb-4">
                        <strong class="text-deepbrown">Recipe Details:</strong> ${item.recipe?.RecipeDetails || 'N/A'}
                    </div>
                    <div class="mb-4">
                        <strong class="text-deepbrown">Ingredients:</strong>
                        <ul class="list-disc pl-5">${ingredientsList}</ul>
                    </div>
                    <div class="mb-4">
                        <strong class="text-deepbrown">Image:</strong> 
                        <img src="${item.ImagePath || '/images/placeholder.jpg'}" alt="Image" class="w-32 h-32 object-cover rounded mx-auto">
                    </div>
                `;
                document.getElementById('viewItemContent').innerHTML = viewContent;
                document.getElementById('viewItemModal').classList.remove('hidden');
            } else {
                throw new Error(data.error || 'Failed to load menu item');
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            Swal.fire({
                title: 'Error!',
                text: `Failed to load menu item: ${error.message}`,
                icon: 'error'
            });
        });
}

function closeViewModal() {
    console.log('closeViewModal called');
    document.getElementById('viewItemModal').classList.add('hidden');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main');
    if (sidebar) sidebar.classList.remove('hidden');
    if (mainContent) mainContent.classList.remove('md:ml-0');
}

function editItem(item) {
    console.log('editItem called', item);
    const form = document.getElementById('menuItemForm');
    form.dataset.operation = 'edit';
    document.getElementById('modalTitle').textContent = 'Edit Menu Item';
    document.getElementById('menuItemIdDisplay').classList.remove('hidden');
    document.getElementById('menuItemId').value = item.MenuItemID || '';
    document.getElementById('menuItemIdReadonly').value = item.MenuItemID || '';
    document.getElementById('itemName').value = item.ItemName || '';
    document.getElementById('category').value = item.Category || 'Food';
    document.getElementById('price').value = item.Price || '';
    document.getElementById('qty').value = item.Qty || '';
    document.getElementById('existingImagePath').value = item.ImagePath || '';
    document.getElementById('recipeName').value = item.recipe?.RecipeName || '';
    document.getElementById('recipeDetails').value = item.recipe?.RecipeDetails || '';
    document.getElementById('ingredientList').innerHTML = '';
    if (item.ingredients && item.ingredients.length > 0) {
        item.ingredients.forEach(ingredient => addIngredientField(ingredient));
    } else {
        addIngredientField();
    }
    document.getElementById('menuItemModal').classList.remove('hidden');
}

function handleFormSubmit(event) {
    console.log('handleFormSubmit called');
    event.preventDefault();
    const form = document.getElementById('menuItemForm');
    const isEdit = form.dataset.operation === 'edit';
    const menuItemId = document.getElementById('menuItemId').value.trim();
    const menuName = document.getElementById('itemName').value.trim();
    const category = document.getElementById('category').value;
    const price = parseFloat(document.getElementById('price').value);
    const qty = parseInt(document.getElementById('qty').value);
    const recipeName = document.getElementById('recipeName').value.trim();
    const recipeDetails = document.getElementById('recipeDetails').value.trim();

    // Client-side validation
    if (!menuName) {
        Swal.fire({ title: 'Error!', text: 'Menu Name is required.', icon: 'error' });
        return;
    }
    if (!['Food', 'Beverage'].includes(category)) {
        Swal.fire({ title: 'Error!', text: 'Invalid Category.', icon: 'error' });
        return;
    }
    if (isNaN(price) || price <= 0) {
        Swal.fire({ title: 'Error!', text: 'Price must be a positive number.', icon: 'error' });
        return;
    }
    if (isNaN(qty) || qty < 0) {
        Swal.fire({ title: 'Error!', text: 'Quantity must be non-negative.', icon: 'error' });
        return;
    }
    if (isEdit && (!menuItemId || parseInt(menuItemId) <= 0)) {
        Swal.fire({ title: 'Error!', text: 'Valid Menu Item ID is required for editing.', icon: 'error' });
        return;
    }
    if ((recipeName && !recipeDetails) || (!recipeName && recipeDetails)) {
        Swal.fire({ title: 'Error!', text: 'Both Recipe Name and Details are required if one is provided.', icon: 'error' });
        return;
    }

    const formData = new FormData(form);
    const action = isEdit ? 'edit' : 'add';
    formData.set('action', action); // Ensure action is set
    if (!isEdit) {
        console.log('Removing MenuItemID for add operation');
        formData.delete('MenuItemID');
    }

    console.log('Form Data - action:', action);
    console.log('Form Data - isEdit:', isEdit);
    console.log('Form Data - menuItemId:', menuItemId);
    for (let [key, value] of formData.entries()) {
        console.log(`Form Data - ${key}: ${value}`);
    }

    fetch('menuitem_handler.php', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            console.log('Fetch response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Server response:', JSON.stringify(data, null, 2));
            if (data.success && data.item) {
                const item = data.item;
                console.log('Received item from server:', item);
                if (!item.MenuItemID || item.MenuItemID <= 0) {
                    console.error('Invalid MenuItemID in server response:', item.MenuItemID);
                    throw new Error('Server returned invalid Menu Item ID');
                }
                let row = isEdit ? document.getElementById(`menuItem-${item.MenuItemID}`) : null;
                if (row) {
                    // Update existing row
                    row.innerHTML = `
                        <td class="p-4">${item.MenuItemID}</td>
                        <td class="p-4">
                            <img src="${item.ImagePath || '/images/placeholder.jpg'}" alt="Image" class="w-16 h-16 object-cover rounded">
                        </td>
                        <td class="p-4">${item.ItemName}</td>
                        <td class="p-4">${item.Category || 'N/A'}</td>
                        <td class="p-4">₱${Number(item.Price).toFixed(2)}</td>
                        <td class="p-4">${item.Qty}</td>
                        <td class="p-4 flex space-x-2">
                            <button data-id="${item.MenuItemID}" data-item='${JSON.stringify(item)}' class="view-btn bg-yellow-500 text-white px-4 py-2 rounded-btn hover:bg-yellow-600 transition duration-200 flex items-center">
                                <i class="fas fa-eye mr-1"></i>View
                            </button>
                            <button data-item='${JSON.stringify(item)}' class="edit-btn bg-blue-500 text-white px-4 py-2 rounded-btn hover:bg-blue-600 transition duration-200 flex items-center">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                            <button data-id="${item.MenuItemID}" data-name="${item.ItemName || 'Unknown Item'}" class="delete-btn bg-red-500 text-white px-4 py-2 rounded-btn hover:bg-red-600 transition duration-200 flex items-center">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                        </td>
                    `;
                } else {
                    // Add new row
                    const tableBody = document.querySelector('#menuTable tbody');
                    row = document.createElement('tr');
                    row.id = `menuItem-${item.MenuItemID}`;
                    row.className = 'border-b border-champagnelbeige hover:bg-softpillow transition duration-150';
                    row.innerHTML = `
                        <td class="p-4">${item.MenuItemID}</td>
                        <td class="p-4">
                            <img src="${item.ImagePath || '/images/placeholder.jpg'}" alt="Image" class="w-16 h-16 object-cover rounded">
                        </td>
                        <td class="p-4">${item.ItemName}</td>
                        <td class="p-4">${item.Category || 'N/A'}</td>
                        <td class="p-4">₱${Number(item.Price).toFixed(2)}</td>
                        <td class="p-4">${item.Qty}</td>
                        <td class="p-4 flex space-x-2">
                            <button data-id="${item.MenuItemID}" data-item='${JSON.stringify(item)}' class="view-btn bg-yellow-500 text-white px-4 py-2 rounded-btn hover:bg-yellow-600 transition duration-200 flex items-center">
                                <i class="fas fa-eye mr-1"></i>View
                            </button>
                            <button data-item='${JSON.stringify(item)}' class="edit-btn bg-blue-500 text-white px-4 py-2 rounded-btn hover:bg-blue-600 transition duration-200 flex items-center">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                            <button data-id="${item.MenuItemID}" data-name="${item.ItemName || 'Unknown Item'}" class="delete-btn bg-red-500 text-white px-4 py-2 rounded-btn hover:bg-red-600 transition duration-200 flex items-center">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                }
                if (row) {
                    const viewBtn = row.querySelector('.view-btn');
                    const editBtn = row.querySelector('.edit-btn');
                    const deleteBtn = row.querySelector('.delete-btn');
                    if (viewBtn) {
                        viewBtn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            e.preventDefault();
                            const id = viewBtn.getAttribute('data-id');
                            console.log('Dynamic view button clicked, data-id:', id);
                            viewItemModal(id);
                        });
                    }
                    if (editBtn) {
                        editBtn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            e.preventDefault();
                            const item = JSON.parse(editBtn.getAttribute('data-item'));
                            editItem(item);
                        });
                    }
                    if (deleteBtn) {
                        deleteBtn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            e.preventDefault();
                            const id = deleteBtn.getAttribute('data-id');
                            const name = deleteBtn.getAttribute('data-name');
                            deleteItem(id, name);
                        });
                    }
                }
                closeModal();
                Swal.fire({
                    title: 'Success!',
                    text: `Menu Item has been ${isEdit ? 'updated' : 'added'} successfully.`,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                console.error('Server response error:', data.error || 'No item data returned');
                throw new Error(data.error || 'Unknown error occurred');
            }
        })
        .catch(error => {
            console.error('Form submission error:', error);
            Swal.fire({
                title: 'Error!',
                text: `Failed to save the item: ${error.message}`,
                icon: 'error'
            });
        });
}

function deleteItem(menuItemId, itemName) {
    console.log('deleteItem called', { menuItemId, itemName });
    if (!menuItemId || isNaN(menuItemId) || parseInt(menuItemId) <= 0) {
        console.error('Invalid MenuItemID for deletion:', menuItemId);
        Swal.fire({
            title: 'Error!',
            text: 'Invalid Menu Item ID. Cannot delete.',
            icon: 'error'
        });
        return;
    }
    const safeItemName = itemName || 'Unknown Item';
    Swal.fire({
        title: `Delete ${safeItemName}?`,
        text: "This action cannot be undone and will also remove related order items, recipes, and food costing data!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('MenuItemID', menuItemId);

            console.log('Sending delete request for MenuItemID:', menuItemId);
            for (let [key, value] of formData.entries()) {
                console.log(`Delete FormData - ${key}: ${value}`);
            }

            fetch('menuitem_handler.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    console.log('Delete fetch response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`Network response was not ok: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Delete response:', data);
                    if (data.success) {
                        const row = document.getElementById(`menuItem-${menuItemId}`);
                        if (row) {
                            row.remove();
                            console.log(`Removed row for MenuItemID: ${menuItemId}`);
                        }
                        Swal.fire({
                            title: 'Deleted!',
                            text: `${safeItemName} has been deleted.`,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        throw new Error(data.error || 'Unknown error occurred during deletion');
                    }
                })
                .catch(error => {
                    console.error('Delete error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: `Failed to delete ${safeItemName}: ${error.message}. Check the server logs for details or contact support.`,
                        icon: 'error'
                    });
                });
        }
    });
}

function closeModal() {
    console.log('closeModal called');
    document.getElementById('menuItemModal').classList.add('hidden');
}

function closeSuccessPopup() {
    console.log('closeSuccessPopup called');
    document.getElementById('successMessage').classList.add('hidden');
}

function filterMenu() {
    console.log('filterMenu called');
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#menuTable tbody tr');
    rows.forEach(row => {
        const name = row.querySelector('td:nth-child(3)').innerText.toLowerCase();
        const category = row.querySelector('td:nth-child(4)').innerText.toLowerCase();
        const show = name.includes(searchInput) || category.includes(searchInput);
        row.style.display = show ? '' : 'none';
    });
}

function scanViewButtons() {
    console.log('Scanning view buttons for invalid data-id values...');
    const viewButtons = document.querySelectorAll('.view-btn');
    viewButtons.forEach((button, index) => {
        const id = button.getAttribute('data-id');
        const row = button.closest('tr');
        const rowId = row ? row.id : 'unknown';
        if (!id || isNaN(id) || parseInt(id) <= 0) {
            console.warn(`Invalid data-id on view button #${index + 1}:`, id, 'in row:', rowId);
            button.disabled = true;
            button.classList.add('opacity-50', 'cursor-not-allowed');
            button.title = 'Invalid Menu Item ID';
        } else {
            console.log(`Valid data-id on view button #${index + 1}:`, id, 'in row:', rowId);
        }
    });
    console.log(`Total view buttons scanned: ${viewButtons.length}`);
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOMContentLoaded event fired');
    scanViewButtons();
    const viewButtons = document.querySelectorAll('.view-btn');
    viewButtons.forEach((button, index) => {
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            e.preventDefault();
            const id = button.getAttribute('data-id');
            const row = button.closest('tr');
            const rowId = row ? row.id : 'unknown';
            console.log(`View button #${index + 1} clicked, data-id:`, id, 'type:', typeof id, 'row:', rowId);
            if (!id || isNaN(id) || parseInt(id) <= 0) {
                console.error('Invalid data-id on view button:', id, 'in row:', rowId);
                Swal.fire({
                    title: 'Error!',
                    text: 'Invalid Menu Item ID. Please try again.',
                    icon: 'error'
                });
                return;
            }
            viewItemModal(id);
        });
    });

    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            e.preventDefault();
            const item = JSON.parse(button.getAttribute('data-item'));
            editItem(item);
        });
    });

    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            e.preventDefault();
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            deleteItem(id, name);
        });
    });

    const toggleButton = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main');
    if (toggleButton && sidebar && mainContent) {
        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            mainContent.classList.toggle('md:ml-80');
            mainContent.classList.toggle('md:ml-0');
        });
    }
});