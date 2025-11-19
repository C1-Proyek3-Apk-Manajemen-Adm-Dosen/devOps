<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Dashboard - SiDoRa')</title>

  {{-- CSS global (Tailwind) --}}
  @vite('resources/css/app.css')

  {{-- halaman boleh nambah CSS sendiri --}}
  @stack('styles')

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="font-[Poppins] bg-white min-h-screen flex flex-col relative text-sm">
  {{-- Navbar --}}
  @include('partials.navbar')

  {{-- Overlay (muncul pas sidebar dibuka di mobile) --}}
  <div id="overlay" class="hidden fixed inset-0 bg-black bg-opacity-40 z-40 md:hidden"></div>

  <div class="flex flex-1 overflow-hidden relative">
    {{-- Sidebar --}}
    @include('partials.sidebar')

    {{-- Main Content --}}
    <main class="flex-1 bg-[#E9EBF0] m-4 md:m-8 rounded-3xl p-4 md:p-6 overflow-y-auto transition-all duration-300">
      @yield('content')
    </main>
  </div>

  {{-- Script toggle sidebar --}}
  <script>
    const sidebar = document.getElementById('sidebar');
    const toggleSidebar = document.getElementById('toggleSidebar');
    const closeSidebar  = document.getElementById('closeSidebar');
    const overlay       = document.getElementById('overlay');

    toggleSidebar?.addEventListener('click', () => {
      sidebar?.classList.remove('-translate-x-full');
      overlay?.classList.remove('hidden');
    });
    closeSidebar?.addEventListener('click', () => {
      sidebar?.classList.add('-translate-x-full');
      overlay?.classList.add('hidden');
    });
    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
    }
  </script>

  {{-- JS global --}}
  @vite([
    'resources/js/modal.js',
    'resources/js/logoutModal.js',
    'resources/js/loginValidation.js',
    'resources/js/tu/upload-dokumen.js',
    'resources/js/tu/riwayat.js',
    'resources/js/app.js',
    'resources/js/tu/monitoring.js',
    'resources/js/tu/edit-hak-akses.js',
    'resources/js/tu/upload-dokumen.js'
  ])

  {{-- halaman boleh nambah JS sendiri (mis. riwayat.js) --}}
  @stack('scripts')

  @include('components.modals.logout-modal')
</body>
</html>