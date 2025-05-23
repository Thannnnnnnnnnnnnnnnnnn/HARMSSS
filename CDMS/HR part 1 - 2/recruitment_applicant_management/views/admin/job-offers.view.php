<?php require 'partials/admin/head.php' ?>

<div class="flex min-h-screen w-full">
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <?php require 'partials/admin/sidebar.php' ?>

    <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
        <?php require 'partials/admin/navbar.php' ?>
        <main class="px-8 py-8">
            <div>
                <h2>Accepted Offers</h2>
            </div>
            <div class="overflow-x-auto rounded-box shadow-lg mb-6">
                <table class="table text-center ">
                    <thead class="bg-[#594423] text-white">
                        <tr>
                            <th>Offer ID</th>
                            <th>Position</th>
                            <th>Applicant ID</th>
                            <th>Application Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($offers_accepted as $offer) : ?>
                            <tr>
                                <th class="border-r"><?= htmlspecialchars($offer['offer_id']) ?></th>
                                <td class="border-r"><?= htmlspecialchars($offer['position']) ?></td>
                                <td class="border-r"><?= htmlspecialchars($offer['applicant_id']) ?></td>
                                <td class="border-r <?= $offer['status'] === 'hired' ? 'font-bold text-green-500' : '' ?>"><?= htmlspecialchars($offer['status']) ?></td>
                                <td>
                                    <a href="/admin/job-offer-view?id=<?= $offer['offer_id'] ?>" class="btn bg-blue-500 hover:bg-blue-600 text-white tracking-wider">view</a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <div>
                <h2>Recent Offers</h2>
            </div>
            <div class="overflow-x-auto rounded-box shadow-lg mb-4">
                <table class="table text-center ">
                    <thead class="bg-[#594423] text-white">
                        <tr>
                            <th>Offer ID</th>
                            <th>Position</th>
                            <th>Applicant ID</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($offers as $offer) : ?>
                            <tr>
                                <th class="border-r"><?= htmlspecialchars($offer['offer_id']) ?></th>
                                <td class="border-r"><?= htmlspecialchars($offer['position']) ?></td>
                                <td class="border-r"><?= htmlspecialchars($offer['applicant_id']) ?></td>
                                <td>
                                    <a href="/admin/job-offer-view?id=<?= htmlspecialchars($offer['offer_id']) ?>" class="btn btn-primary"><i class="fa-solid fa-eye"></i></a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <?php if (count($offers) <= 0) : ?>
                <div class="text-center">
                    <span class="text-red-500">No Job offer found.</span>
                </div>
            <?php endif ?>
        </main>
    </div>
</div>

<?php require 'partials/admin/footer.php' ?>