<?php require 'partials/head.php' ?>
<?php require 'partials/manager/navbar.php' ?>

<main class="max-w-6xl mx-auto mt-6 p-6 flex-grow">
    <a href="/manager/applicants" class="text-blue-500 hover:underline"><i class="fa-solid fa-arrow-left"></i> Go back to applicants tab.</a>
    <div class="py-3 px-2 text-xl font-semibold">
        Basic information
    </div>
    <div class="overflow-x-auto rounded-box bg-base-100 shadow-lg">
        <table class="table text-center">
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
    <div>
        <div class="py-4 px-2 text-xl font-semibold">
            Documents
        </div>
        <div class="overflow-x-auto rounded-box bg-base-100 shadow-lg">
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
    </div>
    <div>
        <div class="py-4 px-2 text-xl font-semibold">
            Interview Schedules
        </div>
        <div class="overflow-x-auto rounded-box bg-base-100 shadow-lg">
            <table class="table text-center">
                <thead class="bg-[#594423] text-white">
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Location</th>
                        <th>Mode</th>
                        <th>Interview_type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($interviews) >= 1) : ?>
                        <?php foreach ($interviews as $interview): ?>
                            <tr>
                                <th><?= $interview['schedule_id'] ?></th>
                                <th><?= $interview['date'] ?></th>
                                <th><?= $interview['time'] ?></th>
                                <th><?= $interview['location'] ?></th>
                                <th><?= $interview['mode'] ?></th>
                                <th><?= $interview['interview_type'] ?></th>
                                <th><?= $interview['interview_status'] ?></th>
                            </tr>
                        <?php endforeach ?>
                    <?php else : ?>
                        <tr class="text-gray-700 bg-white hover:bg-gray-100">
                            <td colspan="7" class="p-3">No interview schedules found.</td>
                        </tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require 'partials/footer.php' ?>