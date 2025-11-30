@php
// ambil segmen pertama dari URL (misal: /dosen/dashboard → 'dosen')
$role = request()->segment(1);
$current = request()->path(); // untuk cek halaman aktif
@endphp

<aside id="sidebar"
    class="w-64 bg-white flex flex-col items-start px-6 py-4 fixed md:static top-[72px] md:top-0 left-0 h-[calc(100%-4.5rem)] md:h-auto 
           -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-40 
           rounded-2xl md:rounded-none shadow-lg md:shadow-none">

    <!-- Tombol Tutup (muncul hanya di mobile) -->
    <button id="closeSidebar" class="md:hidden self-end mb-4 text-gray-600 hover:text-[#050C9C]">
        <i class="fas fa-times text-xl"></i>
    </button>

    <nav class="w-full mt-2 md:mt-4 space-y-2">
        {{--ADMINISTRATOR --}}
        @if(Auth::check() && Auth::user()->role === 'administrator')

        {{-- Badge Administrator Panel --}}
        <div class="w-full mb-4 px-2">
            <span class="inline-block bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                <i class="fas fa-shield-alt"></i> Administrator Panel
            </span>
        </div>

        {{-- Dashboard Admin --}}
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold  transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#050C9C] text-white' : 'text-black hover:text-[#050C9C]' }}">
            <i class="fa-solid fa-gauge-high text-xl w-6 text-center"></i>
            <span class="font-semibold">Dashboard</span>
        </a>

        {{-- Manajemen Pengguna --}}
        <a href="{{ route('admin.users.index') }}"
            class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold  transition {{ request()->routeIs('admin.users.*') ? 'bg-[#050C9C] text-white' : 'text-black hover:text-[#050C9C]' }}">
            <i class="fa-solid fa-users-cog text-xl w-6 text-center"></i>
            <span class="font-semibold">Kelola Pengguna</span>
        </a>

        {{-- Tambah Pengguna (Quick Access) --}}
        <a href="{{ route('admin.users.create') }}"
            class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold  transition {{ request()->routeIs('admin.users.create') ? 'bg-[#050C9C] text-white' : 'text-black hover:text-[#050C9C]' }}">
            <i class="fa-solid fa-user-plus text-xl w-6 text-center"></i>
            <span class="font-semibold">Tambah Pengguna</span>
        </a>

        {{-- Divider --}}
        <div class="border-t border-white/20 my-2"></div>
        @endif
        {{-- ==================== DOSEN ==================== --}}
        @if ($role === 'dosen')
        <a href="{{ url('/dosen/dashboard') }}"
            class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold  transition
                {{ request()->is('dosen/dashboard') ? 'bg-[#050C9C] text-white' : 'text-black hover:text-[#050C9C]' }}">
            <i class="fas fa-home"></i> Dashboard
        </a>

        <a href="{{ url('/dosen/dokumen') }}"
            class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold  transition
                {{ request()->is('dosen/dokumen') ? 'bg-[#050C9C] text-white' : 'text-black hover:text-[#050C9C]' }}">
            <i class="fas fa-file-alt"></i> Dokumen Saya
        </a>

        <a href="{{ url('/dosen/upload') }}"
            class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold  transition
                {{ request()->is('dosen/upload') ? 'bg-[#050C9C] text-white' : 'text-black hover:text-[#050C9C]' }}">
            <i class="fas fa-upload"></i> Upload Dokumen
        </a>

        <a href="{{ url('/dosen/portofolio') }}"
            class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold transition
                {{ request()->is('dosen/portofolio') ? 'bg-[#050C9C] text-white' : 'text-black hover:text-[#050C9C]' }}">
            <i class="fas fa-folder-open"></i> Portofolio
        </a>

        <a href="{{ url('/dosen/riwayat') }}"
            class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold transition
                {{ request()->is('dosen/riwayat') ? 'bg-[#050C9C] text-white' : 'text-black hover:text-[#050C9C]' }}">
            <i class="fas fa-history"></i> Riwayat Upload
        </a>


        {{-- ==================== KAPRODI ==================== --}}
        @elseif ($role === 'kaprodi')
        <a href="{{ url('/kaprodi/dashboard') }}"
            class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold  transition
                {{ request()->is('kaprodi/dashboard') ? 'bg-[#050C9C] text-white' : 'text-black hover:text-[#050C9C]' }}">
            <i class="fas fa-home"></i> Dashboard
        </a>

        <a href="{{ url('/kaprodi/daftar') }}"
            class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold  transition
                {{ request()->is('kaprodi/daftar') ? 'bg-[#050C9C] text-white' : 'text-black hover:text-[#050C9C]' }}">
            <i class="fas fa-table"></i> Daftar Dokumen
        </a>

        {{-- ==================== TU ==================== --}}
        @elseif ($role === 'tu')
        <a href="{{ url('/tu/dashboard') }}"
            class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold  transition
                {{ request()->is('tu/dashboard') ? 'bg-[#050C9C] text-white' : 'text-black hover:text-[#050C9C]' }}">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="{{ url('/tu/monitoring') }}"
            class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold  transition
                {{ request()->is('tu/monitoring') ? 'bg-[#050C9C] text-white' : 'text-black hover:text-[#050C9C]' }}">
            <i class="fas fa-users-cog"></i> Monitoring Dokumen
        </a>
        <a href="{{ url('/tu/upload-dokumen') }}"
            class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold  transition
                {{ request()->is('tu/upload-dokumen') ? 'bg-[#050C9C] text-white' : 'text-black hover:text-[#050C9C]' }}">
            <i class="fas fa-file-alt"></i> Upload Dokumen
        </a>
        <a href="{{ url('/tu/riwayat-upload') }}"
            class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold transition
                {{ request()->is('tu/riwayat-upload') ? 'bg-[#050C9C] text-white' : 'text-black hover:text-[#050C9C]' }}">
            <i class="fas fa-file-alt"></i> Riwayat Upload
        </a>
        @endif
        <!-- Tombol Logout -->
        <div class="mt-auto mb-6 px-4">
            <button id="openLogoutModal"
                class="w-full flex items-center gap-3 justify-center border border-gray-300 hover:bg-red-100 text-red-500 font-semibold py-3 rounded-full transition duration-200">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </button>
        </div>

    </nav>
</aside>