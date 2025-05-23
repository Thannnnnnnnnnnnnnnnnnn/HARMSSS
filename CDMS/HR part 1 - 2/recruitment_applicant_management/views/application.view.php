<?php require 'partials/head.php' ?>
<?php require 'partials/navbar.php' ?>
<?php if ($updated == true) : ?>
    <div class="flex justify-center">
        <div id="alert-3" class="flex items-center p-4 mt-4 text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>
            <span class="sr-only">Info</span>
            <div class="ms-3 text-sm font-medium">
                Record Successfully Updated !
            </div>
            <button type="button" class="ms-4 -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-3" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                </svg>
            </button>
        </div>
    </div>
<?php endif ?>
<main class="max-w-7xl mx-auto p-8 flex-grow">
    <?php if (count($h_applications) >= 1) : ?>
        <div>
            <button id="view-history" class="hover:bg-[#594423] hover:text-white transition py-2 px-4 text-black cursor-pointer rounded-xl mb-5">View application history</button>
        </div>
    <?php endif ?>
    <div class="flex flex-col justify-center items-center ">
        <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
            <table class="table table-xs text-center">
                <thead class="bg-[#594423] text-white">
                    <tr>
                        <th class="px-2 py-2">Applicant ID</th>
                        <th class="px-4 py-2">First Name</th>
                        <th class="px-2 py-2">Last Name</th>
                        <th class="px-4 py-2">Contact Number</th>
                        <th class="px-2 py-2">Age</th>
                        <th class="px-2 py-2">Date of Birth</th>
                        <th class="px-4 py-2">Address</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-2 py-2">Status</th>
                        <th class="px-4 py-2">Resume</th>
                        <th class="px-4 py-2">Job applying for</th>
                        <th class="px-2 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $application) : ?>
                        <?php if ($application['status'] != 'declined') : ?>
                            <tr class="text-center hover:bg-gray-100">
                                <td class="px-2 py-2"><?= htmlspecialchars($application['applicant_id']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($application['first_name']) ?></td>
                                <td class="px-2 py-2"><?= htmlspecialchars($application['last_name']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($application['contact_number']) ?></td>
                                <td class="px-2 py-2"><?= htmlspecialchars($application['age']) ?></td>
                                <td class="px-2 py-2"><?= htmlspecialchars($application['date_of_birth']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($application['address']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($application['email']) ?></td>
                                <td class="px-2 py-2">
                                    <p class="px-3 py-1 rounded-lg 
                            <?= htmlspecialchars($application['status']) == 'hired' ? 'text-green-500' : (htmlspecialchars($application['status']) == 'rejected' ? 'text-red-500' : 'text-black') ?>">
                                        <?= htmlspecialchars($application['status']) ?>
                                    </p>
                                </td>
                                <td class="px-4 py-2">
                                    <?php if (!empty($application['resume'])) : ?>
                                        <a href="<?= htmlspecialchars($application['resume']) ?>" class="text-blue-500 hover:underline" target="_blank">View Resume</a>
                                    <?php else : ?>
                                        <p class="text-lg text-red-500">No Resume</p>
                                    <?php endif ?>
                                </td>
                                <td class="px-4 py-2"><?= htmlspecialchars($application['job_title']) ?></td>
                                <td class="px-2 py-2">
                                    <button type="button" id="updateBtnShow" class="block font-medium border border-[#594423] text-sm  text-center hover:bg-[#594423] hover:text-white transition py-2 px-4 text-black cursor-pointer rounded-xl">
                                        Update
                                    </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endif ?>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <div id="history-table" class="flex flex-col">
            <h3 class="mt-5 text-white bg-[#594423] border rounded-lg border-gray-200 py-2 px-6">Application history</h3>
            <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
                <table class="table text-center">
                    <!-- head -->
                    <thead class="bg-[#594423] text-white">
                        <tr>
                            <th class="px-2 py-2">Applicant ID</th>
                            <th class="px-4 py-2">First Name</th>
                            <th class="px-2 py-2">Last Name</th>
                            <th class="px-4 py-2">Contact Number</th>
                            <th class="px-2 py-2">Age</th>
                            <th class="px-2 py-2">Date of Birth</th>
                            <th class="px-4 py-2">Address</th>
                            <th class="px-4 py-2">Email</th>
                            <th class="px-2 py-2">Status</th>
                            <th class="px-4 py-2">Resume</th>
                            <th class="px-4 py-2">Job applying for</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($h_applications as $h_application) : ?>
                            <tr class=" text-center hover:bg-gray-100">
                                <td class="px-2 py-2"><?= htmlspecialchars($h_application['applicant_id']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($h_application['first_name']) ?></td>
                                <td class="px-2 py-2"><?= htmlspecialchars($h_application['last_name']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($h_application['contact_number']) ?></td>
                                <td class="px-2 py-2"><?= htmlspecialchars($h_application['age']) ?></td>
                                <td class="px-2 py-2"><?= htmlspecialchars($h_application['date_of_birth']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($h_application['address']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($h_application['email']) ?></td>
                                <td class="px-2 py-2">
                                    <p class="px-3 py-1 rounded-lg 
                            <?= htmlspecialchars($h_application['status']) == 'hired' ? 'text-green-500' : (htmlspecialchars($h_application['status']) == 'rejected' ? 'text-red-500' : 'text-black') ?>">
                                        <?= htmlspecialchars($h_application['status']) ?>
                                    </p>
                                </td>
                                <td class="px-4 py-2">
                                    <?php if (!empty($h_application['resume'])) : ?>
                                        <a href="<?= htmlspecialchars($h_application['resume']) ?>" class="text-blue-500 hover:underline" target="_blank">View Resume</a>
                                    <?php else : ?>
                                        <p class="text-lg text-red-500">No Resume</p>
                                    <?php endif ?>
                                </td>
                                <td class="px-4 py-2"><?= htmlspecialchars($h_application['job_title']) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="<?= $rejectApplication == true ? '' : 'hidden' ?> text-red-500 text-center pt-10 text-lg">
            <p>You have an unfinished application!</p>
            <p>application forbidden if there's an unfinished application</p>
        </div>
        <div class="<?= $hiredApplication == true ? '' : 'hidden' ?> text-green-500 text-center pt-10 text-lg">
            <p>You're already Hired !</p>
        </div>
        <div class="<?= count($applications) < 1 ? '' : 'hidden' ?> text-red-500 text-center pt-10 text-lg">
            <p>No Applications data found.</p>
        </div>
        <div class="<?= count($applications) < 1 ? 'hidden' : '' ?>">
            <div class="w-7xl">
                <h2 class="text-[#594423] text-xl font-semibold p-4 mt-5">Documents</h2>
                <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
                    <table class="table text-center">
                        <thead class="bg-[#594423] text-white">
                            <tr>
                                <th class="px-4 py-2">Philhealth</th>
                                <th class="px-4 py-2">SSS</th>
                                <th class="px-4 py-2">Pag-ibig</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-center hover:bg-gray-100">
                                <td class="px-4 py-2">
                                    <?php if (!empty($documents['philhealth'])) : ?>
                                        <a href="../<?= htmlspecialchars($documents['philhealth']) ?>" class="text-blue-500 hover:underline" target="_blank">View file</a>
                                    <?php else : ?>
                                        <p class="text-red-500">No Philhealth</p>
                                    <?php endif ?>
                                </td>
                                <?php if (!empty($documents['sss'])) : ?>
                                    <td class="px-4 py-2">
                                        <?php if (!empty($documents['sss'])) : ?>
                                            <a href="../<?= htmlspecialchars($documents['sss']) ?>" class="text-blue-500 hover:underline" target="_blank">View file</a>
                                        <?php else : ?>
                                            <p class="text-red-500">No SSS</p>
                                        <?php endif ?>
                                    </td>
                                <?php endif ?>
                                <td class="px-4 py-2">
                                    <?php if (!empty($documents['pagibig'])) : ?>
                                        <a href="../<?= htmlspecialchars($documents['pagibig']) ?>" class="text-blue-500 hover:underline" target="_blank">View file</a>
                                    <?php else : ?>
                                        <p class="text-red-500">No Pag-ibig</p>
                                    <?php endif ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="pb-5">
                    <?php if ($interview >= 1) : ?>
                        <h2 class="text-[#594423] text-xl font-semibold p-4 mt-5">Interview Schedule</h2>
                        <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
                            <table class="table text-center">
                                <thead class="bg-[#594423] text-white">
                                    <tr>
                                        <th class="px-4 py-2">Applicant ID</th>
                                        <th class="px-4 py-2">Job applying for</th>
                                        <th class="px-4 py-2">interview date</th>
                                        <th class="px-4 py-2">interview type</th>
                                        <th class="px-4 py-2">Location</th>
                                        <th class="px-4 py-2">Interviewer</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($applications as $application) : ?>
                                        <?php if ($application['status'] == 'initial-interview'): ?>
                                            <tr class="border-b border-gray-300 text-center hover:bg-gray-100">
                                                <td class="px-4 py-2"><?= htmlspecialchars($interview['applicant_id']) ?? '' ?></td>
                                                <td class="px-4 py-2"><?= htmlspecialchars($interview['job_title']) ?? '' ?></td>
                                                <td class="px-4 py-2"><?= htmlspecialchars($interview['date']) ?? '' ?></td>
                                                <td class="px-4 py-2"><?= htmlspecialchars($interview['mode']) ?? '' ?></td>
                                                <td class="px-4 py-2"><?= htmlspecialchars($interview['location']) ?? '' ?></td>
                                                <td class="px-4 py-2"><?= htmlspecialchars($interview['username']) ?? '' ?></td>
                                            </tr>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </div>
        <div id="updateForm" class="bg-white rounded-lg shadow-xl hidden">
            <h1 class="py-3 px-2.5 bg-[#594423] mx-3 my-3 text-white font-semibold rounded-lg shadow-md">Update Information</h1>
            <form class="p-4 md:p-5" method="POST" enctype="multipart/form-data" id="updateFormSubmit">
                <input type="hidden" name="update" value="1">
                <input type="hidden" name="applicant_id" value="<?= htmlspecialchars($application['applicant_id']) ?>">
                <div class="grid gap-4 mb-4 grid-cols-2">
                    <div class="col-span-2 sm:col-span-1">
                        <label for="first_name" class="block mb-2 text-md font-medium text-[#594423] dark:text-[#594423]">First name</label>
                        <input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($application['first_name']) ?>" class="bg-gray-50 border border-gray-300 text-[#594423] text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" required>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="last_name" class="block mb-2 text-md font-medium text-[#594423] dark:text-[#594423]">Last name</label>
                        <input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($application['last_name']) ?>" class="bg-gray-50 border border-gray-300 text-[#594423] text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 " required>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="contact_number" class="block mb-2 text-md font-medium text-[#594423] dark:text-[#594423]">Contact Number</label>
                        <input type="text" name="contact_number" id="contact_number" value="<?= htmlspecialchars($application['contact_number']) ?>" class="bg-gray-50 border border-gray-300 text-[#594423] text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 " required>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="address" class="block mb-2 text-md font-medium text-[#594423] dark:text-[#594423]">Address</label>
                        <input type="text" name="address" id="address" value="<?= htmlspecialchars($application['address']) ?>" class="bg-gray-50 border border-gray-300 text-[#594423] text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 " required>
                    </div>
                    <div class="col-span-2 sm:col-span-2">
                        <label for="email" class="block mb-2 text-md font-medium text-[#594423] dark:text-[#594423]">Email</label>
                        <input type="text" name="email" id="email" value="<?= htmlspecialchars($application['email']) ?>" class="bg-gray-50 border border-gray-300 text-[#594423] text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 " readonly>
                    </div>
                    <div class="col-span-1">
                        <label for="resume" class="block mb-2 text-md font-medium text-[#594423]">Resume</label>
                        <input type="file" name="resume" id="resume" class="bg-gray-50 border border-gray-300 text-[#594423] text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 ">
                        <input type="hidden" name="old_resume" value="<?= htmlspecialchars($application['resume']) ?>">
                        <?php if (!empty($application['resume'])): ?>
                            <p class="mt-2 text-sm dark:text-[#594423]">
                                Current File:
                                <a href="<?= htmlspecialchars($application['resume']) ?>" target="_blank" class="text-blue-500 underline">
                                    View Resume
                                </a>
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="col-span-1">
                        <label for="philhealth" class="block mb-2 text-md font-medium text-[#594423]">Philhealth</label>
                        <input type="file" name="philhealth" id="philhealth" class="bg-gray-50 border border-gray-300 text-[#594423] text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 ">
                        <input type="hidden" name="old_philhealth" value="<?= htmlspecialchars($documents['philhealth']) ?>">
                        <?php if (!empty($documents['philhealth'])): ?>
                            <p class="mt-2 text-sm dark:text-[#594423]">
                                Current File:
                                <a href="<?= htmlspecialchars($documents['philhealth']) ?>" target="_blank" class="text-blue-500 underline">
                                    View philhealth
                                </a>
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="col-span-1">
                        <label for="sss" class="block mb-2 text-md font-medium text-[#594423]">SSS</label>
                        <input type="file" name="sss" id="sss" class="bg-gray-50 border border-gray-300 text-[#594423] text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 ">
                        <input type="hidden" name="old_sss" value="<?= htmlspecialchars($documents['sss']) ?>">
                        <?php if (!empty($documents['sss'])): ?>
                            <p class="mt-2 text-sm  dark:text-[#594423]">
                                Current File:
                                <a href="<?= htmlspecialchars($documents['sss']) ?>" target="_blank" class="text-blue-500 underline">
                                    View SSS
                                </a>
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="col-span-1">
                        <label for="pagibig" class="block mb-2 text-md font-medium text-[#594423]">Pag-ibig</label>
                        <input type="file" name="pagibig" id="pagibig" class="bg-gray-50 border border-gray-300 text-[#594423] text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 ">
                        <input type="hidden" name="old_pagibig" value="<?= htmlspecialchars($documents['pagibig']) ?>">
                        <?php if (!empty($documents['pagibig'])): ?>
                            <p class="mt-2 text-sm dark:text-[#594423]">
                                Current File:
                                <a href="<?= htmlspecialchars($documents['pagibig']) ?>" target="_blank" class="text-blue-500 underline">
                                    View Pagibig
                                </a>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="text-center">
                    <button type="button" id="updateBtn" class="border border-[#594423] inline-flex items-centerfocus:ring-4 font-medium text-sm hover:bg-[#594423] hover:text-white transition py-2 px-4 text-black cursor-pointer rounded-xl shadow-xl">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    $(document).ready(function() {
        $('#updateBtnShow').on('click', () => {
            if ($('#updateForm').hasClass('hidden')) {
                $('#updateForm').removeClass('hidden');
                $('#updateForm').addClass('block');
                $('#view-history').addClass('hidden');
            } else {
                $('#updateForm').removeClass('block');
                $('#updateForm').addClass('hidden');
                $('#view-history').removeClass('hidden');
            }
        })
    });
    document.addEventListener('DOMContentLoaded', () => {
        const view = document.getElementById('view-history');
        const table = document.getElementById('history-table');
        table.style.display = 'none';
        view.addEventListener('click', () => {
            if (table.style.display === "none") {
                table.style.display = "table";
                view.textContent = "Hide application history";
            } else {
                table.style.display = "none";
                view.textContent = "View application history";
            }
        })
    });
    $('#updateBtn').on('click', function() {
        var isValid = true;
        $('input[required').each(function() {
            if ($(this).val() === '') {
                isValid = false;
                $(this).addClass('border-red-500');
            } else {
                $(this).removeClass('border-red-500');
            }
        });
        if (isValid) {
            swal.fire({
                title: 'Application Updated!',
                text: 'Your application has been updated successfully.',
                icon: 'success',
                confirmButtonText: 'OK',
            });
            $('#updateFormSubmit').submit();
        } else {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Please fill in all required fields.",
            });
        }
    });
</script>

<?php require 'partials/footer.php' ?>