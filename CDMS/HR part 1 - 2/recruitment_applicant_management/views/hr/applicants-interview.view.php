<?php require 'partials/head.php' ?>
<?php require 'partials/hr/navbar.php' ?>
<?php if ($success == true) : ?>
    <div role="alert" class="alert alert-success mx-20 mt-5">
        <box-icon name='check-circle'></box-icon>
        <span>Success! Application successfully sent to hiring manager !</span>
    </div>
<?php endif ?>
<?php if ($rejected == true) : ?>
    <div role="alert" class="alert alert-error mx-20 mt-5">
        <box-icon name='x-circle'></box-icon>
        <span>Application rejected.</span>
    </div>
<?php endif ?>

<main class="max-w-6xl mx-auto mt-6 p-6 flex-grow">
    <?php require 'partials/hr/nav.php' ?>
    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table text-center">
            <thead class="bg-[#594423] text-white">
                <tr>
                    <th class="p-3">Schedule ID</th>
                    <th class="p-3">Firstname</th>
                    <th class="p-3">lastname</th>
                    <th class="p-3">Date</th>
                    <th class="p-3">Location</th>
                    <th class="p-3">Type</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($interviews as $interview) : ?>
                    <tr class="hover:bg-gray-100">
                        <td class="p-3 font-bold schedule_id"><?= htmlspecialchars($interview['schedule_id']) ?></td>
                        <td class="p-3 applicant_id hidden"><?= htmlspecialchars($interview['applicant_id']) ?></td>
                        <td class="p-3 "><?= htmlspecialchars($interview['first_name']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($interview['last_name']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($interview['date']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($interview['location']) ?></td>
                        <td class="p-3 "><?= htmlspecialchars($interview['mode']) ?></td>
                        <td class="p-3 "><?= htmlspecialchars($interview['status']) ?></td>
                        <td class="p-3  text-center text-white">
                            <div class="flex gap-3">
                                <button class="pass btn bg-green-400 hover:bg-green-500 px-3" id="pass_btn"><box-icon name='check'></box-icon></button>
                                <div class="hidden">
                                    <form method="post" id="pass_form">
                                        <input type="hidden" name="schedule_id" id="p_schedule_id">
                                        <input type="hidden" name="applicant_id" id="p_applicant_id">
                                        <input type="hidden" name="pass" value="true">
                                        <div class="border border-[#594423] py-4 flex flex-col rounded-xl my-3">
                                            <input type="text" name="remarks" id="approve_remarks" class="mx-2 border border-gray-300 py-5" />
                                        </div>
                                    </form>
                                </div>
                                <button class="reject btn bg-red-400 hover:bg-red-500 px-3" id="reject_btn"><box-icon name='x'></box-icon></button>
                                <div class=" hidden">
                                    <form method="post" id="reject_form">
                                        <input type="hidden" name="schedule_id" id="r_schedule_id" class="text-black">
                                        <input type="hidden" name="applicant_id" id="r_applicant_id" class="text-black">
                                        <div class="border border-[#594423] py-4 flex flex-col rounded-xl my-3">
                                            <input type="text" name="remarks" id="reject_remarks" class="mx-2 border border-gray-300 py-5" />
                                        </div>
                                        <input type="hidden" name="reject" value="true">
                                        <button type="button" class="text-white bg-red-600 hover:bg-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center" id="reject_btn">
                                            Yes, I'm sure
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <?php if (count($interviews) < 1) : ?>
        <div class="my-5">
            <div role="alert" class="alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>No data found.</span>
            </div>
        </div>
    <?php endif ?>
</main>

<script>
    document.querySelectorAll('.pass').forEach(button => {
        button.addEventListener('click', function() {
            const row = event.target.closest('tr');
            const schedule_id = row.querySelector('.schedule_id').textContent;
            const applicant_id = row.querySelector('.applicant_id').textContent;
            const p_schedule_id = document.getElementById('p_schedule_id');
            const p_applicant_id = document.getElementById('p_applicant_id');
            p_schedule_id.value = schedule_id;
            p_applicant_id.value = applicant_id;
        });
    });

    document.querySelectorAll('.reject').forEach(button => {
        button.addEventListener('click', function() {
            const row = event.target.closest('tr');
            const schedule_id = row.querySelector('.schedule_id').textContent;
            const applicant_id = row.querySelector('.applicant_id').textContent;
            const r_schedule_id = document.getElementById('r_schedule_id');
            const r_applicant_id = document.getElementById('r_applicant_id');
            r_schedule_id.value = schedule_id;
            r_applicant_id.value = applicant_id;
        });
    });

    $('#pass_btn').on('click', function() {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            input: "text",
            inputPlaceholder: "Enter your remarks",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Approve applicant",
            cancelButtonText: "No, cancel!",
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Applicant Approved!",
                    text: "Application successfully approved",
                    icon: "success"
                });
                let approve_remarks = document.getElementById('approve_remarks');
                approve_remarks.value = result.value;
                document.getElementById('pass_form').submit();
            }
        });
    });
    $('#reject_btn').on('click', function() {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            input: "text",
            inputPlaceholder: "Enter your remarks",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Reject applicant",
            cancelButtonText: "No, cancel!",
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Applicant Rejected!",
                    text: "Application successfully rejected",
                    icon: "success"
                });
                let reject_remarks = document.getElementById('reject_remarks');
                reject_remarks.value = result.value;
                document.getElementById('reject_form').submit();
            }
        });
    });
</script>

<?php require 'partials/footer.php' ?>