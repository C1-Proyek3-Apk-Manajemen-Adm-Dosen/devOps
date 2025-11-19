<?php $__env->startSection('title', 'Riwayat Upload TU - SiDoRa'); ?>

<?php $__env->startPush('styles'); ?>
  <?php echo app('Illuminate\Foundation\Vite')('resources/css/tu/riwayat.css'); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="p-8" id="riwayatBox">

    
    <div class="flex items-center justify-between gap-4 mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Riwayat Upload TU</h1>
        <form method="GET" class="w-full max-w-sm">
            <input type="hidden" name="cat" value="<?php echo e(request('cat')); ?>">
            <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input
                    type="text"
                    name="q"
                    value="<?php echo e(request('q')); ?>"
                    class="w-full pl-10 pr-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#050C9C]"
                    placeholder="Cari dokumen…">
            </div>
        </form>
    </div>

    
    <form method="GET" class="flex flex-wrap items-center gap-3 mb-6">
        <input type="hidden" name="q" value="<?php echo e(request('q')); ?>">

        <div class="select-cat-wrapper">
            <select name="cat"
                    class="select-cat border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#050C9C]">
                <option value="">Semua Kategori</option>
                <option value="st" <?php if(request('cat')==='st'): echo 'selected'; endif; ?>>Surat Tugas (ST)</option>
                <option value="sk" <?php if(request('cat')==='sk'): echo 'selected'; endif; ?>>Surat Keputusan (SK)</option>
                <option value="rp" <?php if(request('cat')==='rp'): echo 'selected'; endif; ?>>Riwayat Pengajaran</option>
            </select>
            <span class="select-cat-arrow">▾</span>
        </div>

        <button class="px-4 py-2 rounded-xl bg-[#050C9C] text-white hover:bg-[#001070] transition">
            Terapkan
        </button>
    </form>

    
    <div class="riwayat-wrapper">

        
        <div class="riwayat-header">
            <div>NO</div>
            <div>NAMA DOKUMEN</div>
            <div>KATEGORI</div>
            <div>TANGGAL UPLOAD</div>
            <div>DOSEN</div>
            <div>AKSI</div>
        </div>

        
        <div class="riwayat-body">
            <?php $__empty_1 = true; $__currentLoopData = $docs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $alias = $d->alias
                      ?? (strtolower($d->nama_kategori ?? '') ?
                          (str_contains(strtolower($d->nama_kategori),'tugas') ? 'st'
                          : (str_contains(strtolower($d->nama_kategori),'keputusan') ? 'sk'
                          : (str_contains(strtolower($d->nama_kategori),'rps') ? 'rp' : 'none')))
                          : 'none');
                ?>

                <div class="riwayat-row">

                    
                    <div><?php echo e($docs->firstItem() + $i); ?></div>

                    
                    <div>
                        <div class="flex items-center gap-3">
                            <div class="doc-icon">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="leading-tight">
                                <div class="doc-title"><?php echo e($d->judul); ?></div>
                                <?php if(!empty($d->nomor_dokumen)): ?>
                                    <div class="doc-sub"><?php echo e($d->nomor_dokumen); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    
                    <div>
                        <span class="chip chip--<?php echo e($alias); ?>"><?php echo e($d->nama_kategori ?? 'Tidak Ada Kategori'); ?></span>
                    </div>

                    
                    <div class="text-sm text-gray-700">
                        <?php echo e(\Carbon\Carbon::parse($d->created_at)->locale('id')->translatedFormat('d F Y')); ?>

                    </div>

                    
                    <div class="text-sm text-gray-700">
                        <?php echo e($recipientsMap[$d->dokumen_id] ?? '–'); ?>

                    </div>

                    
                    <div>
                        <a href="<?php echo e(route('tu.dokumen.show', $d->dokumen_id)); ?>"
                           class="text-[#050C9C] hover:underline font-medium text-sm">Detail</a>
                    </div>

                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="riwayat-row">
                    <div class="col-span-6 text-center text-gray-500">
                        Belum ada unggahan dokumen.
                    </div>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="border-t border-gray-100">
            <?php echo e($docs->links()); ?>

        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
  <?php echo app('Illuminate\Foundation\Vite')('resources/js/tu/riwayat.js'); ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SEMESTER 3 TEKNIK INFORMATIKA\PROYEK-3-PBO\APK-MANAJEMEN-DOSEN\devOps\devOps\resources\views/tu/riwayat-upload.blade.php ENDPATH**/ ?>