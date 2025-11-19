@extends('layouts.app')

@section('title', 'Detail Dokumen - SiDoRa')

@push('styles')
<style>
    /* Icon Versi Dokumen — Outline Putih */
    .icon-tag-outline {
        color: transparent !important;
        -webkit-text-stroke: 1.6px #fff !important;
        text-stroke: 1.6px #fff !important;
        font-weight: 900 !important;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .bg-white {
        animation: fadeInUp 0.5s ease-out;
    }

    button:hover {
        transform: translateY(-2px);
    }

    @media (max-width: 640px) {
        .text-3xl {
            font-size: 1.75rem;
        }

        .text-2xl {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="p-6 md:p-8" id="detailBox">

    {{-- ====== BACK BUTTON (icon-only box + text di luar) ====== --}}
    <div class="mb-6 flex items-center gap-3">

        {{-- Kotak Icon --}}
        <a href="{{ route('dosen.riwayat') }}"
           class="flex items-center justify-center w-10 h-10 rounded-xl bg-white shadow-md border border-slate-200
                  hover:shadow-lg transition">
            <i class="fa-solid fa-chevron-left text-slate-700 text-sm"></i>
        </a>

        {{-- Text Judul --}}
        <span class="text-lg font-semibold text-slate-800">
            Detail Dokumen
        </span>

    </div>

    {{-- ===== CARD PUTIH ===== --}}
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden">

        {{-- HEADER BIRU --}}
        <div class="bg-gradient-to-r from-[#050C9C] to-blue-600 px-6 py-4 md:px-8">
            <h1 class="text-white text-lg md:text-xl font-semibold">Detail Dokumen</h1>
        </div>

        {{-- ===== ISI CARD ===== --}}
        <div class="p-6 md:p-8">

            @php
                $versiTerbaru = $latest ?? $latestVersi ?? null;
                $kategoriNama = $dokumen->kategori->nama ?? $dokumen->nama_kategori ?? 'Tidak ada';
            @endphp

            {{-- GRID 2 KOLOM (50:50) --}}
            <div class="grid md:grid-cols-2 gap-6 md:gap-8">

                {{-- ========= KOLOM KIRI ========= --}}
                <div class="space-y-5">

                    {{-- Nomor Dokumen --}}
                    <div class="bg-gradient-to-r from-[#050C9C] to-blue-600 text-white rounded-2xl px-6 py-4 shadow-lg">
                        <p class="text-[13px] font-semibold text-blue-100">Nomor Dokumen</p>
                        <p class="mt-1 text-3xl font-semibold leading-tight">
                            {{ $dokumen->nomor_dokumen ?? '—' }}
                        </p>
                    </div>

                    {{-- Judul --}}
                    <div>
                        <p class="text-[13px] font-semibold text-slate-700">Judul Dokumen</p>
                        <div class="mt-1 px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50">
                            {{ $dokumen->judul ?? '-' }}
                        </div>
                    </div>

                    {{-- Tanggal --}}
                    <div>
                        <p class="text-[13px] font-semibold text-slate-700">Tanggal Upload</p>
                        <div class="mt-1 px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50">
                            {{ optional($dokumen->created_at)->translatedFormat('d F Y') ?? '-' }}
                        </div>
                    </div>

                    {{-- Kategori (dalam kotak besar) --}}
                    <div>
                        <p class="text-[13px] font-semibold text-slate-700">Kategori Dokumen</p>
                        <div class="mt-1 px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 flex items-center">
                            <span
                                class="inline-flex items-center px-4 py-1.5 rounded-full border border-blue-200 bg-blue-50 
                                       font-semibold text-blue-700 text-sm">
                                {{ $kategoriNama }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- ========= KOLOM KANAN ========= --}}
                <div class="flex flex-col gap-6">

                    {{-- Deskripsi --}}
                    <div>
                        <p class="text-[13px] font-semibold text-slate-700">Deskripsi Dokumen</p>
                        <div class="mt-1 px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 min-h-[140px]">
                            {{ $dokumen->deskripsi ?? '-' }}
                        </div>
                    </div>

                    {{-- ========== VERSI + DOWNLOAD FULL WIDTH ========== --}}
                    <div class="space-y-3">

                        {{-- Label Versi --}}
                        <p class="text-[13px] font-semibold text-slate-700">Versi Dokumen</p>

                        {{-- Box Versi --}}
                        @if($versiTerbaru)
                            <div class="w-full flex items-center px-4 py-2.5 rounded-xl 
                                        border border-slate-200 bg-slate-50">

                                {{-- ICON GRADIENT OUTLINE --}}
                                <span class="flex items-center justify-center w-12 h-12 rounded-xl
                                             bg-gradient-to-r from-[#050C9C] to-[#1E40FF] shadow-md">
                                    <i class="fa-solid fa-tag icon-tag-outline text-lg"></i>
                                </span>

                                {{-- Text Versi --}}
                                <span class="ml-4 text-base font-semibold text-slate-800">
                                    v{{ $versiTerbaru->nomor_versi ?? $versiTerbaru->versi ?? '1' }}
                                </span>
                            </div>
                        @else
                            <div class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-500">
                                Belum ada versi dokumen.
                            </div>
                        @endif

                        {{-- Tombol Download --}}
                        <div>
                            @if($versiTerbaru && !empty($versiTerbaru->file_path))
                                <a href="{{ $versiTerbaru->file_path }}"
                                   class="inline-flex items-center justify-center w-full px-6 py-3 rounded-xl
                                          bg-gradient-to-r from-[#050C9C] to-blue-600 text-white font-semibold
                                          shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition">
                                    <i class="fa-solid fa-download mr-2"></i>
                                    Download Dokumen
                                </a>
                            @else
                                <button disabled
                                    class="inline-flex items-center justify-center w-full px-6 py-3 rounded-xl
                                           bg-slate-300 text-slate-600 font-semibold cursor-not-allowed">
                                    <i class="fa-solid fa-download mr-2"></i>
                                    File belum tersedia
                                </button>
                            @endif
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
