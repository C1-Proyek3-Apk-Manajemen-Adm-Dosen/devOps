@extends('layouts.app')

@section('title', 'Notifikasi Dosen')

@section('content')
<div class="space-y-6">

    <h1 class="text-2xl font-bold text-[#050C9C]">Semua Notifikasi</h1>
    <p class="text-gray-500 mb-4">Daftar notifikasi akses dokumen yang diberikan kepada Anda.</p>

    <div class="bg-white/40 backdrop-blur-lg p-6 rounded-2xl shadow border border-white/30">
        @if ($notifikasi->isEmpty())
            <p class="text-gray-500 text-center py-6">Belum ada notifikasi.</p>
        @else
            <ul class="divide-y divide-gray-200">
                @foreach ($notifikasi as $n)
                    <li class="py-3 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-100 text-[#050C9C] rounded-full w-8 h-8 flex items-center justify-center">
                                <i class="fa-solid fa-bell text-sm"></i>
                            </div>
                            <div>
                                <p class="text-gray-800 font-medium">
                                    {{ $n->pemberiAkses->nama_lengkap ?? 'Pengguna' }}
                                </p>
                                <p class="text-gray-500 text-sm">
                                    Memberi akses ke dokumen:
                                    <span class="font-semibold text-[#050C9C]">
                                        {{ $n->dokumen->judul ?? '-' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-400">
                            {{ \Carbon\Carbon::parse($n->created_at)->translatedFormat('d F Y, H:i') }}
                            ({{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }})
                        </span>

                    </li>
                @endforeach
            </ul>

            <div class="mt-4">
                {{ $notifikasi->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
