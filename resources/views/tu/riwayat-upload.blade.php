@extends('layouts.app')

@section('title', 'Riwayat Upload TU - SiDoRa')

@push('styles')
  @vite('resources/css/tu/riwayat.css')
@endpush

@section('content')
<div class="p-8" id="riwayatBox">

    {{-- Bar atas: Judul & Search --}}
    <div class="flex items-center justify-between gap-4 mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Riwayat Upload TU</h1>
        <form method="GET" class="w-full max-w-sm">
            <input type="hidden" name="cat" value="{{ request('cat') }}">
            <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    class="w-full pl-10 pr-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#050C9C]"
                    placeholder="Cari dokumen…">
            </div>
        </form>
    </div>

    {{-- Bar bawah: Filter kategori --}}
    <form method="GET" class="flex flex-wrap items-center gap-3 mb-6">
        <input type="hidden" name="q" value="{{ request('q') }}">

        <div class="select-cat-wrapper">
            <select name="cat"
                    class="select-cat border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#050C9C]">
                <option value="">Semua Kategori</option>
                <option value="st" @selected(request('cat')==='st')>Surat Tugas (ST)</option>
                <option value="sk" @selected(request('cat')==='sk')>Surat Keputusan (SK)</option>
                <option value="rp" @selected(request('cat')==='rp')>Riwayat Pengajaran</option>
            </select>
            <span class="select-cat-arrow">▾</span>
        </div>

        <button class="px-4 py-2 rounded-xl bg-[#050C9C] text-white hover:bg-[#001070] transition">
            Terapkan
        </button>
    </form>

    {{-- Tabel Grid Riwayat --}}
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
                        <span class="chip chip--{{ $alias }}">{{ $d->nama_kategori ?? 'Tidak Ada Kategori' }}</span>
                    </div>

                    {{-- TANGGAL UPLOAD --}}
                    <div class="text-sm text-gray-700">
                        {{ \Carbon\Carbon::parse($d->created_at)->locale('id')->translatedFormat('d F Y') }}
                    </div>

                    {{-- DOSEN --}}
                    <div class="text-sm text-gray-700">
                        {{ $recipientsMap[$d->dokumen_id] ?? '–' }}
                    </div>

                    {{-- AKSI --}}
                    <div>
                        <a href="{{ route('tu.dokumen.show', $d->dokumen_id) }}"
                           class="text-[#050C9C] hover:underline font-medium text-sm">Detail</a>
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

        {{-- Pagination --}}
        <div class="border-t border-gray-100">
            {{ $docs->links() }}
        </div>

    </div>
</div>
@endsection

@push('scripts')
  @vite('resources/js/tu/riwayat.js')
@endpush
