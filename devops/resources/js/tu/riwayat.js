/* ==========================================================
   📜 Riwayat Upload TU — Script (v1.0)
   ========================================================== */

// Scroll halus kembali ke tabel saat klik pagination
document.addEventListener("click", (e) => {
    const link = e.target.closest('nav[aria-label="Pagination Navigation"] a');
    if (!link) return;
    sessionStorage.setItem("scrollToRiwayat", "1");
  });
  
  // Setelah reload, scroll ke atas daftar
  window.addEventListener("load", () => {
    if (sessionStorage.getItem("scrollToRiwayat")) {
      const box = document.getElementById("riwayatBox");
      if (box) box.scrollIntoView({ behavior: "smooth", block: "start" });
      sessionStorage.removeItem("scrollToRiwayat");
    }
  });
  
  // Efek hover lembut pada baris (opsional)
  document.addEventListener("DOMContentLoaded", () => {
    const rows = document.querySelectorAll("table.cards tbody tr");
    rows.forEach((row) => {
      row.addEventListener("mouseenter", () => {
        row.style.transition = "background-color .2s ease";
        row.style.backgroundColor = "#f8fafc";
      });
      row.addEventListener("mouseleave", () => {
        row.style.backgroundColor = "#fff";
      });
    });
  });
  
  // Util toast kecil (opsional untuk notifikasi)
  export function showToast(message, type = "info") {
    const cls = {
      success: "bg-green-500",
      error:   "bg-red-500",
      warning: "bg-yellow-500",
      info:    "bg-blue-500",
    }[type] || "bg-blue-500";
  
    const el = document.createElement("div");
    el.className = `fixed top-5 right-5 px-5 py-3 rounded-xl text-white shadow-lg font-medium z-[9999] ${cls}`;
    el.textContent = message;
    document.body.appendChild(el);
  
    setTimeout(() => {
      el.style.transition = "all .25s ease";
      el.style.opacity = "0";
      el.style.transform = "translateY(-8px)";
      setTimeout(() => el.remove(), 250);
    }, 2400);
  }
  