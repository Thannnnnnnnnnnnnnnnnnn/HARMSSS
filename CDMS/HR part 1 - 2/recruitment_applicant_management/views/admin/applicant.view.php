<?php require '../../partials/admin/head.php' ?>

<div class="flex min-h-screen w-full text-[#594423]">
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <?php require '../../partials/admin/sidebar.php' ?>

    <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
        <?php require '../../partials/admin/navbar.php' ?>
        <main class="px-2 py-5">
            <div class="text-end pe-7 text-blue-500 hover:underline hover:text-blue-600">
                <a href="applicants.php"><i class="fa-solid fa-arrow-left"></i> Back to Applicants tab</a>
            </div>
            <h2 class="text-lg py-5 font-normal">Applicant, <strong><?= $applicant['first_name'] ?></strong></h2>
            <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
                <table class="table text-center">
                    <thead class="bg-[#594423]">
                        <tr class="text-white">
                            <th>ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Date of Birth</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Job applying for</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= htmlspecialchars($applicant['applicant_id']) ?></td>
                            <td><?= htmlspecialchars($applicant['first_name']) ?></td>
                            <td><?= htmlspecialchars($applicant['last_name']) ?></td>
                            <td><?= htmlspecialchars($applicant['date_of_birth']) ?></td>
                            <td><?= htmlspecialchars($applicant['contact_number']) ?></td>
                            <td><?= htmlspecialchars($applicant['email']) ?></td>
                            <td class="<?= $applicant['status'] === 'hired' ? 'text-green-500 font-bold' : '' ?>"><?= htmlspecialchars($applicant['status']) ?></td>
                            <td><?= htmlspecialchars($applicant['job_title']) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<?php require '../../partials/admin/footer.php' ?>