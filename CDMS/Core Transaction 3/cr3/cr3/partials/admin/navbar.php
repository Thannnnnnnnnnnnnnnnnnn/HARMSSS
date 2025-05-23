
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/cr3/cr3bo/conn.php"; ?>
<!-- Main + Navbar -->
<div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
    <!-- Navbar -->
    <nav class="h-16 w-full bg-white border-b border-[#F7E6CA] flex justify-between items-center px-6 py-4">
        <!-- Left Navigation Section -->
        <div class="left-nav flex items-center space-x-4 max-w-96 w-full">
            <!-- Toggle Menu Button-->
            <button aria-label="Toggle menu" class="menu-btn text-[#4E3B2A] focus:outline-none hover:bg-[#F7E6CA] hover:rounded-full">
                <i class="fa-solid fa-bars text-[#594423] text-xl w-11 py-2"></i>
            </button>

            <div class="relative w-full flex pr-2">
                <input type="text"
                    id="searchInput"
                    class="bg-[#FFF6E8] h-10 rounded-lg grow w-full pl-10 pr-4 focus:ring-2 focus:ring-[#F7E6CA] focus:outline-none"
                    placeholder="Search something..."
                    aria-label="Search input" />
                <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-[#4E3B2A]"></i>
            </div>
        </div>


        <!-- Right Navigation Section -->
        <div class="right-nav  items-center space-x-6 hidden lg:flex">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/cr3/cr3bo/notification.php"; ?>



            <div class="flex items-center space-x-2">
                <i class="fa-regular fa-user bg-[#594423] text-white px-4 py-2 rounded-lg text-lg" aria-label="User profile"></i>
                <div class="info flex flex-col py-2">

                    <p><?= htmlspecialchars($user['username']) ?></p>
                    <p>
                        <?php if ($user['role'] == 'admin') {
                            echo 'Administrator';
                        } elseif ($user['role'] == 'manager') {
                            echo 'Manager';
                        } elseif ($user['role'] == 'staff') {
                            echo 'Staff';
                        } else {
                            echo 'Guest';
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </nav>