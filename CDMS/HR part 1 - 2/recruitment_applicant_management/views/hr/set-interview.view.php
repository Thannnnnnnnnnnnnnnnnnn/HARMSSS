<?php require 'partials/head.php' ?>
<?php require 'partials/hr/navbar.php' ?>
<div class="my-5">
    <a href="/hr/applicants-approved" class="text-blue-500 hover:underline px-4 flex items-center"><box-icon name='left-arrow-alt'></box-icon> back to applicants page</a>
</div>
<main class="max-w-6xl mx-auto p-6 flex-grow">
    <div class="overflow-x-auto shadow-lg rounded-lg bg-white">
        <table class="table text-center">
            <thead class="bg-[#594423] text-white">
                <tr>
                    <th>Applicant ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Resume</th>
                    <th>Posting ID</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th><?= htmlspecialchars($applicant['applicant_id']) ?></th>
                    <td><?= htmlspecialchars($applicant['first_name']) ?></td>
                    <td><?= htmlspecialchars($applicant['last_name']) ?></td>
                    <td><?= htmlspecialchars($applicant['contact_number']) ?></td>
                    <td><?= htmlspecialchars($applicant['email']) ?></td>
                    <td><a href="../<?= htmlspecialchars($applicant['resume']) ?>">view resume</a></td>
                    <td><?= htmlspecialchars($applicant['posting_id']) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="my-5 bg-white shadow-lg rounded-lg p-6">
        <form method="post" id="interview-form" class="py-2.5">
            <div class="text-center">
                <h3 class="text-xl font-bold py-3 px-4">Interview Schedule</h3>
            </div>
            <div class="grid grid-cols-2 ">
                <input type="hidden" name="applicant_id" value="<?= htmlspecialchars($applicant['applicant_id']) ?>" id="applicant_id" />
                <div class="col-span-1 flex flex-col justify-center items-center ">
                    <label for="date">Interview date</label>
                    <input type="date" name="date" placeholder="Type here" class="input text-center border border-[#594423] my-3" required />
                    <?php if ($errors['date'] ?? '') : ?>
                        <span><?= $errors['date'] ?></span>
                    <?php endif ?>
                </div>
                <div class="col-span-1 flex flex-col justify-center items-center">
                    <label for="time">Interview time</label>
                    <input type="time" name="time" placeholder="Type here" class="input text-center border border-[#594423] my-3" required />
                    <?php if ($errors['time'] ?? '') : ?>
                        <span><?= $errors['time'] ?></span>
                    <?php endif ?>
                </div>
                <div class="col-span-2 flex flex-col justify-center items-center mx-12">
                    <label for="location">Location</label>
                    <input type="text" name="location" placeholder="Type here" class="input text-center border border-[#594423] my-3 w-full" required />
                    <?php if ($errors['location'] ?? '') : ?>
                        <span><?= $errors['location'] ?></span>
                    <?php endif ?>
                </div>
                <div class="flex flex-col justify-center items-center">
                    <label for="mode">Mode</label>
                    <select class="select text-center border border-[#594423] my-3" required name="mode" id="mode">
                        <option>In-person</option>
                        <option>Online</option>
                    </select>
                    <?php if ($errors['mode'] ?? '') : ?>
                        <span><?= $errors['mode'] ?></span>
                    <?php endif ?>
                </div>
                <div class="flex flex-col justify-center items-center">
                    <label for="interview_type">Interview type</label>
                    <select class="select text-center border border-[#594423] my-3" required name="interview_type" id="interview_type">
                        <option>Initial</option>
                    </select>
                    <?php if ($errors['interview_type'] ?? '') : ?>
                        <span><?= $errors['interview_type'] ?></span>
                    <?php endif ?>
                </div>
            </div>
            <div class="col-span-3 flex justify-center ">
                <button type="button" class="btn shadow-lg hover:bg-[#594423] border border-[#594423] hover:text-white px-4 py-2 my-4" id="interview">Set interview</button>
            </div>
        </form>
    </div>
</main>
<script>
    $('#interview').on('click', function() {
        $('input[required]').each(function() {
            var isValid = true;
            if ($(this).val() === '') {
                isValid = false;
                $(this).addClass('border-red-500');
            } else {
                $(this).removeClass('border-red-500');
            }
            if (isValid) {
                swal.fire({
                    title: 'Interview Set!',
                    text: 'Interview set successfully!',
                    icon: 'success',
                });
                $('#interview-form').submit();
            } else {
                swal.fire({
                    title: 'Error!',
                    text: 'Please fill all the fields.',
                    icon: 'error',
                });
            }
        });
    });
</script>
<?php require 'partials/footer.php' ?>