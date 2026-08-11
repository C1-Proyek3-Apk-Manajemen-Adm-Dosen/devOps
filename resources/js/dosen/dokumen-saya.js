document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ dokumen-saya.js loaded');
    
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            this.classList.add('animate-pulse');
            
            searchTimeout = setTimeout(() => {
                this.classList.remove('animate-pulse');
                const searchTerm = e.target.value.toLowerCase();
                const rows = document.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                        row.classList.add('animate-fadeIn');
                        } else {
                        row.style.display = 'none';
                    }
                });
            }, 300);
        });
    }
    
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(0px)';
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

console.log('✅ Dokumen Saya Dosen - All features loaded successfully');
