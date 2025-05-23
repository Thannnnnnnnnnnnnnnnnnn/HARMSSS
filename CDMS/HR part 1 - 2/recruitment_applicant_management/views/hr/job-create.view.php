<?php require 'partials/head.php' ?>
<?php require 'partials/hr/navbar.php' ?>

<main class="max-w-4xl mx-auto mt-8 p-8 flex-grow w-full">
    <nav class="mb-4 text-right">
        <ul>
            <li><a href="/hr/" class="text-[#594423] hover:bg-[#594423] hover:text-white border border-[#594423] py-2 px-4 rounded-lg transition shadow-lg">Go Back...</a></li>
        </ul>
    </nav>
    <h2 class="text-3xl font-bold text-[#4e3b2a] mb-5">Post a Job</h2>

    <form class="bg-[#FFF6E8] p-8 rounded-lg shadow-lg border border-[#594423] grid grid-cols-1 md:grid-cols-2 gap-6" method="post">
        <div>
            <label class="block text-[#594423] font-medium mb-1">Job Title</label>
            <input type="text" class="w-full p-3 border border-[#594423] rounded-lg focus:ring-2 focus:ring-[#594423]" placeholder="Enter job title" name="job_title" value="<?= $_POST['job_title'] ?? '' ?>" required>
            <?php if (empty($_POST['job_title'])) : ?>
                <p class="text-red-500 text-sm ps-3" id="jobTitleError"><?= $errors['job_title'] ?? '' ?></p>
            <?php endif ?>
        </div>
        <div>
            <label class="block text-[#594423] font-medium mb-1">Location</label>
            <input type="text" class="w-full p-3 border border-[#594423] rounded-lg focus:ring-2 focus:ring-[#594423]" placeholder="Enter job location" name="location" value="<?= $_POST['location'] ?? '' ?>" required>
            <?php if (empty($_POST['location'])) : ?>
                <span class="text-red-500 text-sm ps-3" id="locationError"><?= $errors['location'] ?? '' ?></span>
            <?php endif ?>
        </div>
        <div class="col-span-2">
            <label class="block text-[#594423] font-medium mb-1">Company</label>
            <input type="text" class="w-full p-3 border border-[#594423] rounded-lg focus:ring-2 focus:ring-[#594423]" value="Avalon" name="company" value="<?= $_POST['company'] ?? '' ?>" readonly required>
        </div>
        <div class="col-span-1">
            <label class="block text-[#594423] font-medium mb-1">Department</label>
            <select type="text" class="w-full p-3 border border-[#594423] rounded-lg focus:ring-2 focus:ring-[#594423]" name="department_id" required>
                <?php foreach ($departments as $department): ?>
                    <option value="<?= $department['department_id'] ?>"><?= $department['dept_name'] ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-span-1">
            <label class="block text-[#594423] font-medium mb-1">Role</label>
            <select type="text" class="w-full p-3 border border-[#594423] rounded-lg focus:ring-2 focus:ring-[#594423]" name="role_id" required>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['role_id'] ?>"><?= $role['role_title'] ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-[#594423] font-medium mb-1">Description</label>
            <textarea class="w-full p-3 border border-[#594423] rounded-lg focus:ring-2 focus:ring-[#594423]" placeholder="Enter job description" name="description" required><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            <?php if (empty($_POST['description'])) : ?>
                <span class="text-red-500 text-sm ps-3" id="descriptionError"><?= $errors['description'] ?? '' ?></span>
            <?php endif ?>
        </div>
        <div class="md:col-span-2">
            <label class="block text-[#594423] font-medium mb-1">Requirements</label>
            <textarea class="w-full p-3 border border-[#594423] rounded-lg focus:ring-2 focus:ring-[#594423]" placeholder="Enter job requirements" name="requirements" required><?= isset($_POST['requirements']) ? htmlspecialchars($_POST['requirements']) : ''; ?></textarea>
            <?php if (empty($_POST['requirements'])) : ?>
                <span class="text-red-500 text-sm ps-3" id="requirementsError"><?= $errors['requirements'] ?? '' ?></span>
            <?php endif ?>
        </div>
        <div>
            <label class="block text-[#594423] font-medium mb-1">Salary</label>
            <input type="number" class="w-full p-3 border border-[#594423] rounded-lg focus:ring-2 focus:ring-[#594423]" placeholder="Enter salary range" name="salary" value="<?= $_POST['salary'] ?? '' ?>" required>
            <?php if (empty($_POST['salary'])) : ?>
                <span class="text-red-500 text-sm ps-3" id="salaryError"><?= $errors['salary'] ?? '' ?></span>
            <?php endif ?>
        </div>
        <div>
            <label class="block text-[#594423] font-medium mb-1">Job Type</label>
            <select class="w-full p-3 border border-[#594423] rounded-lg focus:ring-2 focus:ring-[#594423]" name="employment_type" required>
                <option value="full-time">Full-time</option>
                <option value="part-time">Part-time</option>
            </select>
        </div>
        <div class="md:col-span-2 flex justify-center">
            <button type="submit" class="border border-[#594423] text-[#594423] hover:text-white px-6 py-3 rounded-lg font-semibold hover:bg-[#594423] transition-all focus:ring-2 focus:ring-[#594423]">Post Job</button>
        </div>
    </form>
</main>
<?php require 'partials/footer.php' ?>