<?php require 'partials/admin/head.php' ?>

<div class="flex min-h-screen w-full">
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <?php require 'partials/admin/sidebar.php' ?>

    <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
        <?php require 'partials/admin/navbar.php' ?>
        <main class="px-8 py-8">
            <?php if (isset($error)): ?>
                <div role="alert" class="alert alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?= $error ?></span>
                </div>
            <?php endif ?>
            <div class="pb-4 flex justify-between items-center text-[#594423]">
                <h1 class="font-semibold text-lg tracking-wide">Department Accounts</h1>
                <button class="btn border border-[#594423] text-[#594423] hover:bg-[#594423] hover:text-white" id="addBtn">Add account</button>
            </div>
            <div class="hidden border border-[#594423] rounded-lg mb-5 bg-white" id="addModal">
                <div class="py-4 ps-4 bg-[#594423] my-2 mx-2 rounded-lg">
                    <h2 class="font-semibold text-lg tracking-wide text-white">Add Department Account</h2>
                </div>
                <form method="POST" class="grid grid-cols-3 gap-4 p-4" id="addForm">
                    <input type="hidden" name="status" value="active" class="input input-bordered w-full max-w-xs" required />
                    <div class="col-span-1 flex flex-col justify-center items-center">
                        <label for="first_name" class="me-2">First Name:</label>
                        <input type="text" name="first_name" placeholder="Juan" class="input input-bordered w-full max-w-xs" required />
                    </div>
                    <div class="col-span-1 flex flex-col justify-center items-center">
                        <label for="last_name" class="me-2">Last Name:</label>
                        <input type="text" name="last_name" placeholder="Dela cruz" class="input input-bordered w-full max-w-xs" required />
                    </div>
                    <div class="col-span-1 flex flex-col justify-center items-center">
                        <label for="username" class="me-2">Username:</label>
                        <input type="text" name="username" placeholder="juandelacruz" class="input input-bordered w-full max-w-xs" required />
                    </div>
                    <div class="col-span-1 flex flex-col justify-center items-center">
                        <label for="email" class="me-2">Email:</label>
                        <input type="text" name="email" placeholder="juanDelacruz@example.com" class="input input-bordered w-full max-w-xs" required />
                    </div>
                    <div class="col-span-1 flex flex-col justify-center items-center">
                        <label for="password" class="me-2">Password:</label>
                        <input type="password" name="password" placeholder="********" class="input input-bordered w-full max-w-xs" required />
                    </div>
                    <div class="col-span-1 flex flex-col justify-center items-center">
                        <label for="role" class="me-2">Role:</label>
                        <select type="text" name="role" class="input input-bordered w-full max-w-xs text-center" required>
                            <option disabled selected>Choose an option:</option>
                            <option value="admin">Admin</option>
                            <option value="recruiter">Recruiter</option>
                            <option value="hiring manager">Hiring Manager</option>
                            <option value="manager">Manager</option>
                        </select>
                    </div>
                    <div class="col-span-3 flex flex-col justify-center items-center">
                        <label for="module" class="me-2">Module:</label>
                        <select type="text" name="module" placeholder="Dela cruz" class="input input-bordered w-full max-w-xs text-center" required>
                            <option disabled selected>Choose an option:</option>
                            <option value="recruitment and applicant management">Recruitment and Applicant Management</option>
                            <option value="learning and training management">Learning and training management</option>
                            <option value="performance management">Performance Management</option>
                            <option value="social recognition">Social recognition</option>
                        </select>
                    </div>
                    <div class="col-span-3 flex items-center justify-end me-10">
                        <button type="button" id="addSubmitBtn" class="btn border border-[#594423] hover:bg-[#594423] hover:text-white">Add account</button>
                    </div>
                </form>
            </div>
            <div class=" overflow-x-auto rounded-box shadow-lg bg-base-100">
                <table class="table text-center">
                    <thead class="bg-[#594423] text-white">
                        <tr>
                            <th>Dept Account ID</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Module</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($accounts as $account): ?>
                            <tr>
                                <th><?= htmlspecialchars($account['dept_accounts_id']) ?></th>
                                <td><?= htmlspecialchars($account['first_name'] . " " . $account['last_name']) ?></td>
                                <td><?= htmlspecialchars($account['username']) ?></td>
                                <td><?= htmlspecialchars($account['email']) ?></td>
                                <td><?= htmlspecialchars($account['status']) ?></td>
                                <td><?= htmlspecialchars($account['module']) ?></td>
                                <td><a href="/admin/um/dept_accounts-view?id=<?= htmlspecialchars($account['dept_accounts_id']) ?>"><i class="fa-solid fa-eye"></i></a></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>
<script>
    $('#addBtn').on('click', function() {
        $('#addModal').toggleClass('hidden');
    });
    $('#addSubmitBtn').on('click', function() {
        let isValid = true;
        $('input[required], select[required]').each(function() {
            if ($(this).val() === '') {
                isValid = false;
                $(this).addClass('border-red-500');
                swal.fire({
                    title: 'ERROR',
                    text: 'all fields must have a value',
                    icon: 'error',
                });
            } else {
                $(this).removeClass('border-red-500');
            }
        })
        if (isValid) {
            swal.fire({
                title: 'Success!',
                text: 'Department account has been added successfully.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
            $('#addForm').submit();
        } else {
            swal.fire({
                title: 'ERROR',
                text: 'An error occured',
                icon: 'error',
            });
        }
    });
</script>
<?php require 'partials/admin/footer.php' ?>