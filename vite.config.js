import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                
                'resources/js/app.js',
                'resources/js/modal.js',
                'resources/js/logoutModal.js',
                'resources/js/loginValidation.js',
                
                'resources/js/tu/upload-dokumen.js',
                'resources/js/tu/riwayat.js',
                'resources/js/tu/monitoring.js',
                'resources/js/tu/edit-hak-akses-modal.js',
                'resources/js/tu/detail-dokumen.js',
                
                'resources/js/dosen/dokumen-saya.js',
                'resources/js/dosen/detail-dokumen.js',
                'resources/js/dosen/edit-hak-akses-modal.js',
                
                'resources/css/dosen/dokumen-saya.css',
                'resources/css/dosen/edit-hak-akses.css',
                'resources/css/dosen/detail-dokumen.css',
                'resources/css/tu/detail-dokumen.css',
                'resources/css/tu/edit-hak-akses-modal.css',
                'resources/css/tu/monitoring.css',
            ],
            refresh: true,
        }),
    ],
});