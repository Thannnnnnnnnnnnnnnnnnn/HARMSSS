<?php require 'partials/admin/head.php' ?>

<div class="flex min-h-screen w-full">
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <?php require 'partials/admin/sidebar.php' ?>

    <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
        <?php require 'partials/admin/navbar.php' ?>
        <main class="px-8 py-8">
            <div class="pb-4 flex justify-between items-center text-[#594423]">
                <h1 class="font-semibold text-lg tracking-wide"><?= $account['first_name'] . " " . $account['last_name'] ?></h1>
                <a href="/admin/um/dept_accounts" class="btn border border-[#594423] hover:bg-[#594423] hover:text-white">Back to accounts page</a>
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
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th><?= htmlspecialchars($account['dept_accounts_id']) ?></th>
                            <td><?= htmlspecialchars($account['first_name'] . " " . $account['last_name']) ?></td>
                            <td><?= htmlspecialchars($account['username']) ?></td>
                            <td><?= htmlspecialchars($account['email']) ?></td>
                            <td><?= htmlspecialchars($account['status']) ?></td>
                            <td><?= htmlspecialchars($account['module']) ?></td>
                        </tr>
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