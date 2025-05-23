     
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
   <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>  
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
           body{
        font-family: "cinzel",Georgia;
  
         }
         td{
            padding: 7px;
         }
         select.custom-select option {
           background-color: #FFF6E8;
             color: #594423; 
            font-weight: 600;
             padding: 10px;
            }
           

            select.custom-select option:hover {
          background-color: #f1ddbe;
             color: #3e2d20;
           }
         .custom-select {

    color: black;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    padding: 0.5rem;
    width: 100%;
    font-size: 0.875rem; 
    transition: all 0.3s ease;
    appearance: none; 

  }
        #dime{
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        #dime::-webkit-scrollbar{
            display: none;
        }
  
  .custom-select:focus {
   
    outline: none;
    border-color: #f7e6ca;
    box-shadow: 0 0 0 3px rgba(247, 230, 202, 0.5); 
  }
.custom-select option {
    background: #fff;
    color: #333;
    font-weight: 500;
  }
        .sidebar-collapsed {
            width: 85px;
        }

        .sidebar-expanded {
            width: 320px;
        }

        .sidebar-collapsed .menu-name span,
        .sidebar-collapsed .menu-name .arrow {
            display: none;
        }

        .sidebar-collapsed .menu-name i {
            margin-right: 0;
        }

        .sidebar-collapsed .menu-drop {
            display: none;
        }

        .sidebar-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            position: fixed;
            inset: 0;
            z-index: 40;
            display: none;
        }

        .sidebar-overlay.active {
            display: block;
        }

        .close-sidebar-btn {
            display: none;
        }

        @media (max-width: 968px) {
            .sidebar {
                position: fixed;
                left: -100%;
                transition: left 0.3s ease-in-out;
            }

            .sidebar.mobile-active {
                left: 0;
            }

            .main {
                margin-left: 0 !important;
            }

            .close-sidebar-btn {
                display: block;
            }
        }

        .menu-name {
            position: relative;
            overflow: hidden;
        }

        .menu-name::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            height: 2px;
            width: 0;
            background-color: #4E3B2A;
            transition: width 0.3s ease;
        }

        .menu-name:hover::after {
            width: 100%;
        }

        body {
            font-family: "cinzel", Georgia;
        }

        td {
            padding: 1px;
        }

        .sidebar-collapsed {
            width: 85px;
        }

        .sidebar-expanded {
            width: 320px;
        }

        .sidebar-collapsed .menu-name span,
        .sidebar-collapsed .menu-name .arrow {
            display: none;
        }

        .sidebar-collapsed .menu-name i {
            margin-right: 0;
        }

        .sidebar-collapsed .menu-drop {
            display: none;
        }

        .sidebar-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            position: fixed;
            inset: 0;
            z-index: 40;
            display: none;
        }

        .sidebar-overlay.active {
            display: block;
        }

        .close-sidebar-btn {
            display: none;
        }

        @media (max-width: 968px) {
            .sidebar {
                position: fixed;
                left: -100%;
                transition: left 0.3s ease-in-out;
            }

            .sidebar.mobile-active {
                left: 0;
            }

            .main {
                margin-left: 0 !important;
            }

            .close-sidebar-btn {
                display: block;
            }
        }

        .menu-name {
            position: relative;
            overflow: hidden;
        }

        .menu-name::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            height: 2px;
            width: 0;
            background-color: #4E3B2A;
            transition: width 0.3s ease;
        }

        .menu-name:hover::after {
            width: 100%;
        }
    </style>
</head>

<body>
 <?php if (isset($_SESSION['success_message'])): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Success!',
    text: <?= json_encode($_SESSION['success_message']) ?>,
    confirmButtonColor: '#594423',
    background: '#FFF6E8',
    color: '#594423',
});
</script>
<?php unset($_SESSION['success_message']); endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Oops...',
    text: <?= json_encode($_SESSION['error_message']) ?>,
    confirmButtonColor: '#d33',
});
</script>
<?php unset($_SESSION['error_message']); endif; ?>

<?php if (isset($_SESSION['show_insert_modal'])): ?>
<script>

document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById('insertModal');
    if (modal) modal.classList.remove('hidden');
});
</script>
<?php unset($_SESSION['show_insert_modal']); endif; ?>
    <div class="flex min-h-screen w-full">