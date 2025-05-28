<?php require '../partials/head.php' ?>
<?php require '../partials/navbar.php' ?>

<main class="max-w-4xl mx-auto mt-10 p-8 flex-grow">
    <div class="p-10 rounded-lg shadow-lg border border-[#594423]">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
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
                <span class="inline-block mt-3 text-xs font-semibold px-3 py-2 bg-[#594423] text-white rounded-md">
                    <?= htmlspecialchars($job['employment_type']) ?>
                </span>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="job-application.php?id=<?= htmlspecialchars($job['posting_id']) ?>"
                    class="text-center bg-white border border-[#594423] hover:bg-[#594423] hover:text-white transition py-2 px-4 text-black cursor-pointer rounded-xl shadow-md">
                    Apply Now
                </a>
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
                <h2 class="text-xl font-semibold text-[#594423]">Department</h2>
                <p class="mt-2 leading-relaxed">
                    <?= nl2br(htmlspecialchars($dept['department_name'])) ?>
                </p>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-[#594423]">Salary</h2>
                <p class="mt-2 font-medium">
                    <?= htmlspecialchars($job['salary']) ?>
                </p>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-700">How to Apply</h2>
                <p class="mt-2 text-gray-700">
                    Interested candidates can apply by clicking the <strong>"Apply Now"</strong> button above.
                </p>
            </div>
        </div>
        <div class="mt-10">
            <a href="home.php" class="text-[#594423] font-medium hover:text-[#594423] flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Job Listings
            </a>
        </div>
    </div>
</main>

<?php require '../partials/footer.php' ?>