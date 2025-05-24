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
            <?php if (isset($delete)) : ?>
                <div role="alert" class="alert alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-normal">Record deleted successfully! It has been removed from the system.</span>
                </div>
            <?php endif ?>
            <?php if ($updated ?? '' === true) : ?>
                <div role="alert" class="alert alert-success">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-normal">Your changes have been successfully updated!</span>
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
                                    <a href="/admin/applicant?id=<?= htmlspecialchars($applicant['applicant_id']) ?>" class="btn border border-black"><i class="fa-solid fa-eye"></i></a>
                                    <a href="/admin/applicant-update?id=<?= htmlspecialchars($applicant['applicant_id']) ?>" class="openModal btn btn-primary my-2"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <button data-id="<?= $user['user_id'] ?>" class="deleteModal btn btn-error my-2" onclick="my_modal_3.showModal()"><i class="fa-solid fa-trash"></i></button>
                                    <dialog id="my_modal_3" class="modal modal-bottom sm:modal-middle">
                                        <div class="modal-box">
                                            <form method="dialog">
                                                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>
                                            <h3 class="text-xl font-bold">Alert</h3>
                                            <p class="py-4">Are you sure you want to delete this record? This action cannot be undone.</p>
                                            <div class="flex justify-center gap-5">
                                                <form method="post">
                                                    <input type="hidden" name="delete" value="true">
                                                    <input type="hidden" name="id" id="delete_id">
                                                    <button class="btn btn-error" type="submit">Delete</button>
                                                </form>
                                                <form method="dialog">
                                                    <button class="btn">Cancel</button>
                                                </form>
                                            </div>
                                        </div>
                                        <form method="dialog" class="modal-backdrop">
                                            <button>close</button>
                                        </form>
                                    </dialog>
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
    document.querySelectorAll('.deleteModal').forEach((button) => {
        button.addEventListener('click', (event) => {
            const row = event.target.closest('tr');
            const idValue = row.querySelector('.applicant_id').textContent;
            document.getElementById('delete_id').value = idValue;
        });
    });
</script>
<?php require '../../partials/admin/footer.php' ?>