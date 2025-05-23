<?php require '../../partials/admin/head.php' ?>

<div class="flex min-h-screen w-full">
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <?php require '../../partials/admin/sidebar.php' ?>
    <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
        <?php require '../../partials/admin/navbar.php' ?>
        <main class="px-8 py-8">
            <div>
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl tracking-wider font-semibold">Initial Interview</h2>
                    <a href="interview_schedules-create.php" class="btn border border-[#594423] hover:bg-[#594423] hover:text-white shadow-lg">create interview schedule</a>
                </div>
                <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
                    <table class="table text-center">
                        <thead class="bg-[#594423] text-white">
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Location</th>
                                <th>Mode</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Applicant</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($initial_schedules as $initial_schedule) : ?>
                                <tr>
                                    <td class="schedule_id border-r border-t"><?= htmlspecialchars($initial_schedule['schedule_id']) ?></td>
                                    <td class="date border-r border-t"><?= htmlspecialchars($initial_schedule['date']) ?></td>
                                    <td class="time border-r border-t"><?= htmlspecialchars($initial_schedule['time']) ?></td>
                                    <td class="location border-r border-t"><?= htmlspecialchars($initial_schedule['location']) ?></td>
                                    <td class="mode border-r border-t"><?= htmlspecialchars($initial_schedule['mode']) ?></td>
                                    <td class="interview_type border-r border-t"><?= htmlspecialchars($initial_schedule['interview_type']) ?></td>
                                    <td class="interview_status border-r border-t"><?= htmlspecialchars($initial_schedule['interview_status']) ?></td>
                                    <td class="first_name border-r border-t"><?= htmlspecialchars($initial_schedule['first_name']) ?></td>
                                    <td class="border-t">
                                        <div class="flex justify-center gap-2">
                                            <form method="POST" id="passForm">
                                                <input type="hidden" name="pass" value="true">
                                                <input type="hidden" name="applicant_id" value="<?= htmlspecialchars($initial_schedule['applicant_id']) ?>">
                                                <input type="hidden" name="schedule_id" value="<?= htmlspecialchars($initial_schedule['schedule_id']) ?>">
                                                <button type="button" title="Passed" id="passBtn" class="btn bg-green-500 text-xl rounded-lg"><i class="fa-solid fa-user-check"></i></button>
                                            </form>
                                            <form method="POST" id="failForm">
                                                <input type="hidden" name="fail" value="true">
                                                <input type="hidden" name="applicant_id" value="<?= htmlspecialchars($initial_schedule['applicant_id']) ?>">
                                                <input type="hidden" name="schedule_id" value="<?= htmlspecialchars($initial_schedule['schedule_id']) ?>">
                                                <button type="button" title="Failed" id="failBtn" class="btn bg-red-500 text-xl rounded-lg"><i class="fa-solid fa-user-xmark"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <div class="my-5 flex items-center mb-5">
                    <h2 class="text-xl tracking-wider font-semibold">Final Interview</h2>
                </div>
                <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
                    <table class="table text-center">
                        <thead class="bg-[#594423] text-white">
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Location</th>
                                <th>Mode</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Applicant</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($final_schedules as $final_schedule) : ?>
                                <tr>
                                    <td class="schedule_id border-r border-t"><?= htmlspecialchars($final_schedule['schedule_id']) ?></td>
                                    <td class="date border-r border-t"><?= htmlspecialchars($final_schedule['date']) ?></td>
                                    <td class="time border-r border-t"><?= htmlspecialchars($final_schedule['time']) ?></td>
                                    <td class="location border-r border-t"><?= htmlspecialchars($final_schedule['location']) ?></td>
                                    <td class="mode border-r border-t"><?= htmlspecialchars($final_schedule['mode']) ?></td>
                                    <td class="interview_type border-r border-t"><?= htmlspecialchars($final_schedule['interview_type']) ?></td>
                                    <td class="interview_status border-r border-t"><?= htmlspecialchars($final_schedule['interview_status']) ?></td>
                                    <td class="first_name border-r border-t"><?= htmlspecialchars($final_schedule['first_name']) ?></td>
                                    <td class="border-t">
                                        <div class="flex justify-center gap-2">
                                            <form method="POST" id="passForm">
                                                <input type="hidden" name="pass" value="true">
                                                <input type="hidden" name="applicant_id" value="<?= htmlspecialchars($final_schedule['applicant_id']) ?>">
                                                <input type="hidden" name="schedule_id" value="<?= htmlspecialchars($final_schedule['schedule_id']) ?>">
                                                <button type="button" title="Passed" id="passBtn" class="btn bg-green-500 text-xl rounded-lg"><i class="fa-solid fa-user-check"></i></button>
                                            </form>
                                            <form method="POST" id="failForm">
                                                <input type="hidden" name="fail" value="true">
                                                <input type="hidden" name="applicant_id" value="<?= htmlspecialchars($final_schedule['applicant_id']) ?>">
                                                <input type="hidden" name="schedule_id" value="<?= htmlspecialchars($final_schedule['schedule_id']) ?>">
                                                <button type="button" title="Failed" id="failBtn" class="btn bg-red-500 text-xl rounded-lg"><i class="fa-solid fa-user-xmark"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <div class="my-5 flex items-center mb-5">
                    <h2 class="text-xl tracking-wider font-semibold">Done Interview</h2>
                </div>
                <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
                    <table class="table text-center">
                        <thead class="bg-[#594423] text-white">
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Location</th>
                                <th>Mode</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Applicant</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($final_schedules as $final_schedule) : ?>
                                <tr>
                                    <td class="schedule_id border-r border-t"><?= htmlspecialchars($final_schedule['schedule_id']) ?></td>
                                    <td class="date border-r border-t"><?= htmlspecialchars($final_schedule['date']) ?></td>
                                    <td class="time border-r border-t"><?= htmlspecialchars($final_schedule['time']) ?></td>
                                    <td class="location border-r border-t"><?= htmlspecialchars($final_schedule['location']) ?></td>
                                    <td class="mode border-r border-t"><?= htmlspecialchars($final_schedule['mode']) ?></td>
                                    <td class="interview_type border-r border-t"><?= htmlspecialchars($final_schedule['interview_type']) ?></td>
                                    <td class="interview_status border-r border-t"><?= htmlspecialchars($final_schedule['interview_status']) ?></td>
                                    <td class="first_name border-r border-t"><?= htmlspecialchars($final_schedule['first_name']) ?></td>
                                    <td class="border-t">
                                        <div class="flex justify-center gap-2">
                                            <button type="button" class="btn rounded-lg bg-blue-500 text-xl"><i class="fa-solid fa-eye"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if (count($initial_schedules) + count($final_schedules)  < 1) : ?>
                <div role="alert" class="alert alert-error my-6 mx-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Error! No Data Found.</span>
                </div>
            <?php endif ?>
        </main>
    </div>
</div>
<script>
    $('#passBtn').on('click', function() {
        // console.log('Pass button clicked');
        swal.fire({
            title: 'Application Passed',
            text: "Applicant passed the interview",
            icon: 'success',
        });
        $('#passForm').submit();
    });
    $('#failBtn').on('click', function() {
        swal.fire({
            title: 'Application Failed',
            text: "Applicant failed the interview",
            icon: 'error',
        });
        $('#failForm').submit();
    });
</script>
<?php require '../../partials/admin/footer.php' ?>