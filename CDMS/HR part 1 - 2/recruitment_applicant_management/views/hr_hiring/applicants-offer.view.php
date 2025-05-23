<?php require 'partials/head.php' ?>
<?php require 'partials/hr_hiring/navbar.php' ?>

<main class="max-w-7xl mx-auto p-6 flex-grow">
    <?php require 'partials/hr_hiring/nav.php' ?>
    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table text-center">
            <thead class="bg-[#594423] text-white">
                <tr>
                    <th class="p-3">First Name</th>
                    <th class="p-3">Last Name</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">address</th>
                    <th class="p-3">Phone</th>
                    <th class="p-3">Resume</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Job Applying for</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text-gray-700 bg-white hover:bg-gray-100">
                    <td class="p-3"><?= htmlspecialchars($applicant['first_name'] ?? '') ?></td>
                    <td class="p-3"><?= htmlspecialchars($applicant['last_name'] ?? '') ?></td>
                    <td class="p-3"><?= htmlspecialchars($applicant['email'] ?? '') ?></td>
                    <td class="p-3"><?= htmlspecialchars($applicant['address'] ?? '') ?></td>
                    <td class="p-3"><?= htmlspecialchars($applicant['contact_number'] ?? '') ?></td>
                    <td class="p-3">
                        <?php if (!empty($applicant['resume'])) : ?>
                            <a href="../<?= htmlspecialchars($applicant['resume'] ?? '') ?>" class="text-blue-500 hover:underline" target="_blank">View Resume</a>
                        <?php else : ?>
                            <p class="text-lg text-red-500">No Resume</p>
                        <?php endif ?>
                    </td>
                    <td class="p-3 text-center">
                        <p class="rounded-lg text-sm">
                            <?= htmlspecialchars($applicant['status'] ?? '') ?>
                        </p>
                    </td>
                    <td class=" p-3 text-center"><?= htmlspecialchars($applicant['job_title'] ?? '') ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="container mx-auto max-w-3xl p-6 md:p-8 my-10 bg-white rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-[#594423] mb-6 text-center">Job Posting Details</h1>

        <form method="POST" class="space-y-6" id="offer">

            <div>
                <label for="position" class="block text-sm font-medium text-[#594423] mb-1">Position:</label>
                <input type="text" id="position" name="position" required placeholder="e.g., Senior Web Developer" value="<?= htmlspecialchars($offer['position'] ?? '') ?: $applicant['job_title'] ?>"
                    class="w-full px-4 py-2 border border-[#594423] rounded-md bg-white text-[#594423] focus:ring-2 focus:ring-[#594423]/50 focus:border-[#594423] outline-none placeholder:text-gray-400" readonly>
                <?php if ($errors['position'] ?? '') : ?>
                    <div class="mt-4 border border-red-800 text-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-[#F7E6CA]" role="alert">
                        <span class="font-medium">Danger alert!</span> <?= $errors['position'] ?>.
                    </div>
                <?php endif ?>
            </div>

            <div>
                <label for="work_location" class="block text-sm font-medium text-[#594423] mb-1">Work Location:</label>
                <input type="text" id="work_location" name="work_location" required placeholder="e.g., Remote or City, State" value="<?= htmlspecialchars($offer['work_location'] ?? '') ?: $applicant['location'] ?>"
                    class="w-full px-4 py-2 border border-[#594423] rounded-md bg-white text-[#594423] focus:ring-2 focus:ring-[#594423]/50 focus:border-[#594423] outline-none placeholder:text-gray-400">
                <?php if ($errors['work_location'] ?? '') : ?>
                    <div class="mt-4 border border-red-800 text-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-[#F7E6CA]" role="alert">
                        <span class="font-medium">Danger alert!</span> <?= $errors['work_location'] ?>.
                    </div>
                <?php endif ?>
            </div>

            <div>
                <label for="salary" class="block text-sm font-medium text-[#594423] mb-1">Salary Range:</label>
                <input type="number" id="salary" name="salary" placeholder="e.g., $80,000 - $100,000 per year (Optional)" required value="<?= htmlspecialchars($offer['salary'] ?? '')  ?: $applicant['salary'] ?>"
                    class="w-full px-4 py-2 border border-[#594423] rounded-md bg-white text-[#594423] focus:ring-2 focus:ring-[#594423]/50 focus:border-[#594423] outline-none placeholder:text-gray-400">
                <?php if ($errors['salary'] ?? '') : ?>
                    <div class="mt-4 border border-red-800 text-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-[#F7E6CA]" role="alert">
                        <span class="font-medium">Danger alert!</span> <?= $errors['salary'] ?>.
                    </div>
                <?php endif ?>
            </div>

            <div class="flex flex-col md:flex-row md:space-x-4 space-y-6 md:space-y-0">
                <div class="flex-1">
                    <label for="time_in" class="block text-sm font-medium text-[#594423] mb-1">Work Start Time:</label>
                    <input type="time" id="time_in" name="time_in" required value="<?= htmlspecialchars($offer['time_in'] ?? '') ?>"
                        class="w-full px-4 py-2 border border-[#594423] rounded-md bg-white text-[#594423] focus:ring-2 focus:ring-[#594423]/50 focus:border-[#594423] outline-none">
                    <?php if ($errors['time_in'] ?? '') : ?>
                        <div class="mt-4 border border-red-800 text-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-[#F7E6CA]" role="alert">
                            <span class="font-medium">Danger alert!</span> <?= $errors['time_in'] ?>.
                        </div>
                    <?php endif ?>
                </div>
                <div class="flex-1">
                    <label for="time_out" class="block text-sm font-medium text-[#594423] mb-1">Work End Time:</label>
                    <input type="time" id="time_out" name="time_out" required value="<?= htmlspecialchars($offer['time_out'] ?? '') ?>"
                        class="w-full px-4 py-2 border border-[#594423] rounded-md bg-white text-[#594423] focus:ring-2 focus:ring-[#594423]/50 focus:border-[#594423] outline-none">
                    <?php if ($errors['time_out'] ?? '') : ?>
                        <div class="mt-4 border border-red-800 text-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-[#F7E6CA]" role="alert">
                            <span class="font-medium">Danger alert!</span> <?= $errors['time_out'] ?>.
                        </div>
                    <?php endif ?>
                </div>
            </div>

            <div>
                <label for="schedule" class="block text-sm font-medium text-[#594423] mb-1">Work Schedule:</label>
                <select id="schedule" name="schedule" required
                    class="w-full px-4 py-2 border border-[#594423] rounded-md bg-white text-[#594423] focus:ring-2 focus:ring-[#594423]/50 focus:border-[#594423] outline-none appearance-none pr-8">
                    <option disabled selected>-- Select Schedule --</option>
                    <option value="mon-fri">Monday - Friday (Standard)</option>
                    <option value="mon-sat">Monday - Saturday</option>
                    <option value="tues-sun">Tuesday - Sunday</option>
                    <option value="wed-mon">Wednesday - Monday</option>
                    <option value="thurs-tues">Thursday - Tuesday</option>
                    <option value="fri-wed">Friday - Wednesday</option>
                    <option value="sat-thurs">Saturday - Thursday</option>
                    <option value="sun-fri">Sunday - Friday</option>
                    <option value="flexible">Flexible / Other</option>
                    <option value="rotating">Rotating Shift</option>
                </select>
                <?php if ($errors['schedule'] ?? '') : ?>
                    <div class="mt-4 border border-red-800 text-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-[#F7E6CA]" role="alert">
                        <span class="font-medium">Danger alert!</span> <?= $errors['schedule'] ?>.
                    </div>
                <?php endif ?>
            </div>

            <div>
                <label for="responsibilities" class="block text-sm font-medium text-[#594423] mb-1">Responsibilities:</label>
                <textarea id="responsibilities" name="responsibilities" rows="6" required placeholder="List key responsibilities..."
                    class="w-full px-4 py-2 border border-[#594423] rounded-md bg-white text-[#594423] focus:ring-2 focus:ring-[#594423]/50 focus:border-[#594423] outline-none placeholder:text-gray-400"><?= htmlspecialchars($offer['responsibilities'] ?? '')  ?: '' ?></textarea>
                <?php if ($errors['responsibilities'] ?? '') : ?>
                    <div class="mt-4 border border-red-800 text-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-[#F7E6CA]" role="alert">
                        <span class="font-medium">Danger alert!</span> <?= $errors['responsibilities'] ?>.
                    </div>
                <?php endif ?>
            </div>

            <div>
                <label for="benefits" class="block text-sm font-medium text-[#594423] mb-1">Benefits:</label>
                <textarea id="benefits" name="benefits" rows="4" placeholder="List company benefits (e.g., health insurance, paid time off)..." required
                    class="w-full px-4 py-2 border border-[#594423] rounded-md bg-white text-[#594423] focus:ring-2 focus:ring-[#594423]/50 focus:border-[#594423] outline-none placeholder:text-gray-400"><?= htmlspecialchars($offer['benefits'] ?? '')  ?: '' ?></textarea>
                <?php if ($errors['benefits'] ?? '') : ?>
                    <div class="mt-4 border border-red-800 text-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-[#F7E6CA]" id="benefits-error" role="alert">
                        <span class="font-medium">Danger alert!</span> <?= $errors['benefits'] ?>
                    </div>
                <?php endif ?>
            </div>

            <div class="flex justify-end space-x-4 pt-4">
                <button type="submit" name="submit" value="true" id="submit"
                    class="px-6 py-2 bg-[#594423] text-[#F7E6CA] font-semibold rounded-md shadow-sm hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-[#594423] focus:ring-offset-2 focus:ring-offset-[#F7E6CA] transition duration-150 ease-in-out">
                    Submit
                </button>
            </div>

        </form>
    </div>
</main>

<script>
    $(document).ready(function() {

        $('#submit').on('click', function(e) {
            var isValid = true;
            var $form = $('#offer');

            $form.find('input[required], textarea[required], select[required]').each(function() {
                var $field = $(this);

                if ($field.val() === null || $field.val().trim() === '') {
                    isValid = false;
                    $field.addClass('border-red-500');
                } else {
                    $field.removeClass('border-red-500');
                }
            });

            if (isValid) {
                Swal.fire({
                    title: 'Job offer ready!',
                    text: 'The job offer details are complete and will be sent.',
                    icon: 'success',
                    showConfirmButton: true,
                }).then((result) => {
                    if (result.isConfirmed || result.dismiss === Swal.DismissReason.timer) {
                        if ($form.find('input[name="submit"]').length === 0) {
                            $form.append('<input type="hidden" name="submit" value="true">');
                        } else {
                            $form.find('input[name="submit"]').val('true');
                        }
                        $form.submit();
                    }
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: 'Please fill all the required fields marked in red.',
                    icon: 'error'
                });
                $form.find('.border-red-500').first().focus();
            }
        });

    });
</script>

<?php require 'partials/footer.php' ?>