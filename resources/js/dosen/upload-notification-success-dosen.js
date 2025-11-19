// Show success modal
window.showSuccessNotification = function () {
    const modal = document.getElementById('successNotificationModal');
    const modalContent = document.getElementById('modalContent');

    if (modal && modalContent) {
        modal.classList.remove('hidden');

        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }
};

// Close modal
window.closeSuccessNotification = function () {
    const modal = document.getElementById('successNotificationModal');
    const modalContent = document.getElementById('modalContent');

    if (modal && modalContent) {
        modal.classList.add('opacity-0');
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
};

// Auto show on page load
document.addEventListener('DOMContentLoaded', () => {
    const successMessage = document.querySelector('.alert-success');
    if (successMessage) window.showSuccessNotification();

    const modal = document.getElementById('successNotificationModal');

    // Click outside modal
    if (modal) {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) window.closeSuccessNotification();
        });
    }
});

// ESC to close
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        const modal = document.getElementById('successNotificationModal');
        if (modal && !modal.classList.contains('hidden')) {
            window.closeSuccessNotification();
        }
    }
});
