
<?php include('includes/head.php'); ?>
<body>
    <div class="flex min-h-screen w-full">
        <!-- Sidebar -->
        <?php include('includes/sidebar.php'); ?>

        <!-- Main + Navbar -->
        <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
            <!-- Navbar -->
            <?php include('includes/navbar.php'); ?>
            <!-- Main Content -->
           <main class="px-8 py-4">
  <div class="p-4 space-y-6">
 <!-- Welcome Section -->
 <div class="flex items-center justify-between bg-gradient-to-r from-blue-50 to-white px-6 py-5 rounded-2xl shadow-md mb-6">
  <div class="flex items-center gap-4">
    <div class="bg-blue-100 p-3 rounded-full shadow-sm">
      <img src="images/logo.png" alt="Profile picture of Ms. Madelyn Cline" class="w-9 h-9 rounded-full object-cover">
    </div>
    <div>
      <h1 class="text-2xl font-bold text-gray-800">DASHBOARD</h1>
      <p class="text-sm text-gray-600">Mr. John Mark Balacy · Hotel Finance Admin</p>
      <p class="text-sm text-gray-600">Mr. Ric Jason Altamante · Hotel Finance Manager</p>
    </div>
  </div>
  <div class="text-right hidden sm:block">
    <p class="text-sm text-gray-500">Today is <span class="font-medium text-gray-700">
      <?php echo date('F j, Y'); ?>
    </span></p>
  </div>
</div>

    <!-- Total Budget Created -->
    <div class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition-all flex items-center justify-between">
      <div>
        <h2 class="text-gray-600 text-lg font-semibold">Total Budget Created</h2>
        <p class="text-4xl font-bold text-gray-800 mt-2">₱1,250,000</p>
      </div>
      <div class="bg-orange-100 p-4 rounded-full text-orange-500">
        <i class="fas fa-wallet text-3xl"></i>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      
      <!-- Recent Transactions -->
      <div class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition-all">
        <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
          <i class="fas fa-clock text-gray-600"></i> Recent Transactions
        </h2>
        <table class="w-full text-left text-sm rounded overflow-hidden">
          <thead class="text-gray-500 border-b">
            <tr>
              <th>Date</th>
              <th>Description</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody class="text-gray-700">
            <tr class="border-b hover:bg-gray-50">
              <td>April 10</td>
              <td>Room Booking</td>
              <td>₱5,000</td>
            </tr>
            <tr class="border-b hover:bg-gray-50">
              <td>April 9</td>
              <td>Supplier Payment</td>
              <td>₱12,000</td>
            </tr>
            <tr class="border-b hover:bg-gray-50">
              <td>April 8</td>
              <td>Food Sale</td>
              <td>₱8,500</td>
            </tr>
            <tr class="hover:bg-gray-50">
              <td>March 7</td>
              <td>Utility Expense</td>
              <td>₱3,000</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Logs -->
      <div class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition-all">
        <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
          <i class="fas fa-list-alt text-gray-600"></i> Logs
        </h2>
        <div class="mb-3 flex gap-2">
          <button class="bg-gray-200 hover:bg-gray-300 text-sm font-semibold px-3 py-1 rounded">
            <i class="fas fa-edit mr-1"></i> Crude History
          </button>
          <button class="bg-gray-100 hover:bg-gray-200 text-sm font-semibold px-3 py-1 rounded">
            <i class="fas fa-sign-in-alt mr-1"></i> Login History
          </button>
        </div>
        <ul class="text-sm text-gray-700 space-y-3">
          <li>
            <strong class="text-orange-600">[UPDATE]</strong> Modified transaction #1004  
            <div class="text-xs text-gray-500">April 11, 2024, 10:23 AM</div>
          </li>
          <li>
            <strong class="text-blue-600">[LOGOUT]</strong> User logout  
            <div class="text-xs text-gray-500">April 11, 2024, 09:15 AM</div>
          </li>
        </ul>
      </div>
    </div>

    <!-- History Section -->
