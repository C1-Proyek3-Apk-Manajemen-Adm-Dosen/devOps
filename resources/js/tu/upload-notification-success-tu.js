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