document.addEventListener('DOMContentLoaded', () => {
    const notifBtn = document.getElementById('notifBtn');
    const notifDropdown = document.getElementById('notifDropdown');

    if (!notifBtn || !notifDropdown) return;

    notifBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        notifDropdown.classList.toggle('hidden');
        notifDropdown.classList.toggle('scale-95');
        notifDropdown.classList.toggle('scale-100');
    });

    document.addEventListener('click', (e) => {
        if (!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
            notifDropdown.classList.add('hidden');
            notifDropdown.classList.remove('scale-100');
            notifDropdown.classList.add('scale-95');
        }
    });
});
