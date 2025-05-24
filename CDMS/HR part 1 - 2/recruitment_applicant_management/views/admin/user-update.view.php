<?php require '../../partials/admin/head.php' ?>


<div class="flex min-h-screen w-full text-[#594423]">
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <?php require '../../partials/admin/sidebar.php' ?>
    <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
        <?php require '../../partials/admin/navbar.php' ?>
        <div class="text-end pe-10 pt-10">
            <a href="/admin/users" class="hover:underline text-blue-500"><i class="fa-solid fa-arrow-left"></i> Back to users page</a>
        </div>
        <div class="bg-white px-15 py-10 mx-10 my-5 rounded-lg shadow-lg">
            <div class="bg-[#594423] text-white text-lg font-semibold rounded-lg px-5 py-4 shadow-md mb-5 tracking-wider">
                <h2>Update user account</h2>
            </div>
            <form method="post">
                <div class="grid grid-cols-2 md:grid-cols-2 gap-4 mb-5 justify-items-center p-5 text-black">
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id']) ?>">
                    <div class="flex flex-col items-center">
                        <label for="first_name">First Name:</label>
                        <input type="text" class="input" id="first_name" name="first_name" placeholder="First Name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                    </div>
                    <div class="flex flex-col items-center">
                        <label for="last_name">Last Name:</label>
                        <input type="text" class="input" id="last_name" name="last_name" placeholder="Last Name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                    </div>
                    <div class="flex flex-col items-center">
                        <label for="username">Username:</label>
                        <input type="text" class="input" id="username" name="username" placeholder="Username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    <div class="flex flex-col items-center">
                        <label for="email">Email:</label>
                        <input type="email" class="input" id="email" name="email" placeholder="Email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="col-span-2 flex flex-col items-center w-full">
                        <label for="role">Role:</label>
                        <select name="role" id="role" class="select" required>
                            <option value="<?= htmlspecialchars($user['role']) ?>" class="bg-gray-200"><?= htmlspecialchars($user['role']) ?></option>
                            <option value="admin">admin</option>
                            <option value="manager">manager</option>
                            <option value="hiring manager">hiring manager</option>
                            <option value="recruiter">recruiter</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <button type="button" id="submitBtn" class="btn px-7 hover:bg-[#594423] hover:text-white tracking-wide border-[#594423]">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#submitBtn').click(function() {
                var isValid = true;
                $('input[required], select[required]').each(function() {
                    if ($(this).val() === '') {
                        isValid = false;
                        $(this).addClass('border-red-500');
                    } else {
                        $(this).removeClass('border-red-500');
                    }
                });

                if (isValid) {
                    swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'User account updated successfully.',
                        timer: 1800,
                    });
                    $('form').submit();
                } else {
                    swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please fill in all fields.',
                        timer: 1800,
                    });
                }
            });
        });
    </script>
    <?php require '../../partials/admin/footer.php' ?>