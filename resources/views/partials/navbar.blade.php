<nav class="w-full bg-white mt-4 flex items-center justify-between flex-wrap px-4 md:px-6 py-3">
    <!-- Left Section: Logo + Tombol Sidebar -->
    <div class="flex items-center gap-2 flex-shrink-0">
        <!-- Tombol Toggle Sidebar (muncul hanya di mobile) -->
        <button id="toggleSidebar" class="md:hidden mr-2 text-[#050C9C]">
            <i class="fas fa-bars text-2xl"></i>
        </button>

        <!-- Logo -->
        <div class="flex flex-col ml-4">
            <h1 class="text-2xl md:text-4xl font-bold text-[#050C9C] leading-none">SiDoRa</h1>
            <p class="hidden md:block text-xs text-gray-500 leading-none">Sistem Dokumen & Arsip Dosen</p>
        </div>
    </div>

    <!-- Middle Section: Search + Notification -->
    <div
        class="flex items-center gap-3 flex-1 justify-center order-last md:order-none w-full md:w-auto mt-3 md:mt-0 relative">
        <!-- Input Search -->
        <input type="text" placeholder="Search"
            class="w-full md:w-80 border border-gray-300 rounded-full px-5 py-2 text-sm focus:ring-2 focus:ring-purple-400 focus:outline-none transition" />

        <!-- Notifikasi -->
        @auth
            @if (Auth::user()->role === 'tu')
                <x-notification.notification-tu />
            @elseif (Auth::user()->role === 'dosen')
                <x-notification.notification-dosen />
            @elseif (Auth::user()->role === 'koordinator')
                <x-notification.notification-koordinator />
            @endif
        @endauth

    </div>

    <!-- Right Section: Profile + Logout -->
    <div class="flex items-center gap-4 flex-shrink-0">
        <!-- Profil Dinamis -->
        @auth
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-blue-100 text-[#050C9C] font-bold rounded-full flex items-center justify-center uppercase">
                    {{ substr(Auth::user()->nama_lengkap, 0, 1) }}
                </div>
                <div class="flex flex-col leading-tight">
                    <p class="text-xs text-gray-400">Hi,</p>
                    <p class="text-sm font-semibold">
                        <span class="text-[#050C9C]">{{ strtoupper(Auth::user()->role) }}</span>
                        <span class="text-gray-700">{{ Auth::user()->nama_lengkap }}</span>
                    </p>
                </div>
            </div>
        @else
            <p class="text-sm text-gray-500">Guest</p>
        @endauth
    </div>
</nav>
