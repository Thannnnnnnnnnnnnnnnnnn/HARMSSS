const cart = {}; // Initialize the cart object

// Function to add an item to the order
function addToOrder(item) {
  const itemId = item.ItemID || item.id || item.ItemName; // Use a unique ID or ItemName as fallback
  if (cart[itemId]) {
    cart[itemId].quantity += 1; // Increase quantity if item already in cart
  } else {
    // Add new item to cart with quantity of 1
    cart[itemId] = {
      ...item,
      quantity: 1
    };
  }

  // Show SweetAlert for successful item addition
  Swal.fire({
    title: 'Item Added!',
    text: `${item.ItemName} has been added to your order.`,
    icon: 'success',
    confirmButtonText: 'OK'
  });

  updateCartUI(); // Update the UI to reflect the new cart state
}

// Function to update the cart UI
function updateCartUI() {
  const cartItemsContainer = document.getElementById('cartItems');
  cartItemsContainer.innerHTML = ''; // Clear existing cart items
  let total = 0;

  const orderDate = new Date().toLocaleDateString(); // Get today's date for the order

  // Loop through the cart items and generate the corresponding table rows
  Object.keys(cart).forEach(key => {
    const item = cart[key];
    const price = parseFloat(item.Price);
    const subtotal = item.quantity * price;
    total += subtotal;

    // Create a new row for the cart table
    const row = document.createElement('tr');
    row.innerHTML = `
      <!-- MenuItemID Column -->
      <td class="menuItemID py-2 px-4 border-b text-center">${item.MenuItemID}</td>

      <!-- MenuName Column -->
      <td class="menuName py-2 px-4 border-b text-center">${item.ItemName}</td>

      <!-- Quantity Column -->
    <td class="qty py-2 px-4 border-b text-center">
    <div class="flex items-center justify-center gap-2">
    <!-- Hidden input field for storing the quantity value -->
    <input type="hidden" id="quantity-${key}" value="${item.quantity}" />
    <span id="quantity-span-${key}">${item.quantity}</span>  <!-- Visible quantity -->
</td>

      <!-- Price Column -->
      <td class="price py-2 px-4 border-b text-center">₱${price.toFixed(2)}</td>

      <!-- Subtotal Column -->
      <td class="subtotal py-2 px-4 border-b text-center">₱${subtotal.toFixed(2)}</td>

      <!-- Order Date Column -->
      <td class="orderDate py-2 px-4 border-b text-center">${orderDate}</td>

      <!-- Action Column -->
      <td class="py-2 px-4 border-b text-center">
        <button onclick="removeFromCart('${key}')" class="text-red-500 hover:underline text-sm">Remove</button>
      </td>
    `;

    // Append the new row to the cart items container
    cartItemsContainer.appendChild(row);
  });

  // Update the total amount in the UI
  document.getElementById('cartTotal').textContent = `₱${total.toFixed(2)}`;
}

// Function to change the quantity of an item in the cart
function changeQuantity(key, delta) {
  // Find the hidden input field and span element
  const quantityInput = document.getElementById(`quantity-${key}`);
  const quantitySpan = document.getElementById(`quantity-span-${key}`);
  
  // Get the current quantity
  let currentQuantity = parseInt(quantityInput.value) || 0;

  // Update the quantity by delta (either +1 or -1)
  currentQuantity += delta;
  
  // Ensure that the quantity is never less than 1
  if (currentQuantity < 1) currentQuantity = 1;
  
  // Update the hidden input and the span element
  quantityInput.value = currentQuantity;
  quantitySpan.textContent = currentQuantity;
}

// Function to remove an item from the cart
function removeFromCart(key) {
  if (cart[key]) {
    const itemName = cart[key].ItemName || "Item"; // Get item name for alert message

    // Delete the item from the cart
    delete cart[key];

    // Show SweetAlert for item removal
    Swal.fire({
      title: 'Item Removed!',
      text: `${itemName} has been removed from your cart.`,
      icon: 'success',
      confirmButtonText: 'OK'
    });

    updateCartUI(); // Refresh the cart UI after removing the item
  }
}

