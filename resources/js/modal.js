document.addEventListener('DOMContentLoaded', () => {
    // === BUKA MODAL ===
    document.querySelectorAll('[data-modal-target]').forEach(button => {
        button.addEventListener('click', () => {
            const target = button.getAttribute('data-modal-target');
            document.getElementById(target)?.classList.remove('hidden');
        });
    });

    // === TUTUP MODAL ===
    document.querySelectorAll('[data-close-modal]').forEach(button => {
        button.addEventListener('click', () => {
            const target = button.getAttribute('data-close-modal');
            document.getElementById(target)?.classList.add('hidden');
        });
    });

    // === TUTUP MODAL JIKA KLIK DI LUARNYA ===
    document.querySelectorAll('[id^="modal"]').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.classList.add('hidden');
        });
    });

    // === FILE UPLOAD BUTTONS (handle semua modal) ===
    const uploadSets = [
        ['customFileBtnRPS', 'realFileInputRPS', 'fileNameRPS'],
        ['customFileBtnSKP', 'realFileInputSKP', 'fileNameSKP'],
        ['customFileBtnBKD', 'realFileInputBKD', 'fileNameBKD'],
        ['customFileBtnBukti', 'realFileInputBukti', 'fileNameBukti']
    ];

    uploadSets.forEach(([btnId, inputId, labelId]) => {
        const button = document.getElementById(btnId);
        const input = document.getElementById(inputId);
        const label = document.getElementById(labelId);

        if (button && input && label) {
            // klik tombol → buka file explorer
            button.addEventListener('click', () => input.click());

            // ganti teks sesuai file yang dipilih
            input.addEventListener('change', () => {
                label.textContent = input.files.length
                    ? input.files[0].name
                    : 'No File Chosen';
            });
        }
    });
});
