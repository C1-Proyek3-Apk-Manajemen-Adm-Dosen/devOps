function showAlert(type, message) {
    const alertContainer = document.createElement('div');
    const bgColor = type === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-green-50 border-green-200 text-green-700';
    const icon = type === 'error' 
        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
    
    alertContainer.className = `fixed top-4 right-4 z-50 p-4 rounded-xl border ${bgColor} flex items-center gap-3 shadow-xl animate-slideInRight max-w-md`;
    alertContainer.innerHTML = `
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            ${icon}
        </svg>
        <span class="text-sm font-medium">${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    
    document.body.appendChild(alertContainer);
    
    setTimeout(() => {
        alertContainer.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => alertContainer.remove(), 300);
    }, 5000);
}

function resetFileInput() {
    const fileLabelVersi = document.getElementById('fileLabelVersi');
    const fileUploadAreaVersi = document.getElementById('fileUploadAreaVersi');
    const uploadIconVersi = document.getElementById('uploadIconVersi');
    
    if (fileLabelVersi) {
        fileLabelVersi.innerHTML = 'Klik untuk pilih file';
    }
    
    if (fileUploadAreaVersi) {
        fileUploadAreaVersi.classList.remove('border-green-400', 'bg-green-50');
        fileUploadAreaVersi.classList.add('border-gray-300', 'bg-gray-50');
    }
    
    if (uploadIconVersi) {
        uploadIconVersi.innerHTML = `
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
        `;
        uploadIconVersi.classList.remove('bg-green-200');
        uploadIconVersi.classList.add('bg-gray-200');
    }
}

function resetUploadVersiForm() {
    const form = document.getElementById('uploadVersiForm');
    const fileInputVersi = document.getElementById('fileInputVersi');
    const fileLabelVersi = document.getElementById('fileLabelVersi');
    const fileUploadAreaVersi = document.getElementById('fileUploadAreaVersi');
    const uploadIconVersi = document.getElementById('uploadIconVersi');
    const catatanPerubahan = document.getElementById('catatanPerubahan');
    
    if (form) form.reset();
    
    if (fileLabelVersi) {
        fileLabelVersi.innerHTML = 'Klik untuk pilih file';
    }
    
    if (fileUploadAreaVersi) {
        fileUploadAreaVersi.classList.remove('border-green-400', 'bg-green-50');
        fileUploadAreaVersi.classList.add('border-gray-300', 'bg-gray-50');
    }
    
    if (uploadIconVersi) {
        uploadIconVersi.innerHTML = `
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
        `;
        uploadIconVersi.classList.remove('bg-green-200');
        uploadIconVersi.classList.add('bg-gray-200');
    }
    
    if (catatanPerubahan) {
        catatanPerubahan.value = '';
    }
}

function openUploadVersiModal() {
    const modal = document.getElementById('uploadVersiModal');
    const modalContent = document.getElementById('modalContent');
    
    console.log('🔓 Opening modal:', { modal, modalContent });
    
    if (modal && modalContent) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        
        setTimeout(() => {
            modalContent.classList.remove('opacity-0', 'scale-95');
            modalContent.classList.add('opacity-100', 'scale-100');
        }, 10);
    } else {
        console.error('❌ Modal elements not found!');
    }
}

function closeUploadVersiModal() {
    const modal = document.getElementById('uploadVersiModal');
    const modalContent = document.getElementById('modalContent');
    
    console.log('🔒 Closing modal');
    
    if (modal && modalContent) {
        modalContent.classList.remove('opacity-100', 'scale-100');
        modalContent.classList.add('opacity-0', 'scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            resetUploadVersiForm();
        }, 200);
    }
}

window.openUploadVersiModal = openUploadVersiModal;
window.closeUploadVersiModal = closeUploadVersiModal;
window.showAlert = showAlert;

console.log('✅ detail-dokumen.js - Functions registered:', {
    openUploadVersiModal: typeof window.openUploadVersiModal,
    closeUploadVersiModal: typeof window.closeUploadVersiModal,
    showAlert: typeof window.showAlert
});

document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Detail Dokumen TU page loaded');
    window.scrollTo({ top: 0, behavior: 'smooth' });

    const fileInputVersi = document.getElementById('fileInputVersi');
    const fileLabelVersi = document.getElementById('fileLabelVersi');
    const fileUploadAreaVersi = document.getElementById('fileUploadAreaVersi');
    const uploadIconVersi = document.getElementById('uploadIconVersi');

    if (fileInputVersi) {
        fileInputVersi.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                
                if (fileSize > 20) {
                    fileInputVersi.value = '';
                    resetFileInput();
                    showAlert('error', `File terlalu besar! Ukuran maksimal 20MB. File Anda: ${fileSize}MB`);
                    return;
                }
                
                fileLabelVersi.innerHTML = `
                    <span class="font-bold text-gray-800">${file.name}</span>
                    <br>
                    <span class="text-xs text-gray-500">${fileSize} MB</span>
                `;
                
                fileUploadAreaVersi.classList.remove('border-gray-300', 'bg-gray-50');
                fileUploadAreaVersi.classList.add('border-green-400', 'bg-green-50');
                
                uploadIconVersi.innerHTML = `
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                `;
                uploadIconVersi.classList.remove('bg-gray-200');
                uploadIconVersi.classList.add('bg-green-200');
            } else {
                resetFileInput();
            }
        });
    }

    const uploadVersiForm = document.getElementById('uploadVersiForm');
    if (uploadVersiForm) {
        uploadVersiForm.addEventListener('submit', function(e) {
            const fileInput = document.getElementById('fileInputVersi');
            
            if (!fileInput.files.length) {
                e.preventDefault();
                showAlert('error', '⚠️ Pilih file terlebih dahulu!');
                return;
            }

            const btnSubmit = document.getElementById('btnSubmitVersi');
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = `
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Mengupload...
                `;
            }
        });
    }

    // ESC key handler
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modal = document.getElementById('uploadVersiModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeUploadVersiModal();
            }
        }
    });
});