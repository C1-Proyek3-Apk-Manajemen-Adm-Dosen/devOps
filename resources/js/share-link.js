import Swal from 'sweetalert2';

window.openShareModal = async function(dokumenId, judul) {
    const modal = document.getElementById('share-modal');
    const input = document.getElementById('share-link-input');
    const title = document.getElementById('share-doc-title');

    // buka modal
    modal.classList.remove('hidden');

    // tampil judul dokumen
    title.textContent = judul;

    // loading dulu
    input.value = 'Generating link...';

    try {
        const res = await fetch(`/dokumen/${dokumenId}/share-link`);
        
        if (!res.ok) {
            const errorData = await res.json();
            throw new Error(errorData.error || 'Failed to generate link');
        }
        
        const data = await res.json();
        input.value = data.url;
        
        // Optional: tampilkan info expiry
        if (data.expires_at) {
            console.log('Link expires at:', data.expires_at);
        }
    } catch (err) {
        console.error('Error generating share link:', err);
        input.value = 'Failed to generate link: ' + err.message;
        
        Swal.fire({
            icon: 'error',
            title: 'Gagal Generate Link',
            text: err.message,
            confirmButtonColor: '#050C9C'
        });
    }
}

window.copyShareLink = function() {
    const input = document.getElementById('share-link-input');
    
    if (input.value.includes('Failed') || input.value.includes('Generating')) {
        Swal.fire({
            icon: 'warning',
            title: 'Link Belum Siap',
            text: 'Link belum siap atau gagal di-generate!',
            confirmButtonColor: '#050C9C'
        });
        return;
    }
    
    navigator.clipboard.writeText(input.value)
        .then(() => {
            // Tutup modal dulu
            closeShareModal();
            
            // Tampilkan SweetAlert success dengan custom styling
            Swal.fire({
                icon: 'success',
                title: 'Link Berhasil Di-Copy!',
                html: `<p style="color: #6B7280; font-size: 14px; margin-top: 8px;">Link telah tersimpan di clipboard.<br>Anda dapat langsung paste untuk berbagi dokumen</p>`,
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                customClass: {
                    popup: 'rounded-2xl',
                    title: 'text-xl font-bold',
                    icon: 'border-0'
                },
                iconColor: '#10B981',
                backdrop: 'rgba(0, 0, 0, 0.4)'
            });
        })
        .catch(err => {
            console.error('Failed to copy:', err);
            Swal.fire({
                icon: 'error',
                title: 'Gagal Copy Link',
                text: 'Terjadi kesalahan saat menyalin link',
                confirmButtonColor: '#050C9C'
            });
        });
}

window.closeShareModal = function() {
    document.getElementById('share-modal').classList.add('hidden');
}
