<?php require 'partials/admin/head.php' ?>

<div class="flex min-h-screen w-full">
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <?php require 'partials/admin/sidebar.php' ?>

    <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
        <?php require 'partials/admin/navbar.php' ?>
        <main class="px-8 py-8">
            <div class="pb-4 flex justify-between items-center text-[#594423]">
                <h1 class="font-semibold text-lg tracking-wide">Department Transactions</h1>
            </div>
            <div class="overflow-x-auto rounded-box shadow-lg bg-base-100">
                <table class="table text-center">
                    <thead class="bg-[#594423] text-white">
                        <tr>
                            <th>Dept Transaction ID</th>
                            <th>Department ID</th>
                            <th>User ID</th>
                            <th>Transaction Type</th>
                            <th>Description</th>
                            <th>Department Affected</th>
                            <th>Module Affected</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transac): ?>
                            <tr>
                                <th><?= htmlspecialchars($transac['dept_transc_id']) ?></th>
                                <td><?= htmlspecialchars($transac['department_id']) ?></td>
                                <td><?= htmlspecialchars($transac['user_id']) ?></td>
                                <td><?= htmlspecialchars($transac['transaction_type']) ?></td>
                                <td><?= htmlspecialchars($transac['description']) ?></td>
                                <td><?= htmlspecialchars($transac['department_affected']) ?></td>
                                <td><?= htmlspecialchars($transac['module_affected']) ?></td>
                                <td><a href="/admin/um/dept_transaction-view?id=<?= htmlspecialchars($transac['dept_transc_id']) ?>"><i class="fa-solid fa-eye"></i></a></td>
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