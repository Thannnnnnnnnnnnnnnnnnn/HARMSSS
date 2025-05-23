<?php require 'partials/head.php' ?>
<div class="z-50 min-h-screen w-full flex items-center justify-center bg-[#F7E6CA] p-4">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-xl shadow-md border border-gray-200">
        <?php if ($success ?? '' == true) : ?>
            <div role="alert" class="alert alert-success">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Registration Successfull. </span>
                <a href="/" class="btn rounded-2xl">login page</a>
            </div>
        <?php endif ?>
        <div class="flex justify-center">
            <img src="/img/Logo-Name.png" alt="Avalon Logo" class="w-48 md:w-64">
        </div>
        <h2 class="text-center text-2xl font-semibold text-[#594423]">
            Registration
        </h2>
        <form method="POST" class="space-y-4">
            <div class="grid grid-cols-2 md:grid-cols-2 sm:grid-cols-1 gap-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
                        first_name
                    </label>
                    <input
                        type="text"
                        name="first_name"
                        id="first_name"
                        placeholder="Juan"
                        value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>"
                        class="w-full py-2 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition duration-150 ease-in-out"
                        required>
                    <?php if ($errors['first_name'] ?? '') : ?>
                        <div>
                            <span class="text-xs text-red-500"><?= $errors['first_name'] ?></span>
                        </div>
                    <?php endif ?>
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                        last_name
                    </label>
                    <input
                        type="text"
                        name="last_name"
                        id="last_name"
                        placeholder="Dela Cruz"
                        value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>"
                        class="w-full py-2 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition duration-150 ease-in-out"
                        required>
                    <?php if ($errors['last_name'] ?? '') : ?>
                        <div>
                            <span class="text-xs text-red-500"><?= $errors['last_name'] ?></span>
                        </div>
                    <?php endif ?>
                </div>
            </div>
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
                    Username
                </label>
                <input
                    type="text"
                    name="username"
                    id="username"
                    placeholder="Juan Dela Cruz"
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                    class="w-full py-2 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition duration-150 ease-in-out"
                    required>
            </div>
            <?php if ($errors['username'] ?? '') : ?>
                <div>
                    <span class="text-xs text-red-500"><?= $errors['username'] ?></span>
                </div>
            <?php endif ?>
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
                    type="button"
                    class="w-full py-2.5 px-4 bg-[#594423] hover:bg-[#7e6b4c] text-white font-semibold rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition duration-150 ease-in-out" id="registerBtn">
                    Register
                </button>
            </div>
            <div class="text-center text-sm text-gray-500">
                <p>Already have an account? <a href="/" class="text-blue-500">Click here.</a></p>
            </div>
        </form>
    </div>
</div>
<script src="/js/passwordBtn.js"></script>
<script src="/js/registerAlert.js"></script>
<?php require 'partials/footer.php' ?>