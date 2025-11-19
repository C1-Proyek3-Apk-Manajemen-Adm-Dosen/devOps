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
        <?php if(auth()->guard()->check()): ?>
            <?php if(Auth::user()->role === 'tu'): ?>
                <?php if (isset($component)) { $__componentOriginal78be8df7fa445859a8f9cbb29069b56a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal78be8df7fa445859a8f9cbb29069b56a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.notification.notification-tu','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('notification.notification-tu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal78be8df7fa445859a8f9cbb29069b56a)): ?>
<?php $attributes = $__attributesOriginal78be8df7fa445859a8f9cbb29069b56a; ?>
<?php unset($__attributesOriginal78be8df7fa445859a8f9cbb29069b56a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal78be8df7fa445859a8f9cbb29069b56a)): ?>
<?php $component = $__componentOriginal78be8df7fa445859a8f9cbb29069b56a; ?>
<?php unset($__componentOriginal78be8df7fa445859a8f9cbb29069b56a); ?>
<?php endif; ?>
            <?php elseif(Auth::user()->role === 'dosen'): ?>
                <?php if (isset($component)) { $__componentOriginal6237feff71b86377d0eddd580cc82041 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6237feff71b86377d0eddd580cc82041 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.notification.notification-dosen','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('notification.notification-dosen'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6237feff71b86377d0eddd580cc82041)): ?>
<?php $attributes = $__attributesOriginal6237feff71b86377d0eddd580cc82041; ?>
<?php unset($__attributesOriginal6237feff71b86377d0eddd580cc82041); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6237feff71b86377d0eddd580cc82041)): ?>
<?php $component = $__componentOriginal6237feff71b86377d0eddd580cc82041; ?>
<?php unset($__componentOriginal6237feff71b86377d0eddd580cc82041); ?>
<?php endif; ?>
            <?php elseif(Auth::user()->role === 'koordinator'): ?>
                <?php if (isset($component)) { $__componentOriginalbd7a94fe4365bea7c83b131a87a1ae9a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbd7a94fe4365bea7c83b131a87a1ae9a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.notification.notification-koordinator','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('notification.notification-koordinator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbd7a94fe4365bea7c83b131a87a1ae9a)): ?>
<?php $attributes = $__attributesOriginalbd7a94fe4365bea7c83b131a87a1ae9a; ?>
<?php unset($__attributesOriginalbd7a94fe4365bea7c83b131a87a1ae9a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbd7a94fe4365bea7c83b131a87a1ae9a)): ?>
<?php $component = $__componentOriginalbd7a94fe4365bea7c83b131a87a1ae9a; ?>
<?php unset($__componentOriginalbd7a94fe4365bea7c83b131a87a1ae9a); ?>
<?php endif; ?>
            <?php endif; ?>
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
<?php /**PATH D:\SEMESTER 3 TEKNIK INFORMATIKA\PROYEK-3-PBO\APK-MANAJEMEN-DOSEN\devOps\devOps\resources\views/partials/navbar.blade.php ENDPATH**/ ?>