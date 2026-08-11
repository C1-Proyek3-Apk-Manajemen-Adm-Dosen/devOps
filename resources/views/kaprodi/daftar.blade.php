@extends('layouts.app')

@section('title', 'Daftar Dokumen - SiDoRa')

@push('styles')
    @vite('resources/css/kaprodi/daftar.css')
@endpush

@section('content')
<div class="p-6 md:p-8">

    {{-- Judul halaman --}}
    <div class="mb-4 md:mb-6 flex items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Daftar Dokumen</h1>
            <p class="text-xs md:text-sm text-gray-500 mt-1">
                Semua dokumen yang diunggah dosen untuk direview Kaprodi.
            </p>
        </div>

        {{-- Search bar abu + rounded --}}
        <form method="GET" class="kp-search-wrap hidden md:block">
            {{-- keep current filters saat search --}}
            <input type="hidden" name="kategori_id" value="{{ $selectedKategori ?? request('kategori_id') }}">
            <input type="hidden" name="period" value="{{ $selectedPeriod ?? request('period') }}">
            <input type="hidden" name="dosen_id" value="{{ $selectedDosen ?? request('dosen_id') }}">

            <span class="kp-search-icon">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input
                type="text"
                name="q"
                value="{{ $searchTerm ?? request('q') }}"
                class="kp-search-input"
                placeholder="Cari dokumen (judul, nomor, kategori, dosen)...">
        </form>
    </div>

    {{-- Filter bar --}}
    <form method="GET" class="kp-filter-bar mb-4 md:mb-6">

        {{-- bawa nilai search juga --}}
        <input type="hidden" name="q" value="{{ $searchTerm ?? request('q') }}">

        {{-- Jenis / kategori dokumen --}}
        <div class="kp-select-wrap">
            <select name="kategori_id" class="kp-select">
                <option value="all" {{ ($selectedKategori ?? request('kategori_id','all')) === 'all' ? 'selected' : '' }}>
                    Semua Jenis Dokumen
                </option>
                @foreach($kategoriOptions as $kat)
                    <option value="{{ $kat->kategori_id }}"
                        {{ (string)($selectedKategori ?? request('kategori_id')) === (string)$kat->kategori_id ? 'selected' : '' }}>
                        {{ $kat->nama_kategori }}
                    </option>
                @endforeach
            </select>
            <span class="kp-select-arrow">▾</span>
        </div>

        {{-- Periode --}}
        <div class="kp-select-wrap">
            <select name="period" class="kp-select">
                @php
                    $period = $selectedPeriod ?? request('period','all');
                @endphp
                <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Period: All</option>
                <option value="30"  {{ $period === '30' ? 'selected' : '' }}>30 hari</option>
                <option value="90"  {{ $period === '90' ? 'selected' : '' }}>90 hari</option>
                <option value="365" {{ $period === '365' ? 'selected' : '' }}>365 hari</option>
            </select>
            <span class="kp-select-arrow">▾</span>
        </div>

        {{-- Dosen --}}
        <div class="kp-select-wrap">
            <select name="dosen_id" class="kp-select">
                @php
                    $dosenSelected = $selectedDosen ?? request('dosen_id','all');
                @endphp
                <option value="all" {{ $dosenSelected === 'all' ? 'selected' : '' }}>Semua Dosen</option>
                @foreach($dosenOptions as $dosen)
                    <option value="{{ $dosen->id_user }}"
                        {{ (string)$dosenSelected === (string)$dosen->id_user ? 'selected' : '' }}>
                        {{ $dosen->nama_lengkap }}
                    </option>
                @endforeach
            </select>
            <span class="kp-select-arrow">▾</span>
        </div>

        {{-- Tombol Terapkan --}}
        <button type="submit" class="kp-btn-apply">
            Terapkan
        </button>

        {{-- Search bar untuk mobile (di bawah filter) --}}
        <div class="kp-search-wrap w-full md:hidden mt-2">
            <span class="kp-search-icon">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input
                type="text"
                name="q"
                value="{{ $searchTerm ?? request('q') }}"
                class="kp-search-input"
                placeholder="Cari dokumen (judul, nomor, kategori, dosen)...">
        </div>
    </form>

    {{-- Wrapper tabel --}}
    <div class="kp-wrapper">

        {{-- Header --}}
        <div class="kp-header">
            <div>NO</div>
            <div>NAMA DOKUMEN</div>
            <div>KATEGORI</div>
            <div>TANGGAL UPLOAD</div>
            <div>DOSEN</div>
            <div>AKSI</div>
        </div>

        {{-- Body --}}
        <div class="kp-body">
            @forelse($docs as $index => $d)
                @php
                    $nomor = ($docs->currentPage() - 1) * $docs->perPage() + $index + 1;

                    $katName   = $d->nama_kategori;
                    $lowerKat  = $katName ? strtolower(trim($katName)) : null;
                    $chipClass = 'kp-chip-default';

                    if ($lowerKat === 'bkd')            $chipClass = 'kp-chip-bkd';
                    elseif ($lowerKat === 'rps')        $chipClass = 'kp-chip-rps';
                    elseif ($lowerKat === 'surat tugas')     $chipClass = 'kp-chip-st';
                    elseif ($lowerKat === 'surat keputusan') $chipClass = 'kp-chip-sk';
                @endphp

                <div class="kp-row">
                    {{-- NO --}}
                    <div>{{ $nomor }}</div>

                    {{-- Nama dokumen + nomor --}}
                    <div>
                        <div class="kp-doc-main">
                            <div class="kp-doc-icon">
                                <i class="fa-regular fa-file-lines"></i>
                            </div>
                            <div>
                                <div class="kp-doc-title">
                                    {{ $d->judul ?? '-' }}
                                </div>
                                <div class="kp-doc-sub">
                                    {{ $d->nomor_dokumen ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Kategori --}}
                    <div>
                        @if($katName)
                            <span class="kp-chip {{ $chipClass }}">
                                {{ $katName }}
                            </span>
                        @else
                            <span class="text-xs text-gray-500">-</span>
                        @endif
                    </div>

                    {{-- Tanggal upload --}}
                    <div class="text-sm text-gray-700">
                        {{ optional($d->created_at)->locale('id')->translatedFormat('d F Y') ?? '-' }}
                    </div>

                    {{-- Dosen --}}
                    <div class="text-sm text-gray-800">
                        {{ $d->dosen_nama ?? $d->nama_lengkap ?? '-' }}
                    </div>

                    {{-- Aksi: link Detail --}}
                    <div>
                        <a href="{{ route('kaprodi.dokumen.show', $d->dokumen_id) }}"
                           class="kp-link-detail">
                            Detail
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-sm text-gray-500 px-4 py-6">
                    Tidak ada dokumen yang ditemukan.
                </div>
            @endforelse
        </div>

        {{-- Footer pagination --}}
        <div class="kp-border-top-soft kp-footer">
            <div class="kp-footer-total">
                <div class="kp-footer-total-icon">
                    <i class="fa-regular fa-file-lines"></i>
                </div>
                <div class="text-sm text-gray-700">
                    <span>Total Dokumen:</span>
                    <span class="font-bold text-[color:var(--kp-primary)]">
                        {{ $docs->total() }}
                    </span>
                </div>
            </div>

            <div class="kp-footer-right">
                <div class="kp-footer-pageinfo">
                    Halaman {{ $docs->currentPage() }} dari {{ $docs->lastPage() }}
                </div>

                {{-- Custom pagination 5 item per halaman --}}
                <div class="kp-pagination">
                    {{-- Prev --}}
                    @php
                        $prevUrl = $docs->currentPage() > 1
                            ? $docs->previousPageUrl()
                            : null;
                    @endphp
                    <a class="kp-arrow {{ $prevUrl ? '' : 'disabled' }}"
                       @if($prevUrl) href="{{ $prevUrl }}" @endif>
                        ‹
                    </a>

                    {{-- pages --}}
                    @foreach ($docs->getUrlRange(1, $docs->lastPage()) as $page => $url)
                        @if ($page == $docs->currentPage())
                            <span class="kp-page is-active">{{ $page }}</span>
                        @else
                            <a class="kp-page" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @php
                        $nextUrl = $docs->currentPage() < $docs->lastPage()
                            ? $docs->nextPageUrl()
                            : null;
                    @endphp
                    <a class="kp-arrow {{ $nextUrl ? '' : 'disabled' }}"
                       @if($nextUrl) href="{{ $nextUrl }}" @endif>
                        ›
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
