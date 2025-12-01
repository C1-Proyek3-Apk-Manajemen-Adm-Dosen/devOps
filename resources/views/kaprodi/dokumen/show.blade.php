@extends('layouts.app')

@section('title', 'Detail Dokumen - SiDoRa')

@push('styles')
<style>
    /* Icon versi dokumen (tag) outline putih */
    .kp-icon-tag-outline {
        color: transparent !important;
        -webkit-text-stroke: 1.6px #fff !important;
        font-weight: 900 !important;
    }

    /* Icon copy di kartu nomor dokumen */
    .kp-copy-icon {
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

    .kp-copy-icon:hover {
        opacity: 1;
    }

    /* Toast abu-abu di bawah tengah layar */
    .toast-copy {
        position: fixed;
        left: 50%;
        bottom: 24px;
        transform: translateX(-50%);
        background: #374151;
        color: #ffffff;
        padding: 8px 20px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        display: none;
        z-index: 9999;
        box-shadow: 0 6px 20px rgba(0,0,0,0.18);
        white-space: nowrap;
    }

    /* ===== Chip kategori ===== */
    .kp-chip {
        display: inline-flex;
        align-items: center;
        gap: .375rem;
        padding: .30rem .9rem;
        border-radius: 999px;
        font-weight: 600;
        font-size: .75rem;
        border: 1px solid transparent;
        line-height: 1;
    }
    .kp-chip::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: currentColor;
    }

    .kp-chip-default { color:#4338ca; background:#eef2ff; border-color:#e0e7ff; }
    .kp-chip-bkd     { color:#6d28d9; background:#ede9fe; border-color:#ddd6fe; }
    .kp-chip-rps     { color:#ea580c; background:#ffedd5; border-color:#fed7aa; }
    .kp-chip-st      { color:#2563eb; background:#e0f2ff; border-color:#bfdbfe; }
    .kp-chip-sk      { color:#c026d3; background:#fce7ff; border-color:#f9c5ff; }
</style>
@endpush

@section('content')
<div class="p-4 md:p-8 min-h-screen">

    {{-- Tombol back + judul --}}
    <div class="mb-4 flex items-center gap-3">
        <a href="{{ route('kaprodi.daftar') }}"
           class="w-9 h-9 flex items-center justify-center rounded-xl shadow-sm bg-white text-gray-600 hover:bg-gray-50">
            <i class="fa-solid fa-chevron-left text-sm"></i>
        </a>
        <h1 class="text-lg md:text-xl font-semibold text-gray-800">
            Detail Dokumen
        </h1>
    </div>

    {{-- Alert Error & Success --}}
    @if (session('error'))
        <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="mb-4 p-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    {{-- Card utama --}}
    <div class="bg-white rounded-3xl shadow-md overflow-hidden">

        {{-- Header biru --}}
        <div class="bg-gradient-to-r from-[#050C9C] to-blue-700 px-5 py-2.5 flex items-center justify-between">
            <h2 class="text-base font-bold text-white">Detail Dokumen</h2>

            {{-- Tombol titik tiga / share --}}
            <button type="button"
                id="btn-share-kaprodi"
                data-id="{{ $dokumen->dokumen_id }}"
                data-title="{{ $dokumen->judul ?? '' }}"
                class="text-white/90 hover:text-white p-1.5 rounded-lg hover:bg-white/20 transition">
                <i class="fa-solid fa-ellipsis-vertical text-lg"></i>
            </button>
        </div>

        {{-- Isi --}}
        <div class="px-6 py-6 md:px-8 md:py-8">

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
                            <i class="fa-regular fa-copy kp-copy-icon"
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

                    {{-- Nama Dosen --}}
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-gray-700">
                            Nama Dosen
                        </label>
                        <div class="border border-gray-200 rounded-2xl px-4 py-2.5 bg-gray-50 text-sm text-gray-800">
                            {{ $dokumen->nama_lengkap ?? '-' }}
                        </div>
                    </div>

                    {{-- Tanggal Upload --}}
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-gray-700">
                            Tanggal Upload
                        </label>
                        <div class="border border-gray-200 rounded-2xl px-4 py-2.5 bg-gray-50 text-sm text-gray-800">
                            {{ optional($dokumen->created_at)->locale('id')->translatedFormat('d F Y') ?? '-' }}
                        </div>
                    </div>

                    {{-- Kategori --}}
                    @php
                        $katName   = $dokumen->nama_kategori ?? null;
                        $lowerKat  = $katName ? strtolower(trim($katName)) : null;
                        $chipClass = 'kp-chip-default';

                        if ($lowerKat === 'bkd')                 $chipClass = 'kp-chip-bkd';
                        elseif ($lowerKat === 'rps')            $chipClass = 'kp-chip-rps';
                        elseif ($lowerKat === 'surat tugas')    $chipClass = 'kp-chip-st';
                        elseif ($lowerKat === 'surat keputusan')$chipClass = 'kp-chip-sk';
                    @endphp

                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-gray-700">
                            Kategori Dokumen
                        </label>
                        <div class="border border-gray-200 rounded-2xl px-4 py-2.5 bg-gray-50 text-sm text-gray-800">
                            @if($katName)
                                <span class="kp-chip {{ $chipClass }}">
                                    {{ $katName }}
                                </span>
                            @else
                                <span class="text-xs text-gray-500">-</span>
                            @endif
                        </div>
                    </div>

                </div>

                {{-- ================= KANAN ================= --}}
                <div class="space-y-4">

                    {{-- Deskripsi --}}
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-gray-700">
                            Deskripsi Dokumen
                        </label>
                        <div class="border border-gray-200 rounded-2xl bg-gray-50 px-4 py-3 min-h-[120px] text-sm text-gray-700">
                            {{ $dokumen->deskripsi ?? '-' }}
                        </div>
                    </div>

                    {{-- Versi Dokumen + Tombol Download --}}
                    <div class="flex flex-col gap-3">
                        <label class="text-sm font-semibold text-gray-700">
                            Versi Dokumen
                        </label>

                        @if($latest)
                            <div class="w-full flex items-center px-4 py-2.5 rounded-xl
                                        border border-gray-200 bg-gray-50">
                                <span class="flex items-center justify-center w-12 h-12 rounded-xl
                                             bg-gradient-to-r from-[#050C9C] to-[#1E40FF] shadow-md">
                                    <i class="fa-solid fa-tag kp-icon-tag-outline text-lg"></i>
                                </span>

                                <span class="ml-4 text-base font-semibold text-slate-800">
                                    v{{ $latest->nomor_versi }}
                                </span>
                            </div>
                        @else
                            <div class="border border-gray-200 rounded-2xl px-4 py-2.5 bg-gray-50 text-sm text-gray-800">
                                Belum ada versi dokumen.
                            </div>
                        @endif

                        {{-- Tombol Download --}}
                        <div class="pt-1">
                            @if($latest && !empty($latest->file_path))
                                <a href="{{ route('kaprodi.dokumen.download', ['id' => $dokumen->dokumen_id, 'versi' => $latest->nomor_versi]) }}"
                                   class="inline-flex items-center justify-center w-full gap-2 px-4 py-2.5 rounded-2xl text-sm font-medium
                                          bg-gradient-to-r from-[#050C9C] to-[#1E40FF] text-white
                                          shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition">
                                    <i class="fa-solid fa-download text-xs"></i>
                                    Unduh Dokumen
                                </a>
                            @else
                                <div class="inline-flex items-center justify-center w-full gap-2 px-4 py-2.5 rounded-2xl text-sm font-medium bg-gray-200 text-gray-600">
                                    <i class="fa-solid fa-download text-xs opacity-70"></i>
                                    Dokumen belum tersedia
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
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
    const el = document.getElementById('nomorDokumenText');
    if (!el) return;

    const text = (el.innerText || el.textContent || '').trim();
    if (!text) return;

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text);
    } else {
        const temp = document.createElement('textarea');
        temp.value = text;
        document.body.appendChild(temp);
        temp.select();
        document.execCommand('copy');
        document.body.removeChild(temp);
    }

    const toast = document.getElementById('toast-copy');
    if (!toast) return;

    toast.style.display = 'block';
    setTimeout(() => {
        toast.style.display = 'none';
    }, 1800);
}

function openShareModal(id, title) {
    console.log('openShareModal', id, title);
    // nanti diisi modal share beneran
}

// pas DOM siap, pasang event listener ke tombol share
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btn-share-kaprodi');
    if (!btn) return;

    btn.addEventListener('click', function () {
        const id = this.getAttribute('data-id');
        const title = this.getAttribute('data-title');
        openShareModal(id, title);
    });
});
</script>
@endpush
