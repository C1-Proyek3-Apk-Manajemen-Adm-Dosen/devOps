// resources/js/dosen/upload-dokumen-dosen.js

document.addEventListener('DOMContentLoaded', function() {
    console.log('Upload dokumen DOSEN JS loaded');

    const fileInput         = document.getElementById('fileInput');
    const fileLabel         = document.getElementById('fileLabel');
    const fileUploadArea    = document.getElementById('fileUploadArea');
    const hakAksesDropdown  = document.getElementById('hakAksesDropdown');
    const hakAksesMenu      = document.getElementById('hakAksesMenu');
    const selectAllUsers    = document.getElementById('selectAllUsers');
    const searchUser        = document.getElementById('searchUser');
    const uploadForm        = document.getElementById('uploadForm');
    const hakAksesCheckboxes = document.querySelectorAll('.hak-akses-checkbox');

    // 1. Inisialisasi Flatpickr untuk Tanggal Terbit
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
                    shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
                    longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                },
            },
            onReady: function(dateObj, dateStr, instance) {
                instance.calendarContainer.style.borderRadius = '1rem';
                instance.calendarContainer.style.boxShadow = '0 10px 25px rgba(0,0,0,0.1)';
            }
        });
    }

    // 2. Fungsi untuk memperbarui label Hak Akses dan status 'Pilih Semua'
    function updateHakAksesLabel() {
        const allCheckboxes = document.querySelectorAll('.hak-akses-checkbox');
        const checkboxes    = document.querySelectorAll('.hak-akses-checkbox:checked');
        const label         = document.getElementById('hakAksesLabel');
        const validation    = document.getElementById('hakAksesValidation');
        
        if (!label || !validation) return;

        if (checkboxes.length === 0) {
            label.textContent = 'Pilih pengguna yang dapat mengakses';
            label.classList.add('text-gray-500');
            label.classList.remove('text-gray-900', 'font-medium');
            validation.value = '';
            if (selectAllUsers) selectAllUsers.checked = false;
        } else if (checkboxes.length === allCheckboxes.length) {
            label.textContent = `✓ Semua pengguna dipilih (${checkboxes.length})`;
            label.classList.remove('text-gray-500');
            label.classList.add('text-gray-900', 'font-medium');
            validation.value = 'valid';
            if (selectAllUsers) selectAllUsers.checked = true;
        } else {
            label.textContent = `✓ ${checkboxes.length} pengguna dipilih`;
            label.classList.remove('text-gray-500');
            label.classList.add('text-gray-900', 'font-medium');
            validation.value = 'valid';
            if (selectAllUsers) selectAllUsers.checked = false;
        }
    }

    // 3. File input: update label & tampilan area upload
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const file     = e.target.files[0];
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                fileLabel.innerHTML = `
                    <span class="font-medium text-gray-900">${file.name}</span><br>
                    <span class="text-xs text-gray-500">${fileSize} MB</span>
                `;
                fileUploadArea?.classList.add('file-selected');
            } else {
                fileLabel.innerHTML = 'Klik untuk pilih file';
                fileUploadArea?.classList.remove('file-selected');
            }
        });

        // Klik area upload = trigger input file (kecuali tombol)
        if (fileUploadArea) {
            fileUploadArea.addEventListener('click', function(e) {
                if (e.target.tagName !== 'BUTTON') {
                    fileInput.click();
                }
            });
        }
    }

    // 4. Dropdown Hak Akses: buka / tutup
    if (hakAksesDropdown && hakAksesMenu) {
        hakAksesDropdown.addEventListener('click', function() {
            hakAksesMenu.classList.toggle('hidden');
        });
    }

    // Tutup dropdown jika klik di luar elemen
    document.addEventListener('click', function(event) {
        if (hakAksesDropdown && hakAksesMenu &&
            !hakAksesDropdown.contains(event.target) &&
            !hakAksesMenu.contains(event.target)) {
            hakAksesMenu.classList.add('hidden');
        }
    });

    // 5. Checkbox individual: update label
    hakAksesCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateHakAksesLabel);
    });

    // 6. 'Pilih Semua Pengguna'
    if (selectAllUsers) {
        selectAllUsers.addEventListener('change', function() {
            const isChecked = selectAllUsers.checked;
            const visibleCheckboxes = document.querySelectorAll(
                '.user-checkbox:not([style*="display: none"]) .hak-akses-checkbox'
            );
            
            visibleCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            
            updateHakAksesLabel();
        });
    }

    // 7. Filter pengguna
    if (searchUser) {
        searchUser.addEventListener('keyup', function() {
            const searchValue = searchUser.value.toLowerCase();
            const userItems   = document.querySelectorAll('.user-checkbox');
            let visibleCheckboxesCount       = 0;
            let visibleCheckedCheckboxesCount = 0;
            
            userItems.forEach(item => {
                const username = item.getAttribute('data-username') || '';
                const useremail = item.getAttribute('data-useremail') || '';
                const checkbox  = item.querySelector('.hak-akses-checkbox');
                
                if (username.includes(searchValue) || useremail.includes(searchValue)) {
                    item.style.display = 'flex';
                    visibleCheckboxesCount++;
                    if (checkbox && checkbox.checked) {
                        visibleCheckedCheckboxesCount++;
                    }
                } else {
                    item.style.display = 'none';
                }
            });
            
            if (selectAllUsers && visibleCheckboxesCount > 0) {
                selectAllUsers.checked = (visibleCheckedCheckboxesCount === visibleCheckboxesCount);
            } else if (selectAllUsers) {
                selectAllUsers.checked = false;
            }
        });
    }

    // 8. Validasi minimal 1 hak akses saat submit
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('.hak-akses-checkbox:checked');
            
            if (checkboxes.length === 0) {
                e.preventDefault();
                alert('⚠️ Pilih minimal 1 pengguna untuk hak akses!');
                if (hakAksesDropdown && hakAksesMenu) {
                    hakAksesDropdown.classList.add('border-red-500', 'ring-2', 'ring-red-200');
                    hakAksesMenu.classList.remove('hidden'); 
                    
                    setTimeout(() => {
                        hakAksesDropdown.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
                    }, 2000);
                }
            }
        });
    }

    // 9. Initial Label Update (misalnya ada old('hak_akses'))
    updateHakAksesLabel();
});
