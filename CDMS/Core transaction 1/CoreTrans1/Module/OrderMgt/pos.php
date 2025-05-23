<?php

require_once __DIR__ . "/../../includes/Database.php";

// Connect to kitchen database
$dbKitchen = new Database();
$connKitchen = $dbKitchen->connect("kitchen");

// Connect to orders database
$dbOrders = new Database();
$connOrders = $dbOrders->connect("orders");

// Ensure both connections succeeded
if (!$connKitchen || !$connOrders) {
    die("❌ Database connection failed.");
}

// Fetch menu and pos for page load
$menuItems = [];
if ($result = $connKitchen->query("SELECT * FROM menuitems")) {
    $menuItems = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
}

$posList = [];
if ($result = $connOrders->query("SELECT * FROM pos")) {
    $posList = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
} else {
    die("❌ Failed to fetch POS terminals: " . $connOrders->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Management</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      font-family: Georgia, serif;
    }
    h1, h2 {
      font-family: 'Cinzel', serif;
      font-weight: bold;
    }
  </style>
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
</head>
<body class="bg-softpillow p-8 text-deepbrown">

<!-- Fixed Header -->
<div class="fixed top-0 left-0 right-0 bg-white z-20 shadow-lg py-4 px-6 flex items-center justify-between mb-8">
  <h1 class="text-4xl font-bold">🧾 Order Management with POS System</h1>
  <div class="flex items-center gap-4">
    <a href="../../Dashboard.php" title="Go to Dashboard"
       class="bg-deepbrown text-white p-3 rounded-full hover:bg-champagnelbeige hover:text-deepbrown transition-all duration-150 flex items-center justify-center">
      <i class="fas fa-home text-xl"></i>
    </a>
    <div class="relative">
      <button id="accountButton" title="Account" class="bg-deepbrown text-white p-3 rounded-full hover:bg-champagnelbeige hover:text-deepbrown transition-all duration-150 flex items-center justify-center">
        <i class="fas fa-user text-xl"></i>
      </button>
      <div id="accountDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border-2 border-champagnelbeige rounded-large shadow-subtle p-2">
        <button onclick="confirmLogout()" class="w-full text-left px-4 py-2 text-deepbrown hover:bg-champagnelbeige rounded-btn transition-all duration-150 flex items-center gap-2">
          <i class="fas fa-sign-out-alt"></i> Logout
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Main Content Below Header -->
<div class="pt-24">

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Menu / Product List Container -->
    <div class="lg:col-span-2 flex flex-col h-full">
      <!-- Sticky Search and Filter Section -->
      <div class="sticky top-0 z-10 bg-white pb-4 pt-2">
        <div class="flex flex-col sm:flex-row gap-4 items-center">
          <input
            type="text"
            id="searchInput"
            placeholder="🔍 Search by item name..."
            class="border-2 border-deepbrown rounded-btn px-4 py-2 w-full"
            oninput="filterMenu()"
          />
          <select
            id="categoryFilter"
            class="border-2 border-deepbrown rounded-btn px-4 py-2 w-full"
            onchange="filterMenu()"
          >
            <option value="">📂 All Categories</option>
            <?php
              $categories = array_unique(array_filter(array_column($menuItems, 'Category')));
              sort($categories);
              foreach ($categories as $category):
            ?>
              <option value="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Scrollable Product List -->
      <div class="bg-white border-2 border-champagnelbeige rounded-large shadow-subtle p-4 overflow-y-auto max-h-[600px] mt-2">
        <div id="menuGrid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
          <?php foreach ($menuItems as $item): ?>
            <div 
              class="menu-item border-2 border-deepbrown bg-white rounded-large p-4 shadow-subtle flex flex-col items-center text-center h-full min-h-[400px] justify-between"
              data-name="<?= strtolower($item['ItemName']) ?>"
              data-category="<?= strtolower($item['Category'] ?? '') ?>"
            >
              <div class="w-full">
                <img 
                  src="<?= htmlspecialchars($item['ImagePath'] ?? '/images/placeholder.jpg') ?>" 
                  alt="<?= htmlspecialchars($item['ItemName']) ?>" 
                  class="w-40 h-40 object-cover rounded-md mb-3 mx-auto"
                  onerror="this.src='/images/placeholder.jpg'"
                >

  
              </div>
              <div class="flex flex-col items-center justify-start flex-grow w-full">
                <h3 class="font-bold text-lg mb-1 min-h-[48px]"><?= htmlspecialchars($item['ItemName']) ?></h3>
                <p class="text-sm mb-2 text-deepbrown">₱<?= number_format($item['Price'], 2) ?></p>
                <?php if (!empty($item['Category'])): ?>
                  <span class="text-xs px-3 py-1 bg-champagnelbeige text-deepbrown rounded-full block mb-3">
                    <?= htmlspecialchars($item['Category']) ?>
                  </span>
                <?php else: ?>
                  <div class="mb-3 h-[24px]"></div>
                <?php endif; ?>
              </div>
              <button
                onclick='addToOrder(<?= json_encode($item) ?>)'
                class="w-full px-4 py-2 bg-deepbrown text-white rounded-btn hover:bg-champagnelbeige hover:text-deepbrown transition-all duration-150"
              >
                ➕ Add to Order
              </button>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Customer Info Sidebar (Right Side) -->
    <div class="lg:col-span-1 flex flex-col">
      <div class="bg-white border-2 border-champagnelbeige rounded-large shadow-subtle p-4 sticky top-24">
        <h2 class="text-2xl font-bold mb-4">👤 Customer Info</h2>
        <div class="flex flex-col gap-4">
          <div>
            <label class="block font-semibold mb-1" for="customerName">Customer Name</label>
            <input
              type="text"
              id="customerName"
              placeholder="Enter name"
              class="w-full border rounded p-2"
            />
          </div>
          <div>
            <label class="block font-semibold mb-1" for="orderType">Order Type</label>
            <select
              id="orderType"
              class="w-full border rounded p-2"
              onchange="handleOrderTypeChange()"
            >
              <option value="">Select</option>
              <option value="Dine-in">Dine-in</option>
              <option value="Room Service">Room Service</option>
            </select>
          </div>
          <div id="tableSelector" class="hidden">
            <label class="block font-semibold mb-1" for="tableNumber">Table #</label>
            <select id="tableNumber" class="w-full border rounded p-2">
              <option value="">Select Table</option>
              <?php for ($i = 1; $i <= 20; $i++) { echo "<option value='$i'>Table #$i</option>"; } ?>
            </select>
          </div>
          <div id="roomSelector" class="hidden">
            <label class="block font-semibold mb-1" for="roomNumber">Room #</label>
            <select id="roomNumber" class="w-full border rounded p-2">
              <option value="">Select Room</option>
              <?php for ($i = 1; $i <= 50; $i++) { echo "<option value='$i'>Room #$i</option>"; } ?>
            </select>
          </div>
          <div>
            <label class="block font-semibold mb-1" for="posid">POS Terminal</label>
            <select id="posid" name="posid" class="w-full border rounded p-2">
              <?php
                if (!empty($posList)) {
                  foreach ($posList as $terminal) {
                    $selected = ($terminal['terminal_name'] === 'Cashier') ? 'selected' : '';
                    $terminalName = htmlspecialchars($terminal['terminal_name']);
                    $location = htmlspecialchars($terminal['location']);
                    echo "<option value='{$terminal['posid']}' data-location='{$location}' $selected>{$terminalName} – {$location}</option>";
                  }
                } else {
                  echo "<option value='' disabled selected>No POS terminals available</option>";
                }
              ?>
            </select>
          </div>
          <button
            onclick="openCartModal()"
            class="w-full px-4 py-2 bg-deepbrown text-white rounded-btn hover:bg-champagnelbeige hover:text-deepbrown transition-all duration-150"
          >
            🛒 View Cart
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Cart Modal -->
<div id="cartModal" role="dialog" aria-modal="true" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
  <div class="bg-white w-full sm:max-w-lg md:max-w-2xl lg:max-w-4xl xl:max-w-5xl rounded-2xl p-6 sm:p-8 md:p-10 shadow-2xl relative overflow-auto max-h-[80vh] transition-all duration-300 scale-95">
    <button onclick="closeCartModal()" aria-label="Close Modal" class="absolute top-4 right-4 text-3xl font-bold text-gray-600 hover:text-red-500 transition">
      ×
    </button>
    <h2 class="text-3xl font-bold text-center mb-6 text-gray-800">🛒 Your Cart</h2>
    <div class="flex justify-center items-center mb-6 gap-4">
      <img src="../../image/Logo.png" alt="Logo" class="h-16 w-auto">
      <img src="../../image/Logo-Name.png" alt="Logo Name" class="h-14 w-auto">
    </div>
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 mb-6">
      <h3 class="text-xl font-semibold text-gray-800 mb-4">👤 Customer Information</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-700">
        <div><strong>Customer Name:</strong> <span id="modalCustomerName">—</span></div>
        <div><strong>POS Terminal:</strong> <span id="modalPosTerminal">—</span></div>
        <div><strong>Order Type:</strong> <span id="modalOrderType">—</span></div>
        <div id="tableInfo" class="hidden"><strong></strong> <span id="modalTableNumber">—</span></div>
        <div id="roomInfo" class="hidden"><strong></strong> <span id="modalRoomNumber">—</span></div>
      </div>
    </div>
    <div class="overflow-x-auto overflow-y-auto max-h-64 border border-gray-300 rounded-xl mb-6">
      <table class="w-full table-auto text-left">
        <thead class="bg-gray-100 sticky top-0 text-sm text-gray-700">
          <tr>
            <th class="py-3 px-4 text-center font-semibold">Menu Item ID</th>
            <th class="py-3 px-4 text-center font-semibold">Menu Name</th>
            <th class="py-3 px-4 text-center font-semibold">Qty</th>
            <th class="py-3 px-4 text-center font-semibold">Price</th>
            <th class="py-3 px-4 text-center font-semibold">Subtotal</th>
            <th class="py-3 px-4 text-center font-semibold">Order Date</th>
            <th class="py-3 px-4 text-center font-semibold">Action</th>
          </tr>
        </thead>
        <tbody id="cartItems" class="text-center text-gray-800 text-sm">
          <!-- JS Populated -->
        </tbody>
      </table>
    </div>
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 border-t pt-6 w-full">
      <div class="text-xl font-semibold text-gray-700">Total:</div>
      <div id="cartTotal" class="text-xl font-semibold text-green-600">₱0.00</div>
      <div class="flex gap-4 items-center mt-6 sm:mt-0">
        <button onclick="saveOrderData()" id="saveOrderData" type="button"
          class="px-4 py-2 bg-deepbrown text-white rounded-btn hover:bg-champagnelbeige hover:text-deepbrown transition-all duration-150">
          ✅ Confirm Order
        </button>
        <button onclick="generateReceipt()" id="generateReceipt" type="button"
          class="px-4 py-2 bg-deepbrown text-white rounded-btn hover:bg-champagnelbeige hover:text-deepbrown transition-all duration-150">
          🖨️ Print Receipt
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  // Toggle account dropdown
  document.getElementById('accountButton').addEventListener('click', function() {
    const dropdown = document.getElementById('accountDropdown');
    dropdown.classList.toggle('hidden');
  });

  // Close dropdown when clicking outside
  document.addEventListener('click', function(event) {
    const accountButton = document.getElementById('accountButton');
    const accountDropdown = document.getElementById('accountDropdown');
    if (!accountButton.contains(event.target) && !accountDropdown.contains(event.target)) {
      accountDropdown.classList.add('hidden');
    }
  });

  // Confirm logout with SweetAlert
  function confirmLogout() {
    Swal.fire({
      title: 'Are you sure?',
      text: 'You will be logged out of the system.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#594423',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, logout',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = '../../logout.php';
      }
    });
  }

  function generateReceipt() {
    window.open("generate_receipt.php", "_blank");
  }
</script>
<script src="../../assets/js.js"></script>
</body>
</html>