<?php require '../partials/admin/head.php' ?>

<div class="flex min-h-screen w-full">
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <?php require '../partials/admin/sidebar.php' ?>

    <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
        <?php require '../partials/admin/navbar.php' ?>
        <main class="px-1 py-8">
            <div class="text-end pe-10 mb-5">
                <a href="/admin/job-offers" class="my-4 link link-hover text-blue-500"><i class="fa-solid fa-arrow-left"></i>Back to job-offers</a>
            </div>
            <div>
                <h2>Job offer</h2>
            </div>
            <div class="overflow-x-auto shadow-lg rounded-lg bg-base-100">
                <table class="table text-center">
                    <thead class="bg-[#594423] text-white">
                        <tr>
                            <th>Offer ID</th>
                            <th>Position</th>
                            <th>Applicant ID</th>
                            <th>Work location</th>
                            <th>Schedule</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Salary</th>
                            <th>User Decision</th>
                            <th>Application Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th class="border-r"><?= htmlspecialchars($offer['offer_id']) ?></th>
                            <td class="border-r"><?= htmlspecialchars($offer['position']) ?></td>
                            <td class="border-r"><?= htmlspecialchars($offer['applicant_id']) ?></td>
                            <td class="border-r"><?= htmlspecialchars($offer['work_location']) ?></td>
                            <td class="border-r"><?= htmlspecialchars($offer['schedule']) ?></td>
                            <td class="border-r"><?= htmlspecialchars($offer['time_in']) ?></td>
                            <td class="border-r"><?= htmlspecialchars($offer['time_out']) ?></td>
                            <td class="border-r"><?= htmlspecialchars($offer['salary']) ?></td>
                            <td class="border-r"><?= htmlspecialchars($offer['user_decision']) ?></td>
                            <td class="border-r"><?= htmlspecialchars($applicant['status']) ?></td>
                            <td>
                                <form method="post" id="hire-form">
                                    <input type="hidden" name="hire" value="true">
                                    <input type="hidden" name="offer_id" value="<?= htmlspecialchars($offer['offer_id']) ?>">
                                    <button type="button" id="hire" class="btn bg-green-500 text-white rounded-lg" <?= $offer['user_decision'] === 'offer-sent' ? 'disabled' : ($offer['user_decision'] === 'declined' ? 'disabled' : '') ?>>Hire</button>
                                </form>
                                <form method="post" id="delete-form">
                                    <input type="hidden" name="delete" value="true">
                                    <input type="hidden" name="offer_id" value="<?= htmlspecialchars($offer['offer_id']) ?>">
                                    <button type="button" class="btn bg-red-500 text-white rounded-lg" id="delete">DELETE</button>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="overflow-x-auto rounded-box shadow-lg bg-base-100 my-5">
                <table class="table text-center ">
                    <thead class=" bg-[#594423] text-white">
                        <tr>
                            <th>Benefits</th>
                            <th>Responsibilities</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border-r"><?= htmlspecialchars($offer['benefits']) ?></td>
                            <td class="border-r"><?= htmlspecialchars($offer['responsibilities']) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-white shadow-lg rounded-lg mt-5 p-4">
                <h2 class="text-xl font-semibold mb-4 text-white bg-[#594423] ps-5 py-4 rounded-lg">Job Offer Details</h2>

                <table class="w-full border-collapse mt-5">
                    <tbody class="[&>tr>td]:py-2 [&>tr>td]:px-2 [&>tr>td]:align-top">

                        <tr>
                            <td>
                                <span class="block mb-1 text-sm font-medium text-gray-600">Position</span>
                                <p class="text-gray-900"><?= htmlspecialchars($offer['position']) ?></p>
                            </td>
                            <td>
                                <span class="block mb-1 text-sm font-medium text-gray-600">Work Location</span>
                                <p class="text-gray-900"><?= htmlspecialchars($offer['work_location']) ?></p>
                            </td>
                            <td>
                                <span class="block mb-1 text-sm font-medium text-gray-600">Schedule</span>
                                <p class="text-gray-900 capitalize"><?= htmlspecialchars($offer['schedule']) ?></p>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <span class="block mb-1 text-sm font-medium text-gray-600">Time In</span>
                                <p class="text-gray-900"><?= htmlspecialchars(date("g:i A", strtotime($offer['time_in'] ?? ''))) ?></p>
                            </td>
                            <td>
                                <span class="block mb-1 text-sm font-medium text-gray-600">Time Out</span>
                                <p class="text-gray-900"><?= htmlspecialchars(date("g:i A", strtotime($offer['time_out'] ?? ''))) ?></p>
                            </td>
                            <td>
                                <span class="block mb-1 text-sm font-medium text-gray-600">Salary</span>
                                <p class="text-gray-900"><?= htmlspecialchars(number_format($offer['salary'] ?? 0, 2)) ?></p>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="3" class="text-xl font-semibold text-white bg-[#594423] ps-5 py-3 rounded-lg my-3 mt-4">
                                Applicant Information
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <span class="block mb-1 text-sm font-medium text-gray-600">Applicant ID</span>
                                <p class="text-gray-900"><?= htmlspecialchars($applicant['applicant_id']) ?></p>
                            </td>
                            <td>
                                <span class="block mb-1 text-sm font-medium text-gray-600">First Name</span>
                                <p class="text-gray-900"><?= htmlspecialchars($applicant['first_name']) ?></p>
                            </td>
                            <td>
                                <span class="block mb-1 text-sm font-medium text-gray-600">Last Name</span>
                                <p class="text-gray-900"><?= htmlspecialchars($applicant['last_name']) ?></p>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <span class="block mb-1 text-sm font-medium text-gray-600">Contact Number</span>
                                <p class="text-gray-900"><?= htmlspecialchars($applicant['contact_number']) ?></p>
                            </td>
                            <td>
                                <span class="block mb-1 text-sm font-medium text-gray-600">Age</span>
                                <p class="text-gray-900"><?= htmlspecialchars($applicant['age']) ?></p>
                            </td>
                            <td>
                                <span class="block mb-1 text-sm font-medium text-gray-600">Date of Birth</span>
                                <p class="text-gray-900"><?= htmlspecialchars(date("F j, Y", strtotime($applicant['date_of_birth']))) ?></p>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <span class="block mb-1 text-sm font-medium text-gray-600">Address</span>
                                <p class="text-gray-900"><?= htmlspecialchars($applicant['address']) ?></p>
                            </td>
                            <td>
                                <span class="block mb-1 text-sm font-medium text-gray-600">Email</span>
                                <p class="text-gray-900"><?= htmlspecialchars($applicant['email']) ?></p>
                            </td>
                            <td>
                                <span class="block mb-1 text-sm font-medium text-gray-600">Resume</span>
                                <a href="../<?= htmlspecialchars($applicant['resume']) ?>" target="_blank" class="text-blue-500 hover:underline block py-1">View File</a>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="3" class="text-xl font-semibold text-white bg-[#594423] ps-5 py-3 rounded-lg my-3 mt-4">
                                Documents
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <span class="block mb-1 text-sm font-medium text-gray-600">Philhealth</span>
                                <a href="../<?= htmlspecialchars($applicant['philhealth']) ?>" target="_blank" class="text-blue-500 hover:underline block py-1">View File</a>
                            </td>
                            <td>
                                <span class="block mb-1 text-sm font-medium text-gray-600">SSS</span>
                                <a href="../<?= htmlspecialchars($applicant['sss']) ?>" target="_blank" class="text-blue-500 hover:underline block py-1">View File</a>
                            </td>
                            <td>
                                <span class="block mb-1 text-sm font-medium text-gray-600">Pag-ibig</span>
                                <a href="../<?= htmlspecialchars($applicant['pagibig']) ?>" target="_blank" class="text-blue-500 hover:underline block py-1">View File</a>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="3" class="text-xl font-semibold text-white bg-[#594423] ps-5 py-3 rounded-lg my-3 mt-4">
                                Applied Job Information
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <span class="block mb-1 text-sm font-medium text-gray-600">Job Title</span>
                                <p class="text-gray-900"><?= htmlspecialchars($applicant['job_title']) ?></p>
                            </td>
                            <td>
                                <span class="block mb-1 text-sm font-medium text-gray-600">Location</span>
                                <p class="text-gray-900"><?= htmlspecialchars($applicant['location']) ?></p>
                            </td>
                            <td>
                                <span class="block mb-1 text-sm font-medium text-gray-600">Employment Type</span>
                                <p class="text-gray-900 capitalize"><?= htmlspecialchars($applicant['employment_type']) ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>
<script>
    $('#hire').on('click', function() {
        swal.fire({
            title: 'HIRE',
            text: 'Are you sure you want to hire this applicant?',
            icon: 'info',
            confirmButtonText: "Yes I'm sure",
            showCancelButton: true,
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire(
                    'Hired!',
                    'Your data has been updated.',
                    'success'
                )
                document.getElementById('hire-form').submit();
            };
        });
    });
    $('#delete').on('click', function() {
        Swal.fire({
            title: 'DELETE',
            text: 'Are you sure you want to delete this job offer?',
            icon: 'error',
            confirmButtonText: "Yes I'm sure",
            showCancelButton: true,
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire(
                    'Deleted!',
                    'Your data has been deleted.',
                    'success'
                )
                document.getElementById('delete-form').submit();
            };
        });
    });
</script>

<?php require '../partials/admin/footer.php' ?>