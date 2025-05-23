<?php require 'partials/head.php' ?>
<?php require 'partials/hr_hiring/navbar.php' ?>

<main class="max-w-7xl mx-auto mt-6 p-6 flex-grow">
    <?php require 'partials/hr_hiring/nav.php' ?>
    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table table-sm text-center">
            <!-- head -->
            <thead class="bg-[#594423] text-white">
                <tr>
                    <th class="p-3">Schedule ID</th>
                    <th class="p-3">Date</th>
                    <th class="p-3">location</th>
                    <th class="p-3">Interview Status</th>
                    <th class="p-3">First Name</th>
                    <th class="p-3">Last Name</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Job Applying for</th>
                    <th class="p-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applicants as $applicant): ?>
                    <tr class="text-gray-700 bg-white hover:bg-gray-100">
                        <td class="p-3"><?= htmlspecialchars($applicant['schedule_id']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($applicant['date']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($applicant['location']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($applicant['interview_status']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($applicant['first_name']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($applicant['last_name']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($applicant['email']) ?></td>
                        <?php if (htmlspecialchars($applicant['status']) === 'job offered'): ?>
                            <td class="p-3 text-center font-bold"><?= htmlspecialchars($applicant['status']) ?></td>
                        <?php else: ?>
                            <td class="p-3 text-center"><?= htmlspecialchars($applicant['status']) ?></td>
                        <?php endif ?>
                        <td class="p-3 text-center"><?= htmlspecialchars($applicant['job_title']) ?></td>
                        <td class="p-3 flex gap-2 flex-col text-center ">
                            <?php if (htmlspecialchars($applicant['status'])) ?>
                            <a href="/hr_hiring/applicants-offer?id=<?= htmlspecialchars($applicant['applicant_id']) ?>" class="border border-[#594423] text-[#594423] text-md font-normal px-2 py-2 rounded hover:text-white hover:bg-[#594423] transition">Job offer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (count($applicants) < 1) : ?>
        <div role="alert" class="alert alert-error mx-20 mt-5">
            <box-icon name='check-circle'></box-icon>
            <span>No data found.</span>
        </div>
    <?php endif ?>
</main>

<script>
    document.querySelectorAll('.openModal').forEach(button => {
        button.addEventListener('click', function() {
            let applicantId = this.getAttribute('data-applicant-id');
            document.getElementById('modalApplicantId').value = applicantId;
        });
    });
</script>

<?php require 'partials/footer.php' ?>