// resources/js/tu/upload-dokumen.js

document.addEventListener('DOMContentLoaded', function() {
    console.log('Upload dokumen JS loaded'); // Debug: Pastikan file ini terpanggil

    const fileInput = document.getElementById('fileInput');
    const fileLabel = document.getElementById('fileLabel');
    const fileUploadArea = document.getElementById('fileUploadArea');
    const hakAksesDropdown = document.getElementById('hakAksesDropdown');
    const hakAksesMenu = document.getElementById('hakAksesMenu');
    const selectAllUsers = document.getElementById('selectAllUsers');
    const searchUser = document.getElementById('searchUser');
    const uploadForm = document.getElementById('uploadForm');
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
// resources/js/tu/upload-notification-success-tu.js

/**
 * Fungsi untuk menampilkan notifikasi sukses upload
 * Kita tambahkan "window." agar bisa diakses secara global
 */
window.showSuccessNotification = function() {
    const modal = document.getElementById('successNotificationModal');
    const modalContent = document.getElementById('modalContent');
    
    if (modal && modalContent) {
        // Tampilkan modal
        modal.classList.remove('hidden');
        
        // Trigger animation dengan delay
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }
}

/**
 * Fungsi untuk menutup notifikasi sukses
 * Kita tambahkan "window." agar bisa diakses oleh onclick=""
 */
window.closeSuccessNotification = function() {
    const modal = document.getElementById('successNotificationModal');
    const modalContent = document.getElementById('modalContent');
    
    if (modal && modalContent) {
        // Trigger fade out animation
        modal.classList.add('opacity-0');
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        
        // Sembunyikan modal setelah animasi selesai
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300); // Sesuaikan dengan durasi transisi (duration-300)
    }
}

/**
 * Auto show notifikasi jika ada session success
 */
document.addEventListener('DOMContentLoaded', function() {
    // Cek apakah ada session success dari Laravel
    const successMessage = document.querySelector('.alert-success');
    
    if (successMessage) {
        // Tampilkan modal notifikasi
        // Kita panggil fungsi global yang sudah kita buat
        window.showSuccessNotification();
    }
});

/**
 * Close modal saat klik di luar area modal
 */
document.addEventListener('click', function(event) {
    const modal = document.getElementById('successNotificationModal');
    
    // Cek jika user mengklik backdrop (event.target === modal)
    if (modal && event.target === modal) {
        window.closeSuccessNotification();
    }
});

/**
 * Close modal dengan tombol ESC
 */
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('successNotificationModal');
        // Pastikan modal sedang tampil sebelum menutup
        if (modal && !modal.classList.contains('hidden')) {
            window.closeSuccessNotification();
        }
    }
});
    // 2. Fungsi untuk memperbarui label file input
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                fileLabel.innerHTML = `<span class="font-medium text-gray-900">${file.name}</span><br><span class="text-xs text-gray-500">${fileSize} MB</span>`;
                fileUploadArea.classList.add('file-selected');
            } else {
                fileLabel.innerHTML = 'Klik untuk pilih file';
                fileUploadArea.classList.remove('file-selected');
            }
        });
        
        // Pemicu klik input file dari area upload
        if (fileUploadArea) {
             fileUploadArea.addEventListener('click', function(e) {
                // Pastikan tidak mengklik tombol "Pilih File" di dalamnya (tombol sudah punya onclick-nya sendiri)
                if (e.target.tagName !== 'BUTTON') { 
                    fileInput.click();
                }
            });
        }
    }


    // 3. Fungsi untuk mengelola dropdown Hak Akses
    if (hakAksesDropdown && hakAksesMenu) {
        hakAksesDropdown.addEventListener('click', function() {
            hakAksesMenu.classList.toggle('hidden');
        });
    }

    // Tutup dropdown jika klik di luar elemen
    document.addEventListener('click', function(event) {
        if (hakAksesDropdown && hakAksesMenu && !hakAksesDropdown.contains(event.target) && !hakAksesMenu.contains(event.target)) {
            hakAksesMenu.classList.add('hidden');
        }
    });

    // 4. Fungsi untuk memperbarui label Hak Akses dan status 'Pilih Semua'
    function updateHakAksesLabel() {
        const allCheckboxes = document.querySelectorAll('.hak-akses-checkbox');
        const checkboxes = document.querySelectorAll('.hak-akses-checkbox:checked');
        const label = document.getElementById('hakAksesLabel');
        const validation = document.getElementById('hakAksesValidation');
        
        if (!label || !validation) return; // Guard

        if (checkboxes.length === 0) {
            label.textContent = 'Pilih pengguna yang dapat mengakses';
            label.classList.add('text-gray-500');
            label.classList.remove('text-gray-900', 'font-medium');
            validation.value = ''; // Untuk validasi form
            if (selectAllUsers) selectAllUsers.checked = false;
        } else if (checkboxes.length === allCheckboxes.length) {
            label.textContent = `✓ Semua pengguna dipilih (${checkboxes.length})`;
            label.classList.remove('text-gray-500');
            label.classList.add('text-gray-900', 'font-medium');
            validation.value = 'valid'; // Untuk validasi form
            if (selectAllUsers) selectAllUsers.checked = true;
        } else {
            label.textContent = `✓ ${checkboxes.length} pengguna dipilih`;
            label.classList.remove('text-gray-500');
            label.classList.add('text-gray-900', 'font-medium');
            validation.value = 'valid'; // Untuk validasi form
            if (selectAllUsers) selectAllUsers.checked = false;
        }
    }

    // Panggil updateHakAksesLabel saat ada perubahan pada checkbox individual
    hakAksesCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateHakAksesLabel);
    });

    // 5. Fungsi untuk mengelola 'Pilih Semua Pengguna'
    if (selectAllUsers) {
        selectAllUsers.addEventListener('change', function() {
            const isChecked = selectAllUsers.checked;
            // Hanya mengontrol checkbox yang saat ini tidak tersembunyi oleh filter
            const visibleCheckboxes = document.querySelectorAll('.user-checkbox:not([style*="display: none"]) .hak-akses-checkbox');
            
            visibleCheckboxes.forEach(checkbox => {
                 checkbox.checked = isChecked;
            });
            
            updateHakAksesLabel(); 
        });
    }

    // 6. Fungsi untuk memfilter pengguna
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
            
            // Perbarui status 'Pilih Semua' hanya untuk item yang terlihat
            if (selectAllUsers && visibleCheckboxesCount > 0) {
                selectAllUsers.checked = (visibleCheckedCheckboxesCount === visibleCheckboxesCount);
            } else if (selectAllUsers) {
                selectAllUsers.checked = false;
            }
        });
    }

    // 7. Validasi minimal 1 hak akses saat submit
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('.hak-akses-checkbox:checked');
            
            if (checkboxes.length === 0) {
                e.preventDefault();
                alert('⚠️ Pilih minimal 1 pengguna untuk hak akses!');
                if (hakAksesDropdown) {
                    // Beri highlight merah
                    hakAksesDropdown.classList.add('border-red-500', 'ring-2', 'ring-red-200');
                    // Tampilkan menu
                    hakAksesMenu.classList.remove('hidden'); 
                    
                    setTimeout(() => {
                        hakAksesDropdown.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
                    }, 2000);
                }
            }
        });
    }
    
    // 8. Initial Label Update (Agar label Hak Akses terisi jika ada old('hak_akses'))
    updateHakAksesLabel();
});