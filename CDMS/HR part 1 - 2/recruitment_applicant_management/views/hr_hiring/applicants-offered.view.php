<?php require 'partials/head.php' ?>
<?php require 'partials/hr_hiring/navbar.php' ?>

<main class="max-w-7xl mx-auto mt-6 p-6 flex-grow">
    <?php require 'partials/hr_hiring/nav.php' ?>
    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table text-center">
            <thead class="bg-[#594423] text-white">
                <tr>
                    <th class="">Applicant ID</th>
                    <th class="">First Name</th>
                    <th class="">Last Name</th>
                    <th class="">Email</th>
                    <th class="">address</th>
                    <th class="">Phone</th>
                    <th class="">Resume</th>
                    <th class="">Status</th>
                    <th class="">Job</th>
                    <th class="">User Decision</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applicants as $applicant): ?>
                    <tr class="text-gray-700 bg-white hover:bg-gray-100">
                        <td class=""><?= htmlspecialchars($applicant['applicant_id']) ?></td>
                        <td class=""><?= htmlspecialchars($applicant['first_name']) ?></td>
                        <td class=""><?= htmlspecialchars($applicant['last_name']) ?></td>
                        <td class=""><?= htmlspecialchars($applicant['email']) ?></td>
                        <td class=""><?= htmlspecialchars($applicant['address']) ?></td>
                        <td class=""><?= htmlspecialchars($applicant['contact_number']) ?></td>
                        <td class="">
                            <?php if (!empty($applicant['resume'])) : ?>
                                <a href="../<?= htmlspecialchars($applicant['resume']) ?>" class="text-blue-500 hover:underline" target="_blank">View Resume</a>
                            <?php else : ?>
                                <p class="text-lg text-red-500">No Resume</p>
                            <?php endif ?>
                        </td>
                        <td class=" text-center">
                            <p class="px-4 py-2 rounded-lg text-sm">
                                <?= htmlspecialchars($applicant['status']) ?>
                            </p>
                        </td>
                        <td class=" text-center"><?= htmlspecialchars($applicant['job_title']) ?></td>
                        <td class=" text-center <?= $applicant['user_decision'] === 'accepted' ? 'text-green-500' : ($applicant['user_decision'] == 'rejected' ? 'text-red-500' : 'text-black') ?>"><?= htmlspecialchars($applicant['user_decision']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require 'partials/footer.php' ?>