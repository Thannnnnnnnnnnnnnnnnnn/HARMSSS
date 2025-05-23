<?php require 'partials/admin/head.php' ?>

<div class="flex min-h-screen w-full">
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <?php require 'partials/admin/sidebar.php' ?>

    <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
        <?php require 'partials/admin/navbar.php' ?>
        <main class="px-8 py-8">
            <div class="pb-4 flex justify-between items-center text-[#594423]">
                <h1 class="font-semibold text-lg tracking-wide">Department Log History</h1>
            </div>
            <div class="overflow-x-auto rounded-box shadow-lg bg-base-100">
                <table class="table text-center">
                    <thead class="bg-[#594423] text-white">
                        <tr>
                            <th>Dept Log ID</th>
                            <th>Department</th>
                            <th>Email</th>
                            <th>Event Type</th>
                            <th>Failure Reason</th>
                            <th>IP Address</th>
                            <th>user_agent</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <th><?= htmlspecialchars($log['dept_log_id']) ?></th>
                                <td><?= htmlspecialchars($log['department_id']) ?></td>
                                <td><?= htmlspecialchars($log['email']) ?></td>
                                <td><?= htmlspecialchars($log['event_type']) ?></td>
                                <?php if (htmlspecialchars($log['failure_reason'] === null)): ?>
                                    <td class="text-gray-300">Null</td>
                                <?php else : ?>
                                    <td><?= htmlspecialchars($log['failure_reason']) ?></td>
                                <?php endif ?>
                                <td><?= htmlspecialchars($log['ip_address']) ?></td>
                                <td><?= htmlspecialchars($log['user_agent']) ?></td>
                                <td><a href="/admin/um/dept_log_history-view?id=<?= htmlspecialchars($log['dept_log_id']) ?>"><i class="fa-solid fa-eye"></i></a></td>
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