<?php require 'partials/head.php' ?>
<?php require 'partials/hr_hiring/navbar.php' ?>
<?php if ($success == true) : ?>
    <div role="alert" class="alert alert-success mx-20 mt-5">
        <box-icon name='check-circle'></box-icon>
        <span>Success! Application approved ! </span>
    </div>
<?php endif ?>
<?php if ($rejected == true) : ?>
    <div role="alert" class="alert alert-error mx-20 mt-5">
        <box-icon name='x-circle'></box-icon>
        <span>Application rejected.</span>
    </div>
<?php endif ?>

<main class="max-w-6xl mx-auto  p-6 flex-grow">
    <?php require 'partials/hr_hiring/nav.php' ?>
    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table text-center">
            <thead class="bg-[#594423] text-white">
                <tr>
                    <th class="p-3">Schedule ID</th>
                    <th class="p-3">Applicant Firstname</th>
                    <th class="p-3">Applicant lastname</th>
                    <th class="p-3">Date</th>
                    <th class="p-3">Location</th>
                    <th class="p-3">Type</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($interviews as $interview) : ?>
                    <tr class="hover:bg-gray-100 border-t border-gray-300">
                        <td class="p-3"><?= htmlspecialchars($interview['schedule_id']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($interview['first_name']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($interview['last_name']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($interview['date']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($interview['location']) ?></td>
                        <td class="p-3 "><?= htmlspecialchars($interview['mode']) ?></td>
                        <td class="p-3 "><?= htmlspecialchars($interview['status']) ?></td>
                        <td class="p-3 flex gap-3 text-center text-white">
                            <button class="btn bg-green-400 hover:bg-green-500 px-3" id="pass_btn"><box-icon name='check'></box-icon></button>
                            <div class="hidden">
                                <form method="post" id="pass_form">
                                    <input type="text" name="pass" id="pass" value="true">
                                    <input type="text" name="applicant_id" id="applicant_id" value="<?= htmlspecialchars($interview['applicant_id']) ?>" class="text-black">
                                    <input type="text" name="schedule_id" id="schedule_id" value="<?= htmlspecialchars($interview['schedule_id']) ?>" class="text-black">
                                    <input type="text" name="remarks" id="approve_remarks" class="text-black mx-2 border border-gray-300 py-5" />
                                </form>
                            </div>
                            <button class="btn bg-red-400 hover:bg-red-500 px-3" id="reject_btn"><box-icon name='x'></box-icon></button>
                            <div class="hidden">
                                <form method="post" id="reject_form">
                                    <input type="text" name="reject" id="reject" value="true">
                                    <input type="text" name="applicant_id" id="applicant_id" value="<?= htmlspecialchars($interview['applicant_id']) ?>" class="text-black">
                                    <input type="text" name="remarks" id="reject_remarks" class="text-black mx-2 border border-gray-300 py-5" />
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <?php if (count($interviews) < 1) : ?>
        <div role="alert" class="alert alert-error mx-20 mt-5">
            <box-icon name='check-circle'></box-icon>
            <span>No data found.</span>
        </div>
    <?php endif ?>
</main>

<script>
    $('#pass_btn').on('click', function() {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            input: "text",
            inputPlaceholder: "Enter your remarks",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#38c100",
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
                    text: "Application rejected",
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