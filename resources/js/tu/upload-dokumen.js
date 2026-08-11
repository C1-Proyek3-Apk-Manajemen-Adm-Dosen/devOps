// =====================================================
// UPLOAD DOKUMEN TU - CLEAN VERSION
// =====================================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('Upload dokumen TU JS loaded');

    const fileInput = document.getElementById('fileInput');
    const fileLabel = document.getElementById('fileLabel');
    const fileUploadArea = document.getElementById('fileUploadArea');
    const hakAksesDropdown = document.getElementById('hakAksesDropdown');
    const hakAksesMenu = document.getElementById('hakAksesMenu');
    const hakAksesLabel = document.getElementById('hakAksesLabel');
    const searchUser = document.getElementById('searchUser');
    const selectAllUsers = document.getElementById('selectAllUsers');
    const uploadForm = document.getElementById('uploadForm');
    const hakAksesCheckboxes = document.querySelectorAll('.hak-akses-checkbox');
    const hakAksesValidation = document.getElementById('hakAksesValidation');

    // =====================================================
    // 1. FLATPICKR DATE PICKER
    // =====================================================
    if (document.getElementById('tanggalTerbit') && typeof flatpickr !== 'undefined') {
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
            onReady: function(dateObj, dateStr, instance) {
                instance.calendarContainer.style.borderRadius = '1rem';
                instance.calendarContainer.style.boxShadow = '0 10px 25px rgba(0,0,0,0.1)';
            }
        });
    }

    // =====================================================
    // 2. FILE UPLOAD HANDLER
    // =====================================================
    if (fileInput && fileUploadArea) {
        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                fileLabel.innerHTML = `<span class="font-medium text-gray-900">${file.name}</span><br><span class="text-xs text-gray-500">${fileSize} MB</span>`;
                fileUploadArea.classList.add('file-selected', 'border-[#050C9C]', 'bg-blue-50');
            } else {
                fileLabel.innerHTML = 'Klik untuk pilih file';
                fileUploadArea.classList.remove('file-selected', 'border-[#050C9C]', 'bg-blue-50');
            }
        });

        // Trigger file input saat klik area (tapi bukan tombol)
        fileUploadArea.addEventListener('click', function(e) {
            if (e.target.tagName !== 'BUTTON') {
                fileInput.click();
            }
        });
    }

    // =====================================================
    // 3. HAK AKSES DROPDOWN
    // =====================================================
    if (hakAksesDropdown && hakAksesMenu) {
        hakAksesDropdown.addEventListener('click', function() {
            hakAksesMenu.classList.toggle('hidden');
        });
    }

    // Close dropdown saat klik di luar
    document.addEventListener('click', function(event) {
        if (hakAksesDropdown && hakAksesMenu && 
            !hakAksesDropdown.contains(event.target) && 
            !hakAksesMenu.contains(event.target)) {
            hakAksesMenu.classList.add('hidden');
        }
    });

    // =====================================================
    // 4. UPDATE LABEL HAK AKSES
    // =====================================================
    function updateHakAksesLabel() {
        const allCheckboxes = document.querySelectorAll('.hak-akses-checkbox');
        const checkboxes = document.querySelectorAll('.hak-akses-checkbox:checked');
        
        if (!hakAksesLabel || !hakAksesValidation) return;

        if (checkboxes.length === 0) {
            hakAksesLabel.textContent = 'Pilih pengguna yang dapat mengakses';
            hakAksesLabel.classList.add('text-gray-500');
            hakAksesLabel.classList.remove('text-gray-900', 'font-medium');
            hakAksesValidation.value = '';
            if (selectAllUsers) selectAllUsers.checked = false;
        } else if (checkboxes.length === allCheckboxes.length) {
            hakAksesLabel.textContent = `✓ Semua pengguna dipilih (${checkboxes.length})`;
            hakAksesLabel.classList.remove('text-gray-500');
            hakAksesLabel.classList.add('text-gray-900', 'font-medium');
            hakAksesValidation.value = 'valid';
            if (selectAllUsers) selectAllUsers.checked = true;
        } else {
            hakAksesLabel.textContent = `✓ ${checkboxes.length} pengguna dipilih`;
            hakAksesLabel.classList.remove('text-gray-500');
            hakAksesLabel.classList.add('text-gray-900', 'font-medium');
            hakAksesValidation.value = 'valid';
            if (selectAllUsers) selectAllUsers.checked = false;
        }
    }

    // Listen perubahan checkbox
    hakAksesCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateHakAksesLabel);
    });

    // =====================================================
    // 5. SELECT ALL USERS
    // =====================================================
    if (selectAllUsers) {
        selectAllUsers.addEventListener('change', function() {
            const isChecked = selectAllUsers.checked;
            const visibleCheckboxes = document.querySelectorAll('.user-checkbox:not([style*="display: none"]) .hak-akses-checkbox');
            
            visibleCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            
            updateHakAksesLabel();
        });
    }

    // =====================================================
    // 6. SEARCH USER
    // =====================================================
    if (searchUser) {
        searchUser.addEventListener('keyup', function() {
            const searchValue = searchUser.value.toLowerCase();
            const userItems = document.querySelectorAll('.user-checkbox');
            let visibleCheckboxesCount = 0;
            let visibleCheckedCheckboxesCount = 0;
            
            userItems.forEach(item => {
                const username = item.getAttribute('data-username');
                const useremail = item.getAttribute('data-useremail');
                const checkbox = item.querySelector('.hak-akses-checkbox');
                
                if (username.includes(searchValue) || useremail.includes(searchValue)) {
                    item.style.display = 'flex';
                    visibleCheckboxesCount++;
                    if (checkbox.checked) {
                        visibleCheckedCheckboxesCount++;
                    }
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Update status 'Pilih Semua' hanya untuk item yang terlihat
            if (selectAllUsers && visibleCheckboxesCount > 0) {
                selectAllUsers.checked = (visibleCheckedCheckboxesCount === visibleCheckboxesCount);
            } else if (selectAllUsers) {
                selectAllUsers.checked = false;
            }
        });
    }

    // =====================================================
    // 7. FORM VALIDATION
    // =====================================================
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('.hak-akses-checkbox:checked');
            
            if (checkboxes.length === 0) {
                e.preventDefault();
                alert('⚠️ Pilih minimal 1 pengguna untuk hak akses!');
                
                if (hakAksesDropdown) {
                    hakAksesDropdown.classList.add('border-red-500', 'ring-2', 'ring-red-200');
                    hakAksesMenu.classList.remove('hidden');
                    
                    setTimeout(() => {
                        hakAksesDropdown.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
                    }, 2000);
                }
            }
        });
    }

    // =====================================================
    // 8. INITIAL SETUP
    // =====================================================
    updateHakAksesLabel();
});