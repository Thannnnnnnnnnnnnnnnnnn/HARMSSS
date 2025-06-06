<?php require '../../partials/admin/head.php' ?>

<div class="flex min-h-screen w-full text-black">
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <?php require '../../partials/admin/sidebar.php' ?>

    <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
        <?php require '../../partials/admin/navbar.php' ?>
        <main class="px-2 py-3">
            <?php if (isset($error)) : ?>
                <div role="alert" class="alert alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-normal"><?= $error ?></span>
                </div>
            <?php endif ?>
            <div>
                <div class="my-3 mx-5">
                    <h1 class="font-semibold text-lg">New hired applicants</h1>
                </div>
                <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
                    <table class="table text-center">
                        <thead class="bg-[#594423] text-white">
                            <tr class="text-center">
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Age</th>
                                <th>Date of Birth</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($newhires as $newhire): ?>
                                <tr>
                                    <td><?= $newhire['applicant_id'] ?></td>
                                    <td><?= $newhire['first_name'] ?></td>
                                    <td><?= $newhire['last_name'] ?></td>
                                    <td><?= $newhire['age'] ?></td>
                                    <td><?= $newhire['date_of_birth'] ?></td>
                                    <td><?= $newhire['contact_number'] ?></td>
                                    <td><?= $newhire['email'] ?></td>
                                    <td><?= $newhire['status'] ?></td>
                                    <td>
                                        <a href="applicant.php?id=<?= htmlspecialchars($newhire['applicant_id']) ?>" class="btn border border-black"><i class="fa-solid fa-eye"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <h1 class="my-5 mx-5 text-lg font-semibold">Applicants</h1>
            <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
                <table class="table table-sm">
                    <thead class="bg-[#594423] text-white">
                        <tr class="text-center">
                            <th>ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Age</th>
                            <th>Date of Birth</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Application Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applicants as $applicant) : ?>
                            <tr class="text-center">
                                <td class="applicant_id border-t"><?= htmlspecialchars($applicant['applicant_id']) ?></td>
                                <td class="first_name border-t"><?= htmlspecialchars($applicant['first_name']) ?></td>
                                <td class="last_name border-t"><?= htmlspecialchars($applicant['last_name']) ?></td>
                                <td class="age border-t"><?= htmlspecialchars($applicant['age']) ?></td>
                                <td class="date_of_birth border-t"><?= htmlspecialchars($applicant['date_of_birth']) ?></td>
                                <td class="contact_number border-t"><?= htmlspecialchars($applicant['contact_number']) ?></td>
                                <td class="email border-t"><?= htmlspecialchars($applicant['email']) ?></td>
                                <td class="created_at border-t"><?= htmlspecialchars($applicant['created_at']) ?></td>
                                <td class="border-t">
                                    <?php if ($applicant['status'] === 'applied') : ?>
                                        <div class="flex justify-center gap-2">
                                            <form method="POST" class="approveForm">
                                                <input type="hidden" name="approve" value="true">
                                                <input type="hidden" name="applicant_id" value="<?= $applicant['applicant_id'] ?>">
                                                <button type="button" title="Approve" class="btn bg-green-500 text-xl rounded-lg approveBtn" data-applicant-id="<?= $applicant['applicant_id'] ?>"><i class="fa-solid fa-user-check"></i></button>
                                            </form>
                                            <form method="POST" class="rejectForm">
                                                <input type="hidden" name="reject" value="true">
                                                <input type="hidden" name="applicant_id" value="<?= $applicant['applicant_id'] ?>">
                                                <button type="button" title="Reject" class="btn bg-red-500 text-xl rounded-lg rejectBtn" data-applicant-id="<?= $applicant['applicant_id'] ?>"><i class="fa-solid fa-user-xmark"></i></button>
                                            </form>
                                        </div>
                                    <?php elseif ($applicant['status'] == 'final interview passed') : ?>
                                        <div class="flex justify-center gap-2">
                                            <form method="POST" class="hireForm">
                                                <input type="hidden" name="hire" value="true">
                                                <input type="hidden" name="applicant_id" value="<?= $applicant['applicant_id'] ?>">
                                                <button type="button" title="Hire" class="btn text-xl bg-green-500 text-white rounded-xl hireBtn" data-applicant-id="<?= $applicant['applicant_id'] ?>"><i class="fa-solid fa-handshake"></i></button>
                                            </form>
                                        </div>
                                    <?php else : ?>
                                        <div>
                                            <p class="text-gray-500">application under process</p>
                                        </div>
                                    <?php endif ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<script>
    $('.approveBtn').on('click', function() {
        swal.fire({
            title: 'Are you sure?',
            text: "You are about to approve this applicant!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, approve it!'
        }).then((result) => {
            if (result.isConfirmed) {
                swal.fire(
                    'Approved!',
                    'The applicant has been approved.',
                    'success'
                );
                $('.approveForm').submit();
            }
        });
    });
    $('.rejectBtn').on('click', function() {
        swal.fire({
            title: 'Are you sure?',
            text: "You are about to reject this applicant!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, reject it!'
        }).then((result) => {
            if (result.isConfirmed) {
                swal.fire(
                    'Rejected!',
                    'The applicant has been rejected.',
                    'success'
                );
                $('.rejectForm').submit();
            }
        });
    });
    $('.hireBtn').on('click', function() {
        const clickedButton = $(this);
        const parentForm = clickedButton.closest('.hireForm');

        swal.fire({
            title: 'Are you sure?',
            text: "You are about to hire this applicant! This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, hire it!'
        }).then((result) => {
            if (result.isConfirmed) {
                swal.fire(
                    'Hired!',
                    'The applicant has been hired.',
                    'success'
                );
                parentForm.submit();
            }
        });
    });
</script>
<?php require '../../partials/admin/footer.php' ?>