<?php $__env->startSection('title', 'Detail Dokumen - SiDoRa'); ?>

<?php $__env->startSection('content'); ?>
<div class="px-4 sm:px-6 lg:px-8 py-2">

    
    <?php if(session('error')): ?>
        <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-medium"><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
        <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="text-sm font-medium"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>
    


    
    <div class="mb-3 flex items-center gap-2">
        <a href="<?php echo e(route('tu.monitoring')); ?>" 
           class="w-9 h-9 bg-white rounded-xl shadow-md hover:shadow-lg flex items-center justify-center text-gray-600 hover:text-[#050C9C] transition-all duration-200 hover:-translate-x-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-800">Detail Dokumen</h1>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        
        
        <div class="bg-gradient-to-r from-[#050C9C] to-blue-700 px-5 py-2.5">
            <h2 class="text-base font-bold text-white">Detail Dokumen</h2>
        </div>

        
        <div class="p-5 grid grid-cols-1 lg:grid-cols-2 gap-5">
            
            
            <div class="space-y-3.5">
                
                
                <div class="bg-gradient-to-br from-[#050C9C] to-blue-700 rounded-xl p-4 shadow-lg">
                    <p class="text-[9px] font-semibold text-white/80 uppercase tracking-wide mb-0.5">Nomor Dokumen</p>
                    <p class="text-2xl font-bold text-white"><?php echo e($dokumen->nomor_dokumen ?? '21'); ?></p>
                </div>

                
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Judul Dokumen</label>
                    <div class="bg-gray-50 rounded-xl px-3 py-2.5 border border-gray-200">
                        <p class="text-sm font-bold text-gray-800"><?php echo e($dokumen->judul ?? 'ersya'); ?></p>
                    </div>
                </div>

                
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Tanggal Upload</label>
                    <div class="bg-gray-50 rounded-xl px-3 py-2.5 border border-gray-200">
                        <p class="text-sm font-semibold text-gray-800">
                            <?php echo e(\Carbon\Carbon::parse($dokumen->tanggal_terbit ?? now())->translatedFormat('d F Y')); ?>

                        </p>
                    </div>
                </div>

                
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Kategori Dokumen</label>
                    <div class="bg-gray-50 rounded-xl px-3 py-2.5 border border-gray-200">
                        <?php
                            $kategoriNama = $dokumen->kategori?->nama_kategori ?? 'Surat Tugas';
                            $badgeClass = match($kategoriNama) {
                                'Surat Keputusan' => 'bg-blue-100 text-blue-700 border-2 border-blue-300',
                                'Surat Tugas' => 'bg-blue-100 text-blue-700 border-2 border-blue-300',
                                'Riwayat Pengajaran' => 'bg-green-100 text-green-700 border-2 border-green-300',
                                'RPS', 'Rencana Pembelajaran Semester' => 'bg-blue-100 text-blue-700 border-2 border-blue-300',
                                'BKD', 'Buku Kerja Dosen' => 'bg-orange-100 text-orange-700 border-2 border-orange-300',
                                'SKP' => 'bg-pink-100 text-pink-700 border-2 border-pink-300',
                                default => 'bg-gray-100 text-gray-700 border-2 border-gray-300'
                            };
                        ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-bold <?php echo e($badgeClass); ?>">
                            <?php echo e($kategoriNama); ?>

                        </span>
                    </div>
                </div>

            </div>

            
            <div class="space-y-4">
                
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Deskripsi Dokumen</label>
                    <div class="bg-gray-50 rounded-xl px-3 py-2.5 border border-gray-200 h-[180px] overflow-y-auto">
                        <p class="text-sm text-gray-700 leading-relaxed">
                            <?php echo e($dokumen->deskripsi ?? 'Tidak ada deskripsi'); ?>

                        </p>
                    </div>
                </div>

                
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Versi Dokumen</label>
                    <div class="bg-gray-50 rounded-xl px-3 py-2.5 border border-gray-200 flex items-center gap-2.5">
                        <div class="w-10 h-10 bg-gradient-to-br from-[#050C9C] to-blue-700 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <?php
                            $versiTerbaru = $dokumen->versi()->latest('nomor_versi')->first();
                        ?>
                        <p class="text-lg font-bold text-gray-800">v<?php echo e($versiTerbaru?->nomor_versi ?? 1); ?></p>
                    </div>
                </div>

                
                <div>
                    <a href="<?php echo e(route('tu.dokumen.download', $dokumen->dokumen_id)); ?>" 
                       class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#050C9C] to-blue-700 text-white rounded-xl font-bold text-sm hover:from-blue-700 hover:to-[#050C9C] transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download Dokumen
                    </a>
                </div>

            </div>

        </div>

    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/tu/detail-dokumen.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/tu/detail-dokumen.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SEMESTER 3 TEKNIK INFORMATIKA\PROYEK-3-PBO\APK-MANAJEMEN-DOSEN\devOps\devOps\resources\views/tu/detail-dokumen.blade.php ENDPATH**/ ?>