<!-- Wrapper harus relative supaya dropdown nempel ke tombol -->
<div class="relative inline-block">
    <!-- Tombol Lonceng -->
    <button id="notifBtn" class="relative p-2 rounded-full hover:bg-gray-200 transition">
        <i class="fas fa-bell text-[#050C9C] text-2xl"></i>
        <span class="absolute top-1 right-1 bg-red-500 w-2.5 h-2.5 rounded-full"></span>
    </button>

    <!-- Dropdown Notifikasi -->
    <div id="notifDropdown"
        class="hidden absolute right-0 mt-3 w-80 bg-white shadow-lg rounded-lg border border-gray-200 z-50 
    transition-all duration-200 ease-out transform origin-top-right scale-95">
        <div class="px-4 py-2 font-semibold text-gray-700 bg-gray-50 border-b">Notifikasi TU</div>
        <ul class="max-h-64 overflow-y-auto divide-y">
            <li class="px-4 py-3 hover:bg-gray-100 cursor-pointer">
                📄 Surat Tugas: “Kegiatan Sosialisasi Kurikulum”
            </li>
            <li class="px-4 py-3 hover:bg-gray-100 cursor-pointer">
                📨 SK Dosen Tetap baru diterbitkan
            </li>
            <li class="px-4 py-3 hover:bg-gray-100 cursor-pointer">
                ⚠️ Revisi dokumen Portofolio diperlukan
            </li>
        </ul>
        <div class="text-center py-2 bg-gray-50 hover:bg-gray-100 cursor-pointer text-sm text-purple-600 font-semibold">
            Lihat semua
        </div>
    </div>
</div>
<?php /**PATH D:\Proyek 3\devOps\resources\views/components/notification-dropdown.blade.php ENDPATH**/ ?>