<div class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition-all">
  <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
    <i class="fas fa-folder-open text-gray-600"></i> History
  </h2>

  <!-- Tabs -->
  <div class="flex gap-4 mb-4">
    <button onclick="showTab('allocation')" id="tab-allocation"
      class="tab-btn bg-blue-100 text-blue-700 px-3 py-1 rounded text-sm font-semibold hover:bg-blue-200">
      <i class="fas fa-coins mr-1"></i> Allocation
    </button>
    <button onclick="showTab('payable')" id="tab-payable"
      class="tab-btn bg-gray-100 text-gray-700 px-3 py-1 rounded text-sm font-semibold hover:bg-gray-200">
      <i class="fas fa-file-invoice-dollar mr-1"></i> Payable
    </button>
    <button onclick="showTab('collection')" id="tab-collection"
      class="tab-btn bg-gray-100 text-gray-700 px-3 py-1 rounded text-sm font-semibold hover:bg-gray-200">
      <i class="fas fa-hand-holding-usd mr-1"></i> Collection
    </button>
  </div>

  <!-- Allocation Data -->
  <ul id="allocation" class="tab-content text-sm text-gray-700 space-y-2">
    <li class="flex justify-between border-b pb-2 hover:text-blue-600">
      <span>April 5</span><span>Payroll Fund</span>
    </li>
    <li class="flex justify-between border-b pb-2 hover:text-blue-600">
      <span>April 1</span><span>Maintenance Budget</span>
    </li>
      <li class="flex justify-between border-b pb-2 hover:text-blue-600">
      <span>April 1</span><span>Maintenance Budget</span>
    </li>
      <li class="flex justify-between border-b pb-2 hover:text-blue-600">
      <span>April 1</span><span>Maintenance Budget</span>
    </li>
      <li class="flex justify-between border-b pb-2 hover:text-blue-600">
      <span>April 1</span><span>Maintenance Budget</span>
    </li>
  </ul>

  <!-- Payable Data -->
  <ul id="payable" class="tab-content text-sm text-gray-700 space-y-2 hidden">
    <li class="flex justify-between border-b pb-2 hover:text-red-600">
      <span>April 4</span><span>Electric Bill</span>
    </li>
    <li class="flex justify-between border-b pb-2 hover:text-red-600">
      <span>March 30</span><span>Vendor Payment</span>
    </li>
        <li class="flex justify-between border-b pb-2 hover:text-red-600">
      <span>March 30</span><span>Vendor Payment</span>
    </li>
        <li class="flex justify-between border-b pb-2 hover:text-red-600">
      <span>March 30</span><span>Vendor Payment</span>
    </li>
        <li class="flex justify-between border-b pb-2 hover:text-red-600">
      <span>March 30</span><span>Vendor Payment</span>
    </li>
  </ul>

  <!-- Collection Data -->
  <ul id="collection" class="tab-content text-sm text-gray-700 space-y-2 hidden">
    <li class="flex justify-between border-b pb-2 hover:text-green-600">
      <span>April 3</span><span>Event Booking</span>
    </li>
    <li class="flex justify-between border-b pb-2 hover:text-green-600">
      <span>March 28</span><span>Restaurant Sales</span>
    </li>
        <li class="flex justify-between border-b pb-2 hover:text-green-600">
      <span>March 28</span><span>Restaurant Sales</span>
    </li>
        <li class="flex justify-between border-b pb-2 hover:text-green-600">
      <span>March 28</span><span>Restaurant Sales</span>
    </li>
        <li class="flex justify-between border-b pb-2 hover:text-green-600">
      <span>March 28</span><span>Restaurant Sales</span>
    </li>
  </ul>
</div>


  </div>
</main>

        </div>
        
    </div>

    
        <script src="./assets/js.js"></script>

</body>
</html>
<script>
  function showTab(tabId) {
    // Hide all content
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));

    // Reset all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.classList.remove('bg-blue-100', 'text-blue-700');
      btn.classList.add('bg-gray-100', 'text-gray-700');
    });

    // Show selected tab
    document.getElementById(tabId).classList.remove('hidden');

    // Highlight active button
    document.getElementById(`tab-${tabId}`).classList.remove('bg-gray-100', 'text-gray-700');
    document.getElementById(`tab-${tabId}`).classList.add('bg-blue-100', 'text-blue-700');
  }
</script>
