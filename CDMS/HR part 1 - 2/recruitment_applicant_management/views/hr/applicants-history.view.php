<?php require 'partials/head.php' ?>
<?php require 'partials/hr/navbar.php' ?>

<main class="max-w-7xl mx-auto mt-6 p-6 flex-grow">
    <?php require 'partials/hr/nav.php' ?>
    <div class="overflow-x-auto shadow-lg rounded-lg bg-white">
        <table class="table table-xs text-center">
            <thead class="bg-[#594423] text-white">
                <tr>
                    <th>Interview ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Mode</th>
                    <th>Interview Type</th>
                    <th>Interview Status</th>
                    <th>Applicant ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Interviewer ID</th>
                    <th>Current Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($interviews as $interview) : ?>
                    <tr>
                        <th><?= htmlspecialchars($interview['schedule_id']) ?></th>
                        <th><?= htmlspecialchars($interview['date']) ?></th>
                        <th><?= htmlspecialchars($interview['time']) ?></th>
                        <th><?= htmlspecialchars($interview['location']) ?></th>
                        <th><?= htmlspecialchars($interview['mode']) ?></th>
                        <th><?= htmlspecialchars($interview['interview_type']) ?></th>
                        <th><?= htmlspecialchars($interview['interview_status']) ?></th>
                        <th><?= htmlspecialchars($interview['applicant_id']) ?></th>
                        <th><?= htmlspecialchars($interview['first_name']) ?></th>
                        <th><?= htmlspecialchars($interview['last_name']) ?></th>
                        <th><?= htmlspecialchars($interview['interviewer_id']) ?></th>
                        <th><?= htmlspecialchars($interview['status']) ?></th>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <div class="mt-10">
        <p class="font-bold">Total interviews: <?= count($interviews) ?></p>
    </div>
</main>

<?php require 'partials/footer.php' ?>