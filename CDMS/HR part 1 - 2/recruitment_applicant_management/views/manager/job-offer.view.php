<?php require 'partials/head.php' ?>
<?php require 'partials/manager/navbar.php' ?>

<main class="max-w-6xl mx-auto p-6 flex-grow">
    <div class="max-w-5xl mx-auto mt-6 p-4 flex-grow">
        <div class="my-3">
            <a href="/manager/job-offers" class="text-blue-500 hover:underline"><i class="fa-solid fa-arrow-left"></i> Go back to Job offers</a>
        </div>
        <div class="overflow-x-auto rounded-box bg-base-100 shadow-lg">
            <table class="table text-center">
                <thead class="bg-[#594423] text-white">
                    <tr>
                        <th>Offer ID</th>
                        <th>Applicant Name</th>
                        <th>Position</th>
                        <th>Work Location</th>
                        <th>User Decision</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= htmlspecialchars($offer['offer_id']) ?></td>
                        <td><?= htmlspecialchars($offer['first_name']) . ' ' . htmlspecialchars($offer['last_name']) ?></td>
                        <td><?= htmlspecialchars($offer['position']) ?></td>
                        <td><?= htmlspecialchars($offer['work_location']) ?></td>
                        <td><?= htmlspecialchars($offer['user_decision']) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="overflow-x-auto rounded-box bg-base-100 shadow-lg my-10">
            <table class="table text-center">
                <thead class="bg-[#594423] text-white">
                    <tr>
                        <th>Schedule</th>
                        <th>Time in</th>
                        <th>Time out</th>
                        <th>Salary</th>
                        <th>Benefits</th>
                        <th>Responsibilities</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= htmlspecialchars($offer['schedule']) ?></td>
                        <td><?= htmlspecialchars($offer['time_in']) ?></td>
                        <td><?= htmlspecialchars($offer['time_out']) ?></td>
                        <td><?= htmlspecialchars($offer['salary']) ?></td>
                        <td><?= htmlspecialchars($offer['benefits']) ?></td>
                        <td><?= htmlspecialchars($offer['responsibilities']) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
</main>

<?php require 'partials/footer.php' ?>