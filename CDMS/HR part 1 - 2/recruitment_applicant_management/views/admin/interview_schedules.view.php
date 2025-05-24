<?php require '../../partials/admin/head.php' ?>

<div class="flex min-h-screen w-full">
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <?php require '../../partials/admin/sidebar.php' ?>
    <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
        <?php require '../../partials/admin/navbar.php' ?>
        <main class="px-8 py-8">
            <?php if ($updated ?? '' === true) : ?>
                <div role="alert" class="alert alert-success mb-5">
                    <box-icon name='check-circle'></box-icon>
                    <span>Interview schedule Updated successfully.</span>
                </div>
            <?php endif ?>
            <?php if ($deleted ?? '' === true) : ?>
                <div role="alert" class="alert alert-success mb-5">
                    <box-icon name='check-circle'></box-icon>
                    <span>Interview schedule Deleted.</span>
                </div>
            <?php endif ?>
            <div>
                <div class="flex items-center justify-end mb-5">
                    <a href="/admin/interview_schedules-create" class="btn border border-[#594423] hover:bg-[#594423] hover:text-white shadow-lg">create interview schedule</a>
                </div>
                <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
                    <table class="table text-center">
                        <thead class="bg-[#594423] text-white">
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Location</th>
                                <th>Mode</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Applicant</th>
                                <th>Interviewer</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $schedule) : ?>
                                <tr>
                                    <td class="schedule_id border-r border-t"><?= htmlspecialchars($schedule['schedule_id']) ?></td>
                                    <td class="date border-r border-t"><?= htmlspecialchars($schedule['date']) ?></td>
                                    <td class="time border-r border-t"><?= htmlspecialchars($schedule['time']) ?></td>
                                    <td class="location border-r border-t"><?= htmlspecialchars($schedule['location']) ?></td>
                                    <td class="mode border-r border-t"><?= htmlspecialchars($schedule['mode']) ?></td>
                                    <td class="interview_type border-r border-t"><?= htmlspecialchars($schedule['interview_type']) ?></td>
                                    <td class="interview_status border-r border-t"><?= htmlspecialchars($schedule['interview_status']) ?></td>
                                    <td class="first_name border-r border-t"><?= htmlspecialchars($schedule['first_name']) ?></td>
                                    <td class="username border-r border-t"><?= htmlspecialchars($schedule['username']) ?></td>
                                    <td class="border-t">
                                        <button data-id="<?= $user['user_id'] ?>" class="openModal btn btn-primary my-2" onclick="my_modal_5.showModal()"><i class="fa-solid fa-pen-to-square"></i></button>
                                        <dialog id="my_modal_5" class="modal modal-bottom sm:modal-middle">
                                            <div class="modal-box max-w-3xl">
                                                <form method="dialog">
                                                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                                </form>
                                                <h3 class="text-lg font-semibold">Update Record</h3>
                                                <hr class="my-4">
                                                <form method="post" class="grid grid-cols-2 gap-2">
                                                    <input type="hidden" name="update" value="true" required>
                                                    <div class="my-4 col-span-1">
                                                        <label for="i_schedule_id">Schedule ID</label>
                                                        <input type="number" name="schedule_id" id="i_schedule_id" class="input text-center" required readonly />
                                                    </div>
                                                    <div class="my-4 col-span-1">
                                                        <label for="i_first_name">Applicant</label>
                                                        <input type="text" name="first_name" id="i_first_name" class="input text-center" required readonly />
                                                    </div>
                                                    <div class="my-4 col-span-1">
                                                        <label for="date">date</label>
                                                        <input type="date" name="date" class="input text-center" id="i_date" required />
                                                        <?php if (isset($errors['date'])): ?>
                                                            <div class="text-red-400 text-xs">
                                                                <?= $errors['date'] ?>
                                                            </div>
                                                        <?php endif ?>
                                                    </div>
                                                    <div class=" my-4 col-span-1 flex flex-col items-center">
                                                        <label for="time">time</label>
                                                        <input type="time" name="time" class="input text-center" id="i_time" required />
                                                        <?php if (isset($errors['time'])): ?>
                                                            <div class="text-red-400 text-xs">
                                                                <?= $errors['time'] ?>
                                                            </div>
                                                        <?php endif ?>
                                                    </div>
                                                    <div class=" my-4 col-span-1 flex flex-col items-center">
                                                        <label for="location">location</label>
                                                        <input type="text" name="location" class="input text-center" id="i_location" required />
                                                        <?php if (isset($errors['location'])): ?>
                                                            <div class="text-red-400 text-xs">
                                                                <?= $errors['location'] ?>
                                                            </div>
                                                        <?php endif ?>
                                                    </div>
                                                    <div class=" my-4 col-span-1">
                                                        <label for="mode">Mode</label>
                                                        <select type="date" name="mode" class="input text-center" id="i_mode" required>
                                                            <option selected disabled class="text-center">Choose an option:</option>
                                                            <option value="Online" class="text-center">Online</option>
                                                            <option value="In-Person" class="text-center">In-Person</option>
                                                        </select>
                                                        <?php if (isset($errors['mode'])): ?>
                                                            <div class="text-red-400 text-xs">
                                                                <?= $errors['mode'] ?>
                                                            </div>
                                                        <?php endif ?>
                                                    </div>
                                                    <div class=" my-4 col-span-1">
                                                        <label for="interview_type">Interview Type</label>
                                                        <select name="interview_type" class="input text-center" id="i_interview_type" required>
                                                            <option selected disabled class="text-center">Choose an option:</option>
                                                            <option value="initial" class="text-center">initial</option>
                                                            <option value="final" class="text-center">final</option>
                                                        </select>
                                                        <?php if (isset($errors['interview_type'])): ?>
                                                            <div class="text-red-400 text-xs">
                                                                <?= $errors['interview_type'] ?>
                                                            </div>
                                                        <?php endif ?>
                                                    </div>
                                                    <div class="my-4 col-span-1">
                                                        <label for="interview_status">interview_status</label>
                                                        <select type="interview_status" placeholder="Type here" name="interview_status" class="input text-center" id="i_interview_status" required>
                                                            <option selected disabled class="text-center">Choose an option:</option>
                                                            <option value="done" class="text-center">done</option>
                                                            <option value="ongoing" class="text-center">ongoing</option>
                                                        </select>
                                                        <?php if (isset($errors['interview_status'])): ?>
                                                            <div class="text-red-400 text-xs">
                                                                <?= $errors['interview_status'] ?>
                                                            </div>
                                                        <?php endif ?>
                                                    </div>
                                                    <div class="col-span-2 text-center my-6">
                                                        <div>
                                                            <button type="submit" class="btn btn-primary px-10 tracking-wider">Update</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <form method="dialog" class="modal-backdrop">
                                                <button>close</button>
                                            </form>
                                        </dialog>
                                        <button data-id="<?= htmlspecialchars($user['user_id']) ?>" class="deleteModal btn btn-error my-2" onclick="my_modal_3.showModal()"><i class="fa-solid fa-trash"></i></button>
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
            </div>
            <?php if (count($schedules) < 1) : ?>
                <div role="alert" class="alert alert-error my-6 mx-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Error! No Data Found.</span>
                </div>
            <?php endif ?>
        </main>
    </div>
