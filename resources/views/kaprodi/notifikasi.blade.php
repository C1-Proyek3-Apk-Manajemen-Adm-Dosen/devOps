@extends('layouts.app')

@section('title', 'Notifikasi Kaprodi')

@section('content')

<div class="space-y-6">
    <h1 class="text-2xl font-bold text-[#050C9C] mb-4">Semua Notifikasi</h1>

    <div class="bg-white/40 backdrop-blur-lg p-6 rounded-2xl">
        <ul class="divide-y divide-gray-200">

            @foreach($notifikasi as $notif)
                <li class="py-3 flex justify-between items-center">
                    <div>
                        <p class="font-semibold text-gray-800">
                            {{ $notif->pemberiAkses->nama_lengkap ?? 'Pengguna' }}
                        </p>
                        <p class="text-gray-600 text-sm">
                            memberi akses dokumen:
                            <span class="text-[#050C9C] font-semibold">
                                {{ $notif->dokumen->judul ?? 'Dokumen' }}
                            </span>
                        </p>
                    </div>

                    <p class="text-xs text-gray-400">
                        {{ \Carbon\Carbon::parse($notif->created_at)->translatedFormat('d F Y, H:i') }}
                        ({{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }})
                    </p>

                </li>
            @endforeach

        </ul>
    </div>

    <div class="mt-4">
        {{ $notifikasi->links() }}
    </div>
</div>

@endsection
