@extends('layouts.app')

@section('title', 'Riwayat Upload TU - SiDoRa')

@push('styles')
  @vite('resources/css/tu/riwayat.css')
@endpush

@section('content')
<div class="p-4 md:p-8" id="riwayatBox">

    {{-- Bar atas: Judul & Search --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 class="text-xl md:text-2xl font-semibold text-gray-800">
            Riwayat Upload TU
        </h1>

        <form method="GET" class="w-full md:w-80">
            {{-- bawa nilai filter lain --}}
            <input type="hidden" name="cat" value="{{ request('cat') }}">
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

    {{-- Bar bawah: Filter kategori + period --}}
    <form method="GET" class="flex flex-col sm:flex-row sm:items-center gap-3 flex-wrap mb-6">
        {{-- bawa nilai search --}}
        <input type="hidden" name="q" value="{{ request('q') }}">

        {{-- Filter kategori --}}
        <div class="select-cat-wrapper w-full sm:w-auto">
            <select name="cat"
                    class="select-cat w-full border border-gray-300 rounded-xl px-3 py-2 text-xs md:text-sm
                           focus:outline-none focus:ring-2 focus:ring-[#050C9C]">
                <option value="">Semua Kategori</option>
                <option value="st" @selected(request('cat')==='st')>Surat Tugas (ST)</option>
                <option value="sk" @selected(request('cat')==='sk')>Surat Keputusan (SK)</option>
                <option value="rp" @selected(request('cat')==='rp')>Riwayat Pengajaran</option>
            </select>
            <span class="select-cat-arrow">▾</span>
        </div>

        {{-- Filter period --}}
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
    {{-- LIST MOBILE / TABLET: CARD ( < xl )                  --}}
    {{-- ===================================================== --}}
    <div class="space-y-3 xl:hidden">
        @forelse($docs as $i => $d)
            @php
                $alias = $d->alias
                  ?? (strtolower($d->nama_kategori ?? '') ?
                      (str_contains(strtolower($d->nama_kategori),'tugas') ? 'st'
                      : (str_contains(strtolower($d->nama_kategori),'keputusan') ? 'sk'
                      : (str_contains(strtolower($d->nama_kategori),'rps') ? 'rp' : 'none')))
                      : 'none');

                $recipientText = $recipientsMap[$d->dokumen_id] ?? '–';
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

                    {{-- NAMA + NOMOR + KATEGORI + TANGGAL + DOSEN --}}
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

                        {{-- Kategori --}}
                        <div>
                            <span class="chip chip--{{ $alias }} mt-1">
                                {{ $d->nama_kategori ?? 'Tidak Ada Kategori' }}
                            </span>
                        </div>

                        {{-- Tanggal --}}
                        <div class="text-[11px] text-gray-500">
                            {{ \Carbon\Carbon::parse($d->created_at)->locale('id')->translatedFormat('d F Y') }}
                        </div>

                        {{-- Dosen --}}
                        <div class="text-[11px] text-gray-500">
                            {{ $recipientText }}
                        </div>
                    </div>
                </div>

                {{-- Aksi --}}
                <div class="flex items-center">
                    <a href="{{ route('tu.dokumen.show', $d->dokumen_id) }}"
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

    {{-- FOOTER MOBILE (Total dok + pagination) --}}
    <div class="xl:hidden mt-3">
        @php
            $current = $docs->currentPage();
            $last    = $docs->lastPage();
        @endphp
        <div class="border-top-soft riwayat-footer">
            {{-- Total dokumen kiri --}}
            <div class="riwayat-footer-total">
                <span class="doc-total-icon">
                    <i class="fa-regular fa-file-lines"></i>
                </span>
                <span class="label-total">Total Dokumen:</span>
                <span class="label-total-number">{{ $docs->total() }}</span>
            </div>

            {{-- Info halaman + pagination kanan --}}
            <div class="riwayat-footer-right">
                <span class="riwayat-footer-pageinfo">
                    Halaman {{ $current }} dari {{ $last }}
                </span>
                <div class="pagination">
                    {{-- prev --}}
                    @if ($docs->onFirstPage())
                        <span class="pagination-arrow disabled">&lt;</span>
                    @else
                        <a href="{{ $docs->previousPageUrl() }}" class="pagination-arrow">&lt;</a>
                    @endif

                    {{-- page numbers --}}
                    @for ($page = 1; $page <= $last; $page++)
                        @if ($page == $current)
                            <span class="pagination-page is-active">{{ $page }}</span>
                        @else
                            <a href="{{ $docs->url($page) }}" class="pagination-page">{{ $page }}</a>
                        @endif
                    @endfor

                    {{-- next --}}
                    @if ($docs->hasMorePages())
                        <a href="{{ $docs->nextPageUrl() }}" class="pagination-arrow">&gt;</a>
                    @else
                        <span class="pagination-arrow disabled">&gt;</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- DESKTOP: TABEL GRID ( >= xl )                        --}}
    {{-- ===================================================== --}}
    <div class="hidden xl:block">
        <div class="riwayat-wrapper">

            {{-- Header --}}
            <div class="riwayat-header">
                <div>NO</div>
                <div>NAMA DOKUMEN</div>
                <div>KATEGORI</div>
                <div>TANGGAL UPLOAD</div>
                <div>DOSEN</div>
                <div>AKSI</div>
            </div>

            {{-- Body --}}
            <div class="riwayat-body">
                @forelse($docs as $i => $d)
                    @php
                        $alias = $d->alias
                          ?? (strtolower($d->nama_kategori ?? '') ?
                              (str_contains(strtolower($d->nama_kategori),'tugas') ? 'st'
                              : (str_contains(strtolower($d->nama_kategori),'keputusan') ? 'sk'
                              : (str_contains(strtolower($d->nama_kategori),'rps') ? 'rp' : 'none')))
                              : 'none');

                        $recipientText = $recipientsMap[$d->dokumen_id] ?? '–';
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
                            <span class="chip chip--{{ $alias }}">
                                {{ $d->nama_kategori ?? 'Tidak Ada Kategori' }}
                            </span>
                        </div>

                        {{-- TANGGAL UPLOAD --}}
                        <div class="text-sm text-gray-700">
                            {{ \Carbon\Carbon::parse($d->created_at)->locale('id')->translatedFormat('d F Y') }}
                        </div>

                        {{-- DOSEN --}}
                        <div class="text-sm text-gray-700">
                            {{ $recipientText }}
                        </div>

                        {{-- AKSI --}}
                        <div>
                            <a href="{{ route('tu.dokumen.show', $d->dokumen_id) }}"
                               class="text-[#050C9C] hover:underline font-medium text-sm">
                                Detail
                            </a>
                        </div>

                    </div>
                @empty
                    <div class="riwayat-row">
                        <div class="col-span-6 text-center text-gray-500">
                            Belum ada unggahan dokumen.
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- FOOTER DESKTOP: total dokumen + pagination --}}
            @php
                $current = $docs->currentPage();
                $last    = $docs->lastPage();
            @endphp
            <div class="border-top-soft riwayat-footer">
                {{-- Total dokumen kiri --}}
                <div class="riwayat-footer-total">
                    <span class="doc-total-icon">
                        <i class="fa-regular fa-file-lines"></i>
                    </span>
                    <span class="label-total">Total Dokumen:</span>
                    <span class="label-total-number">{{ $docs->total() }}</span>
                </div>

                {{-- Info halaman + pagination kanan --}}
                <div class="riwayat-footer-right">
                    <span class="riwayat-footer-pageinfo">
                        Halaman {{ $current }} dari {{ $last }}
                    </span>
                    <div class="pagination">
                        {{-- prev --}}
                        @if ($docs->onFirstPage())
                            <span class="pagination-arrow disabled">&lt;</span>
                        @else
                            <a href="{{ $docs->previousPageUrl() }}" class="pagination-arrow">&lt;</a>
                        @endif

                        {{-- page numbers --}}
                        @for ($page = 1; $page <= $last; $page++)
                            @if ($page == $current)
                                <span class="pagination-page is-active">{{ $page }}</span>
                            @else
                                <a href="{{ $docs->url($page) }}" class="pagination-page">{{ $page }}</a>
                            @endif
                        @endfor

                        {{-- next --}}
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

@push('scripts')
  @vite('resources/js/tu/riwayat.js')
@endpush
