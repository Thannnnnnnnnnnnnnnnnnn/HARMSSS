<?php require '../partials/head.php' ?>
<div class="z-50 min-h-screen w-full flex items-center justify-center bg-[#F7E6CA] p-4">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-xl shadow-md border border-gray-200">
        <?php if (isset($_SESSION['error'])) : ?>
            <div role="alert" class="alert alert-error mb-5">
                <i class="fa-regular fa-circle-xmark text-xl"></i>
                <span>You must be logged in before you can apply</span>
            </div>
        <?php endif ?>
        <div class="flex justify-center">
            <img src="../img/Logo-Name.png" alt="Avalon Logo" class="w-48 md:w-64">
        </div>
        <h2 class="text-center text-2xl font-semibold text-[#594423]">
            Registration
        </h2>
        <form method="POST" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email
                </label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    placeholder="you@example.com"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    class="w-full py-2 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition duration-150 ease-in-out"
                    required>
            </div>
            <?php if ($errors['email'] ?? '') : ?>
                <div>
                    <span class="text-xs text-red-500"><?= $errors['email'] ?></span>
                </div>
            <?php endif ?>
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        value="<?= htmlspecialchars($_POST['password'] ?? '') ?>"
                        class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:rin
                        g-[#4E3B2A] focus:border-[#4E3B2A]"
                        required>
                    <button
                        type="button"
                        id="togglePasswordVisibility"
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-sm text-gray-500 hover:text-gray-700 focus:outline-none"
                        aria-label="Toggle password visibility">
                        Show
                    </button>
                </div>
                <?php if ($errors['password'] ?? '') : ?>
                    <div class="mt-1">
                        <span class="text-xs text-red-500"><?= $errors['password'] ?></span>
                    </div>
                <?php endif ?>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <div class="relative">
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="••••••••"
                        value="<?= htmlspecialchars($_POST['confirm_password'] ?? '') ?>"
                        class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:rin
                        g-[#4E3B2A] focus:border-[#4E3B2A]"
                        required>
                    <button
                        type="button"
                        id="ctogglePasswordVisibility"
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-sm text-gray-500 hover:text-gray-700 focus:outline-none"
                        aria-label="Toggle password visibility">
                        Show
                    </button>
                </div>
                <?php if ($errors['password'] ?? '') : ?>
                    <div class="mt-1">
                        <span class="text-xs text-red-500"><?= $errors['password'] ?></span>
                    </div>
                <?php endif ?>
            </div>
            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full py-2.5 px-4 bg-[#594423] hover:bg-[#7e6b4c] text-white font-semibold rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition duration-150 ease-in-out" id="registerBtn">
                    Register
                </button>
            </div>
            <div class="text-center text-sm text-gray-500">
                <p>Already have an account? <a href="index.php" class="text-blue-500">Click here.</a></p>
            </div>
        </form>
    </div>
</div>
<script src="../js/passwordBtn.js"></script>
<!-- <script src="../js/registerAlert.js"></script> -->
<?php require '../partials/footer.php' ?>