</div>
<script>
    document.querySelectorAll('.openModal').forEach((button) => {
        button.addEventListener('click', (event) => {
            const row = event.target.closest('tr');
            const schedule_id = row.querySelector('.schedule_id').textContent;
            const date = row.querySelector('.date').textContent;
            const time = row.querySelector('.time').textContent;
            const location = row.querySelector('.location').textContent;
            const mode = row.querySelector('.mode').textContent;
            const interview_type = row.querySelector('.interview_type').textContent;
            const interview_status = row.querySelector('.interview_status').textContent;
            const first_name = row.querySelector('.first_name').textContent;
            document.getElementById('i_schedule_id').value = schedule_id;
            document.getElementById('i_date').value = date;
            document.getElementById('i_time').value = time;
            document.getElementById('i_location').value = location;
            document.getElementById('i_mode').value = mode;
            document.getElementById('i_interview_type').value = interview_type;
            document.getElementById('i_interview_status').value = interview_status;
            document.getElementById('i_first_name').value = first_name;
        });
    })
    document.querySelectorAll('.deleteModal').forEach((button) => {
        button.addEventListener('click', (event) => {
            const row = event.target.closest('tr');
            const schedule_id = row.querySelector('.schedule_id').textContent;
            // if (schedule_id == null) {
            //     console.log('null value');
            // } else {
            //     console.log('status ok');
            // }
            // console.log(schedule_id);
            document.getElementById('delete_id').value = schedule_id;
        });
    })
</script>
<?php require '../../partials/admin/footer.php' ?>