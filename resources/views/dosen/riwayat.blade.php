@extends('layouts.app')

@section('title', 'Riwayat Upload Dosen - SiDoRa')

@push('styles')
  @vite('resources/css/dosen/riwayat.css')
@endpush

@section('content')
<div class="p-4 md:p-8" id="riwayatBox">

    {{-- Bar atas: Judul & Search --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl md:text-2xl font-semibold text-gray-800">
                Dokumen yang sudah di upload
            </h1>
            <p class="text-gray-500 text-xs md:text-sm mt-1">
                Semua riwayat dokumen yang kamu upload sebagai dosen.
            </p>
        </div>

        <form method="GET" class="w-full md:w-80">
            {{-- bawa nilai filter lain --}}
            <input type="hidden" name="kategori_id" value="{{ request('kategori_id') }}">
            <input type="hidden" name="period" value="{{ request('period') }}">

            <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-xs md:text-sm"></i>
                </span>
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    class="w-full pl-9 pr-3 py-2 rounded-xl border border-gray-300 text-xs md:text-sm
                           focus:outline-none focus:ring-2 focus:ring-[#050C9C]"
                    placeholder="Cari dokumen…">
            </div>
        </form>
    </div>

    {{-- Bar bawah: Filter kategori & period --}}
    <form method="GET" class="flex flex-col sm:flex-row sm:items-center gap-3 mb-6">
        {{-- bawa nilai search --}}
        <input type="hidden" name="q" value="{{ request('q') }}">

        {{-- Jenis dokumen (kategori) --}}
        <div class="select-cat-wrapper w-full sm:w-auto">
            <select name="kategori_id"
                    class="select-cat w-full border border-gray-300 rounded-xl px-3 py-2 text-xs md:text-sm
                           focus:outline-none focus:ring-2 focus:ring-[#050C9C]">
                <option value="all">Semua Jenis Dokumen</option>
                @foreach($kategories as $kat)
                    <option value="{{ $kat->kategori_id }}"
                        @selected((string)request('kategori_id') === (string)$kat->kategori_id)>
                        {{ $kat->nama_kategori }}
                    </option>
                @endforeach
            </select>
            <span class="select-cat-arrow">▾</span>
        </div>

        {{-- Period --}}
        <div class="select-cat-wrapper w-full sm:w-auto">
            <select name="period"
                    class="select-cat w-full border border-gray-300 rounded-xl px-3 py-2 text-xs md:text-sm
                           focus:outline-none focus:ring-2 focus:ring-[#050C9C]">
                <option value="all" @selected(request('period') === 'all' || !request('period'))>
                    Period: All
                </option>
                <option value="30" @selected(request('period') === '30')>30 hari terakhir</option>
                <option value="90" @selected(request('period') === '90')>3 bulan terakhir</option>
                <option value="365" @selected(request('period') === '365')>1 tahun terakhir</option>
            </select>
            <span class="select-cat-arrow">▾</span>
        </div>

        <button
            class="px-4 py-2 rounded-xl bg-[#050C9C] text-white text-xs md:text-sm
                   hover:bg-[#001070] transition self-start">
            Terapkan
        </button>
    </form>

    {{-- ===================================================== --}}
    {{-- LIST MOBILE: CARD                                    --}}
    {{-- ===================================================== --}}
    <div class="space-y-3 xl:hidden">
        @forelse($docs as $i => $d)
            @php
                $namaKat = $d->nama_kategori ?? '';
                $namaLower = strtolower(trim($namaKat));
                $chipClass = 'chip-default';

                if ($namaLower === 'bkd') {
                    $chipClass = 'chip-bkd';
                } elseif ($namaLower === 'bukti pengajaran') {
                    $chipClass = 'chip-bukti-pengajaran';
                } elseif ($namaLower === 'buku kerja dosen') {
                    $chipClass = 'chip-buku-kerja-dosen';
                } elseif ($namaLower === 'rps') {
                    $chipClass = 'chip-rps';
                } elseif ($namaLower === 'skp') {
                    $chipClass = 'chip-skp';
                } elseif ($namaLower === 'surat keputusan') {
                    $chipClass = 'chip-sk';
                } elseif ($namaLower === 'surat tugas') {
                    $chipClass = 'chip-st';
                }
            @endphp

            <div class="bg-white rounded-3xl shadow-sm px-4 py-3 flex justify-between gap-3">
                <div class="flex gap-3">
                    {{-- NO --}}
                    <div class="flex-shrink-0 flex items-start pt-1">
                        <span class="w-6 h-6 flex items-center justify-center rounded-full
                                     bg-[#050C9C] text-white text-xs font-semibold">
                            {{ $docs->firstItem() + $i }}
                        </span>
                    </div>

                    {{-- NAMA + KODE + TANGGAL --}}
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <div class="doc-icon">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="leading-tight">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ $d->judul }}
                                </div>
                                @if(!empty($d->nomor_dokumen))
                                    <div class="text-[11px] text-gray-500">
                                        {{ $d->nomor_dokumen }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($namaKat)
                            <span class="chip {{ $chipClass }} mt-1">
                                {{ $namaKat }}
                            </span>
                        @endif

                        <div class="text-[11px] text-gray-500">
                            {{ \Carbon\Carbon::parse($d->created_at)->locale('id')->translatedFormat('d F Y') }}
                        </div>
                    </div>
                </div>

                <div class="flex items-center">
                    <a href="{{ route('dosen.riwayat.show', $d->dokumen_id) }}"
                       class="text-xs font-medium text-[#050C9C]">
                        Detail
                    </a>
                </div>
            </div>
        @empty
            <p class="text-center text-xs text-gray-500 py-6">
                Belum ada unggahan dokumen.
            </p>
        @endforelse
    </div>

    {{-- ===================================================== --}}
    {{-- DESKTOP: GRID                                         --}}
    {{-- ===================================================== --}}
    <div class="hidden xl:block">
        <div class="riwayat-wrapper">

            {{-- Header --}}
            <div class="riwayat-header">
                <div>NO</div>
                <div>NAMA DOKUMEN</div>
                <div>KATEGORI</div>
                <div>TANGGAL UPLOAD</div>
                <div>AKSI</div>
            </div>

            {{-- Body --}}
            <div class="riwayat-body">
                @forelse($docs as $i => $d)
                    @php
                        $namaKat = $d->nama_kategori ?? '';
                        $namaLower = strtolower(trim($namaKat));
                        $chipClass = 'chip-default';

                        if ($namaLower === 'bkd') {
                            $chipClass = 'chip-bkd';
                        } elseif ($namaLower === 'bukti pengajaran') {
                            $chipClass = 'chip-bukti-pengajaran';
                        } elseif ($namaLower === 'buku kerja dosen') {
                            $chipClass = 'chip-buku-kerja-dosen';
                        } elseif ($namaLower === 'rps') {
                            $chipClass = 'chip-rps';
                        } elseif ($namaLower === 'skp') {
                            $chipClass = 'chip-skp';
                        } elseif ($namaLower === 'surat keputusan') {
                            $chipClass = 'chip-sk';
                        } elseif ($namaLower === 'surat tugas') {
                            $chipClass = 'chip-st';
                        }
                    @endphp

                    <div class="riwayat-row">

                        {{-- NO --}}
                        <div>{{ $docs->firstItem() + $i }}</div>

                        {{-- NAMA DOKUMEN --}}
                        <div>
                            <div class="flex items-center gap-3">
                                <div class="doc-icon">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="leading-tight">
                                    <div class="doc-title">{{ $d->judul }}</div>
                                    @if(!empty($d->nomor_dokumen))
                                        <div class="doc-sub">{{ $d->nomor_dokumen }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- KATEGORI --}}
                        <div>
                            @if($namaKat)
                                <span class="chip {{ $chipClass }}">
                                    {{ $namaKat }}
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">Tidak ada kategori</span>
                            @endif
                        </div>

                        {{-- TANGGAL UPLOAD --}}
                        <div class="text-sm text-gray-700">
                            {{ \Carbon\Carbon::parse($d->created_at)->locale('id')->translatedFormat('d F Y') }}
                        </div>

                        {{-- AKSI --}}
                        <div>
                            <a href="{{ route('dosen.riwayat.show', $d->dokumen_id) }}"
                               class="text-[#050C9C] hover:underline font-medium text-sm">
                                Detail
                            </a>
                        </div>

                    </div>
                @empty
                    <div class="riwayat-row">
                        <div class="col-span-5 text-center text-gray-500">
                            Belum ada unggahan dokumen.
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Footer: total dokumen + info halaman + pagination --}}
            @php
                $currentPage = $docs->currentPage();
                $lastPage    = $docs->lastPage();
            @endphp

            <div class="riwayat-footer border-top-soft">
                {{-- total dokumen kiri + icon --}}
                <div class="riwayat-footer-total">
                    <svg class="w-5 h-5 text-[#050C9C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6M7 4h5.586a1 1 0 01.707.293l4.414 4.414A1 1 0 0118 9.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                    </svg>

                    <span class="label-total">Total Dokumen:</span>
                    <span class="label-total-number">{{ $docs->total() }}</span>
                </div>

                {{-- info halaman + pagination kanan --}}
                <div class="riwayat-footer-right">
                    <span class="riwayat-footer-pageinfo">
                        Halaman {{ $currentPage }} dari {{ $lastPage }}
                    </span>

                    <div class="pagination">
                        {{-- tombol previous --}}
                        @if ($docs->onFirstPage())
                            <span class="pagination-arrow disabled">&lt;</span>
                        @else
                            <a href="{{ $docs->previousPageUrl() }}" class="pagination-arrow">&lt;</a>
                        @endif

                        {{-- tombol nomor halaman --}}
                        @for ($page = 1; $page <= $lastPage; $page++)
                            @if ($page == $currentPage)
                                <span class="pagination-page is-active">{{ $page }}</span>
                            @else
                                <a href="{{ $docs->url($page) }}" class="pagination-page">{{ $page }}</a>
                            @endif
                        @endfor

                        {{-- tombol next --}}
                        @if ($docs->hasMorePages())
                            <a href="{{ $docs->nextPageUrl() }}" class="pagination-arrow">&gt;</a>
                        @else
                            <span class="pagination-arrow disabled">&gt;</span>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
