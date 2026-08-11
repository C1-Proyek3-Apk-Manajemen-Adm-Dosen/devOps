document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("logoutModal");
    const openBtn = document.getElementById("openLogoutModal");
    const cancelBtn = document.getElementById("cancelLogout");

    // Buka modal
    openBtn?.addEventListener("click", (e) => {
        e.preventDefault();
        modal.classList.remove("hidden");
    });

    // Tutup modal
    cancelBtn?.addEventListener("click", () => {
        modal.classList.add("hidden");
    });

    // Klik di luar modal
    modal?.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.classList.add("hidden");
        }
    });
});
