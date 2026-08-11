document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Detail Dokumen page loaded');
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

const downloadBtn = document.querySelector('button[class*="purple"]');
if (downloadBtn) {
    downloadBtn.addEventListener('click', function() {
        alert('Fitur download sedang dalam pengembangan');
    });
}
