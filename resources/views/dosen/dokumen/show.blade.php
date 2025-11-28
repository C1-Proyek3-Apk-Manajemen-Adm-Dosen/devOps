@extends('layouts.app')

@section('title', 'Detail Dokumen - SiDoRa')

@push('styles')
    @vite('resources/css/dosen/riwayat.css')

    <style>
        /* Outline putih untuk icon tag (versi dokumen) */
        .icon-tag-outline {
            color: transparent !important;
            -webkit-text-stroke: 1.6px #fff !important;
            font-weight: 900 !important;
        }

        /* Icon copy di kartu nomor dokumen */
        .copy-icon-btn {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #ffffff;
            font-size: 1.3rem;
            opacity: .9;
            transition: .15s;
        }

        .copy-icon-btn:hover {
            opacity: 1;
        }

        /* Toast: di bawah tengah layar (warna abu seperti TU) */
        .toast-copy {
            position: fixed;
            left: 50%;
            bottom: 24px;
            transform: translateX(-50%);
            background: #374151;   /* ABU-ABU GELAP */
            color: white;
            padding: 8px 20px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            display: none;
            z-index: 9999;
            box-shadow: 0 6px 20px rgba(0,0,0,0.18);
            white-space: nowrap;
        }
    </style>
@endpush

@section('content')
<div class="p-4 md:p-8 min-h-screen">

    {{-- Tombol back + judul halaman --}}
    <div class="mb-4 flex items-center gap-3">
        <a href="{{ route('dosen.riwayat') }}"
           class="w-9 h-9 flex items-center justify-center rounded-xl shadow-sm bg-white text-gray-600 hover:bg-gray-50">
            <i class="fa-solid fa-chevron-left text-sm"></i>
        </a>
        <h1 class="text-lg md:text-xl font-semibold text-gray-800">
            Detail Dokumen
        </h1>
    </div>

    {{-- Card utama --}}
    <div class="bg-white rounded-3xl shadow-md overflow-hidden">

        {{-- Header biru --}}
        <div class="px-6 py-4 md:px-8 md:py-5 bg-gradient-to-r from-[#050C9C] to-[#1554ff]">
            <h2 class="text-white font-semibold text-lg">
                Detail Dokumen
            </h2>
        </div>

        {{-- Isi --}}
        <div class="px-6 py-6 md:px-8 md:py-8">

            {{-- GRID 2 KOLOM: kiri & kanan --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- ================= KIRI ================= --}}
                <div class="space-y-4">

                    {{-- Nomor Dokumen besar + icon copy --}}
                    <div class="relative">
                        <div class="bg-gradient-to-r from-[#050C9C] to-[#1554ff] text-white rounded-2xl px-5 py-5 shadow-md">
                            <div class="text-xs font-semibold uppercase tracking-wide opacity-80 mb-1">
                                Nomor Dokumen
                            </div>
                            <div id="nomorDokumenText" class="text-2xl md:text-3xl font-semibold leading-tight">
                                {{ $dokumen->nomor_dokumen ?: '-' }}
                            </div>

                            {{-- ICON COPY --}}
                            <i class="fa-regular fa-copy copy-icon-btn"
                               onclick="copyNomorDokumen()"></i>
                        </div>
                    </div>

                    {{-- Judul Dokumen --}}
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-gray-700">
                            Judul Dokumen
                        </label>
                        <div class="border border-gray-200 rounded-2xl px-4 py-2.5 bg-gray-50 text-sm text-gray-800">
                            {{ $dokumen->judul ?? '-' }}
                        </div>
                    </div>

                    {{-- Tanggal Upload --}}
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-gray-700">
                            Tanggal Upload
                        </label>
                        <div class="border border-gray-200 rounded-2xl px-4 py-2.5 bg-gray-50 text-sm text-gray-800">
                            {{ \Carbon\Carbon::parse($dokumen->created_at)->locale('id')->translatedFormat('d F Y') }}
                        </div>
                    </div>

                    {{-- Kategori Dokumen (kotak + chip di dalam) --}}
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-gray-700">
                            Kategori Dokumen
                        </label>

                        @php
                            $katName   = optional($dokumen->kategori)->nama_kategori;
                            $lowerName = $katName ? strtolower(trim($katName)) : null;
                            $chipClass = 'chip-default';

                            if ($lowerName === 'bkd') {
                                $chipClass = 'chip-bkd';
                            } elseif ($lowerName === 'bukti pengajaran') {
                                $chipClass = 'chip-bukti-pengajaran';
                            } elseif ($lowerName === 'buku kerja dosen') {
                                $chipClass = 'chip-buku-kerja-dosen';
                            } elseif ($lowerName === 'rps') {
                                $chipClass = 'chip-rps';
                            } elseif ($lowerName === 'skp') {
                                $chipClass = 'chip-skp';
                            } elseif ($lowerName === 'surat keputusan') {
                                $chipClass = 'chip-sk';
                            } elseif ($lowerName === 'surat tugas') {
                                $chipClass = 'chip-st';
                            }
                        @endphp

                        <div class="border border-gray-200 rounded-2xl px-4 py-2.5 bg-gray-50 flex items-center">
                            @if ($katName)
                                <span class="chip {{ $chipClass }}">
                                    {{ $katName }}
                                </span>
                            @else
                                <span class="inline-flex items-center justify-center px-4 py-1.5 rounded-full border border-[#c7d2fe] bg-white text-xs md:text-sm text-[#050C9C] font-semibold">
                                    Tidak ada
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ================= KANAN ================= --}}
                <div class="space-y-4">

                    {{-- Deskripsi Dokumen --}}
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-gray-700">
                            Deskripsi Dokumen
                        </label>
                        <div class="border border-gray-200 rounded-2xl bg-gray-50 px-4 py-3 min-h-[120px] text-sm text-gray-700">
                            {{ $dokumen->deskripsi ?? '-' }}
                        </div>
                    </div>

                    {{-- Versi Dokumen (dengan icon tag biru gradasi) --}}
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-gray-700">
                            Versi Dokumen
                        </label>

                        @if($latest)
                            <div class="w-full flex items-center px-4 py-2.5 rounded-xl
                                        border border-gray-200 bg-gray-50">
                                <span class="flex items-center justify-center w-12 h-12 rounded-xl
                                             bg-gradient-to-r from-[#050C9C] to-[#1E40FF] shadow-md">
                                    <i class="fa-solid fa-tag icon-tag-outline text-lg"></i>
                                </span>

                                <span class="ml-4 text-base font-semibold text-slate-800">
                                    v{{ $latest->nomor_versi }}
                                </span>
                            </div>
                        @else
                            <div class="border border-gray-200 rounded-2xl px-4 py-2.5 bg-gray-50 text-sm text-gray-800 mb-2">
                                Belum ada versi dokumen.
                            </div>
                        @endif

                        {{-- Tombol file --}}
                        @if($latest && !empty($latest->file_path))
                            <a href="{{ $latest->file_path }}"
                               class="inline-flex items-center justify-center w-full gap-2 px-4 py-2.5 rounded-2xl text-sm font-medium
                                      bg-gradient-to-r from-[#050C9C] to-[#1E40FF] text-white
                                      shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition">
                                <i class="fa-solid fa-download text-xs"></i>
                                Unduh File
                            </a>
                        @else
                            <div class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl text-sm font-medium bg-gray-200 text-gray-600">
                                <i class="fa-solid fa-download text-xs opacity-70"></i>
                                File belum tersedia
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- (opsional) daftar semua versi dokumen di bawah --}}
            @if($versi->count() > 1)
                <div class="mt-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">
                        Riwayat Versi Dokumen
                    </h3>
                    <div class="space-y-2 text-sm">
                        @foreach($versi as $v)
                            <div class="flex items-center justify-between border border-gray-200 rounded-xl px-4 py-2 bg-gray-50">
                                <span>Versi {{ $v->nomor_versi }}</span>
                                <span class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($v->created_at)->locale('id')->translatedFormat('d F Y') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

{{-- Toast copy --}}
<div id="toast-copy" class="toast-copy">
    Nomor dokumen disalin
</div>

@endsection

@push('scripts')
<script>
    function copyNomorDokumen() {
        const textEl = document.getElementById('nomorDokumenText');
        if (!textEl) return;

        const text = textEl.innerText || '';
        if (!text.trim()) return;

        navigator.clipboard.writeText(text);

        const toast = document.getElementById('toast-copy'); // <-- ID BENAR
        if (!toast) return;

        toast.style.display = 'block';

        setTimeout(() => {
            toast.style.display = 'none';
        }, 1800);
    }
</script>
@endpush
