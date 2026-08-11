@php
$recentNotifikasi = \App\Models\AccessControl::with(['pemberiAkses','dokumen'])
    ->where('grantee_user_id', Auth::user()->id_user)
    ->orderByDesc('created_at')
    ->take(5)
    ->get();

$route = route('dosen.notifikasi');
@endphp

<x-notification.notification-base :data="$recentNotifikasi" :route="$route" />

