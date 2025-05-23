<?php require 'partials/head.php' ?>
<?php require 'partials/manager/navbar.php' ?>

<main class="w-full mx-auto mt-6 p-6 flex-grow">
    <?php require 'partials/manager/nav.php' ?>
    <div class="overflow-x-auto rounded-box bg-base-100 shadow-lg">

        <div class="" id="offers-table">
            <table class="table text-center">
                <thead class="bg-[#594423] text-white">
                    <tr>
                        <th>Offer ID</th>
                        <th>Applicant Name</th>
                        <th>Position</th>
                        <th>Work Location</th>
                        <th>User Decision</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($offers as $offer) : ?>
                        <tr>
                            <td><?= htmlspecialchars($offer['offer_id']) ?></td>
                            <td><?= htmlspecialchars($offer['first_name']) . ' ' . htmlspecialchars($offer['last_name']) ?></td>
                            <td><?= htmlspecialchars($offer['position']) ?></td>
                            <td><?= htmlspecialchars($offer['work_location']) ?></td>
                            <td><?= htmlspecialchars($offer['user_decision']) ?></td>
                            <td>
                                <a href="/manager/job-offer?id=<?= $offer['offer_id'] ?>" class="btn bg-blue-500 text-white"><i class="fa-solid fa-eye"></i></a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<script>

</script>
<?php require 'partials/footer.php' ?>