// Function to filter the menu items based on search input and category filter
function filterMenu() {
  const searchQuery = document.getElementById("searchInput").value.toLowerCase(); // Get the search query
  const selectedCategory = document.getElementById("categoryFilter").value.toLowerCase(); // Get the selected category

  // Loop through each menu item and check if it matches search and category filters
  document.querySelectorAll(".menu-item").forEach(item => {
    const itemName = item.dataset.name.toLowerCase(); // Get the name from the data attribute
    const itemCategory = item.dataset.category.toLowerCase(); // Get the category from the data attribute

    // Check if item matches search and category criteria
    const matchesSearch = itemName.includes(searchQuery);
    const matchesCategory = selectedCategory === "" || itemCategory === selectedCategory;

    // Display the item if it matches both filters, otherwise hide it
    item.style.display = (matchesSearch && matchesCategory) ? "flex" : "none";
  });
}

// Function to open the cart modal
function openCartModal() {
    const name = document.getElementById('customerName')?.value.trim();
    const orderType = document.getElementById('orderType')?.value.trim();
    const table = document.getElementById('tableNumber')?.value.trim();
    const room = document.getElementById('roomNumber')?.value.trim();
    const posSelect = document.getElementById('posid');
    const posTerminal = posSelect?.options[posSelect.selectedIndex]?.text || '—';
  
    // Update modal content
    document.getElementById('modalCustomerName').textContent = name || '—';
    document.getElementById('modalOrderType').textContent = orderType || '—';
    document.getElementById('modalPosTerminal').textContent = posTerminal;
  
    // Show/hide Table or Room info based on Order Type
    if (orderType.toLowerCase() === 'dine-in') {
      document.getElementById('modalTableNumber').textContent = table ? `Table #${table}` : '—';
      document.getElementById('tableInfo').classList.remove('hidden');
      document.getElementById('roomInfo').classList.add('hidden');
    } else if (orderType.toLowerCase() === 'room service') {
      document.getElementById('modalRoomNumber').textContent = room ? `Room #${room}` : '—';
      document.getElementById('roomInfo').classList.remove('hidden');
      document.getElementById('tableInfo').classList.add('hidden');
    } else {
      // If order type is not recognized, hide both
      document.getElementById('tableInfo').classList.add('hidden');
      document.getElementById('roomInfo').classList.add('hidden');
    }
  
    // Show the cart modal
    document.getElementById('cartModal')?.classList.remove('hidden');
  
    // Update cart items if needed
    if (typeof updateCartUI === "function") updateCartUI();
  }
  
// Function to close the cart modal
function closeCartModal() {
  const cartModal = document.getElementById("cartModal");
  cartModal.classList.add("hidden");
  cartModal.classList.remove("flex");
}

// Function to handle changes in order type (Dine-in / Room Delivery)
function handleOrderTypeChange() {
    const orderType = document.getElementById("orderType").value;
    const tableSelector = document.getElementById("tableSelector");
    const roomSelector = document.getElementById("roomSelector");
  
    if (orderType === "Dine-in") {
      tableSelector.classList.remove("hidden");
      roomSelector.classList.add("hidden");
    } else if (orderType === "Room Service") {
      roomSelector.classList.remove("hidden");
      tableSelector.classList.add("hidden");
    } else {
      // Hide both if no valid order type is selected
      roomSelector.classList.add("hidden");
      tableSelector.classList.add("hidden");
    }
  }

// function to calculate subtotal to TotalAmount in cart
function calculateTotalAmount(cartItems) {
  return cartItems.reduce((total, item) => total + item.subtotal, 0);
}

