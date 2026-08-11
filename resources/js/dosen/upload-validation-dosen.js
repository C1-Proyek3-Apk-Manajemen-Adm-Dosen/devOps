// =====================================================
// UPLOAD VALIDATION HANDLER - DOSEN
// Path: resources/js/dosen/upload-validation-dosen.js
// =====================================================

// Note: Swal sudah ter-load dari CDN di blade template
// Tidak perlu import

class UploadValidator {
    constructor() {
        this.form = document.getElementById('uploadForm');
        this.fileInput = document.getElementById('fileInput');
        this.maxFileSize = 20 * 1024 * 1024; // 20MB
        this.init();
    }

    init() {
        if (!this.form) return;

        // Handle file input validation
        this.fileInput?.addEventListener('change', (e) => this.validateFile(e));

        // Handle form submission
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));

        // Check for duplicate error from server
        this.checkDuplicateError();
    }

    /**
     * Validate file size on selection
     */
    validateFile(e) {
        const file = e.target.files[0];
        
        if (!file) return;

        // Check file size
        if (file.size > this.maxFileSize) {
            this.showFileSizeError(file.size);
            this.clearFileInput();
            return false;
        }

        return true;
    }

    /**
     * Handle form submission with validation
     */
    handleSubmit(e) {
        e.preventDefault();

        const validation = this.validateForm();

        if (!validation.isValid) {
            this.showValidationErrors(validation.errors);
            return false;
        }

        // If all validations pass, submit the form
        this.form.submit();
    }

    /**
     * Validate all form fields
     */
    validateForm() {
        const errors = [];

        // Validate Judul
        const judul = this.form.querySelector('[name="judul"]')?.value.trim();
        if (!judul) {
            errors.push({
                field: 'Judul Dokumen',
                message: 'Judul dokumen harus diisi'
            });
        }

        // Validate Tanggal Terbit
        const tanggalTerbit = this.form.querySelector('[name="tanggal_terbit"]')?.value.trim();
        if (!tanggalTerbit) {
            errors.push({
                field: 'Tanggal Terbit',
                message: 'Tanggal terbit harus diisi'
            });
        }

        // Validate Kategori
        const kategori = this.form.querySelector('[name="kategori_id"]')?.value;
        if (!kategori) {
            errors.push({
                field: 'Kategori',
                message: 'Kategori dokumen harus dipilih'
            });
        }

        // Validate File
        const file = this.fileInput?.files[0];
        if (!file) {
            errors.push({
                field: 'File',
                message: 'File dokumen harus diupload'
            });
        } else if (file.size > this.maxFileSize) {
            errors.push({
                field: 'File',
                message: 'Ukuran file maksimal 20MB'
            });
        }

        // Validate Deskripsi
        const deskripsi = this.form.querySelector('[name="deskripsi"]')?.value.trim();
        if (!deskripsi) {
            errors.push({
                field: 'Deskripsi',
                message: 'Deskripsi dokumen harus diisi'
            });
        }

        // Validate Hak Akses
        const hakAksesCheckboxes = this.form.querySelectorAll('[name="owner_user_id[]"]:checked');
        if (hakAksesCheckboxes.length === 0) {
            errors.push({
                field: 'Hak Akses',
                message: 'Minimal 1 pengguna harus dipilih'
            });
        }

        return {
            isValid: errors.length === 0,
            errors: errors
        };
    }

    /**
     * Show file size error modal
     */
    showFileSizeError(fileSize) {
        const fileSizeMB = (fileSize / (1024 * 1024)).toFixed(2);

        Swal.fire({
            icon: 'error',
            title: 'Ukuran File Terlalu Besar!',
            html: `
                <div class="text-left space-y-2">
                    <p class="text-gray-700">File yang Anda pilih melebihi batas maksimal.</p>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 mt-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Ukuran file:</span>
                            <span class="font-semibold text-red-600">${fileSizeMB} MB</span>
                        </div>
                        <div class="flex items-center justify-between text-sm mt-1">
                            <span class="text-gray-600">Maksimal:</span>
                            <span class="font-semibold text-gray-900">20 MB</span>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mt-3">
                        <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                        Silakan pilih file dengan ukuran lebih kecil.
                    </p>
                </div>
            `,
            confirmButtonText: 'Pilih File Lain',
            confirmButtonColor: '#050C9C',
            customClass: {
                popup: 'swal-rounded-popup',
                confirmButton: 'swal-rounded-button'
            },
            didOpen: () => {
                // Apply custom border radius via inline style
                const popup = Swal.getPopup();
                if (popup) {
                    popup.style.borderRadius = '24px';
                }
                
                const confirmBtn = Swal.getConfirmButton();
                if (confirmBtn) {
                    confirmBtn.style.borderRadius = '12px';
                    confirmBtn.style.padding = '10px 24px';
                }
                
                // Apply backdrop blur effect
                const backdrop = document.querySelector('.swal2-container');
                if (backdrop) {
                    backdrop.style.backdropFilter = 'blur(4px)';
                    backdrop.style.backgroundColor = 'rgba(0, 0, 0, 0.4)';
                }
            }
        });
    }

    /**
     * Show validation errors modal
     */
    showValidationErrors(errors) {
        const errorListHTML = errors.map(error => `
            <div class="flex items-start gap-3 p-3 bg-red-50 rounded-lg">
                <div class="flex-shrink-0 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center mt-0.5">
                    <i class="fas fa-times text-white text-xs"></i>
                </div>
                <div class="flex-1 text-left">
                    <p class="font-semibold text-gray-900 text-sm">${error.field}</p>
                    <p class="text-gray-700 text-sm">${error.message}</p>
                </div>
            </div>
        `).join('');

        Swal.fire({
            icon: 'error',
            title: 'Form Belum Lengkap!',
            html: `
                <div class="text-left">
                    <p class="text-gray-700 mb-4">
                        Mohon lengkapi field berikut sebelum mengupload dokumen:
                    </p>
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        ${errorListHTML}
                    </div>
                </div>
            `,
            confirmButtonText: 'Perbaiki Form',
            confirmButtonColor: '#050C9C',
            customClass: {
                popup: 'swal-rounded-popup',
                confirmButton: 'swal-rounded-button'
            },
            didOpen: () => {
                // Apply custom border radius via inline style
                const popup = Swal.getPopup();
                if (popup) {
                    popup.style.borderRadius = '24px';
                }
                
                const confirmBtn = Swal.getConfirmButton();
                if (confirmBtn) {
                    confirmBtn.style.borderRadius = '12px';
                    confirmBtn.style.padding = '10px 24px';
                }
                
                // Apply backdrop blur effect
                const backdrop = document.querySelector('.swal2-container');
                if (backdrop) {
                    backdrop.style.backdropFilter = 'blur(4px)';
                    backdrop.style.backgroundColor = 'rgba(0, 0, 0, 0.4)';
                }
            }
        });
    }

    /**
     * Check for duplicate error from server-side validation
     */
    checkDuplicateError() {
        const duplicateAlert = document.querySelector('[data-duplicate-error]');
        
        if (duplicateAlert) {
            const judulDokumen = duplicateAlert.dataset.judulDokumen || '';
            this.showDuplicateError(judulDokumen);
        }
    }

    /**
     * Show duplicate document error modal
     */
    showDuplicateError(judul) {
        Swal.fire({
            icon: 'warning',
            title: 'Judul Dokumen Sudah Ada!',
            html: `
                <div class="text-left space-y-3">
                    <p class="text-gray-700">
                        Dokumen dengan judul berikut sudah pernah diupload sebelumnya:
                    </p>
                    <div class="bg-amber-50 border border-amber-300 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-file-alt text-amber-600 text-xl mt-1"></i>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">${judul}</p>
                                <p class="text-sm text-gray-600 mt-1">Judul harus unik untuk setiap dokumen</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-3">
                        <p class="text-sm text-gray-700">
                            <i class="fas fa-lightbulb text-blue-500 mr-2"></i>
                            <strong>Saran:</strong> Gunakan judul yang berbeda atau edit dokumen yang sudah ada.
                        </p>
                    </div>
                </div>
            `,
            confirmButtonText: 'Ubah Judul',
            confirmButtonColor: '#050C9C',
            customClass: {
                popup: 'swal-rounded-popup',
                confirmButton: 'swal-rounded-button'
            },
            didOpen: () => {
                // Apply custom border radius via inline style
                const popup = Swal.getPopup();
                if (popup) {
                    popup.style.borderRadius = '24px';
                }
                
                const confirmBtn = Swal.getConfirmButton();
                if (confirmBtn) {
                    confirmBtn.style.borderRadius = '12px';
                    confirmBtn.style.padding = '10px 24px';
                }
                
                // Apply backdrop blur effect
                const backdrop = document.querySelector('.swal2-container');
                if (backdrop) {
                    backdrop.style.backdropFilter = 'blur(4px)';
                    backdrop.style.backgroundColor = 'rgba(0, 0, 0, 0.4)';
                }
            }
        }).then(() => {
            // Focus ke input judul setelah modal ditutup
            const judulInput = document.querySelector('[name="judul"]');
            judulInput?.focus();
            judulInput?.select();
        });
    }

    /**
     * Clear file input
     */
    clearFileInput() {
        if (this.fileInput) {
            this.fileInput.value = '';
            const fileLabel = document.getElementById('fileLabel');
            const fileUploadArea = document.getElementById('fileUploadArea');
            
            if (fileLabel) {
                fileLabel.textContent = 'Klik untuk pilih file';
            }
            
            if (fileUploadArea) {
                fileUploadArea.classList.remove('border-[#050C9C]', 'bg-blue-50', 'file-selected');
            }
        }
    }
}

// Initialize validator when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new UploadValidator();
});

export default UploadValidator;