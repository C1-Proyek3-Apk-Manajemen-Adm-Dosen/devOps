// resources/js/tu/upload-notification-success-tu.js

/**
 * Fungsi untuk menampilkan notifikasi sukses upload
 */
function showSuccessNotification() {
    const modal = document.getElementById('successNotificationModal');
    
    if (modal) {
        // Tampilkan modal
        modal.classList.remove('hidden');
        
        // Trigger animation dengan delay
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);
    }
}

/**
 * Fungsi untuk menutup notifikasi sukses
 */
function closeSuccessNotification() {
    const modal = document.getElementById('successNotificationModal');
    
    if (modal) {
        // Remove show class untuk trigger fade out
        modal.classList.remove('show');
        
        // Sembunyikan modal setelah animasi selesai
        setTimeout(() => {
            modal.classList.add('hidden');
            
            // Redirect atau reload halaman (optional)
            // window.location.reload();
            // window.location.href = '/upload-dokumen';
        }, 300);
    }
}

/**
 * Auto show notifikasi jika ada session success
 * Tambahkan ini di blade template yang include komponen ini
 */
document.addEventListener('DOMContentLoaded', function() {
    // Cek apakah ada session success dari Laravel
    const successMessage = document.querySelector('.alert-success');
    
    if (successMessage) {
        // Sembunyikan alert default
        successMessage.style.display = 'none';
        
        // Tampilkan modal notifikasi
        showSuccessNotification();
    }
});

/**
 * Close modal saat klik di luar area modal
 */
document.addEventListener('click', function(event) {
    const modal = document.getElementById('successNotificationModal');
    const modalContainer = document.querySelector('.success-modal-container');
    
    if (modal && event.target === modal && !modalContainer.contains(event.target)) {
        closeSuccessNotification();
    }
});

/**
 * Close modal dengan tombol ESC
 */
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeSuccessNotification();
    }
});