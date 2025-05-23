<?php require 'partials/head.php' ?>
<?php require 'partials/manager/navbar.php' ?>

<main class="max-w-5xl mx-auto mt-10 mb-10 p-10 rounded-lg bg-white shadow-lg border border-[#594423]">
    <div class="p-10 mb-10 flex flex-col justify-center max-w-5xl">
        <div class="flex flex-col md:flex-row justify-between items-center md:items-center gap-6">
            <div>
                <h1 class="text-3xl font-bold text-[#594423] mb-2">
                    <?= htmlspecialchars($job['job_title']) ?>
                </h1>
                <p class="text-md text-[#594423] font-medium">
                    <strong>Company:</strong> <?= htmlspecialchars($job['company']) ?>
                </p>
                <p class="text-sm mt-2 flex items-center text-[#594423]">
                    <i class="fas fa-map-marker-alt text-[#594423] mr-2"></i>
                    <?= htmlspecialchars($job['location']) ?>
                </p>
                <span class="inline-block mt-3 text-sm font-semibold px-3 py-1 bg-[#594423] text-white rounded-md">
                    <?= htmlspecialchars($job['employment_type']) ?>
                </span>
            </div>
            <div class="mt-4 md:mt-0">
                <button data-modal-target="default-modal" data-modal-toggle="default-modal" class="block text-[#594423]  focus:ring-4 focus:outline-none focus:ring-[#FFF6E8] font-medim font-semibold rounded-lg text-sm px-5 py-2.5 text-center border border-[#594423] hover:bg-[#594423] hover:text-white transition" type="button">
                    Edit Job
                </button>
            </div>
        </div>

        <div class="mt-8 space-y-6 text-[#594423]">
            <div>
                <h2 class="text-xl font-semibold text-[#594423]">Job Description</h2>
                <p class="mt-2 leading-relaxed">
                    <?= nl2br(htmlspecialchars($job['description'])) ?>
                </p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-[#594423]">Qualifications</h2>
                <p class="mt-2 leading-relaxed">
                    <?= nl2br(htmlspecialchars($job['requirements'])) ?>
                </p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-[#594423]">Salary</h2>
                <p class="mt-2 font-medium">
                    <?= htmlspecialchars($job['salary']) ?>
                </p>
            </div>
        </div>

        <div class="mt-10">
            <a href="/manager/" class="text-blue-500 font-medium hover:text-blue-600 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Job Listings
            </a>
        </div>
    </div>
</main>

<div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-4xl max-h-full">
        <div class="relative bg-[#F7E6CA] rounded-lg shadow-sm">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                <h3 class="text-xl font-semibold text-[#594423]">
                    Job Details
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-[#594423] hover:text-white rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="default-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5 space-y-4">
                <form method="post">
                    <div class="grid gap-6 mb-6 md:grid-cols-2">
                        <div>
                            <label for="job_title" class="block mb-2 text-sm font-medium text-[#594423]">Job Title:</label>
                            <input type="text" id="job_title" name="job_title" value="<?= htmlspecialchars($job['job_title']) ?>" class="bg-gray-50 border border-[#594423] text-black text-sm rounded-lg focus:ring-[#594423] focus:border-[#594423] block w-full p-2.5 " placeholder="General Manager" required />
                            <span class="text-red-500 text-sm <?= empty($errors['job_title']) ? 'hidden' : '' ?>"><?= $errors['job_title'] ?? '' ?></span>
                        </div>

                        <div>
                            <label for="company" class="block mb-2 text-sm font-medium text-[#594423]">Company</label>
                            <input type="text" id="company" name="company" value="<?= htmlspecialchars($job['company']) ?>" class="bg-gray-50 border border-[#594423] text-black text-sm rounded-lg focus:ring-[#594423] focus:border-[#594423] block w-full p-2.5" required />
                            <span class="text-red-500 text-sm <?= empty($errors['company']) ? 'hidden' : '' ?>"><?= $errors['company'] ?? '' ?></span>
                        </div>

                        <div class="mb-2 md:col-span-2">

                            <label for="description" class="block mb-2 text-sm font-medium text-[#594423]">Description</label>
                            <textarea id="description" rows="4" name="description" class="block p-2.5 w-full text-sm text-black bg-gray-50 rounded-lg border border-[#594423] focus:ring-[#594423] focus:border-[#594423]"><?= htmlspecialchars($job['description']) ?></textarea>
                            <span class="text-red-500 text-sm <?= empty($errors['description']) ? 'hidden' : '' ?>"><?= $errors['description'] ?? '' ?></span>
                        </div>

                        <div>
                            <label for="location" class="block mb-2 text-sm font-medium text-[#594423]">Location</label>
                            <input type="text" id="location" name="location" value="<?= htmlspecialchars($job['location']) ?>" class="bg-gray-50 border border-[#594423] text-black text-sm rounded-lg focus:ring-[#594423] focus:border-[#594423] block w-full p-2.5 " placeholder="John" required />
                            <span class="text-red-500 text-sm <?= empty($errors['location']) ? 'hidden' : '' ?>"><?= $errors['location'] ?? '' ?></span>
                        </div>

                        <div>
                            <label for="salary" class="block mb-2 text-sm font-medium text-[#594423]">Salary</label>
                            <input type="number" id="salary" name="salary" value="<?= htmlspecialchars($job['salary']) ?>" class="bg-gray-50 border border-[#594423] text-black text-sm rounded-lg focus:ring-[#594423] focus:border-[#594423] block w-full p-2.5 " placeholder="Doe" required />
                        </div>
                        <span class="text-red-500 text-sm <?= empty($errors['salary']) ? 'hidden' : '' ?>"><?= $errors['salary'] ?? '' ?></span>

                        <div class="mb-2 md:col-span-2">
                            <label for="requirements" class="block mb-2 text-sm font-medium text-[#594423]">Requirements</label>
                            <textarea id="requirements" rows="4" name="requirements" class="block p-2.5 w-full text-sm text-black bg-gray-50 rounded-lg border border-[#594423] focus:ring-[#594423] focus:border-[#594423] " placeholder="Write your thoughts here..."><?= htmlspecialchars($job['requirements']) ?></textarea>
                            <span class="text-red-500 text-sm <?= empty($errors['requirements']) ? 'hidden' : '' ?>"><?= $errors['requirements'] ?? '' ?></span>
                        </div>

                        <div class="max-w-sm mx-auto w-full md:col-span-2">
                            <label for="employment_type" class="block mb-2 text-sm font-medium text-[#594423]">Employment Type</label>
                            <select id="employment_type" name="employment_type" class="bg-gray-50 border border-[#594423] text-black text-sm rounded-lg focus:ring-[#594423] focus:border-[#594423] block w-full p-2.5 ">
                                <option selected> Choose: </option>
                                <option value="full-time">full-time</option>
                                <option value="part-time">part-time</option>
                            </select>
                            <span class="text-red-500 text-sm <?= empty($errors['employment_type']) ? 'hidden' : '' ?>"><?= $errors['employment_type'] ?? '' ?></span>
                        </div>
                </form>
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600 md:col-span-2">
                    <button type="submit" class="border border-[#594423] hover:bg-[#594423] hover:text-white font-medium rounded-lg text-sm px-5 py-2.5 text-center text-[#4E3B2A] transition">Update</button>
                    <button data-modal-hide="default-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-white focus:outline-none bg-red-500 rounded-lg border border-red-500 hover:bg-red-600 focus:z-10 focus:ring-4 focus:ring-gray-100 transition">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php require 'partials/footer.php' ?>