<?php include('../includes/head.php'); ?>
<body>
    <div class="flex min-h-screen w-full">
        <!-- Sidebar -->
        <?php include('../includes/sidebar.php'); ?>
        
        <!-- Main + Navbar -->
        <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
            <!-- Navbar -->
            <?php include('../includes/navbar.php'); ?>
            
            <!-- Main Content -->
            <main class="px-8 py-8">
             <h3 class="text-3xl p-3 font-bold">Department Transactions</h3>
                <div class="w-full">
                 
                    <div class="mt-2 overflow-x-auto">
                        <table class="min-w-full bg-white rounded-lg overflow-hidden shadow-md border table-auto">
                            <thead class="bg-[#4E3B2A] text-white">
                                <tr>
                                    <th class="py-3 px-4 text-left border border-[#4E3B2A]">Trail ID</th>
                                    <th class="py-3 px-4 text-left border border-[#4E3B2A]">User Trail ID</th>
                                    <th class="py-3 px-4 text-left border border-[#4E3B2A]">User ID</th>
                                    <th class="py-3 px-4 text-left border border-[#4E3B2A]">Name</th>
                                      <th class="py-3 px-4 text-left border border-[#4E3B2A]">Actions</th>    
                                      <th class="py-3 px-4 text-left border border-[#4E3B2A]">Transac Type</th>                              
                                    <th class="py-3 px-4 text-left border border-[#4E3B2A]">Role</th>  
                                    <th class="py-3 px-4 text-left border border-[#4E3B2A]">Transac Date</th>                     
                                    <th class="py-3 px-4 text-center border border-[#4E3B2A]">Crud</th>
                                </tr>
                            </thead>
                            <tbody id="dataRows" class="text-gray-700">
                                <tr class="border-b hover:bg-gray-100 border border-[#F7E6CA]">
                                    <td class="py-3 px-4 ">1</td>                                     
                                    <td class="py-3 px-4">F20309</td>
                                    <td class="py-3 px-4">s254225000904</td>
                                    <td class="py-3 px-4">John Mark Balacy</td>
                                    <td class="py-3 px-4">Update data</td>
                                       <td class="py-3 px-4">Null</td>
                                    <td class="py-3 px-4">Admin</td>
                                     <td class="py-3 px-4">2025-4-23</td>
                                    <td class="py-3 px-4 text-center"><i class="fa-solid fa-sliders bg-blue-600 rounded-md text-white p-3"></i></td>
                                </tr>
                                <tr class="border-b hover:bg-gray-100 border border-[#F7E6CA]">
                                    <td class="py-3 px-4">2</td>                                     
                                    <td class="py-3 px-4">F20309</td>
                                    <td class="py-3 px-4">S254105470904</td>
                                    <td class="py-3 px-4">John Roy Vinson Dadap </td>
                                    <td class="py-3 px-4">Modify Data</td>
                                    <td class="py-3 px-4">Null</td>
                                    <td class="py-3 px-4">Super Admin</td>
                                    <td class="py-3 px-4">2025-4-23</td>
                                    <td class="py-3 px-4 text-center"><i class="fa-solid fa-sliders bg-blue-600 rounded-md text-white p-3"></i></td>
                                </tr>
                                <tr class="border-b hover:bg-gray-100 border border-[#F7E6CA]">
                                    <td class="py-3 px-4">3</td>                                     
                                    <td class="py-3 px-4">F20309</td>
                                    <td class="py-3 px-4">s254223290904</td>
                                    <td class="py-3 px-4">Ric Jason Altamante</td>
                                    <td class="py-3 px-4">Add Data</td>
                                    <td class="py-3 px-4">Null</td>
                                    <td class="py-3 px-4">Staff</td>
                                    <td class="py-3 px-4">2025-4-23</td>
                                    <td class="py-3 px-4 text-center"><i class="fa-solid fa-sliders bg-blue-600 rounded-md text-white p-3"></i></td>
                                </tr>
                                <tr class="border-b hover:bg-gray-100 border border-[#F7E6CA]">
                                    <td class="py-3 px-4">4</td>                                     
                                    <td class="py-3 px-4">F20309</td>
                                    <td class="py-3 px-4">s254124910904</td>
                                    <td class="py-3 px-4">Noriel Agbon </td>
                                    <td class="py-3 px-4">Admin</td>
                                    <td class="py-3 px-4">Null</td>
                                    <td class="py-3 px-4">Edit Data</td>
                                    <td class="py-3 px-4">2025-4-23</td>
                                    <td class="py-3 px-4 text-center"><i class="fa-solid fa-sliders bg-blue-600 rounded-md text-white p-3"></i></td>
                                </tr>
                                  <tr class="border-b hover:bg-gray-100 border border-[#F7E6CA]">
                                    <td class="py-3 px-4">5</td>                                     
                                    <td class="py-3 px-4">F20309</td>
                                    <td class="py-3 px-4">s254166290904</td>
                                    <td class="py-3 px-4">Angel Mae Parcon </td>
                                    <td class="py-3 px-4">Add Data</td>
                                        <td class="py-3 px-4">Null</td>
                                    <td class="py-3 px-4">Staff</td>
                                      <td class="py-3 px-4">2025-4-23</td>
                                    <td class="py-3 px-4 text-center"><i class="fa-solid fa-sliders bg-blue-600 rounded-md text-white p-3"></i></td>
                                </tr>
                                <tr class="border-b hover:bg-gray-100 border border-[#F7E6CA]">
                                    <td class="py-3 px-4">6</td>                                     
                                    <td class="py-3 px-4">F20309</td>
                                    <td class="py-3 px-4">s254191860904</td>
                                    <td class="py-3 px-4">Jonh Paul Ogabar </td>
                                    <td class="py-3 px-4">Super Admin</td>
                                     <td class="py-3 px-4">Null</td>
                                    <td class="py-3 px-4">Delete Data</td>
                                      <td class="py-3 px-4">2025-4-23</td>
                                    <td class="py-3 px-4 text-center"><i class="fa-solid fa-sliders bg-blue-600 rounded-md text-white p-3"></i></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="../assets/js.js"></script>
</body>
</html>
