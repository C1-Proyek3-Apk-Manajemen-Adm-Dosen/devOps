document.addEventListener('DOMContentLoaded', function() {
    console.log('dokumen-saya.js loaded');

    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');

    if (searchInput && searchForm) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchForm.submit();
            }
        });

        document.querySelectorAll('tbody tr').forEach(row => {
            row.addEventListener('mouseenter', () => row.style.transform = 'translateX(4px)');
            row.addEventListener('mouseleave', () => row.style.transform = 'translateX(0)');
        });

        document.querySelectorAll('a[href*="page="]').forEach(link => {
            link.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
        });

        searchInput.addEventListener('input', function() {
            this.classList.add('ring-2', 'ring-[#050C9C]/20');
        });

        searchInput.addEventListener('blur', function() {
            this.classList.remove('ring-2', 'ring-[#050C9C]/20');
        });
    }

    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(4px)';
        });
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });

    const paginationLinks = document.querySelectorAll('a[href*="page="]');
    paginationLinks.forEach(link => {
        link.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    const tabLinks = document.querySelectorAll('a[href*="tab="]');
    tabLinks.forEach(link => {
        link.addEventListener('click', function() {
            const tableBody = document.querySelector('tbody');
            if (tableBody) {
                tableBody.style.opacity = '0.5';
                setTimeout(() => {
                    tableBody.style.opacity = '1';
                }, 200);
            }
        });
    });
});

function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { day: 'numeric', month: 'long', year: 'numeric' };
    return date.toLocaleDateString('id-ID', options);
}

function getBadgeClass(kategori) {
    const badges = {
        'RPS': 'bg-indigo-100 text-indigo-700 border border-indigo-200',
        'Rencana Pembelajaran Semester': 'bg-indigo-100 text-indigo-700 border border-indigo-200',
        'BKD': 'bg-orange-100 text-orange-700 border border-orange-200',
        'Buku Kerja Dosen': 'bg-orange-100 text-orange-700 border border-orange-200',
        'SKP': 'bg-pink-100 text-pink-700 border border-pink-200',
        'Bukti Pengajaran': 'bg-green-100 text-green-700 border border-green-200',
        'Surat Keputusan': 'bg-purple-100 text-purple-700 border border-purple-200',
        'Surat Tugas': 'bg-blue-100 text-blue-700 border border-blue-200',
    };
    return badges[kategori] || 'bg-gray-100 text-gray-700 border border-gray-200';
}

console.log('Dokumen Saya Dosen - Search sekarang manual (Enter / klik tombol)');