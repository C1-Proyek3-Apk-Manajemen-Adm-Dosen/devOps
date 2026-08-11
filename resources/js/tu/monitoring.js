window.openDetailModal = function(dokumenId) {
    console.log('🔍 Opening modal for dokumen ID:', dokumenId);
    
    const modal = document.getElementById('detailDokumenModal');
    const modalContent = document.getElementById('detailModalContent');
    
    if (!modal) {
        console.error('❌ Modal not found!');
        alert('Error: Modal tidak ditemukan di halaman');
        return;
    }
    
    fetch(`/tu/monitoring/detail/${dokumenId}`)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('✅ Data loaded:', data);
            
            document.getElementById('modal-nomor-dokumen').textContent = data.nomor_dokumen || '-';
            document.getElementById('modal-nama-dokumen').textContent = data.judul || '-';
            document.getElementById('modal-tanggal-terbit').textContent = data.tanggal_terbit_formatted || '-';
            document.getElementById('modal-kategori').textContent = data.kategori || 'Tidak Ada Kategori';
            document.getElementById('modal-deskripsi').textContent = data.deskripsi || 'Tidak ada deskripsi';
            document.getElementById('modal-versi').textContent = `v${data.versi || 1}`;
            
            const badge = document.getElementById('modal-kategori-badge');
            badge.className = 'inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold ' + data.badge_class;
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        })
        .catch(error => {
            console.error('❌ Error:', error);
            alert('Gagal memuat detail dokumen: ' + error.message);
        });
}

window.closeDetailModal = function() {
    const modal = document.getElementById('detailDokumenModal');
    const modalContent = document.getElementById('detailModalContent');
    
    if (!modal) return;
    
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ monitoring.js loaded');
    
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
    
    const modal = document.getElementById('detailDokumenModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDetailModal();
            }
        });
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDetailModal();
        }
    });
});
