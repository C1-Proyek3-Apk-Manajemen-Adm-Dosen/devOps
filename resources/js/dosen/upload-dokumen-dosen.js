// =====================================================
// FILE UPLOAD HANDLER
// =====================================================
const fileInput = document.getElementById('fileInput');
const fileLabel = document.getElementById('fileLabel');
const fileUploadArea = document.getElementById('fileUploadArea');

fileUploadArea?.addEventListener('click', () => {
    fileInput?.click();
});

fileInput?.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (file) {
        fileLabel.textContent = file.name;
        fileUploadArea.classList.add('border-[#050C9C]', 'bg-blue-50');
    } else {
        fileLabel.textContent = 'Klik untuk pilih file';
        fileUploadArea.classList.remove('border-[#050C9C]', 'bg-blue-50');
    }
});

// =====================================================
// FLATPICKR DATE PICKER
// =====================================================
if (document.getElementById('tanggalTerbit')) {
    flatpickr("#tanggalTerbit", {
        dateFormat: "d/m/Y",
        allowInput: true,
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
const hakAksesValidation = document.getElementById('hakAksesValidation');

// Toggle dropdown
hakAksesDropdown?.addEventListener('click', () => {
    hakAksesMenu?.classList.toggle('hidden');
});

// Close dropdown when click outside
document.addEventListener('click', (e) => {
    if (!hakAksesDropdown?.contains(e.target) && !hakAksesMenu?.contains(e.target)) {
        hakAksesMenu?.classList.add('hidden');
    }
});

// Search user functionality
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

// Select all users functionality
selectAllUsers?.addEventListener('change', (e) => {
    const isChecked = e.target.checked;
    hakAksesCheckboxes.forEach(checkbox => {
        if (!checkbox.closest('.user-checkbox').classList.contains('hidden')) {
            checkbox.checked = isChecked;
        }
    });
    updateHakAksesLabel();
});

// Update label when checkbox changed
hakAksesCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', updateHakAksesLabel);
});

// Update label function
function updateHakAksesLabel() {
    const checkedCount = Array.from(hakAksesCheckboxes).filter(cb => cb.checked).length;
    if (checkedCount > 0) {
        hakAksesLabel.textContent = `${checkedCount} pengguna dipilih`;
        hakAksesLabel.classList.remove('text-gray-500');
        hakAksesLabel.classList.add('text-gray-900');
        hakAksesValidation.value = 'valid';
    } else {
        hakAksesLabel.textContent = 'Pilih pengguna yang dapat mengakses';
        hakAksesLabel.classList.add('text-gray-500');
        hakAksesLabel.classList.remove('text-gray-900');
        hakAksesValidation.value = '';
    }
}