<?php require '../../partials/admin/head.php' ?>

<div class="flex min-h-screen w-full text-[#594423]">
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <?php require '../../partials/admin/sidebar.php' ?>

    <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
        <?php require '../../partials/admin/navbar.php' ?>
        <?php if ($success ?? '' == true) : ?>
            <div role="alert" class="alert alert-success mt-10 mx-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Success! Interview successfully scheduled.</span>
                <a href="/admin/interview_schedules" class="btn">Go to interview schedules tab.</a>
            </div>
        <?php endif ?>
        <?php if ($error ?? '' == true) : ?>
            <div role="alert" class="alert alert-error mt-10 mx-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Error!Failed to finish task. Double check inputs</span>
            </div>
        <?php endif ?>
        <main class="px-2 py-10 flex flex-col justify-center">
            <div class="flex justify-end items-center mb-5 mr-10 text-blue-500 hover:text-blue-600 hover:underline">
                <box-icon name='left-arrow-alt'></box-icon>
                <a href="/admin/interview_schedules">Back to interview schedules</a>
            </div>
            <form method="post" class="w-full max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md text-center">
                <div>
                    <h1 class="text-xl font-semibold text-center">Interview schedule form</h1>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-10">
                    <div class="flex flex-col gap-2">
                        <label for="date">Date</label>
                        <input type="date" name="date" id="date" value="<?= htmlspecialchars($_POST['date'] ?? '')  ?>" class="input text-center" required>
                        <?php if (!empty($errors['date'])) : ?>
                            <p class="text-red-500 text-sm"><?= $errors['date'] ?></p>
                        <?php endif ?>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="time">Time</label>
                        <input type="time" name="time" id="time" value="<?= htmlspecialchars($_POST['time'] ?? '')  ?>" class="input text-center" required>
                        <?php if (!empty($errors['time'])) : ?>
                            <p class="text-red-500 text-sm"><?= $errors['time'] ?></p>
                        <?php endif ?>
                    </div>
                    <div class="flex flex-col gap-2 col-span-2">
                        <label for="location">Location</label>
                        <input type="text" name="location" id="location" value="<?= htmlspecialchars($_POST['location'] ?? '')  ?>" class="input w-full text-center" placeholder="interview location" required>
                        <?php if (!empty($errors['location'])) : ?>
                            <p class="text-red-500 text-sm"><?= $errors['location'] ?></p>
                        <?php endif ?>
                    </div>
                    <div class="flex flex-col gap-2 ">
                        <label for="mode">mode</label>
                        <select name="mode" id="mode" class="select text-center" required>
                            <option selected disabled>Choose an option:</option>
                            <option value="In-Person">In-Person</option>
                            <option value="Online">Online</option>
                        </select>
                        <?php if (!empty($errors['mode'])) : ?>
                            <p class="text-red-500 text-sm"><?= $errors['mode'] ?></p>
                        <?php endif ?>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="interview_type">interview type</label>
                        <select name="interview_type" id="interview_type" class="select text-center" required>
                            <option selected disabled>Choose an option:</option>
                            <option value="initial">initial</option>
                            <option value="final">final</option>
                        </select>
                        <?php if (!empty($errors['interview_type'])) : ?>
                            <p class="text-red-500 text-sm"><?= $errors['interview_type'] ?></p>
                        <?php endif ?>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="interview_status">interview status</label>
                        <select name="interview_status" id="interview_status" class="select text-center" required>
                            <option selected disabled>Choose an option:</option>
                            <option value="ongoing">ongoing</option>
                            <option value="done">done</option>
                        </select>
                        <?php if (!empty($errors['interview_status'])) : ?>
                            <p class="text-red-500 text-sm"><?= $errors['interview_status'] ?></p>
                        <?php endif ?>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="applicant_id">applicant_id</label>
                        <select name="applicant_id" id="applicant_id" class="select text-center" required>
                            <option selected disabled>Choose an option</option>
                            <?php foreach ($applicants as $applicant) : ?>
                                <option value="<?= htmlspecialchars($applicant['applicant_id']) ?>"><?= 'ID ' . htmlspecialchars($applicant['applicant_id']) . ': ' . $applicant['first_name'] . ' ' . htmlspecialchars($applicant['last_name']) ?></option>
                            <?php endforeach ?>
                        </select>
                        <?php if (!empty($errors['applicant_id'])) : ?>
                            <p class="text-red-500 text-sm"><?= $errors['applicant_id'] ?></p>
                        <?php endif ?>
                    </div>
                    <div>
                        <input type="hidden" value="<?= $_SESSION['user_id'] ?>" name="interviewer_id" id="interviewer_id">
                    </div>
                    <div class="col-span-2 flex flex-col gap-2 my-5">
                        <button type="button" id="create_interview" class="btn border border-[#594423] hover:bg-[#594423] tracking-wider hover:text-white transition duration-300">
                            create interview schedule
                        </button>
                    </div>
                    <div class="col-span-2 flex flex-col gap-2">
                        <p class="text-xs">Take note : only applicants who doesnt have an active interview schedule will be displayed on the applicant field.</p>
                    </div>
            </form>
        </main>
    </div>
</div>
</div>

<script>
    $(document).ready(function() {
        $('#create_interview').click(function() {
            let isValid = true
            $('input[required], select[required]').each(function() {
                if ($(this).val() == '') {
                    isValid = false;
                    $(this).addClass('border-red-500');
                    $(this).removeClass('border-[#594423]');
                } else {
                    $(this).removeClass('border-red-500');
                    $(this).addClass('border-[#594423]');
                }
            });
            if (isValid) {
                $('form').submit();
            } else {
                swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please fill out all required fields.',
                    timer: 1500,
                });
            }
        });
    })
</script>
<?php require '../../partials/admin/footer.php' ?>