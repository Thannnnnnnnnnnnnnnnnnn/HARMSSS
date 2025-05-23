<?php require 'partials/head.php' ?>
<?php require 'partials/hr_hiring/navbar.php' ?>

<main class="max-w-7xl mx-auto mt-6 p-6 flex-grow">
    <?php require 'partials/hr_hiring/nav.php' ?>
    <div class="overflow-x-auto rounded-box bg-base-100 shadow-lg">
        <table class="table bg-[#594423] text-center">
            <thead>
                <tr class="text-white">
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>address</th>
                    <th>Phone</th>
                    <th>Resume</th>
                    <th>Status</th>
                    <th>Job Applying for</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applicants as $applicant): ?>
                    <tr class="text-gray-700 bg-white hover:bg-gray-100">
                        <td class="font-bold"><?= htmlspecialchars($applicant['applicant_id']) ?></td>
                        <td><?= htmlspecialchars($applicant['first_name']) ?></td>
                        <td><?= htmlspecialchars($applicant['last_name']) ?></td>
                        <td><?= htmlspecialchars($applicant['email']) ?></td>
                        <td><?= htmlspecialchars($applicant['address']) ?></td>
                        <td><?= htmlspecialchars($applicant['contact_number']) ?></td>
                        <td>
                            <?php if (!empty($applicant['resume'])) : ?>
                                <a href="../<?= htmlspecialchars($applicant['resume']) ?>" class="text-[#594423] hover:underline" target="_blank">View Resume</a>
                            <?php else : ?>
                                <p class="text-lg text-red-500">No Resume</p>
                            <?php endif ?>
                        </td>
                        <td class="text-center">
                            <?= htmlspecialchars($applicant['status']) ?>
                        </td>
                        <td class="text-center"><?= htmlspecialchars($applicant['job_title']) ?></td>
                        <td class="text-center">
                            <a href="/hr_hiring/set-interview?id=<?= htmlspecialchars($applicant['applicant_id']) ?>" class="btn border border-[#594423] rounded-lg shadow-md hover:bg-[#594423] hover:text-white py-6">Set Interview</a>
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

<?php require 'partials/footer.php' ?>