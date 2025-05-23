<?php require 'partials/head.php' ?>
<?php require 'partials/manager/navbar.php' ?>

<main class="w-screen mx-auto  p-6 flex-grow flex justify-center font-normal text-black">
    <div class="w-full max-w-5xl min-h-[300px] p-6 bg-white  border border-[#594423] rounded-lg shadow-sm">
        <div class="flex flex-col justify-between py-5 gap-10">
            <div class="grid grid-cols-3 gap-5">
                <div class="col-span-3 flex items-center justify-between">
                    <h1 class="text-xl font-semibold"><?= strtoupper($notification['type']) ?></h1>
                    <?php if ($notification['status'] == 'read') : ?>
                        <p>READ</p>
                    <?php else : ?>
                        <form method="POST" class="mark-as-read-form">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($notification['id']) ?>">
                            <label for="read-<?= $notification['id'] ?>" class="text-[#4E3B2A] me-2">Mark as read</label>
                            <input type="checkbox" name="read" id="read-<?= htmlspecialchars($notification['id']) ?>" class="read-checkbox border border-black">
                        </form>
                    <?php endif ?>
                </div>
                <div class="col-span-3 flex justify-between ">
                    <h3>Job Offer Information :</h3>
                    <p>Status: <span class="<?= $offer['status'] === 'approved' ? 'text-green-500' : ($offer['status'] === 'rejected' ? 'text-red-500' : '') ?>"><?= $offer['status'] ?></span></p>
                </div>
                <div class="border border-[#594423] text-center rounded-lg shadow-md  py-2 px-2">
                    <p class="text-[#594423] text-start">Applicant Name:</p>
                    <p><?= $offer['first_name'] . ' ' . $offer['last_name'] ?></p>
                </div>
                <div class="border border-[#594423] text-center rounded-lg shadow-md  py-2 px-2">
                    <p class="text-[#594423] text-start">Postition:</p>
                    <p><?= $offer['position'] ?></p>
                </div>
                <div class="border border-[#594423] text-center rounded-lg shadow-md  py-2 px-2">
                    <p class="text-[#594423] text-start">Work Location:</p>
                    <p><?= $offer['work_location'] ?></p>
                </div>
                <div class="border border-[#594423] text-center rounded-lg shadow-md  py-2 px-2">
                    <p class="text-[#594423] text-start">Schedule:</p>
                    <p><?= $offer['schedule'] ?></p>
                </div>
                <div class="border border-[#594423] text-center rounded-lg shadow-md  py-2 px-2">
                    <p class="text-[#594423] text-start">Time in:</p>
                    <p><?= $offer['time_in'] ?></p>
                </div>
                <div class="border border-[#594423] text-center rounded-lg shadow-md  py-2 px-2">
                    <p class="text-[#594423] text-start">Time out:</p>
                    <p><?= $offer['time_out'] ?></p>
                </div>
                <div class="border border-[#594423] text-center rounded-lg shadow-md  py-2 px-2">
                    <p class="text-[#594423] text-start">Salary:</p>
                    <p><?= $offer['salary'] ?></p>
                </div>
                <div class="border border-[#594423] text-center rounded-lg shadow-md  py-2 px-2">
                    <p class="text-[#594423] text-start">Benefits:</p>
                    <p><?= $offer['benefits'] ?></p>
                </div>
                <div class="border border-[#594423] text-center rounded-lg shadow-md  py-2 px-2">
                    <p class="text-[#594423] text-start">Responsibilities:</p>
                    <p><?= $offer['responsibilities'] ?></p>
                </div>
                <div class="col-span-3 flex justify-end gap-5">
                    <form method="post" id="approveForm" class="hidden">
                        <input type="hidden" name="approve" value="true">
                        <input type="hidden" name="offer_id" value="<?= htmlspecialchars($offer['offer_id']) ?>">
                        <input type="hidden" name="applicant_id" value="<?= htmlspecialchars($offer['applicant_id']) ?>">
                    </form>
                    <button type="button" id="approveBtn" class="btn bg-green-500 text-white hover:bg-green-600">Approve</button>
                    <form method="post" id="rejectForm" class="hidden">
                        <input type="hidden" name="reject" value="true">
                        <input type="hidden" name="offer_id" value="<?= htmlspecialchars($offer['offer_id']) ?>">
                        <input type="hidden" name="applicant_id" value="<?= htmlspecialchars($offer['applicant_id']) ?>">
                        <input type="hidden" name="remarks" id="remarks">
                    </form>
                    <button type="button" id="rejectBtn" class="btn bg-red-500 text-white hover:bg-red-600">Reject</button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.querySelectorAll('.read-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            this.closest('.mark-as-read-form').submit();
        });
    });

    $(document).ready(function() {
        $('#approveBtn').on('click', () => {
            Swal.fire({
                icon: 'warning',
                title: 'Approve job offer',
                text: 'Are you sure you want to approve this Job offer?',
                showCancelButton: true,
                confirmButtonText: 'Yes, approve it!',
                cancelButtonText: 'No, cancel!',
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Approved!",
                        text: "Job offer Approved.",
                        icon: "success"
                    });
                    $('#approveForm').submit();
                }
            });
        });
        $('#rejectBtn').on('click', () => {
            Swal.fire({
                icon: 'warning',
                title: 'Reject Job ofer',
                text: 'Are you sure you want to reject this Job offer?',
                input: "text",
                showCancelButton: true,
                confirmButtonText: 'Yes, reject it!',
                cancelButtonText: 'No, cancel!',
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#remarks').val(result.value);
                    Swal.fire({
                        title: "Reject!",
                        text: "Job offer Rejected.",
                        icon: "success"
                    });
                    $('#rejectForm').submit();
                }
            });
        })
    });
</script>

<?php require 'partials/footer.php' ?>