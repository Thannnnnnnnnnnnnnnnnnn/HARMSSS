<?php require 'partials/head.php' ?>
<?php require 'partials/manager/navbar.php' ?>

<main class="max-w-7xl mx-auto mt-6 p-6 flex-grow">
    <?php require 'partials/manager/nav.php' ?>
    <div class="overflow-x-auto rounded-box bg-base-100 shadow-lg">
        <table class="table max-w-7xl text-center">
            <thead class="bg-[#594423] text-white">
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>address</th>
                    <th>Phone</th>
                    <th>Resume</th>
                    <th>Status</th>
                    <th>Job Applying for</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applicants as $applicant): ?>
                    <tr class="text-gray-700 bg-white hover:bg-gray-100">
                        <td class="id"><?= htmlspecialchars($applicant['applicant_id']) ?></td>
                        <td><?= htmlspecialchars($applicant['first_name']) ?></td>
                        <td><?= htmlspecialchars($applicant['last_name']) ?></td>
                        <td><?= htmlspecialchars($applicant['email']) ?></td>
                        <td><?= htmlspecialchars($applicant['address']) ?></td>
                        <td><?= htmlspecialchars($applicant['contact_number']) ?></td>
                        <td>
                            <?php if (!empty($applicant['resume'])) : ?>
                                <a href="../<?= htmlspecialchars($applicant['resume']) ?>" class="text-blue-500 hover:underline" target="_blank">View Resume</a>
                            <?php else : ?>
                                <p class="text-lg text-red-500">No Resume</p>
                            <?php endif ?>
                        </td>
                        <td class="<?= $applicant['status'] === 'hired' ? 'text-green-500' : ($applicant['status'] === 'rejected' ? 'text-red-500' : '') ?>"><?= htmlspecialchars($applicant['status']) ?></td>
                        <td><?= htmlspecialchars($applicant['job_title']) ?></td>
                        <td>
                            <a href="/manager/applicant-view?id=<?= htmlspecialchars($applicant['applicant_id']) ?>" class="btn border border-[#594423] hover:bg-[#594423] hover:text-white"><i class="fa-solid fa-eye"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (count($applicants) < 1) : ?>
        <div role="alert" class="alert alert-error mx-20 mt-5">
            <box-icon name='x-circle'></box-icon>
            <span>No Data Found.</span>
        </div>
    <?php endif ?>

</main>
<script>
    document.querySelectorAll('.approve').forEach(button => {
        button.addEventListener('click', (event) => {
            const row = event.target.closest('tr');
            const applicant_id = row.querySelector('.id').textContent;
            const approve_input = document.getElementById('approve');
            approve_input.value = applicant_id;
        });
    });

    document.querySelectorAll('.reject').forEach(button => {
        button.addEventListener('click', (event) => {
            const row = event.target.closest('tr');
            const applicant_id = row.querySelector('.id').textContent;
            const reject = document.getElementById('r_reject');
            reject.value = applicant_id;
        });
    });
    $('#approve').on('click', function() {
        swal.fire({
            title: 'Applicant Approved',
            text: 'The applicant has been approved successfully.',
            icon: 'success',
        });
    });
</script>

<?php require 'partials/footer.php' ?>