function saveOrderData() {
  const customerName = document.getElementById('modalCustomerName').textContent.trim();
  const orderType = document.getElementById('modalOrderType').textContent.trim();
  const tableNumber = document.getElementById('modalTableNumber').textContent.trim();
  const roomNumber = document.getElementById('modalRoomNumber').textContent.trim();

  console.log("Table Number:", tableNumber);
  console.log("Room Number:", roomNumber);
  console.log("Order Type:", orderType);

  let orderLocation = ''; // This will hold table/room info
  
  // Check for Room Service or Table
  if (orderType === 'Room Service' && roomNumber) {
    orderLocation = `Room ${roomNumber}`;
  } else if (tableNumber) {
    orderLocation = `Table ${tableNumber}`;
  } else if (roomNumber) {
    orderLocation = `Room ${roomNumber}`;
  } else {
    orderLocation = 'Unknown'; // Default value if both are missing
  }

  console.log("Order Location:", orderLocation);

  const cartItems = [];
  const items = document.querySelectorAll('#cartItems tr');
  items.forEach(item => {
    const menuItemId = parseInt(item.querySelector('td:nth-child(1)').textContent.trim()) || 0;
    const menuName = item.querySelector('td:nth-child(2)').textContent.trim();

    // Quantity from hidden input
    const quantity = parseInt(item.querySelector('input[type="hidden"]').value) || 0;

    const price = parseFloat(item.querySelector('td:nth-child(4)').textContent.trim().replace(/[₱,]/g, '')) || 0;
    const subTotal = parseFloat(item.querySelector('td:nth-child(5)').textContent.trim().replace(/[₱,]/g, '')) || 0;
    let orderDate = item.querySelector('td:nth-child(6)').textContent.trim();

    if (!orderDate) {
      const now = new Date();
      orderDate = now.toISOString().slice(0, 19).replace('T', ' ');
    }

    cartItems.push({
      menuItemId,
      menuName,
      quantity,
      price,
      subTotal,
      orderDate,
      orderType,         // use the original value from modal
      orderLocation      // use either Table or Room value
    });
  });

  const totalText = document.getElementById('cartTotal').textContent.trim();
  const totalAmount = parseFloat(totalText.replace(/[₱,]/g, '')) || 0;

  const orderData = {
    customerName,
    orderType,      // send original order type to backend
    orderLocation,  // send location separately
    totalAmount,
    cartItems
  };

  fetch('/cr1/Module/OrderMgt/save_order.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(orderData)
  })
  .then(response => response.text())
  .then(text => {
    console.log("Raw response:", text);
    const data = JSON.parse(text);
    console.log("Parsed JSON:", data);

    // Ensure 'orderId' is in the response
    if (data.success && data.orderId) {
      const orderId = data.orderId;  // Fetch the orderId from the response
      console.log("Order ID:", orderId);

      Swal.fire({
        title: 'Order Saved!',
        text: `Order #${orderId} was saved successfully.`,
        icon: 'success',
        confirmButtonText: 'OK',
        timer: 1200
      });
      
    } else {
      Swal.fire({
        title: 'Error!',
        text: data.message || 'There was a problem saving the order.',
        icon: 'error',
        confirmButtonText: 'Try Again'
      });
    }
  })
  .catch(error => {
    console.error("Save error:", error);
    Swal.fire({
      title: 'Request Failed',
      text: 'Unable to save order. Please check your connection.',
      icon: 'error',
      confirmButtonText: 'OK'
    });
  });
}

// Send data to generate receipt
fetch('../Module/OrderMgt/generate_receipt.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    orderId,
    customerName,
    orderType,
    tableNumber,
    roomNumber,
    totalAmount,
    items: Array.from(cartItemElements).map(item => ({
      menuName: item.querySelector(".menu-name")?.textContent.trim(),
      quantity: item.querySelector(".quantity")?.textContent.trim(),
      price: item.querySelector(".price")?.textContent.trim(),
      subtotal: item.querySelector(".subtotal")?.textContent.trim(),
      orderDate: item.querySelector(".order-date")?.textContent.trim()
    }))
  })
})
.then(response => response.blob())
.then(blob => {
  // Create a URL for the PDF blob and trigger download
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `Receipt_${orderID}.pdf`;
  a.click();
  
  // Reset the modal and cart after downloading the receipt
  resetCart();
  resetModal();
})
.catch(error => {
  console.error('Error generating receipt:', error);
  Swal.fire({
    title: 'Error',
    text: 'Unable to generate receipt. Please try again.',
    icon: 'error',
    confirmButtonText: 'OK'
  });
});

// Reset Cart function (Clearing cart UI and storage)
function resetCart() {
  const cartTable = document.getElementById('cart-table');
  if (cartTable) {
    cartTable.innerHTML = ''; 
  }
  localStorage.removeItem('cartItems'); 
  sessionStorage.removeItem('cartItems');
  document.getElementById('cart-total').innerText = '0.00'; 
  document.getElementById('cart-count').innerText = '0'; 
}

// Reset Modal function
function resetModal() {
  const modal = document.getElementById('cart-modal');
  if (modal) {
    modal.classList.remove('show'); 
    const modalBody = modal.querySelector('.modal-body');
    if (modalBody) {
      modalBody.innerHTML = ''; 
    }
  }
}