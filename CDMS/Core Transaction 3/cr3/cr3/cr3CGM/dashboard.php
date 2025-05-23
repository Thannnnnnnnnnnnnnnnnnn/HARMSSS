<?php include '../session.php';?>
<!DOCTYPE html>
<html lang="en">
<head>                                               
<?php include '../header.php';?>
</head>
<body>
 <!-- For Manager Staff and Admin  --> 
    <div class="flex min-h-screen w-full">
<?php include '../sidebar.php';?>
<?php include '../navbar.php';?>

            <!-- Main Content -->
            <main class="px-8 py-8">
   <div class="min-h-screen flex bg-[#FFF6E8] font-[Georgia] text-[#4E3B2A]">

 

  <!-- Main Content -->
  <div class="flex-1 p-6">
    <!-- Header -->
    <header class="mb-6">
      <h1 class="text-3xl font-bold font-[Cinzel]">Dashboard</h1>
      <p class="text-sm mt-1 text-[#6B4F38]">Welcome back, Administrator!</p>
    </header>

   <!-- Stats Section -->
  
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
  <?php
     include 'php/connection.php';
    // Count total checked-out guests
    $stmt = $conn->query("SELECT COUNT(*) FROM guests WHERE check_out IS NOT NULL");
    $totalGuests = $stmt->fetchColumn();

    // Count total interactions
    $stmt = $conn->query("SELECT COUNT(*) FROM interactions");
    $totalInteractions = $stmt->fetchColumn();

    // Count total feedbacks
    $stmt = $conn->query("SELECT COUNT(*) FROM feedback");
    $totalFeedbacks = $stmt->fetchColumn();

    // Count total active users (edit table/condition if needed)
    $stmt = $conn->query("SELECT COUNT(*) FROM guests WHERE status = 'active'");
    $totalActiveUsers = $stmt->fetchColumn();
  ?>

  <!-- Total Guests Card -->
  <div class="bg-white p-6 rounded-2xl border border-[#594423] shadow-md flex items-center gap-4">
    <div class="bg-[#F7E6CA] p-3 rounded-full">
      <i class="bx bx-user text-2xl"></i>
    </div>
    <div>
      <p class="text-sm text-[#6B4F38]">Total Guests</p>
      <p class="text-2xl font-bold"><?php echo $totalGuests; ?></p>
    </div>
  </div>

  <!-- Interactions Card -->
  <div class="bg-white p-6 rounded-2xl border border-[#594423] shadow-md flex items-center gap-4">
    <div class="bg-[#F7E6CA] p-3 rounded-full">
      <i class="bx bx-message-square-detail text-2xl"></i>
    </div>
    <div>
      <p class="text-sm text-[#6B4F38]">Interactions</p>
      <p class="text-2xl font-bold"><?php echo $totalInteractions; ?></p>
    </div>
  </div>

  <!-- Feedbacks Card -->
  <div class="bg-white p-6 rounded-2xl border border-[#594423] shadow-md flex items-center gap-4">
    <div class="bg-[#F7E6CA] p-3 rounded-full">
      <i class="bx bx-star text-2xl"></i>
    </div>
    <div>
      <p class="text-sm text-[#6B4F38]">Feedbacks</p>
      <p class="text-2xl font-bold"><?php echo $totalFeedbacks; ?></p>
    </div>
  </div>

  <!-- Active Users Card -->
  <div class="bg-white p-6 rounded-2xl border border-[#594423] shadow-md flex items-center gap-4">
    <div class="bg-[#F7E6CA] p-3 rounded-full">
      <i class="bx bx-user-check text-2xl"></i>
    </div>
    <div>
      <p class="text-sm text-[#6B4F38]">Active Users</p>
      <p class="text-2xl font-bold"><?php echo $totalActiveUsers; ?></p>
    </div>
  </div>
</section>


   <!-- Recent Interactions -->
<section class="bg-white p-6 rounded-2xl border border-[#594423] shadow-md">
  <div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-bold font-[Cinzel]">Recent Interactions</h2>
    <a href="#" class="text-sm text-[#594423] hover:underline">View All</a>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm text-left">
      <thead>
        <tr class="bg-[#F7E6CA] text-[#4E3B2A]">
          <th class="p-3 font-medium">Guest Name</th>
          <th class="p-3 font-medium">Type</th>
          <th class="p-3 font-medium">Description</th>
          <th class="p-3 font-medium">Date</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#EAD5B0]">
        <?php
        // Fetch latest 5 interactions with guest names
        $stmt = $conn->prepare("
          SELECT i.interaction_type, i.description, i.interaction_date, g.guest_name
          FROM interactions i
          JOIN guests g ON i.GuestID = g.GuestID
          ORDER BY i.interaction_date DESC
          LIMIT 5
        ");
        $stmt->execute();
        $interactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($interactions && count($interactions) > 0) {
          foreach ($interactions as $interaction) {
            echo "<tr>";
            echo "<td class='p-3'>" . htmlspecialchars($interaction['guest_name']) . "</td>";
            echo "<td class='p-3'>" . htmlspecialchars($interaction['interaction_type']) . "</td>";
            echo "<td class='p-3'>" . htmlspecialchars($interaction['description']) . "</td>";
            echo "<td class='p-3'>" . htmlspecialchars($interaction['interaction_date']) . "</td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='4' class='p-3 text-red-500 text-center'>No recent interactions found.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</section>

  </div>
</div>

<!-- Include Boxicons (for icons) -->
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>


         </main>
    </div>

   
    
    <?php include '../footer.php';?>
</body>
</html>
