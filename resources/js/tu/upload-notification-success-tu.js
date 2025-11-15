// resources/js/tu/upload-notification-success-tu.js

/**
 * Fungsi untuk menampilkan notifikasi sukses upload
 */
function showSuccessNotification() {
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
 */
function closeSuccessNotification() {
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
        }, 300);
    }
}

/**
 * Auto show notifikasi jika ada session success
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
    const modalContent = document.getElementById('modalContent');
    
    if (modal && event.target === modal) {
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