<!-- resources/views/components/tu/upload-modals-tu.blade.php -->

<!-- CDN Flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div id="uploadModal"
    class="hidden fixed inset-0 flex items-center justify-center z-50 bg-black/40 backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl p-8 relative">

        <!-- Header dengan Tombol Tutup -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Upload Dokumen</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Form Upload -->
        <form id="uploadForm" method="POST" action="{{ route('tu.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Grid 2 Kolom -->
            <div class="grid grid-cols-2 gap-6 mb-6">
                
                <!-- Kolom Kiri -->
                <div class="space-y-4">
                    <!-- Judul Dokumen -->
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">Judul Dokumen</label>
                        <input type="text" name="judul"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#050C9C] focus:ring-2 focus:ring-[#050C9C]/20 outline-none transition"
                            placeholder="Masukkan judul dokumen" required>
                    </div>

                    <!-- Nomor Dokumen -->
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">Nomor Dokumen</label>
                        <input type="text" name="nomor_dokumen"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#050C9C] focus:ring-2 focus:ring-[#050C9C]/20 outline-none transition"
                            placeholder="Masukkan nomor dokumen">
                    </div>

                    <!-- Tanggal Terbit dengan Flatpickr -->
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">Tanggal Terbit</label>
                        <input type="text" 
                               name="tanggal_terbit" 
                               id="tanggalTerbit"
                               placeholder="Pilih atau ketik tanggal (dd/mm/yyyy)"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#050C9C] focus:ring-2 focus:ring-[#050C9C]/20 outline-none transition"
                               required>
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">Pilih Kategori</label>
                        <select name="kategori_id"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#050C9C] focus:ring-2 focus:ring-[#050C9C]/20 outline-none transition appearance-none bg-white"
                            required>
                            <option value="" disabled selected>Pilih kategori dokumen</option>
                            @foreach ($kategoris as $kategori)
                                <option value="{{ $kategori->kategori_id }}">{{ $kategori->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="space-y-4">
                    <!-- Upload File dengan Design Baru -->
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">Upload File</label>
                        <div class="relative">
                            <input type="file" name="file" accept=".pdf,.doc,.docx,.jpg,.png" id="fileInput"
                                class="hidden" required>
                            <div onclick="document.getElementById('fileInput').click()"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 transition cursor-pointer flex items-center justify-between">
                                <span class="text-gray-500 text-sm truncate" id="fileLabel">No File Choosen</span>
                                <button type="button"
                                    class="px-4 py-2 bg-[#050C9C] hover:bg-[#040a7a] text-white text-sm rounded-lg transition font-medium flex-shrink-0 ml-2">
                                    Choose File
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">Deskripsi</label>
                        <textarea name="deskripsi" rows="4"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#050C9C] focus:ring-2 focus:ring-[#050C9C]/20 outline-none transition resize-none"
                            placeholder="Tulis deskripsi singkat dokumen..." required></textarea>
                    </div>

                    <!-- Hak Akses (Multi-Select) -->
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">Hak Akses</label>
                        <div class="relative">
                            <div id="hakAksesDropdown"
                                onclick="toggleHakAksesDropdown()"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#050C9C] focus:ring-2 focus:ring-[#050C9C]/20 outline-none transition appearance-none bg-white cursor-pointer flex items-center justify-between">
                                <span id="hakAksesLabel" class="text-gray-500 text-sm">Pilih pengguna yang dapat mengakses</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            
                            <!-- Dropdown List -->
                            <div id="hakAksesMenu" class="hidden absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                                <div class="p-2">
                                    <!-- Search Box -->
                                    <div class="mb-2">
                                        <input type="text" 
                                               id="searchUser" 
                                               placeholder="Cari pengguna..."
                                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#050C9C]"
                                               onkeyup="filterUsers()">
                                    </div>
                                    
                                    <!-- Select All -->
                                    <label class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer border-b border-gray-100 mb-1">
                                        <input type="checkbox" 
                                               id="selectAllUsers"
                                               onchange="toggleSelectAll()"
                                               class="w-4 h-4 text-[#050C9C] border-gray-300 rounded focus:ring-[#050C9C] focus:ring-2">
                                        <span class="ml-3 text-sm font-semibold text-gray-700">Pilih Semua</span>
                                    </label>
                                    
                                    <!-- User List -->
                                    @foreach ($users as $user)
                                        <label class="user-checkbox flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition" data-username="{{ strtolower($user->name) }}" data-useremail="{{ strtolower($user->email) }}">
                                            <input type="checkbox" 
                                                   name="hak_akses[]" 
                                                   value="{{ $user->id }}"
                                                   onchange="updateHakAksesLabel()"
                                                   class="hak-akses-checkbox w-4 h-4 text-[#050C9C] border-gray-300 rounded focus:ring-[#050C9C] focus:ring-2">
                                            <div class="ml-3 flex-1">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden input untuk validasi (minimal 1 harus dipilih) -->
                        <input type="hidden" id="hakAksesValidation" required>
                    </div>
                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="pt-2">
                <button type="submit"
                    class="w-full py-3.5 rounded-xl bg-[#050C9C] hover:bg-[#040a7a] text-white font-semibold transition shadow-lg shadow-[#050C9C]/20 hover:shadow-xl">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Script Modal -->
<script>
    function openModal() {
        const modal = document.getElementById('uploadModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('opacity-100'), 10);
    }

    function closeModal() {
        const modal = document.getElementById('uploadModal');
        modal.classList.remove('opacity-100');
        setTimeout(() => modal.classList.add('hidden'), 200);
        
        // Reset form saat modal ditutup
        document.getElementById('uploadForm').reset();
        document.getElementById('fileLabel').textContent = 'No File Choosen';
        document.getElementById('fileLabel').classList.add('text-gray-500');
        document.getElementById('fileLabel').classList.remove('text-gray-900', 'font-medium');
    }

    // Update file label saat file dipilih
    document.getElementById('fileInput').addEventListener('change', function(e) {
        const label = document.getElementById('fileLabel');
        if (e.target.files.length > 0) {
            label.textContent = e.target.files[0].name;
            label.classList.remove('text-gray-500');
            label.classList.add('text-gray-900', 'font-medium');
        } else {
            label.textContent = 'No File Choosen';
            label.classList.add('text-gray-500');
            label.classList.remove('text-gray-900', 'font-medium');
        }
    });

    // Initialize Flatpickr untuk Date Picker
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#tanggalTerbit", {
            dateFormat: "d/m/Y",           // Format dd/mm/yyyy
            allowInput: true,              // Bisa ketik manual
            altInput: false,
            locale: {
                firstDayOfWeek: 1,         // Senin sebagai hari pertama
                weekdays: {
                    shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                },
                months: {
                    shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
                    longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                },
            },
            // Optional: Disable tanggal masa depan (uncomment jika perlu)
            // maxDate: "today",
            
            // Optional: Set default ke hari ini (uncomment jika perlu)
            // defaultDate: "today",
            
            // Custom styling
            onReady: function(dateObj, dateStr, instance) {
                instance.calendarContainer.style.borderRadius = '1rem';
                instance.calendarContainer.style.boxShadow = '0 10px 25px rgba(0,0,0,0.1)';
            }
        });
    });

    // ============ HAK AKSES MULTI-SELECT ============
    
    // Toggle dropdown hak akses
    function toggleHakAksesDropdown() {
        const menu = document.getElementById('hakAksesMenu');
        menu.classList.toggle('hidden');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('hakAksesDropdown');
        const menu = document.getElementById('hakAksesMenu');
        
        if (!dropdown.contains(event.target) && !menu.contains(event.target)) {
            menu.classList.add('hidden');
        }
    });

    // Update label berdasarkan checkbox yang dipilih
    function updateHakAksesLabel() {
        const checkboxes = document.querySelectorAll('.hak-akses-checkbox:checked');
        const label = document.getElementById('hakAksesLabel');
        const validation = document.getElementById('hakAksesValidation');
        const selectAll = document.getElementById('selectAllUsers');
        const totalCheckboxes = document.querySelectorAll('.hak-akses-checkbox').length;
        
        if (checkboxes.length === 0) {
            label.textContent = 'Pilih pengguna yang dapat mengakses';
            label.classList.add('text-gray-500');
            label.classList.remove('text-gray-900', 'font-medium');
            validation.value = ''; // Kosongkan untuk trigger validation error
            selectAll.checked = false;
        } else if (checkboxes.length === totalCheckboxes) {
            label.textContent = `Semua pengguna dipilih (${checkboxes.length})`;
            label.classList.remove('text-gray-500');
            label.classList.add('text-gray-900', 'font-medium');
            validation.value = 'valid'; // Isi untuk pass validation
            selectAll.checked = true;
        } else {
            label.textContent = `${checkboxes.length} pengguna dipilih`;
            label.classList.remove('text-gray-500');
            label.classList.add('text-gray-900', 'font-medium');
            validation.value = 'valid'; // Isi untuk pass validation
            selectAll.checked = false;
        }
    }

    // Toggle select all
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAllUsers');
        const checkboxes = document.querySelectorAll('.hak-akses-checkbox:not([style*="display: none"])');
        
        checkboxes.forEach(checkbox => {
            // Hanya ubah checkbox yang visible (tidak di-filter)
            const parent = checkbox.closest('.user-checkbox');
            if (!parent.style.display || parent.style.display !== 'none') {
                checkbox.checked = selectAll.checked;
            }
        });
        
        updateHakAksesLabel();
    }

    // Filter users berdasarkan search
    function filterUsers() {
        const searchValue = document.getElementById('searchUser').value.toLowerCase();
        const userItems = document.querySelectorAll('.user-checkbox');
        
        userItems.forEach(item => {
            const username = item.getAttribute('data-username');
            const useremail = item.getAttribute('data-useremail');
            
            if (username.includes(searchValue) || useremail.includes(searchValue)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Form validation untuk hak akses
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        const checkboxes = document.querySelectorAll('.hak-akses-checkbox:checked');
        
        if (checkboxes.length === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 pengguna untuk hak akses!');
            document.getElementById('hakAksesDropdown').focus();
            document.getElementById('hakAksesDropdown').classList.add('border-red-500');
            
            setTimeout(() => {
                document.getElementById('hakAksesDropdown').classList.remove('border-red-500');
            }, 2000);
        }
    });
</script>

<style>
    /* Custom styling untuk select arrow */
    select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1.25rem;
        padding-right: 2.5rem;
    }

    /* Custom Flatpickr Styling */
    .flatpickr-calendar {
        border-radius: 1rem !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        border: none !important;
    }

    .flatpickr-day.selected {
        background: #050C9C !important;
        border-color: #050C9C !important;
    }

    .flatpickr-day.selected:hover {
        background: #040a7a !important;
        border-color: #040a7a !important;
    }

    .flatpickr-day:hover {
        background: #e0e7ff !important;
        border-color: #e0e7ff !important;
    }

    .flatpickr-months .flatpickr-prev-month:hover svg,
    .flatpickr-months .flatpickr-next-month:hover svg {
        fill: #050C9C !important;
    }

    .flatpickr-current-month .flatpickr-monthDropdown-months {
        font-weight: 600;
    }

    /* Hak Akses Dropdown Styling */
    #hakAksesMenu {
        animation: slideDown 0.2s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Custom Scrollbar untuk Dropdown */
    #hakAksesMenu::-webkit-scrollbar {
        width: 6px;
    }

    #hakAksesMenu::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    #hakAksesMenu::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    #hakAksesMenu::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>