document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("uploadModal");
    const modalTitle = document.getElementById("modalTitle");
    const jenisDokumenInput = document.getElementById("jenis_dokumen");
    const fileNameSpan = document.getElementById("fileName");

    // buka modal
    window.openModal = function (type) {
        modal.classList.remove("hidden");
        modalTitle.textContent = "Upload " + type;
        jenisDokumenInput.value = type;
    };

    // tutup modal
    window.closeModal = function () {
        modal.classList.add("hidden");
    };

    // update nama file yang dipilih
    window.updateFileName = function (input) {
        const fileName = input.files[0]?.name || "No file chosen";
        fileNameSpan.textContent = fileName;
    };

    // klik di luar modal untuk nutup
    window.addEventListener("click", (e) => {
        if (e.target === modal) closeModal();
    });
    
    // di JS kamu
    window.openModal = function(type) {
        const modal = document.getElementById('uploadModal');
        modal.classList.remove('hidden');
        modal.classList.add('opacity-0');
        setTimeout(() => modal.classList.remove('opacity-0'), 10);
    };

});
