document.addEventListener('DOMContentLoaded', () => {
    const notifBtn = document.getElementById('notifBtn');
    const notifDropdown = document.getElementById('notifDropdown');

    if (!notifBtn || !notifDropdown) return;

    notifBtn.addEventListener('click', (e) => {
        e.stopPropagation(); // biar klik gak langsung nutup
        notifDropdown.classList.toggle('hidden');
    });

    // Klik di luar dropdown untuk menutup
    document.addEventListener('click', (e) => {
        if (!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
            notifDropdown.classList.add('hidden');
        }
    });
});
