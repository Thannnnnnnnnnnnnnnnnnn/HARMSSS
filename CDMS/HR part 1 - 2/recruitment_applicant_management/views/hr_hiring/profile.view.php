<?php require 'partials/head.php' ?>
<?php require 'partials/hr_hiring/navbar.php' ?>
<main class="w-full mx-auto p-6 flex-grow flex flex-col items-center">
    <div class="w-6xl rounded-lg p-5 shadow-xl">
        <h1 class="bg-[#594423] text-white text-lg py-4 px-2.5 rounded-lg"><i class="fa-solid fa-user mx-5"></i>User Settings</h1>
        <div class="mt-5">
            <form method="post" id="update">
                <div class="grid grid-cols-2 gap-2">
                    <div class="flex flex-col items-center">
                        <label for="">First Name</label>
                        <input type="text" name="first_name" id="first_name" class="input text-center" value="<?= !empty(htmlspecialchars($user['first_name'])) ? htmlspecialchars($user['first_name']) : htmlspecialchars($_POST['first_name'] ?? '') ?>" placeholder="Juan" required>
                    </div>
                    <div class="flex flex-col items-center">
                        <label for="">Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="input text-center" value="<?= !empty(htmlspecialchars($user['last_name'])) ? htmlspecialchars($user['last_name']) : htmlspecialchars($_POST['last_name'] ?? '') ?>" placeholder="Delacruz" required>
                    </div>
                    <div class="flex flex-col items-center">
                        <label for="">Username</label>
                        <input type="text" name="username" id="username" class="input text-center" value="<?= htmlspecialchars($user['username']) ?>" required>
                        <?php if (!empty($errors['username'])): ?>
                            <p class="text-red-500 text-xs"><?= $errors['username'] ?></p>
                        <?php endif ?>
                    </div>
                    <div class="flex flex-col items-center">
                        <label for="">Email</label>
                        <input type="email" name="email" id="email" class="input text-center" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                        <p class="text-gray-500 text-xs">email cannot be updated.</p>
                    </div>
                    <div class="col-span-2 text-center">
                        <button type="button" id="submitBtn" class="btn mt-5 bg-blue-500 hover:bg-blue-600 text-white">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>
<script>
    $('#submitBtn').on('click', function(e) {
        e.preventDefault();
        let isValid = true;
        $('input[required]').each(function() {
            if ($(this).val().trim() === '') {
                isValid = false;
                $(this).addClass('border-red-500');
            } else {
                $(this).removeClass('border-red-500');
            }
            if (isValid) {
                Swal.fire({
                    icon: 'success',
                    title: 'Profile updated!',
                    text: 'Profile Updated Successfully',
                    timer: 2000,
                })
                $('#update').submit();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'please fill all inputs',
                    timer: 1800,
                })
            }
        });
    })
</script>
<?php require 'partials/footer.php' ?>