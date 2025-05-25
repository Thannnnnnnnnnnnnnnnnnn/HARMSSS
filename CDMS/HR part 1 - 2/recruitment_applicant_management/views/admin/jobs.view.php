<?php require '../../partials/admin/head.php' ?>


<div class="flex min-h-screen w-full text-[#594423]">
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <?php require '../../partials/admin/sidebar.php' ?>
    <?php if (isset($_SESSION['job-delete'])) : ?>
        <div role="alert" class="alert alert-error mx-10z">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-normal">Record deleted successfully! It has been removed from the system.</span>
        </div>
    <?php endif ?>
    <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">

        <?php require '../../partials/admin/navbar.php' ?>
        <main class="px-2 py-5">
            <?php if (isset($error)) : ?>
                <div role="alert" class="alert alert-error mx-10z">
                    <box-icon name='x-circle'></box-icon>
                    <span class="font-normal"><?= $error ?></span>
                </div>
            <?php endif ?>
            <?php if ($success == true) : ?>
                <div role="alert" class="alert alert-success mx-10z">
                    <box-icon name='check-circle'></box-icon>
                    <span class="font-normal"> Job posted successfully!</span>
                </div>
            <?php endif ?>
            <div class="flex justify-end">
                <button class="btn my-5 me-5 border border-[#594423] bg-[#F7E6CA] hover:bg-[#594423] hover:text-white" onclick="my_modal_2.showModal()">Create Job Posting</button>
                <dialog id="my_modal_2" class="modal">
                    <div class="modal-box max-w-4xl">
                        <form method="dialog">
                            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                        </form>
                        <h3 class="text-lg font-bold">Job posting</h3>
                        <hr class="my-5">
                        <form method="post">
                            <div class="grid grid-cols-2 gap-6">
                                <input type="hidden" name="posted_by" value="<?= $_SESSION['User_ID'] ?>">
                                <div class="flex flex-col items-center col-span-1">
                                    <label for="job_title" class="mb-2 text-black font-normal">Job Title</label>
                                    <input type="text" placeholder="Type here" name="job_title" class="input border border-[#594423] text-center" id="job_title" value="<?= htmlspecialchars($_POST['job_title'] ?? '') ?>" />
                                </div>
                                <div class="flex flex-col items-center col-span-1">
                                    <label for="location" class="mb-2 text-black font-normal">Location</label>
                                    <input type="text" placeholder="Type here" name="location" class="input border border-[#594423] text-center" id="location" value="<?= htmlspecialchars($_POST['location'] ?? '') ?>" />
                                </div>
                                <div class="flex flex-col items-center col-span-2">
                                    <label for="employment_type" class="mb-2 text-black font-normal">Employment Type</label>
                                    <select name="employment_type" id="employment_type" class="w-full text-center input border border-[#594423]" value="<?= htmlspecialchars($_POST['employment_type'] ?? '') ?>">
                                        <option selected disabled class="text-center">Choose an option:</option>
                                        <option value="full-time" class="text-center">full time</option>
                                        <option value="part-time" class="text-center">part time</option>
                                    </select>
                                </div>
                                <div class="flex flex-col items-center col-span-2">
                                    <label for="department" class="mb-2 text-black font-normal">Employment Type</label>
                                    <select name="department" id="department" class="w-full text-center input border border-[#594423]" value="<?= htmlspecialchars($_POST['department'] ?? '') ?>">
                                        <option selected disabled class="text-center">Choose an option:</option>
                                        <?php foreach ($departments as $department) : ?>
                                            <option value="<?= $department['dept_id'] ?>" class="text-center"><?= $department['department_name'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="flex flex-col items-center col-span-1">
                                    <label for="salary" class="mb-2 text-black font-normal">Salary</label>
                                    <input type="number" placeholder="Type here" name="salary" class="input border border-[#594423] text-center" id="salary" value="<?= htmlspecialchars($_POST['salary'] ?? '') ?>" />
                                </div>
                                <div class="flex flex-col items-center col-span-1">
                                    <label for="company" class="mb-2 text-black font-normal">Company</label>
                                    <input type="text" placeholder="Type here" name="company" class="input border border-[#594423] text-center" id="company" value="Avalon Hotel & Restaurant" readonly />
                                </div>
                                <div class="flex flex-col items-center col-span-1">
                                    <label for="description" class="mb-2 text-black font-normal">Description</label>
                                    <textarea name="description" id="description" cols="20" rows="5" class="textarea border border-[#594423] text-center"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                                </div>
                                <div class="flex flex-col items-center col-span-1">
                                    <label for="requirements" class="mb-2 text-black font-normal">requirements</label>
                                    <textarea name="requirements" id="requirements" cols="20" rows="5" class="textarea border border-[#594423] text-center"><?= htmlspecialchars($_POST['requirements'] ?? '') ?></textarea>
                                </div>
                                <div class="col-span-2 text-center">
                                    <button type="submit" class="btn rounded-lg px-10 border border-[#594423] bg-[#F7E6CA] hover:bg-[#594423] hover:text-white">POST JOB</button>
                                </div>
                        </form>
                    </div>
                    <form method="dialog" class="modal-backdrop">
                        <button>close</button>
                    </form>
                </dialog>
            </div>
            <div class="max-w-5xl mx-auto p-4 flex-grow">
                <h2 class="text-2xl font-bold text-[#3D2F1F] mb-6 text-center">Available Job Postings</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    <?php foreach ($postings as $posting) : ?>
                        <div class="bg-white bg-opacity-10 backdrop-filter backdrop-blur-lg p-5 rounded-xl shadow-lg border border-[#594423] flex flex-col h-full transition transform hover:-translate-y-1 hover:shadow-xl">
                            <h3 class="text-lg font-bold text-[#3D2F1F] mb-2"><?= htmlspecialchars($posting['job_title']) ?></h3>

                            <p class="text-sm text-gray-500 font-medium mb-2"><?= htmlspecialchars($posting['company']) ?></p>

                            <p class="text-sm text-gray-600 flex items-center mb-2">
                                <i class="fas fa-map-marker-alt text-[#3D2F1F] mr-2"></i>
                                <?= htmlspecialchars($posting['location']) ?>
                            </p>

                            <p class="inline-block w-fit text-xs font-semibold px-3 py-1 bg-[#3D2F1F] text-white rounded-lg">
                                <?= htmlspecialchars($posting['employment_type']) ?>
                            </p>
                            <p class="">
                                <?php foreach ($departments as $department) : ?>
                                    <?php if ($posting['department_id'] === $department['dept_id']) : ?>
                                        <?= htmlspecialchars($department['department_name']) ?>
                                    <?php endif ?>
                                <?php endforeach ?>
                            </p>

                            <div class="mt-auto pt-4">
                                <a href="job.php?id=<?= htmlspecialchars($posting['posting_id']) ?>"
                                    class="block text-center bg-[#3D2F1F] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-[#594423] transition">
                                    See Details
                                </a>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require '../../partials/admin/footer.php' ?>