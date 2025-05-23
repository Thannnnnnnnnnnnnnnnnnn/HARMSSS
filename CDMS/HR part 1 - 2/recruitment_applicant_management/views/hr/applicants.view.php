<?php require 'partials/head.php' ?>
<?php require 'partials/hr/navbar.php' ?>

<?php if ($approved == true) : ?>
    <div role="alert" class="alert alert-success mx-20 mt-5">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Applicant successfully approved.</span>
        <a href="/hr/applicants-approved" class="btn">Go to Approved applicants tab.</a>
    </div>
<?php endif ?>
<?php if ($rejected == true) : ?>
    <div role="alert" class="alert alert-error mx-20 mt-5">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Application Rejected.</span>
        <!-- <a href="/hr/applicants-approved" class="btn">Go to Approved applicants tab.</a> -->
    </div>
<?php endif ?>

<main class="max-w-7xl mx-auto mt-6 p-6 flex-grow">
    <?php require 'partials/hr/nav.php' ?>
    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table max-w-7xl text-center">
            <thead class="bg-[#594423] text-white">
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>address</th>
                    <th>Phone</th>
                    <th>Resume</th>
                    <th>Status</th>
                    <th>Job Applying for</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applicants as $applicant): ?>
                    <tr class="text-gray-700 bg-white hover:bg-gray-100">
                        <td class="id"><?= htmlspecialchars($applicant['applicant_id']) ?></td>
                        <td><?= htmlspecialchars($applicant['first_name']) ?></td>
                        <td><?= htmlspecialchars($applicant['last_name']) ?></td>
                        <td><?= htmlspecialchars($applicant['email']) ?></td>
                        <td><?= htmlspecialchars($applicant['address']) ?></td>
                        <td><?= htmlspecialchars($applicant['contact_number']) ?></td>
                        <td>
                            <?php if (!empty($applicant['resume'])) : ?>
                                <a href="../<?= htmlspecialchars($applicant['resume']) ?>" class="text-blue-500 hover:underline" target="_blank">View Resume</a>
                            <?php else : ?>
                                <p class="text-lg text-red-500">No Resume</p>
                            <?php endif ?>
                        </td>
                        <td><?= htmlspecialchars($applicant['status']) ?></td>
                        <td><?= htmlspecialchars($applicant['job_title']) ?></td>
                        <td>
                            <div class="flex gap-2">
                                <button class="approve btn bg-green-400 px-3 hover:bg-green-500" onclick="my_modal_1.showModal()"><box-icon name='check-square'></box-icon></button>
                                <dialog id="my_modal_1" class="modal">
                                    <div class="modal-box bg-[#F7E6CA]">
                                        <h3 class="text-lg font-bold text-red-500">ALERT</h3>
                                        <p class="py-4">Please review all applicant details carefully before finalizing the approval process.</p>
                                        <form method="post">
                                            <input type="hidden" name="approve" value="'true">
                                            <input type="hidden" name="applicant_id" id="approve">
                                            <button type="submit" class="btn bg-green-400 hover:bg-green-500 px-2 text-white font-normal" id="approve">Approve Applicant</button>
                                        </form>
                                    </div>
                                    <form method="dialog" class="modal-backdrop">
                                        <button>close</button>
                                    </form>
                                </dialog>
                                <button class="reject btn bg-red-400 px-3 hover:bg-red-500 " onclick="my_modal_2.showModal()"><box-icon name='x'></box-icon></button>
                                <dialog id="my_modal_2" class="modal">
                                    <div class="modal-box">
                                        <h3 class="text-lg font-bold text-red-500">ALERT</h3>
                                        <p class="py-4">are you sure you want to reject this applicant ?.</p>
                                        <form method="post">
                                            <input type="hidden" name="reject" value="'true">
                                            <input type="hidden" name="applicant_id" id="r_reject">
                                            <button class="btn bg-red-400 hover:bg-red-500 px-2 text-white font-normal" type="submit">Reject Applicant</button>
                                        </form>
                                    </div>
                                    <form method="dialog" class="modal-backdrop">
                                        <button>close</button>
                                    </form>
                                </dialog>
                                <a href="/hr/applicant-view?id=<?= htmlspecialchars($applicant['applicant_id']) ?>" class="btn bg-blue-400 text-black hover:bg-blue-500"><i class="fa-solid fa-eye"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (count($applicants) < 1) : ?>
        <div role="alert" class="alert alert-error mx-20 mt-5">
            <box-icon name='x-circle'></box-icon>
            <span>No Data Found.</span>
        </div>
    <?php endif ?>

</main>
<script>
    document.querySelectorAll('.approve').forEach(button => {
        button.addEventListener('click', (event) => {
            const row = event.target.closest('tr');
            const applicant_id = row.querySelector('.id').textContent;
            const approve_input = document.getElementById('approve');
            approve_input.value = applicant_id;
        });
    });

    document.querySelectorAll('.reject').forEach(button => {
        button.addEventListener('click', (event) => {
            const row = event.target.closest('tr');
            const applicant_id = row.querySelector('.id').textContent;
            const reject = document.getElementById('r_reject');
            reject.value = applicant_id;
        });
    });
    $('#approve').on('click', function() {
        swal.fire({
            title: 'Applicant Approved',
            text: 'The applicant has been approved successfully.',
            icon: 'success',
        });
    });
</script>

<?php require 'partials/footer.php' ?>