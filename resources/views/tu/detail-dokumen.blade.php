@extends('layouts.app')
@section('title', 'Detail Dokumen - SiDoRa')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-2">

    {{-- Alert Error & Success --}}
    @if (session('error'))
        <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    @if (session('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- HEADER with Back Button --}}
    <div class="mb-3 flex items-center gap-2">
        <a href="{{ route('tu.monitoring') }}" 
           class="w-9 h-9 bg-white rounded-xl shadow-md hover:shadow-lg flex items-center justify-center text-gray-600 hover:text-[#050C9C] transition-all duration-200 hover:-translate-x-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-800">Detail Dokumen</h1>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        
        {{-- HEADER BLUE --}}
        <div class="bg-gradient-to-r from-[#050C9C] to-blue-700 px-5 py-2.5 flex items-center justify-between">
            <h2 class="text-base font-bold text-white">Detail Dokumen</h2>

            {{-- Tombol titik tiga / share --}}
            <button type="button"
                    onclick="openShareModal({{ $dokumen->dokumen_id }}, '{{ $dokumen->judul }}')"
                    class="text-white/90 hover:text-white p-1.5 rounded-lg hover:bg-white/20 transition">
                <i class="fa-solid fa-ellipsis-vertical text-lg"></i>
            </button>
        </div>

        {{-- CONTENT 2 KOLOM --}}
        <div class="p-5 grid grid-cols-1 lg:grid-cols-2 gap-5">
            
            {{-- KOLOM KIRI --}}
            <div class="space-y-3.5">
                
                {{-- Nomor Dokumen --}}
                <div class="bg-gradient-to-br from-[#050C9C] to-blue-700 rounded-xl p-4 shadow-lg">
                    <p class="text-[9px] font-semibold text-white/80 uppercase tracking-wide mb-0.5">Nomor Dokumen</p>
                    <p class="text-2xl font-bold text-white">{{ $dokumen->nomor_dokumen ?? '01' }}</p>
                </div>

                {{-- Judul --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Judul Dokumen</label>
                    <div class="bg-gray-50 rounded-xl px-3 py-2.5 border border-gray-200">
                        <p class="text-sm font-bold text-gray-800">{{ $dokumen->judul ?? 'Judul Dokumen' }}</p>
                    </div>
                </div>

                {{-- Tanggal Upload --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Tanggal Upload</label>
                    <div class="bg-gray-50 rounded-xl px-3 py-2.5 border border-gray-200">
                        <p class="text-sm font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($dokumen->tanggal_terbit ?? now())->translatedFormat('d F Y') }}
                        </p>
                    </div>
                </div>

                {{-- Kategori --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Kategori Dokumen</label>
                    <div class="bg-gray-50 rounded-xl px-3 py-2.5 border border-gray-200">
                        @php
                            $kategoriNama = $dokumen->kategori?->nama_kategori ?? 'Surat Tugas';
                            $badgeClass = match($kategoriNama) {
                                'Surat Keputusan' => 'bg-blue-100 text-blue-700 border-2 border-blue-300',
                                'Surat Tugas' => 'bg-blue-100 text-blue-700 border-2 border-blue-300',
                                'Riwayat Pengajaran' => 'bg-green-100 text-green-700 border-2 border-green-300',
                                'RPS', 'Rencana Pembelajaran Semester' => 'bg-blue-100 text-blue-700 border-2 border-blue-300',
                                'BKD', 'Buku Kerja Dosen' => 'bg-orange-100 text-orange-700 border-2 border-orange-300',
                                'SKP' => 'bg-pink-100 text-pink-700 border-2 border-pink-300',
                                'Bukti Pengajaran' => 'bg-green-100 text-green-700 border-2 border-green-300',
                                default => 'bg-gray-100 text-gray-700 border-2 border-gray-300'
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-bold {{ $badgeClass }}">
                            {{ $kategoriNama }}
                        </span>
                    </div>
                </div>

            </div>

            <div class="space-y-4">
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Deskripsi Dokumen</label>
                    <div class="bg-gray-50 rounded-xl px-3 py-2.5 border border-gray-200 h-[180px] overflow-y-auto">
                        <p class="text-sm text-gray-700 leading-relaxed">
                            {{ $dokumen->deskripsi ?? 'Tidak ada deskripsi' }}
                        </p>
                    </div>
                </div>

            {{-- Versi & Status (Grid 2 kolom) --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Versi Dokumen</label>
                    <div class="bg-gray-50 rounded-xl px-3 py-2.5 border border-gray-200 flex items-center gap-2.5 h-[60px]">
                        <div class="w-10 h-10 bg-gradient-to-br from-[#050C9C] to-blue-700 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        @php
                            $versiTerbaru = $dokumen->versi()->latest('nomor_versi')->first();
                        @endphp
                        <p class="text-lg font-bold text-gray-800">v{{ $versiTerbaru?->nomor_versi ?? 1 }}</p>
                    </div>
                </div>

                <div class="flex-1">
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Status Dokumen</label>
                    <div class="bg-gray-50 rounded-xl px-3 py-2.5 border border-gray-200 flex items-center h-[60px]">
                        @php
                            $statusBadge = \App\Models\AccessControl::getStatusBadge($statusDokumen);
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-bold {{ $statusBadge['class'] }}">
                            <span class="w-2 h-2 rounded-full {{ $statusBadge['dot'] }} animate-pulse"></span>
                            {{ $statusBadge['text'] }}
                        </span>
                    </div>
                </div>
            </div>

                {{-- Button Download + Upload Versi --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    @if($fileExists) 
                        <a href="{{ route('tu.dokumen.download', $dokumen->dokumen_id) }}"
                           class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#050C9C] to-blue-700 text-white rounded-xl font-bold text-sm hover:from-blue-700 hover:to-[#050C9C] transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download Dokumen
                        </a>
                    @else
                        <button disabled 
                                class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gray-300 text-gray-600 rounded-xl font-bold text-sm cursor-not-allowed shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            File Belum Tersedia
                        </button>
                    @endif

                    <button type="button" 
                            onclick="openUploadVersiModal()"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white text-[#050C9C] border-2 border-[#050C9C] rounded-xl font-bold text-sm hover:bg-[#050C9C] hover:text-white transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                            <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                        </svg>
                        Upload Versi Baru
                    </button>
                </div>

                {{-- RIWAYAT VERSI --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Riwayat Versi</label>
                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-200 max-h-[200px] overflow-y-auto">
                    @forelse($dokumen->versi()->orderByDesc('nomor_versi')->get() as $versi)
                        <div class="py-2 border-b border-gray-200 last:border-0">
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">
                                        v{{ $versi->nomor_versi }}
                                    </span>
                                    <span class="text-xs text-gray-600">
                                        {{ \Carbon\Carbon::parse($versi->tanggal_dokumen)->format('d M Y') }}
                                    </span>
                                </div>
                                
                                @php
                                    $versiFileExists = $fileExistsMap[$versi->file_path] ?? false;
                                @endphp
                                
                                @if($versiFileExists)
                                    <a href="{{ route('tu.dokumen.download', ['id' => $dokumen->dokumen_id, 'versi' => $versi->nomor_versi]) }}" 
                                    class="text-blue-600 hover:text-blue-800 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                    </a>
                                @else
                                    <button disabled class="text-gray-300 cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            
                            @if($versi->catatan_perubahan)
                                <p class="text-xs text-gray-600 italic mt-1 pl-2 border-l-2 border-blue-300">
                                    "{{ $versi->catatan_perubahan }}"
                                </p>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 text-center py-2">Belum ada versi</p>
                    @endforelse
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

@include('tu.modal-upload-versi', ['dokumen' => $dokumen])
@include('components.share-modal')

@endsection

@push('styles')
@vite('resources/css/tu/detail-dokumen.css')
@endpush

@push('scripts')
@vite([
    'resources/js/tu/detail-dokumen.js',
    'resources/js/share-link.js'
])
@endpush