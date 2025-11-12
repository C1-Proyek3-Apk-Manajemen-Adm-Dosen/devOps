<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard - SiDoRa')</title>

    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @stack('styles')

</head>

<body class="font-[Poppins] bg-white min-h-screen flex flex-col relative text-sm">
    <!-- Navbar -->
    @include('partials.navbar')

    <!-- Overlay (muncul pas sidebar dibuka di mobile) -->
    <div id="overlay" class="hidden fixed inset-0 bg-black bg-opacity-40 z-40 md:hidden"></div>

    <div class="flex flex-1 overflow-hidden relative">
        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Main Content -->
        <main class="flex-1 bg-[#E9EBF0] m-4 md:m-8 rounded-3xl p-4 md:p-6 overflow-y-auto transition-all duration-300">
            @yield('content')
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
    @vite(['resources/js/modal.js'])
    @vite(['resources/js/notif.js'])
    @vite(['resources/js/logoutModal.js'])
    @vite(['resources/js/loginValidation.js'])
    @vite(['resources/js/tu/upload-dokumen.js'])
    @vite(['resources/js/tu/edit-hak-akses.js'])
    @vite(['resources/js/tu/monitoring.js'])
    @include('components.modals.logout-modal')

    @stack('scripts')

</body>

</html>