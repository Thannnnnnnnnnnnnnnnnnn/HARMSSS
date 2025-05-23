<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - SOCREG</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            <div class="text-center">
                <i class="fas fa-exclamation-circle text-red-500 text-5xl mb-4"></i>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Error</h1>
                <p class="text-gray-600 mb-6">
                    <?php echo isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'An error occurred.'; ?>
                </p>
                <a href="javascript:history.back()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Go Back
                </a>
            </div>
        </div>
    </div>
</body>
</html> 