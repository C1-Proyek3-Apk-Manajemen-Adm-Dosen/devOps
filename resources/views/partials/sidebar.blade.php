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

            <a href="{{ url('/kaprodi/review') }}"
                class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold  transition
                {{ request()->is('kaprodi/review') ? 'bg-[#050C9C] text-white' : 'text-black hover:text-[#050C9C]' }}">
                <i class="fas fa-search"></i> Review Dokumen
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
