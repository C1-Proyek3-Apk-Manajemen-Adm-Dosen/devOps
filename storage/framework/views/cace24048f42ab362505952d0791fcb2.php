<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Dashboard - SiDoRa'); ?></title>

    <?php echo app('Illuminate\Foundation\Vite')('resources/css/app.css'); ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body class="font-[Poppins] bg-white min-h-screen flex flex-col relative text-sm">
    <!-- Navbar -->
    <?php echo $__env->make('partials.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Overlay (muncul pas sidebar dibuka di mobile) -->
    <div id="overlay" class="hidden fixed inset-0 bg-black bg-opacity-40 z-40 md:hidden"></div>

    <div class="flex flex-1 overflow-hidden relative">
        <!-- Sidebar -->
        <?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Main Content -->
        <main class="flex-1 bg-[#E9EBF0] m-4 md:m-8 rounded-3xl p-4 md:p-6 overflow-y-auto transition-all duration-300">
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

    <!-- Overlay hitam transparan -->
    <div id="overlay" class="hidden fixed inset-0 bg-black bg-opacity-40 z-30 md:hidden"></div>

    <!-- Script toggle sidebar -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleSidebar = document.getElementById('toggleSidebar');
        const closeSidebar = document.getElementById('closeSidebar');
        const overlay = document.getElementById('overlay');

        if (toggleSidebar) {
            toggleSidebar.addEventListener('click', () => {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            });
        }

        if (closeSidebar) {
            closeSidebar.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            });
        }
    </script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/modal.js']); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/notif.js']); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/logoutModal.js']); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/loginValidation.js']); ?>
    <?php echo $__env->make('components.modals.logout-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>

</html>
<?php /**PATH D:\Proyek 3\devOps\resources\views/layouts/app.blade.php ENDPATH**/ ?>