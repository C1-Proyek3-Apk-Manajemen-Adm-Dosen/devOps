@extends('layouts.app')

@section('title', 'Detail Dokumen - SiDoRa')

@push('styles')
    {{-- pakai css riwayat supaya chip kategori sama seperti di tabel --}}
    @vite('resources/css/dosen/riwayat.css')
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

            {{-- GRID 2 KOLOM: kiri & kanan persis seperti contoh --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- ================= KIRI ================= --}}
                <div class="space-y-4">

                    {{-- Nomor Dokumen besar --}}
                    <div>
                        <div class="bg-gradient-to-r from-[#050C9C] to-[#1554ff] text-white rounded-2xl px-5 py-5 shadow-md">
                            <div class="text-xs font-semibold uppercase tracking-wide opacity-80 mb-1">
                                Nomor Dokumen
                            </div>
                            <div class="text-2xl md:text-3xl font-semibold leading-tight">
                                {{ $dokumen->nomor_dokumen ?: '-' }}
                            </div>
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

                    {{-- Versi Dokumen --}}
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-gray-700">
                            Versi Dokumen
                        </label>

                        <div class="border border-gray-200 rounded-2xl px-4 py-2.5 bg-gray-50 text-sm text-gray-800 mb-2">
                            @if($latest)
                                Versi {{ $latest->nomor_versi }}
                            @else
                                Belum ada versi dokumen.
                            @endif
                        </div>

                        {{-- Tombol file --}}
                        @if($latest && !empty($latest->file_path))
                            <a href="{{ $latest->file_path }}"
                               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl text-sm font-medium bg-[#050C9C] text-white hover:bg-[#001070] transition">
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
@endsection
