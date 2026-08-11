@extends('layouts.app')

@section('title', 'Dashboard Dosen - SiDoRa')

@section('content')
    <div class="space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-[#050C9C]">Dashboard Dosen</h1>
            <p class="text-gray-500">
                Selamat datang kembali, {{ Auth::user()->nama_lengkap ?? 'Dosen' }} 👋
            </p>
        </div>

        {{-- Statistik --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
            {{-- Total RPS --}}
            <div class="bg-white/40 backdrop-blur-lg p-6 rounded-2xl shadow border border-white/30">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-[#050C9C] font-semibold">Total Dokumen RPS</h3>
                        <p class="text-4xl font-extrabold text-[#050C9C] mt-2">{{ $totalRPS }}</p>
                    </div>
                    <div class="bg-blue-100 text-[#050C9C] p-3 rounded-xl shadow-inner">
                        <i class="fa-solid fa-book text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Total SKP --}}
            <div class="bg-white/40 backdrop-blur-lg p-6 rounded-2xl shadow border border-white/30">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-[#050C9C] font-semibold">Total Dokumen SKP</h3>
                        <p class="text-4xl font-extrabold text-[#050C9C] mt-2">{{ $totalSKP }}</p>
                    </div>
                    <div class="bg-blue-100 text-[#050C9C] p-3 rounded-xl shadow-inner">
                        <i class="fa-solid fa-file-circle-check text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Total BKD --}}
            <div class="bg-white/40 backdrop-blur-lg p-6 rounded-2xl shadow border border-white/30">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-[#050C9C] font-semibold">Total Dokumen BKD</h3>
                        <p class="text-4xl font-extrabold text-[#050C9C] mt-2">{{ $totalBKD }}</p>
                    </div>
                    <div class="bg-blue-100 text-[#050C9C] p-3 rounded-xl shadow-inner">
                        <i class="fa-solid fa-file-lines text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grafik & Upload --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Grafik Aktivitas --}}
            <div class="bg-white/40 backdrop-blur-lg p-6 rounded-2xl shadow border border-white/30 col-span-2">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-[#050C9C]">Aktivitas Unggah Dokumen</h3>
                    <a href="{{ route('dosen.riwayat') ?? '#' }}"
                       class="text-sm px-3 py-1 border border-[#050C9C]/40 text-[#050C9C] rounded-full hover:bg-[#050C9C] hover:text-white transition inline-block">
                        Lihat Histori
                    </a>
                </div>

                <div class="flex items-center gap-2 mb-2">
                    <i class="fa-solid fa-circle-check text-[#050C9C]"></i>
                    <p class="text-sm text-gray-700">
                        Total <span class="font-bold">{{ array_sum($jumlah->toArray()) }}</span>
                    </p>
                </div>

                <canvas id="uploadChart" height="120"
                    data-tanggal='@json($tanggal)'
                    data-jumlah='@json($jumlah)'></canvas>
            </div>

            {{-- Upload Cepat --}}
            <div class="bg-gradient-to-br from-[#050C9C] to-blue-500 text-white p-6 rounded-2xl shadow flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-semibold mb-2">Mau Upload Dokumen?</h3>
                    <p class="text-sm text-blue-100 mb-4">Klik tombol di bawah untuk mengunggah dokumen baru.</p>
                </div>
                <a href="{{ route('dosen.upload') ?? '#' }}"
                    class="bg-white text-[#050C9C] font-semibold py-2 px-4 rounded-xl text-center hover:bg-blue-100 transition">
                    <i class="fa-solid fa-upload mr-2"></i> Upload Sekarang
                </a>
            </div>
        </div>

        {{-- Notifikasi Terbaru --}}
        <div class="bg-white/40 backdrop-blur-lg p-6 rounded-2xl shadow border border-white/30 mt-4">
            <h3 class="text-lg font-semibold text-[#050C9C] mb-4">Notifikasi Terbaru</h3>

            @if ($notifikasi->isEmpty())
                <p class="text-gray-500">Belum ada notifikasi terbaru.</p>
            @else
                <ul class="divide-y divide-gray-200">
                    @foreach ($notifikasi as $n)
                        <li class="py-3 flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-100 text-[#050C9C] rounded-full w-8 h-8 flex items-center justify-center">
                                    <i class="fa-solid fa-bell text-sm"></i>
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

    {{-- Script Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/js/tu/dashboard-chart.js'])
@endsection
