<?php require '../../partials/admin/head.php' ?>

<div class="flex min-h-screen w-full">
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <?php require '../../partials/admin/sidebar.php' ?>

    <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
        <?php require '../../partials/admin/navbar.php' ?>
        <main class="px-8 py-8">
            <?php if (isset($error)) : ?>
                <div role="alert" class="alert alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-normal"><?= $error ?></span>
                </div>
            <?php endif ?>
            <?php if (isset($delete)) : ?>
                <div role="alert" class="alert alert-success">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-normal">The record has been successfully deleted.</span>
                </div>
            <?php endif ?>
            <div class="flex items-center justify-between mb-4">
                <h1 class="py-5 text-lg font-normal">List of all users</h1>
                <button type="button" class="btn border border-[#594423] hover:bg-[#594423] hover:text-white" id="create">Create User</button>
            </div>
            <div class="mb-5 bg-gray-50 py-3 px-2.5 shadow-lg rounded-lg hidden" id="createUserModal">
                <h1 class="bg-[#594423] text-white text-lg rounded-lg py-3 ps-5">User Creation</h1>
                <form method="post" class="grid grid-cols-3 gap-2 my-4" id="createUserForm">
                    <input type="hidden" name="create" value="true">
                    <div class="col-span-1 flex flex-col items-center">
                        <label for="first_name">First Name</label>
                        <input type="text" placeholder="Juan" name="first_name" class="input" id="first_name" value="<?= $_POST['first_name'] ?? '' ?>" required />
                    </div>
                    <div class="col-span-1 flex flex-col items-center">
                        <label for="last_name">Last_name</label>
                        <input type="text" placeholder="Dela Cruz" name="last_name" class="input" id="last_name" value="<?= $_POST['last_name'] ?? '' ?>" required />
                    </div>
                    <div class="col-span-1 flex flex-col items-center">
                        <label for="username">Username</label>
                        <input type="text" placeholder="@JuanDelaCruz" name="username" class="input" id="username" value="<?= $_POST['first_usernamename'] ?? '' ?>" required />
                    </div>
                    <div class="col-span-1 flex flex-col items-center">
                        <label for="email">Email</label>
                        <input type="email" placeholder="juandelacruz@example.com" name="email" class="input" id="email" value="<?= $_POST['email'] ?? '' ?>" required />
                    </div>
                    <div class="col-span-1 flex flex-col items-center">
                        <label for="password">Password</label>
                        <input type="password" placeholder="use strong password" name="password" class="input" id="password" required />
                    </div>
                    <div class="col-span-1 flex flex-col items-center">
                        <label for="role">Role</label>
                        <select type="text" placeholder="Type here" name="role" class="input text-center" id="role" required>
                            <option selected disabled>--Select an option--</option>
                            <option value="1">Admin</option>
                            <option value="4">Hiring Manager</option>
                            <option value="2">HR</option>
                            <option value="3">User</option>
                        </select>
                    </div>
                    <div class="col-span-3 text-end me-10 mt-5">
                        <button type="button" id="createBtn" class="btn bg-green-500 text-white hover:bg-green-600 rounded-lg">Create User</button>
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto rounded-box shadow-lg mb-6">
                <table class="table table-sm bg-white text-center text-black">
                    <thead class="bg-[#594423] text-white">
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Register via</th>
                            <th>Account Creation</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user) : ?>
                            <tr>
                                <th class="user_id border-r border-t"><?= htmlspecialchars($user['user_id'] ?? '')  ?></th>
                                <td class="username border-r border-t"><?= htmlspecialchars($user['username'] ?? '')  ?></td>
                                <td class="email border-r border-t"><?= htmlspecialchars($user['email'] ?? '')  ?></td>
                                <td class="role border-r border-t"><?= htmlspecialchars($user['role']) ?></td>
                                <td class="border-r border-t"><?= htmlspecialchars($user['register_type'] ?? '')  ?></td>
                                <td class="border-r border-t"><?= htmlspecialchars($user['created_at'] ?? '')  ?></td>
                                <td class="border-t">
                                    <a href="/admin/user-update?id=<?= htmlspecialchars($user['user_id']) ?>" class="openModal btn btn-primary my-2">Update</a>
                                    <button data-id="<?= $user['user_id'] ?>" class="openModal btn btn-error my-2" onclick="my_modal_3.showModal()">Delete</button>
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
                                                    <input type="hidden" name="id" id="id">
                                                    <button class="btn btn-error">Delete</button>
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
    document.getElementById('create').addEventListener('click', () => {
        const createModal = document.getElementById('createUserModal');
        if (createModal.classList.contains('hidden')) {
            createModal.classList.remove('hidden');
        } else {
            createModal.classList.add('hidden');
        }
    })
    $('#createBtn').on('click', function() {
        var isValid = true;
        var $form = $('#createUserForm');

        $form.find('input[required], select[required]').each(function() {
            var $field = $(this);

            if ($field.val() === null || $field.val().trim() === '') {
                isValid = false;
                $field.addClass('border-red-500');
            } else {
                $field.removeClass('border-red-500');
            }
            if (isValid) {
                Swal.fire({
                    title: 'Success!',
                    text: 'User created successfully!',
                    icon: 'success',
                    timer: 2000
                }).then((result) => {
                    $form.submit();
                })
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: 'Please fill in all required fields.',
                    icon: 'error',
                    timer: 2000,
                });
            }
        });
    });

    document.querySelectorAll('.openModal').forEach(button => {
        button.addEventListener('click', () => {
            let row = event.target.closest('tr');
            let idDelete = row.querySelector('.user_id').textContent;
            document.getElementById('id').value = idDelete;
        })
    })
</script>
<?php require '../../partials/admin/footer.php' ?>