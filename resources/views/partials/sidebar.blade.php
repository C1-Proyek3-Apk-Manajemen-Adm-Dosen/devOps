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
    <button id="closeSidebar" class="md:hidden self-end mb-4 text-gray-600 hover:text-purple-600">
        <i class="fas fa-times text-xl"></i>
    </button>

    <nav class="w-full mt-2 md:mt-4 space-y-2">
        {{-- ==================== DOSEN ==================== --}}
        @if ($role === 'dosen')
            <a href="{{ url('/dosen/dashboard') }}"
                class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold  transition
                {{ request()->is('dosen/dashboard') ? 'bg-purple-600 text-white' : 'text-black hover:text-purple-600' }}">
                <i class="fas fa-home"></i> Dashboard
            </a>

            <a href="{{ url('/dosen/dokumen') }}"
                class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold  transition
                {{ request()->is('dosen/dokumen') ? 'bg-purple-600 text-white' : 'text-black hover:text-purple-600' }}">
                <i class="fas fa-file-alt"></i> Dokumen Saya
            </a>

            <a href="{{ url('/dosen/upload') }}"
                class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold  transition
                {{ request()->is('dosen/upload') ? 'bg-purple-600 text-white' : 'text-black hover:text-purple-600' }}">
                <i class="fas fa-upload"></i> Upload Dokumen
            </a>

            <a href="{{ url('/dosen/portofolio') }}"
                class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold transition
                {{ request()->is('dosen/portofolio') ? 'bg-purple-600 text-white' : 'text-black hover:text-purple-600' }}">
                <i class="fas fa-folder-open"></i> Portofolio
            </a>

            <a href="{{ url('/dosen/riwayat') }}"
                class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold transition
                {{ request()->is('dosen/riwayat') ? 'bg-purple-600 text-white' : 'text-black hover:text-purple-600' }}">
                <i class="fas fa-history"></i> Riwayat Upload
            </a>


            {{-- ==================== KAPRODI ==================== --}}
        @elseif ($role === 'kaprodi')
            <a href="{{ url('/kaprodi/dashboard') }}"
                class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold  transition
                {{ request()->is('kaprodi/dashboard') ? 'bg-purple-600 text-white' : 'text-black hover:text-purple-600' }}">
                <i class="fas fa-home"></i> Dashboard
            </a>

            <a href="{{ url('/kaprodi/review') }}"
                class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold  transition
                {{ request()->is('kaprodi/review') ? 'bg-purple-600 text-white' : 'text-black hover:text-purple-600' }}">
                <i class="fas fa-search"></i> Review Dokumen
            </a>

            <a href="{{ url('/kaprodi/daftar') }}"
                class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold  transition
                {{ request()->is('kaprodi/daftar') ? 'bg-purple-600 text-white' : 'text-black hover:text-purple-600' }}">
                <i class="fas fa-table"></i> Daftar Dokumen
            </a>

            {{-- ==================== TU ==================== --}}
        @elseif ($role === 'tu')
            <a href="{{ url('/tu/dashboard') }}"
                class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold  transition
                {{ request()->is('tu/dashboard') ? 'bg-purple-600 text-white' : 'text-black hover:text-purple-600' }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="{{ url('/tu/dokumen-saya') }}"
                class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold  transition
                {{ request()->is('tu/dokumen-saya') ? 'bg-purple-600 text-white' : 'text-black hover:text-purple-600' }}">
                <i class="fas fa-users-cog"></i> Dokumen Saya
            </a>
            <a href="{{ url('/tu/upload-dokumen') }}"
                class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold  transition
                {{ request()->is('tu/upload-dokumen') ? 'bg-purple-600 text-white' : 'text-black hover:text-purple-600' }}">
                <i class="fas fa-file-alt"></i> Upload Dokumen
            </a>
            <a href="{{ url('/tu/riwayat-upload') }}"
                class="flex items-center gap-3 px-6 py-3 rounded-full mx-2 font-semibold transition
                {{ request()->is('tu/riwayat-upload') ? 'bg-purple-600 text-white' : 'text-black hover:text-purple-600' }}">
                <i class="fas fa-file-alt"></i> Riwayat Upload
            </a>
        @endif
    </nav>
</aside>
