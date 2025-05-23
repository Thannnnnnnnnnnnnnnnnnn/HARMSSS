<?php require 'partials/head.php' ?>
<?php require 'partials/navbar.php' ?>


<?php if ($accepted == true) : ?>
    <div role="alert" class="alert alert-success">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Job offer Accepted!</span>
    </div>
<?php elseif ($declined == true) : ?>
    <div role="alert" class="alert alert-error">
        <box-icon name='x-circle'></box-icon>
        <span>Job offer Declined!</span>
    </div>
<?php endif ?>
<main class="w-screen mx-auto  p-6 flex-grow flex justify-center">

    <div class="w-full max-w-7xl min-h-[300px] p-6 bg-white  border border-[#594423] rounded-lg shadow-sm">
        <div class="flex flex-col justify-between py-5 gap-10">
            <div class="flex justify-between">
                <h5 class="mb-2 text-3xl font-bold tracking-tight text-[#594423]">
                    <?= strtoupper($notif['type'] ?? '') ?>
                </h5>
                <?php if ($notif['status'] == 'read') : ?>
                    <p>READ</p>
                <?php else : ?>
                    <form method="POST" class="mark-as-read-form">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($notif['id']) ?>">
                        <label for="read-<?= htmlspecialchars($notif['id']) ?>" class="text-[#4E3B2A]">Mark as read</label>
                        <input type="checkbox" name="read" id="read-<?= htmlspecialchars($notif['id']) ?>" class="read-checkbox border border-black">
                    </form>
                <?php endif ?>
            </div>
            <?php if ($notif['type'] == 'job offer') : ?>
                <h3 class="mb-5 text-xl font-semibold tracking-tight text-[#594423] self-center">
                    <?= strtoupper($offer['position']) ?>
                </h3>
                <div class="bg-[#F7E6CA] py-4 px-2 rounded-xl font-normal">
                    <h3 class="text-lg text-[#594423] font-semibold py-4 ps-2">Work Schedule</h3>
                    <div class="grid grid-cols-3 text-[#594423] text-center">
                        <div>
                            <span class="font-semibold">Days:</span> <?= htmlspecialchars($offer['schedule']) ?>
                        </div>
                        <div>
                            <span class="font-semibold">Time in:</span> <?= htmlspecialchars($offer['time_in']) ?>
                        </div>
                        <div>
                            <span class="font-semibold">Time out:</span> <?= htmlspecialchars($offer['time_out']) ?>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 bg-[#F7E6CA] py-4 px-2 rounded-xl font-normal text-[#594423] text-center">
                    <div>
                        <span class="font-semibold">Work location:</span> <?= htmlspecialchars($offer['work_location']) ?>
                    </div>
                    <div>
                        <span class="font-semibold">Salary:</span> <?= htmlspecialchars($offer['salary']) ?>
                    </div>
                </div>
                <div>
                    <h3 class="font-semibold text-lg text-[#594423] ps-3 py-2">Benefits</h3>
                    <p class="py-4 px-2 rounded-xl bg-[#F7E6CA] font-normal text-[#594423] break-words">
                        <?= htmlspecialchars($offer['benefits']) ?>
                    </p>
                </div>
                <div>
                    <h3 class="font-semibold text-lg pb-2 text-[#594423] ps-3 py-2">Responsibilities</h3>
                    <p class="mb-5 py-4 px-2 rounded-xl bg-[#F7E6CA] font-normal text-[#594423] break-words">
                        <?= htmlspecialchars($offer['responsibilities']) ?>
                    </p>
                </div>

        </div>
        <div class="flex justify-end gap-4 <?= (htmlspecialchars($offer['user_decision']) == 'offer-sent') ? '' : 'hidden' ?>">
            <form method="POST" class="text-center" id="accept-form">
                <input type="hidden" name="id" value="<?= htmlspecialchars($offer['offer_id']) ?>">
                <input type="hidden" name="status_id" value="<?= htmlspecialchars($offer['status_id']) ?>">
                <input type="hidden" name="accepted" value="true">
                <button type="button" id="accept" class="btn bg-green-400 hover:bg-green-600 px-4 text-white font-semibold">Accept</button>
            </form>
            <button class="btn bg-red-400 hover:bg-red-600 px-4 text-white font-semibold" onclick="my_modal_3.showModal()">Decline</button>
            <dialog id="my_modal_3" class="modal">
                <div class="modal-box">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 hover:bg-gray-200">✕</button>
                    </form>
                    <h3 class="text-lg font-bold">Alert!</h3>
                    <p class="py-4">Are you sure you want to <span class="text-red-500 font-normal">Decline</span> this job offer ?</p>
                    <p class="text-sm text-gray-500 mb-5">changes cannot be made once accepted.</p>
                    <form method="POST" class="text-center">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($offer['offer_id']) ?>">
                        <input type="hidden" name="status_id" value="<?= htmlspecialchars($offer['status_id']) ?>">
                        <input type="hidden" name="declined" value="true">
                        <button type="submit" class="btn bg-red-400 hover:bg-red-600 px-4 text-white font-semibold">Yes, I'm sure</button>
                    </form>
                </div>
                <form method="dialog" class="modal-backdrop">
                    <button>close</button>
                </form>
            </dialog>
        </div>
    <?php else : ?>
        <div class="bg-[#FFF6E8] py-5 rounded-lg text-center text-md font-normal border">
            <?= htmlspecialchars($notif['message']) ?? '' ?>
        </div>
    <?php endif ?>
    </div>
    </div>
</main>

<script>
    document.querySelectorAll('.read-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            this.closest('.mark-as-read-form').submit();
        });
    });
    $('#accept').on('click', function() {
        const form = $('#accept-form');
        swal.fire({
            title: 'Alert!',
            text: 'Are you sure you want to accept this job offer?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, I\'m sure',
            cancelButtonText: 'No, cancel it!',
        }).then((result) => {
            if (result.isConfirmed) {
                swal.fire({
                    title: 'Accepted!',
                    text: 'Job offer accepted successfully!',
                    icon: 'success',
                    timer: 1800
                })
                form.submit();
            } else {
                swal.fire({
                    title: 'Cancelled',
                    text: 'Your job offer is safe :)',
                    icon: 'error',
                    timer: 1800
                })
            }
        })
    });
</script>

<?php require 'partials/footer.php' ?>