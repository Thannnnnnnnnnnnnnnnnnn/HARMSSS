<?php require 'partials/head.php' ?>
<?php require 'partials/hr/navbar.php' ?>

<main class="max-w-6xl mx-auto mt-6 p-6 flex-grow">
    <?php require 'partials/hr/nav.php' ?>
    <div class="py-3 px-2 text-xl font-semibold">
        Basic information
    </div>
    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table text-center">
            <!-- head -->
            <thead class="bg-[#594423] text-white">
                <tr>
                    <th class="p-3">First Name</th>
                    <th class="p-3">Last Name</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">address</th>
                    <th class="p-3">Phone</th>
                    <th class="p-3">Resume</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text-gray-700 bg-white hover:bg-gray-100">
                    <td class="p-3"><?= htmlspecialchars($applicant['first_name']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($applicant['last_name']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($applicant['email']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($applicant['address']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($applicant['contact_number']) ?></td>
                    <td class="p-3">
                        <a href="../<?= htmlspecialchars($applicant['resume']) ?>" class="text-blue-500 hover:underline" target="_blank">View Resume</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="py-3 px-2 text-xl font-semibold">
        Documents
    </div>
    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table text-center">
            <thead class="bg-[#594423] text-white">
                <tr>
                    <th class="p-3">philhealth</th>
                    <th class="p-3">SSS</th>
                    <th class="p-3">Pag-ibig</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text-gray-700 bg-white hover:bg-gray-100">
                    <td class="p-3"><a href="../<?= htmlspecialchars($applicant['philhealth']) ?>" class="text-blue-500 hover:underline" target="_blank">View philhealth</a></td>
                    <td class="p-3"><a href="../<?= htmlspecialchars($applicant['sss']) ?>" class="text-blue-500 hover:underline" target="_blank">View sss</a></td>
                    <td class="p-3"><a href="../<?= htmlspecialchars($applicant['pagibig']) ?>" class="text-blue-500 hover:underline" target="_blank">View pagibig</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</main>

<?php require 'partials/footer.php' ?>