<?php
$recentNotifikasi = \App\Models\AccessControl::with(['pemberiAkses','dokumen'])
    ->where('grantee_user_id', Auth::user()->id_user)
    ->orderByDesc('created_at')
    ->take(5)
    ->get();

$route = route('dosen.notifikasi');
?>

<?php if (isset($component)) { $__componentOriginalb22d4a57e45e7fc3865f5f20381d6eba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb22d4a57e45e7fc3865f5f20381d6eba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.notification.notification-base','data' => ['data' => $recentNotifikasi,'route' => $route]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('notification.notification-base'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($recentNotifikasi),'route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($route)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb22d4a57e45e7fc3865f5f20381d6eba)): ?>
<?php $attributes = $__attributesOriginalb22d4a57e45e7fc3865f5f20381d6eba; ?>
<?php unset($__attributesOriginalb22d4a57e45e7fc3865f5f20381d6eba); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb22d4a57e45e7fc3865f5f20381d6eba)): ?>
<?php $component = $__componentOriginalb22d4a57e45e7fc3865f5f20381d6eba; ?>
<?php unset($__componentOriginalb22d4a57e45e7fc3865f5f20381d6eba); ?>
<?php endif; ?>

<?php /**PATH D:\SEMESTER 3 TEKNIK INFORMATIKA\PROYEK-3-PBO\APK-MANAJEMEN-DOSEN\devOps\devOps\resources\views/components/notification/notification-dosen.blade.php ENDPATH**/ ?>