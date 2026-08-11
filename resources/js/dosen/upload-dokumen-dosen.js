// =====================================================
// FILE UPLOAD HANDLER (UI Only - No Validation)
// Path: resources/js/dosen/upload-dokumen-dosen.js
// =====================================================

const fileInput = document.getElementById('fileInput');
const fileLabel = document.getElementById('fileLabel');
const fileUploadArea = document.getElementById('fileUploadArea');

if (fileUploadArea && fileInput) {

    // Klik area = buka file input (kecuali tombol Pilih File)
    fileUploadArea.addEventListener('click', (e) => {
        if (e.target.tagName !== "BUTTON") {
            fileInput.click();
        }
    });

    // File Change - Update UI Only
    fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];

        if (file) {

            // ========== FIX: POTONG NAMA FILE PANJANG ==========
            const maxLength = 40;  // batas karakter tampil
            let displayName = file.name;

            if (file.name.length > maxLength) {
                const ext = file.name.split('.').pop();        // ambil ekstensi
                const base = file.name.substring(0, maxLength - ext.length - 5);
                displayName = base + "... ." + ext;            // contoh: "dokumen_sangat_panjang... .pdf"
            }

            fileLabel.textContent = displayName;
            // ===================================================

            fileUploadArea.classList.add('file-selected', 'border-[#050C9C]', 'bg-blue-50');

        } else {

            // Reset label
            fileLabel.textContent = 'Klik untuk pilih file';
            fileUploadArea.classList.remove('file-selected', 'border-[#050C9C]', 'bg-blue-50');
        }
    });
}

// =====================================================
// FLATPICKR DATE PICKER (max hari ini)
// =====================================================
if (document.getElementById('tanggalTerbit')) {
    flatpickr("#tanggalTerbit", {
        dateFormat: "d/m/Y",
        allowInput: true,
        maxDate: "today",
        locale: {
            firstDayOfWeek: 1,
            weekdays: {
                shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            },
            months: {
                shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            },
        },
    });
}

// =====================================================
// HAK AKSES DROPDOWN
// =====================================================
const hakAksesDropdown = document.getElementById('hakAksesDropdown');
const hakAksesMenu = document.getElementById('hakAksesMenu');
const hakAksesLabel = document.getElementById('hakAksesLabel');
const searchUser = document.getElementById('searchUser');
const selectAllUsers = document.getElementById('selectAllUsers');
const hakAksesCheckboxes = document.querySelectorAll('.hak-akses-checkbox');

// Toggle dropdown
hakAksesDropdown?.addEventListener('click', () => {
    hakAksesMenu?.classList.toggle('hidden');
});

// Close dropdown outside
document.addEventListener('click', (e) => {
    if (!hakAksesDropdown?.contains(e.target) && !hakAksesMenu?.contains(e.target)) {
        hakAksesMenu?.classList.add('hidden');
    }
});

// =====================================================
// SEARCH USER
// =====================================================
searchUser?.addEventListener('input', (e) => {
    const searchTerm = e.target.value.toLowerCase();
    
    document.querySelectorAll('.user-checkbox').forEach(label => {
        const username = label.dataset.username || '';
        const useremail = label.dataset.useremail || '';
        
        if (username.includes(searchTerm) || useremail.includes(searchTerm)) {
            label.classList.remove('hidden');
        } else {
            label.classList.add('hidden');
        }
    });
});

// =====================================================
// SELECT ALL USERS
// =====================================================
selectAllUsers?.addEventListener('change', (e) => {
    const isChecked = e.target.checked;

    hakAksesCheckboxes.forEach(checkbox => {
        if (!checkbox.closest('.user-checkbox').classList.contains('hidden')) {
            checkbox.checked = isChecked;
        }
    });

    updateHakAksesLabel();
});

// =====================================================
// UPDATE LABEL HAK AKSES
// =====================================================
function updateHakAksesLabel() {
    const checkedCount = Array.from(hakAksesCheckboxes).filter(cb => cb.checked).length;

    if (checkedCount > 0) {
        hakAksesLabel.textContent = `${checkedCount} pengguna dipilih`;
        hakAksesLabel.classList.remove('text-gray-500');
        hakAksesLabel.classList.add('text-gray-900');
    } else {
        hakAksesLabel.textContent = 'Pilih pengguna yang dapat mengakses';
        hakAksesLabel.classList.add('text-gray-500');
        hakAksesLabel.classList.remove('text-gray-900');
    }
}

// Listener untuk semua checkbox
hakAksesCheckboxes.forEach(cb => {
    cb.addEventListener('change', updateHakAksesLabel);
});