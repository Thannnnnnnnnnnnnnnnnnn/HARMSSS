<?php require 'partials/head.php' ?>
<?php require 'partials/hr/navbar.php' ?>

<main class="max-w-6xl mx-auto mt-6 p-6 flex-grow">
    <?php require 'partials/hr/nav.php' ?>
    <div class="overflow-x-auto rounded-box bg-base-100 shadow-lg">
        <table class="table text-center">
            <thead class="bg-[#594423] text-white">
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>address</th>
                    <th>Phone</th>
                    <th>Resume</th>
                    <th>Job</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applicants as $applicant): ?>
                    <tr class="text-gray-700 bg-white hover:bg-gray-100">
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
                        <td class=" text-center"><?= htmlspecialchars($applicant['job_title']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require 'partials/footer.php' ?>