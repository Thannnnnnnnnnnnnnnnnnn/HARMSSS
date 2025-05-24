<?php require '../../partials/admin/head.php' ?>

<div class="flex min-h-screen w-full text-black">
    <div class="sidebar-overlay fixed top-0 left-0 w-full h-full bg-black opacity-50 z-40 hidden" id="sidebar-overlay"></div>
    <?php require '../../partials/admin/sidebar.php' ?>

    <div class="main w-full bg-[#FFF6E8] md:ml-[320px] transition-all duration-300">
        <?php require '../../partials/admin/navbar.php' ?>
        <main class="px-4 py-10 flex flex-col items-center">
            <?php if ($success ?? '' == true) : ?>
                <div role="alert" class="alert alert-success mb-4 w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Successfully Updated!</span>
                </div>
            <?php endif ?>
            <?php if ($delete ?? '' == true) : ?>
                <div role="alert" class="alert alert-success mb-4 w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Job posting sucessfully deleted</span>
                </div>
            <?php endif ?>
            <?php if ($error ?? '' == true) : ?>
                <div role="alert" class="alert alert-error mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?= $errors ?></span>
                </div>
            <?php endif ?>
            <div class="w-full max-w-3xl p-6 rounded-lg shadow-lg bg-white border border-[#594423]">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-black mb-2">
                            <?= htmlspecialchars($job['job_title']) ?>
                        </h1>
                        <p class="text-sm md:text-md text-black font-medium">
                            <strong>Company:</strong> <?= htmlspecialchars($job['company']) ?>
                        </p>
                        <p class="text-xs mt-2 flex items-center text-black">
                            <i class="fas fa-map-marker-alt text-black mr-2"></i>
                            <?= htmlspecialchars($job['location']) ?>
                        </p>
                        <span class="inline-block mt-3 text-[0.6rem] md:text-xs font-semibold px-2 md:px-3 py-1 md:py-2 bg-[#594423] text-white rounded-md">
                            <?= htmlspecialchars($job['employment_type']) ?>
                        </span>
                    </div>
                    <div class="mt-4 md:mt-0 text-center flex flex-col md:flex-row items-center">
                        <button class="btn border-[#594423] rounded-lg hover:bg-[#594423] hover:text-white mx-1" onclick="my_modal_2.showModal()"><box-icon name='edit'></box-icon></button>
                        <form method="post" id="deleteForm" class="text-center p-1">
                            <input type="hidden" name="id" value="<?= $_GET['id'] ?>">
                            <input type="hidden" name="delete" value="true">
                            <button type="button" id="deleteBtn" class="btn border-[#594423] rounded-lg hover:bg-[#594423] hover:text-white"><box-icon name='trash'></box-icon></button>
                        </form>
                        <dialog id="my_modal_2" class="modal">
                            <div class="modal-box w-11/12 max-w-3xl">
                                <h3 class="text-lg font-bold">Edit Job details</h3>
                                <form method="dialog">
                                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                </form>
                                <hr class="my-4">
                                <form method="post" id="updateForm" class="grid grid-cols-1 md:grid-cols-2 gap-4 font-normal">
                                    <div>
                                        <p>Job Title</p>
                                        <input class="input validator w-full mt-2" name="job_title" value="<?= $job['job_title'] ?>" type="text" required placeholder="Job Title" />
                                        <?php if ($errors['job_title'] ?? '') : ?>
                                            <p class="text-red-500 text-xs"><?= $errors['job_title'] ?></p>
                                        <?php endif ?>
                                    </div>
                                    <div>
                                        <p>Company</p>
                                        <input class="input validator w-full mt-2" name="company" value="<?= $job['company'] ?>" type="text" required placeholder="company" />
                                        <?php if ($errors['company'] ?? '') : ?>
                                            <p class="text-red-500 text-xs"><?= $errors['company'] ?></p>
                                        <?php endif ?>
                                    </div>
                                    <div class="md:col-span-2">
                                        <fieldset class="border rounded p-2">
                                            <legend class="text-sm px-2">Description</legend>
                                            <textarea class="textarea h-24 w-full" name="description" placeholder="Description..." required><?= $job['description'] ?></textarea>
                                            <?php if ($errors['description'] ?? '') : ?>
                                                <p class="text-red-500 text-xs"><?= $errors['description'] ?></p>
                                            <?php endif ?>
                                        </fieldset>
                                    </div>
                                    <div>
                                        <p>Location</p>
                                        <input class="input validator w-full mt-2" name="location" value="<?= $job['location'] ?>" type="text" required placeholder="Location" />
                                        <?php if ($errors['location'] ?? '') : ?>
                                            <p class="text-red-500 text-xs"><?= $errors['location'] ?></p>
                                        <?php endif ?>
                                    </div>
                                    <div>
                                        <p>Salary</p>
                                        <input class="input validator w-full mt-2" name="salary" value="<?= $job['salary'] ?>" type="text" required placeholder="Salary" />
                                        <?php if ($errors['salary'] ?? '') : ?>
                                            <p class="text-red-500 text-xs"><?= $errors['salary'] ?></p>
                                        <?php endif ?>
                                    </div>
                                    <div class="md:col-span-2">
                                        <fieldset class="border rounded p-2">
                                            <legend class="text-sm px-2">Requirements</legend>
                                            <textarea class="textarea h-24 w-full" name="requirements" placeholder="Requirements..." required><?= $job['requirements'] ?></textarea>
                                            <?php if ($errors['requirements'] ?? '') : ?>
                                                <p class="text-red-500 text-xs"><?= $errors['requirements'] ?></p>
                                            <?php endif ?>
                                        </fieldset>
                                    </div>
                                    <div class="md:col-span-2 flex flex-col items-center">
                                        <p class="my-2">Employment type</p>
                                        <select class="select w-full" name="employment_type" required>
                                            <option selected value="<?= htmlspecialchars($job['employment_type']) ?>"><?= htmlspecialchars($job['employment_type']) ?></option>
                                            <option value="full-time">full-time</option>
                                            <option value="part-time">part-time</option>
                                        </select>
                                        <?php if ($errors['employment_type'] ?? '' == true) : ?>
                                            <p class="text-red-500 text-xs"><?= $errors['employment_type'] ?></p>
                                        <?php endif ?>
                                    </div>
                                    <hr class="md:col-span-2 my-2">
                                    <div class="md:col-span-2 text-right">
                                        <input type="hidden" name="id" value="<?= $job['posting_id'] ?>">
                                        <button type="button" id="updateBtn" class="btn border-[#594423] rounded-lg hover:bg-[#594423] hover:text-white">Update</button>
                                    </div>
                                </form>
                            </div>
                            <form method="dialog" class="modal-backdrop">
                                <button>close</button>
                            </form>
                        </dialog>
                    </div>
                </div>

                <div class="mt-4 space-y-4 text-black">
                    <div>
                        <h2 class="text-lg font-semibold text-black">Job Description</h2>
                        <p class="mt-2 leading-relaxed">
                            <?= nl2br(htmlspecialchars($job['description'])) ?>
                        </p>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-black">Qualifications</h2>
                        <p class="mt-2 leading-relaxed">
                            <?= nl2br(htmlspecialchars($job['requirements'])) ?>
                        </p>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-black">Salary</h2>
                        <p class="mt-2 font-medium">
                            <?= htmlspecialchars($job['salary']) ?>
                        </p>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="/admin/jobs" class="text-black font-medium hover:text-blue-600 hover:underline flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Job Listings
                    </a>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#updateBtn').click(function() {
            let isValid = true;
            $('input[required], select[required], textarea[required]').each(function() {
                if ($(this).val() === '') {
                    $(this).addClass('border-red-500');
                    isValid = false;
                } else {
                    $(this).removeClass('border-red-500');
                }
            });
            if (isValid) {
                $('#updateForm').submit();
            } else {
                swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please fill in all required fields.',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn border-[#594423] rounded-lg hover:bg-[#594423] hover:text-white'
                    },
                    buttonsStyling: false,
                });
            }
        });
        $('#deleteBtn').click(function() {
            swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#594423',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'btn me-5 bg-[#ad1414] rounded-lg hover:bg-[#8c0d0d] text-white',
                    cancelButton: 'btn border-[#594423] rounded-lg hover:bg-[#594423] hover:text-white'
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    swal.fire({
                        title: 'Deleted!',
                        text: 'Your file has been deleted.',
                        icon: 'success',
                        timer: 2000,
                        customClass: {
                            confirmButton: 'btn border-[#594423] rounded-lg hover:bg-[#594423] hover:text-white'
                        },
                        buttonsStyling: false,
                    });
                    $('#deleteForm').submit();
                }
            });
        });
    });
</script>

<?php require '../../partials/admin/footer.php' ?>