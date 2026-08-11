@extends('layouts.app')

@section('title', 'Notifikasi TU - SiDoRa')

@section('content')
    <div class="space-y-6">
        <div class="bg-white/40 backdrop-blur-lg p-6 rounded-2xl shadow border border-white/30">
            <h1 class="text-2xl font-bold text-[#050C9C]">Semua Notifikasi</h1>
            <p class="text-gray-500">Daftar notifikasi akses dokumen yang diberikan kepada TU.</p>
        </div>

        <div class="bg-white/40 backdrop-blur-lg p-4 md:p-6 rounded-2xl shadow border border-white/30">
            @if ($notifikasi->isEmpty())
                <p class="text-gray-500">Belum ada notifikasi.</p>
            @else
                <ul class="divide-y divide-gray-200">
                    @foreach ($notifikasi as $notif)
                        <li class="py-4 flex items-start justify-between">
                            <div class="flex gap-3">
                                <div
                                    class="bg-blue-100 text-[#050C9C] rounded-full w-10 h-10 flex items-center justify-center">
                                    <i class="fa-solid fa-file-circle-check"></i>
                                </div>
                                <div>
                                    <p class="text-gray-700">
                                        <strong>{{ $notif->pemberiAkses->nama_lengkap ?? 'Dosen' }}</strong>
                                        memberi akses dokumen
                                        <span class="font-semibold text-[#050C9C]">
                                            “{{ $notif->dokumen->judul ?? 'Dokumen' }}”
                                        </span>
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ \Carbon\Carbon::parse($notif->created_at)->isoFormat('D MMMM Y, HH:mm') }}
                                        ({{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }})
                                    </p>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-4">
                    {{ $notifikasi->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
