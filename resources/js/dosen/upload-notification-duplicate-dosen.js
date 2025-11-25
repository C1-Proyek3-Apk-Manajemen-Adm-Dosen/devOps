window.showDuplicateFileModal = function () {
    const modal = document.getElementById('duplicateFileModal');
    const modalContent = document.getElementById('duplicateFileModalContent');

    modal.classList.remove('hidden');

    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
};

window.closeDuplicateFileModal = function () {
    const modal = document.getElementById('duplicateFileModal');
    const modalContent = document.getElementById('duplicateFileModalContent');

    modal.classList.add('opacity-0');
    modalContent.classList.add('scale-95', 'opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
};

document.getElementById('cancelDuplicateUpload')?.addEventListener('click', () => {
    window.closeDuplicateFileModal();
});

document.getElementById('confirmUpdateVersion')?.addEventListener('click', () => {
    window.closeDuplicateFileModal();
    window.dispatchEvent(new CustomEvent('update-version'));
});

document.addEventListener('click', (event) => {
    const modal = document.getElementById('duplicateFileModal');
    if (modal && event.target === modal) window.closeDuplicateFileModal();
});
