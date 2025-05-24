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
            <h2 class="text-lg py-5 font-normal">Documents</h2>
            <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
                <table class="table text-center">
                    <thead class="bg-[#594423]">
                        <tr class="text-white">
                            <th>Resume</th>
                            <th>Philhealth</th>
                            <th>SSS</th>
                            <th>Pag-ibig</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?php if ($applicant['resume'] != null) : ?>
                                    <a href="../<?= htmlspecialchars($applicant['resume']) ?>" target="_self" class="hover:underline hover:text-blue-600">View Resume</a>
                                <?php else : ?>
                                    <p class="text-red-400">No resume found</p>
                                <?php endif ?>
                            </td>
                            <td>
                                <?php if ($applicant['philhealth'] != null) : ?>
                                    <a href="../<?= htmlspecialchars($applicant['philhealth']) ?>" target="_self" class="hover:underline hover:text-blue-600">View Philhealth</a>
                                <?php else : ?>
                                    <p class="text-red-400">No philhealth found</p>
                                <?php endif ?>
                            </td>
                            <td>
                                <?php if ($applicant['sss'] != null) : ?>
                                    <a href="../<?= htmlspecialchars($applicant['sss']) ?>" target="_self" class="hover:underline hover:text-blue-600">View SSS</a>
                                <?php else : ?>
                                    <p class="text-red-400">No SSS found</p>
                                <?php endif ?>
                            </td>
                            <td>
                                <?php if ($applicant['pagibig'] != null) : ?>
                                    <a href="../<?= htmlspecialchars($applicant['pagibig']) ?>" target="_self" class="hover:underline hover:text-blue-600">View pagibig</a>
                                <?php else : ?>
                                    <p class="text-red-400">No pagibig found</p>
                                <?php endif ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- <?php if (!$applicant['interview_status'] == null) : ?>
                <h2 class="text-lg py-5 font-normal">Interviews</h2>
                <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
                    <table class="table text-center">
                        <thead class="bg-[#594423]">
                            <tr class="text-white">
                                <th>Interview</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Mode</th>
                                <th>Location</th>
                                <th>Interviewer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?= htmlspecialchars($applicant['interview_type']) ?></td>
                                <td><?= htmlspecialchars($applicant['interview_status']) ?></td>
                                <td><?= htmlspecialchars($applicant['date']) ?></td>
                                <td><?= htmlspecialchars($applicant['time']) ?></td>
                                <td><?= htmlspecialchars($applicant['mode']) ?></td>
                                <td><?= htmlspecialchars($applicant['location']) ?></td>
                                <td><?= htmlspecialchars($interviewer['username']) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif ?> -->
        </main>
    </div>
</div>

<?php require '../../partials/admin/footer.php' ?>