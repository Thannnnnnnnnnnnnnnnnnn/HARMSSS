<?php require 'partials/head.php' ?>
<?php require 'partials/hr_hiring/navbar.php' ?>

<main class="w-screen mx-auto  p-6 flex-grow flex justify-center font-normal text-[#594423]">
    <div class="w-full max-w-5xl min-h-[300px] p-6 bg-white  border border-[#594423] rounded-lg shadow-sm">
        <div class="flex flex-col justify-between py-5 gap-10">
            <div class="grid grid-cols-3 gap-5">
                <div class="col-span-3 flex items-center justify-between">
                    <h1 class="text-xl font-semibold"><?= strtoupper($application['type']) ?></h1>
                    <?php if ($application['status'] == 'read') : ?>
                        <p>READ</p>
                    <?php else : ?>
                        <form method="POST" class="mark-as-read-form">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($application['id']) ?>">
                            <label for="read-<?= htmlspecialchars($application['id']) ?>" class="text-[#4E3B2A] me-2">Mark as read</label>
                            <input type="checkbox" name="read" id="read-<?= htmlspecialchars($application['id']) ?>" class="read-checkbox border border-black">
                        </form>
                    <?php endif ?>
                </div>
                <?php if ($application['type'] === 'application') : ?>
                    <div>
                        <h3>Personal Information :</h3>
                    </div>
                    <div class="col-span-3 bg-[#FFF6E8] border border-[#F7E6CA] shadow-md rounded-lg py-4 flex justify-around text-lg">
                        <p>Firstname: <?= htmlspecialchars($application['first_name']) ?></p>
                        <p>Lastname: <?= htmlspecialchars($application['last_name']) ?></p>
                    </div>
                    <div class="col-span-3 bg-[#FFF6E8] border border-[#F7E6CA] shadow-md rounded-lg py-4 flex justify-around">
                        <p>Age: <?= htmlspecialchars($application['age']) ?></p>
                        <p>Date of Birth: <?= htmlspecialchars($application['date_of_birth']) ?></p>
                    </div>
                    <div class="col-span-3 bg-[#FFF6E8] border border-[#F7E6CA] shadow-md rounded-lg py-4 flex justify-around">
                        <p>Address: <?= htmlspecialchars($application['address']) ?></p>
                        <p>Email: <?= htmlspecialchars($application['email']) ?></p>
                    </div>
                    <div class="col-span-3 bg-[#FFF6E8] border border-[#F7E6CA] shadow-md rounded-lg py-4 flex justify-around">
                        <p><?= htmlspecialchars($application['message']) ?></p>
                    </div>
                    <div class="col-span-3 flex justify-end hover:underline">
                        <a href="/hr_hiring/applicants"><strong>Go to Applicants tab...</strong></a>
                    </div>
                <?php endif ?>

                <?php if ($application['type'] === 'job offer') : ?>
                    <div class="col-span-3 flex justify-between ">
                        <h3>Job Offer Information :</h3>
                        <p>Status: <span class="<?= $offer['status'] === 'approved' ? 'text-green-500' : ($offer['status'] === 'rejected' ? 'text-red-500' : '') ?>"><?= $offer['status'] ?></span></p>
                    </div>
                    <div class="border border-[#594423] text-center rounded-lg shadow-md  py-2 px-2">
                        <p class="text-[#594423] text-start">Applicant Name:</p>
                        <p><?= $offer['first_name'] . ' ' . $offer['last_name'] ?></p>
                    </div>
                    <div class="border border-[#594423] text-center rounded-lg shadow-md  py-2 px-2">
                        <p class="text-[#594423] text-start">Postition:</p>
                        <p><?= $offer['position'] ?></p>
                    </div>
                    <div class="border border-[#594423] text-center rounded-lg shadow-md  py-2 px-2">
                        <p class="text-[#594423] text-start">Work Location:</p>
                        <p><?= $offer['work_location'] ?></p>
                    </div>
                    <div class="border border-[#594423] text-center rounded-lg shadow-md  py-2 px-2">
                        <p class="text-[#594423] text-start">Schedule:</p>
                        <p><?= $offer['schedule'] ?></p>
                    </div>
                    <div class="border border-[#594423] text-center rounded-lg shadow-md  py-2 px-2">
                        <p class="text-[#594423] text-start">Time in:</p>
                        <p><?= $offer['time_in'] ?></p>
                    </div>
                    <div class="border border-[#594423] text-center rounded-lg shadow-md  py-2 px-2">
                        <p class="text-[#594423] text-start">Time out:</p>
                        <p><?= $offer['time_out'] ?></p>
                    </div>
                    <div class="border border-[#594423] text-center rounded-lg shadow-md  py-2 px-2">
                        <p class="text-[#594423] text-start">Salary:</p>
                        <p><?= $offer['salary'] ?></p>
                    </div>
                    <div class="border border-[#594423] text-center rounded-lg shadow-md  py-2 px-2">
                        <p class="text-[#594423] text-start">Benefits:</p>
                        <p><?= $offer['benefits'] ?></p>
                    </div>
                    <div class="border border-[#594423] text-center rounded-lg shadow-md  py-2 px-2">
                        <p class="text-[#594423] text-start">Responsibilities:</p>
                        <p><?= $offer['responsibilities'] ?></p>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</main>

<script>
    document.querySelectorAll('.read-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            this.closest('.mark-as-read-form').submit();
        });
    });
</script>

<?php require 'partials/footer.php' ?>