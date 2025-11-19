<?php $__env->startSection('title', 'Notifikasi TU - SiDoRa'); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-6">
        <div class="bg-white/40 backdrop-blur-lg p-6 rounded-2xl shadow border border-white/30">
            <h1 class="text-2xl font-bold text-[#050C9C]">Semua Notifikasi</h1>
            <p class="text-gray-500">Daftar notifikasi akses dokumen yang diberikan kepada TU.</p>
        </div>

        <div class="bg-white/40 backdrop-blur-lg p-4 md:p-6 rounded-2xl shadow border border-white/30">
            <?php if($notifikasi->isEmpty()): ?>
                <p class="text-gray-500">Belum ada notifikasi.</p>
            <?php else: ?>
                <ul class="divide-y divide-gray-200">
                    <?php $__currentLoopData = $notifikasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="py-4 flex items-start justify-between">
                            <div class="flex gap-3">
                                <div
                                    class="bg-blue-100 text-[#050C9C] rounded-full w-10 h-10 flex items-center justify-center">
                                    <i class="fa-solid fa-file-circle-check"></i>
                                </div>
                                <div>
                                    <p class="text-gray-700">
                                        <strong><?php echo e($notif->pemberiAkses->nama_lengkap ?? 'Dosen'); ?></strong>
                                        memberi akses dokumen
                                        <span class="font-semibold text-[#050C9C]">
                                            “<?php echo e($notif->dokumen->judul ?? 'Dokumen'); ?>”
                                        </span>
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        <?php echo e(\Carbon\Carbon::parse($notif->created_at)->isoFormat('D MMMM Y, HH:mm')); ?>

                                        (<?php echo e(\Carbon\Carbon::parse($notif->created_at)->diffForHumans()); ?>)
                                    </p>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>

                <div class="mt-4">
                    <?php echo e($notifikasi->withQueryString()->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SEMESTER 3 TEKNIK INFORMATIKA\PROYEK-3-PBO\APK-MANAJEMEN-DOSEN\devOps\devOps\resources\views/tu/notifikasi.blade.php ENDPATH**/ ?>