<?php require '../../partials/admin/head.php' ?>

<div class="flex min-h-screen w-full">
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <?php require '../../partials/admin/sidebar.php' ?>

    <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
        <?php require '../../partials/admin/navbar.php' ?>
        <main class="px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white shadow-lg rounded-lg p-4">
                    <div class="text-gray-600 font-semibold text-sm uppercase tracking-wide">Total Applicant(s)</div>
                    <div class="text-3xl font-bold text-gray-800" id="total-applicants">
                        <?= htmlspecialchars($totalApplicants) ?>
                    </div>
                    <div class="text-gray-500 text-xs mt-1">As of Today</div>
                </div>
                <div class="bg-white shadow-lg rounded-lg p-4">
                    <div class="text-gray-600 font-semibold text-sm uppercase tracking-wide">Total Job Postings</div>
                    <div class="text-3xl font-bold text-gray-800" id="total-jobs">
                        <?= htmlspecialchars($totalJobPostings) ?>
                    </div>
                    <div class="text-gray-500 text-xs mt-1">Active</div>
                </div>
                <div class="bg-white shadow-lg rounded-lg p-4">
                    <div class="text-gray-600 font-semibold text-sm uppercase tracking-wide">Ongoing Interviews</div>
                    <div class="text-3xl font-bold text-gray-800" id="total-ongoing-interviews">
                        <?= htmlspecialchars($totalOngoingInterviews) ?>
                    </div>
                    <div class="text-gray-500 text-xs mt-1">Scheduled</div>
                </div>
                <div class="bg-white shadow-lg rounded-lg p-4">
                    <div class="text-gray-600 font-semibold text-sm uppercase tracking-wide">Completed Interviews</div>
                    <div class="text-3xl font-bold text-gray-800" id="total-done-interviews">
                        <?= htmlspecialchars($totalDoneInterviews) ?>
                    </div>
                    <div class="text-gray-500 text-xs mt-1">Finalized</div>
                </div>
                <div class="bg-white shadow-lg rounded-lg p-4">
                    <div class="text-gray-600 font-semibold text-sm uppercase tracking-wide">Total new hire</div>
                    <div class="text-3xl font-bold text-gray-800" id="total-newhire-interviews">
                        <?= htmlspecialchars(count($totalNewHireInterviews)) ?>
                    </div>
                    <div class="text-gray-500 text-xs mt-1">As of Today</div>
                </div>
            </div>
            <div class="mt-8">
                <h2 class="text-xl font-semibold mb-4">Active Job Postings</h2>
                <div class="overflow-x-auto shadow-lg">
                    <div class="overflow-x-auto rounded-box bg-base-100">
                        <table class="table bg-white">
                            <thead>
                                <tr class="bg-[#594423] text-white">
                                    <th class="px-4 py-2 text-left">Title</th>
                                    <th class="px-4 py-2 text-left">Date Posted</th>
                                    <th class="px-4 py-2 text-left">Applicants</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jobs as $job): ?>
                                    <tr class="hover:bg-gray-100">
                                        <td class="border-r border-t px-4 py-2"><?= htmlspecialchars($job['job_title']) ?></td>
                                        <td class="border-r border-t px-4 py-2"><?= htmlspecialchars($job['created_at']) ?></td>
                                        <td class="border-t px-4 py-2"><?= htmlspecialchars($job['applicant_count']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-8">
                <h2 class="text-xl font-semibold mb-4">Recent Applicants</h2>
                <ul>
                    <?php foreach ($recentApplicants as $applicant): ?>
                        <li><?= htmlspecialchars($applicant['first_name']) . ' ' . htmlspecialchars($applicant['last_name']) ?> - Applied for: <?= htmlspecialchars($applicant['job_title']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </main>
    </div>
</div>

<?php require '../../partials/admin/footer.php' ?>