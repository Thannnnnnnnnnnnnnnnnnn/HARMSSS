<?php require '../partials/admin/head.php' ?>

<div class="flex min-h-screen w-full text-[#594423]">
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <?php require '../partials/admin/sidebar.php' ?>

    <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
        <?php require '../partials/admin/navbar.php' ?>
        <main class="px-2 py-5">
            <div class="text-end pe-3">
                <a href="/admin/applicants" class="hover:underline text-blue-500"><i class="fa-solid fa-arrow-left"></i> Back to applicants page</a>
            </div>
            <div class="container mx-auto mt-5 mb-5 p-5 bg-white shadow-lg rounded-lg">
                <div class="bg-[#594423] text-white py-3 rounded-lg shadow-lg ps-3 mb-4">
                    <h1 class="text-2xl font-semibold">Update Applicant Details</h1>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="grid grid-cols-2 md:grid-cols-2 gap-4">
                        <input type="hidden" name="applicant_id" value="<?= htmlspecialchars($applicant['applicant_id']) ?>" class="input">
                        <div class=" my-4 col-span-1 flex flex-col items-center">
                            <label for="first_name">First Name:</label>
                            <input type="text" placeholder="Type here" name="first_name" value="<?= htmlspecialchars($applicant['first_name']) ?>" class="input" id="first_name" />
                            <?php if (isset($errors['first_name'])): ?>
                                <div class="text-red-400 text-xs">
                                    <?= $errors['first_name'] ?>
                                </div>
                            <?php endif ?>
                        </div>
                        <div class=" my-4 col-span-1 flex flex-col items-center">
                            <label for="last_name">Last Name:</label>
                            <input type="text" placeholder="Type here" name="last_name" value="<?= htmlspecialchars($applicant['last_name']) ?>" class="input" id="last_name" />
                            <?php if (isset($errors['last_name'])): ?>
                                <div class="text-red-400 text-xs">
                                    <?= $errors['last_name'] ?>
                                </div>
                            <?php endif ?>
                        </div>
                        <div class=" my-4 col-span-1 flex flex-col items-center">
                            <label for="age">Age:</label>
                            <input type="number" name="age" value="<?= htmlspecialchars($applicant['age']) ?>" class="input" id="age" />
                            <?php if (isset($errors['age'])): ?>
                                <div class="text-red-400 text-xs">
                                    <?= $errors['age'] ?>
                                </div>
                            <?php endif ?>
                        </div>
                        <div class=" my-4 col-span-1 flex flex-col items-center">
                            <label for="date_of_birth">Date of Birth:</label>
                            <input type="date" name="date_of_birth" value="<?= htmlspecialchars($applicant['date_of_birth']) ?>" class="input" id="date_of_birth" />
                            <?php if (isset($errors['date_of_birth'])): ?>
                                <div class="text-red-400 text-xs">
                                    <?= $errors['date_of_birth'] ?>
                                </div>
                            <?php endif ?>
                        </div>
                        <div class=" my-4 col-span-1 flex flex-col items-center">
                            <label for="contact_number">Contact Number:</label>
                            <input type="tel" placeholder="Type here" name="contact_number" value="<?= htmlspecialchars($applicant['contact_number']) ?>" class="input" id="contact_number" />
                            <?php if (isset($errors['contact_number'])): ?>
                                <div class="text-red-400 text-xs">
                                    <?= $errors['contact_number'] ?>
                                </div>
                            <?php endif ?>
                        </div>
                        <div class="my-4 col-span-1 flex flex-col items-center">
                            <label for="email">Email:</label>
                            <input type="email" placeholder="Type here" name="email" value="<?= htmlspecialchars($applicant['email']) ?>" class="input" id="email" />
                            <?php if (isset($errors['email'])): ?>
                                <div class="text-red-400 text-xs">
                                    <?= $errors['email'] ?>
                                </div>
                            <?php endif ?>
                        </div>
                        <div class="col-span-2 text-center">
                            <div>
                                <button type="button" id="submitBtn" class="btn hover:bg-[#594423] hover:text-white border border-[#594423] shadow-md">Update</button>
                            </div>
                        </div>
                    </div>
                </form>
        </main>
    </div>
</div>

<script>
    $(document).ready(function() {
        let isValid = true;
        $('#submitBtn').click(function() {
            $('input').each(function() {
                if ($(this).val() === '') {
                    isValid = false;
                    $(this).addClass('border-red-500');
                } else {
                    $(this).removeClass('border-red-500');
                }
            });

            if (isValid) {
                swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Applicant details updated successfully.',
                })
                $('form').submit();
            } else {
                swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please fill in all fields.',
                    timer: 1800,
                });
            }
        });
    });
</script>
<?php require '../partials/admin/footer.php' ?>