<nav class="w-full bg-white mt-4 flex items-center justify-between flex-wrap px-4 md:px-6 py-3">
    <!-- Left Section: Logo + Tombol Sidebar -->
    <div class="flex items-center gap-2 flex-shrink-0">
        <!-- Tombol Toggle Sidebar (muncul hanya di mobile) -->
        <button id="toggleSidebar" class="md:hidden mr-2 text-[#050C9C]">
            <i class="fas fa-bars text-2xl"></i>
        </button>

        <!-- Logo -->
        <div class="flex flex-col ml-4">
            <h1 class="text-2xl md:text-4xl font-bold text-[#050C9C] leading-none">SiDoRa</h1>
            <p class="hidden md:block text-xs text-gray-500 leading-none">Sistem Dokumen & Arsip Dosen</p>
        </div>
    </div>

    <!-- Middle Section: Search + Notification -->
    <div
        class="flex items-center gap-3 flex-1 justify-center order-last md:order-none w-full md:w-auto mt-3 md:mt-0 relative">
        <!-- Input Search -->
        <input type="text" placeholder="Search"
            class="w-full md:w-80 border border-gray-300 rounded-full px-5 py-2 text-sm focus:ring-2 focus:ring-purple-400 focus:outline-none transition" />

        <!-- Notifikasi -->
        <?php if (isset($component)) { $__componentOriginal0676521d0d1386b8a24fdc18016b8d4a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0676521d0d1386b8a24fdc18016b8d4a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.notification-dropdown','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('notification-dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0676521d0d1386b8a24fdc18016b8d4a)): ?>
<?php $attributes = $__attributesOriginal0676521d0d1386b8a24fdc18016b8d4a; ?>
<?php unset($__attributesOriginal0676521d0d1386b8a24fdc18016b8d4a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0676521d0d1386b8a24fdc18016b8d4a)): ?>
<?php $component = $__componentOriginal0676521d0d1386b8a24fdc18016b8d4a; ?>
<?php unset($__componentOriginal0676521d0d1386b8a24fdc18016b8d4a); ?>
<?php endif; ?>
    </div>

    <!-- Right Section: Profile + Logout -->
    <div class="flex items-center gap-4 flex-shrink-0">
        <!-- Profil Dinamis -->
        <?php if(auth()->guard()->check()): ?>
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-blue-100 text-[#050C9C] font-bold rounded-full flex items-center justify-center uppercase">
                    <?php echo e(substr(Auth::user()->nama_lengkap, 0, 1)); ?>

                </div>
                <div class="flex flex-col leading-tight">
                    <p class="text-xs text-gray-400">Hi,</p>
                    <p class="text-sm font-semibold">
                        <span class="text-[#050C9C]"><?php echo e(strtoupper(Auth::user()->role)); ?></span>
                        <span class="text-gray-700"><?php echo e(Auth::user()->nama_lengkap); ?></span>
                    </p>
                </div>
            </div>
        <?php else: ?>
            <p class="text-sm text-gray-500">Guest</p>
        <?php endif; ?>
    </div>
</nav>
<?php /**PATH D:\Proyek 3\devOps\resources\views/partials/navbar.blade.php ENDPATH**/ ?>