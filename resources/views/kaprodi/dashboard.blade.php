@extends('layouts.app')

@section('title', 'Dashboard Kaprodi')

@section('content')

<div class="px-6 py-4">

        {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-[#050C9C]">Dashboard Kaprodi</h1>
        <p class="text-gray-500">Monitoring status dokumen dosen</p>
    </div>

    {{-- Statistik Dokumen --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-4">

        {{-- Pending --}}
        <div class="bg-white p-6 rounded-2xl shadow flex items-center justify-between border border-gray-200">
            <div>
                <h3 class="text-[#050C9C] font-semibold">Menunggu Review</h3>
                <p class="text-4xl font-extrabold text-[#050C9C] mt-2">{{ $pending }}</p>
            </div>
            <div class="bg-blue-100 text-[#050C9C] p-3 rounded-xl shadow-inner">
                <i class="fa-solid fa-hourglass-half text-2xl"></i>
            </div>
        </div>

        {{-- ACC --}}
        <div class="bg-white p-6 rounded-2xl shadow flex items-center justify-between border border-gray-200">
            <div>
                <h3 class="text-[#050C9C] font-semibold">Dokumen Valid</h3>
                <p class="text-4xl font-extrabold text-[#050C9C] mt-2">{{ $acc }}</p>
            </div>
            <div class="bg-blue-100 text-[#050C9C] p-3 rounded-xl shadow-inner">
                <i class="fa-solid fa-circle-check text-2xl"></i>
            </div>
        </div>

        {{-- Revisi --}}
        <div class="bg-white p-6 rounded-2xl shadow flex items-center justify-between border border-gray-200">
            <div>
                <h3 class="text-[#050C9C] font-semibold">Perlu Revisi</h3>
                <p class="text-4xl font-extrabold text-[#050C9C] mt-2">{{ $revisi }}</p>
            </div>
            <div class="bg-blue-100 text-[#050C9C] p-3 rounded-xl shadow-inner">
                <i class="fa-solid fa-pen-to-square text-2xl"></i>
            </div>
        </div>

        {{-- Ditolak --}}
        <div class="bg-white p-6 rounded-2xl shadow flex items-center justify-between border border-gray-200">
            <div>
                <h3 class="text-[#050C9C] font-semibold">Ditolak</h3>
                <p class="text-4xl font-extrabold text-[#050C9C] mt-2">{{ $tolak }}</p>
            </div>
            <div class="bg-blue-100 text-[#050C9C] p-3 rounded-xl shadow-inner">
                <i class="fa-solid fa-xmark text-2xl"></i>
            </div>
        </div>

    </div>
    {{-- Notifikasi Terbaru --}}
<div class="bg-white/40 backdrop-blur-lg p-6 rounded-2xl shadow border border-white/30 mt-6">

    <h3 class="text-lg font-semibold text-[#050C9C] mb-4">Notifikasi Terbaru</h3>

    @if ($notifikasi->isEmpty())
        <p class="text-gray-500">Belum ada notifikasi terbaru.</p>
    @else
        <ul class="divide-y divide-gray-200">
            @foreach ($notifikasi as $n)
                <li class="py-3 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-100 text-[#050C9C] rounded-full w-10 h-10 flex items-center justify-center">
                            <i class="fa-solid fa-bell text-lg"></i>
                        </div>

                        <div>
                            <p class="text-gray-800 font-medium">{{ $n->nama_lengkap }}</p>
                            <p class="text-gray-500 text-sm">
                                Memberi akses ke dokumen:
                                <span class="font-semibold text-[#050C9C]">{{ $n->judul }}</span>
                            </p>
                        </div>
                    </div>

                    <span class="text-xs text-gray-400">
                        {{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}
                    </span>
                </li>
            @endforeach
        </ul>
    @endif
</div>

</div>

@endsection
