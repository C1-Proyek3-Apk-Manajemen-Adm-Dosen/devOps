<?php $__env->startSection('title', 'Dashboard TU - SiDoRa'); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-6">

        
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[#050C9C]">Dashboard Tata Usaha</h1>
                <p class="text-gray-500">Selamat datang kembali, <?php echo e(Auth::user()->nama_lengkap ?? 'Tata Usaha'); ?> 👋</p>
            </div>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
            
            <div class="bg-white/40 backdrop-blur-lg p-6 rounded-2xl shadow border border-white/30">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-[#050C9C] font-semibold">Total Surat Keputusan</h3>
                        <p class="text-4xl font-extrabold text-[#050C9C] mt-2"><?php echo e($totalKeputusan); ?></p>
                    </div>
                    <div class="bg-blue-100 text-[#050C9C] p-3 rounded-xl shadow-inner">
                        <i class="fa-solid fa-file text-2xl"></i>
                    </div>
                </div>
            </div>

            
            <div class="bg-white/40 backdrop-blur-lg p-6 rounded-2xl shadow border border-white/30">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-[#050C9C] font-semibold">Total Surat Tugas</h3>
                        <p class="text-4xl font-extrabold text-[#050C9C] mt-2"><?php echo e($totalTugas); ?></p>
                    </div>
                    <div class="bg-blue-100 text-[#050C9C] p-3 rounded-xl shadow-inner">
                        <i class="fa-solid fa-file text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="bg-white/40 backdrop-blur-lg p-6 rounded-2xl shadow border border-white/30 col-span-2">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-[#050C9C]">Aktivitas Unggah Dokumen</h3>
                    <a href="<?php echo e(route('tu.riwayat')); ?>"
                        class="text-sm px-3 py-1 border border-[#050C9C]/40 text-[#050C9C] rounded-full hover:bg-[#050C9C] hover:text-white transition inline-block">
                        Lihat Histori
                    </a>

                </div>
                <div class="flex items-center gap-2 mb-2">
                    <i class="fa-solid fa-circle-check text-[#050C9C]"></i>
                    <p class="text-sm text-gray-700">
                        Total <span class="font-bold"><?php echo e(array_sum($jumlah->toArray())); ?></span>
                    </p>
                </div>
                
                <canvas id="uploadChart" height="120" data-tanggal='<?php echo json_encode($tanggal, 15, 512) ?>'
                    data-jumlah='<?php echo json_encode($jumlah, 15, 512) ?>'></canvas>
            </div>

            
            <div
                class="bg-gradient-to-br from-[#050C9C] to-blue-500 text-white p-6 rounded-2xl shadow flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-semibold mb-2">Mau Upload Dokumen Lagi?</h3>
                    <p class="text-sm text-blue-100 mb-4">Klik tombol di bawah untuk mengunggah dokumen baru.</p>
                </div>
                <a href="<?php echo e(route('tu.upload')); ?>"
                    class="bg-white text-[#050C9C] font-semibold py-2 px-4 rounded-xl text-center hover:bg-blue-100 transition">
                    <i class="fa-solid fa-upload mr-2"></i> Upload Sekarang
                </a>
            </div>
        </div>

        
        <div class="bg-white/40 backdrop-blur-lg p-6 rounded-2xl shadow border border-white/30 mt-4">
            <h3 class="text-lg font-semibold text-[#050C9C] mb-4">Notifikasi Terbaru</h3>

            <?php if($notifikasi->isEmpty()): ?>
                <p class="text-gray-500">Belum ada notifikasi terbaru.</p>
            <?php else: ?>
                <ul class="divide-y divide-gray-200">
                    <?php $__currentLoopData = $notifikasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="py-3 flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div
                                    class="bg-blue-100 text-[#050C9C] rounded-full w-8 h-8 flex items-center justify-center">
                                    <i class="fa-solid fa-bell text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-gray-800 font-medium"><?php echo e($n->nama_lengkap); ?></p>
                                    <p class="text-gray-500 text-sm">Memberi akses ke dokumen:
                                        <span class="font-semibold text-[#050C9C]"><?php echo e($n->judul); ?></span>
                                    </p>
                                </div>
                            </div>
                            <span
                                class="text-xs text-gray-400"><?php echo e(\Carbon\Carbon::parse($n->created_at)->diffForHumans()); ?></span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/tu/dashboard-chart.js']); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SEMESTER 3 TEKNIK INFORMATIKA\PROYEK-3-PBO\APK-MANAJEMEN-DOSEN\devOps\devOps\resources\views/tu/dashboard.blade.php ENDPATH**/ ?>