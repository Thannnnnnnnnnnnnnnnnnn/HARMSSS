<?php require 'partials/head.php' ?>
<?php require 'partials/navbar.php' ?>

<main class="max-w-6xl mx-auto mt-6 p-4 flex-grow">
    <div class="max-w-5xl mx-auto p-4 flex-grow">
        <h2 class="text-2xl font-bold text-[#3D2F1F] mb-6 text-center">Available Job Postings</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
            <?php foreach ($postings as $posting) : ?>
                <div class="bg-white bg-opacity-10 backdrop-filter backdrop-blur-lg p-4 md:p-5 rounded-xl shadow-lg border border-[#594423] flex flex-col h-full transition transform hover:-translate-y-1 hover:shadow-xl">
                    <h3 class="text-lg font-bold text-[#3D2F1F] mb-1 md:mb-2"><?= htmlspecialchars($posting['job_title']) ?></h3>
                    <p class="text-sm text-gray-500 font-medium mb-1 md:mb-2"><?= htmlspecialchars($posting['company']) ?></p>
                    <p class="text-xs md:text-sm text-gray-600 flex items-center mb-2 md:mb-3">
                        <i class="fas fa-map-marker-alt text-[#3D2F1F] mr-1 md:mr-2"></i>
                        <?= htmlspecialchars($posting['location']) ?>
                    </p>
                    <span class="inline-block w-fit text-[0.6rem] md:text-xs font-semibold px-2 md:px-3 py-0.5 md:py-1 bg-[#3D2F1F] text-white rounded-lg">
                        <?= htmlspecialchars($posting['employment_type']) ?>
                    </span>
                    <div class="mt-auto pt-2 md:pt-4">
                        <a href="/job-details?id=<?= htmlspecialchars($posting['posting_id']) ?>" class="block text-center bg-[#3D2F1F] text-white px-3 md:px-4 py-1 md:py-2 rounded-lg text-sm font-semibold hover:bg-[#594423] transition">
                            See Details
                        </a>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</main>

<?php require 'partials/footer.php' ?>