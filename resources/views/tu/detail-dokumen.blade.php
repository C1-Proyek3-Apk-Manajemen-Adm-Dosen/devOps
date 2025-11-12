@extends('layouts.app')
@section('title', 'Detail Dokumen - SiDoRa')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-4">

    <div class="mb-4 flex items-center gap-3">
        <a href="{{ route('tu.monitoring') }}" 
           class="w-9 h-9 bg-white rounded-xl shadow-md hover:shadow-lg flex items-center justify-center text-gray-600 hover:text-[#050C9C] transition-all duration-200 hover:-translate-x-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-800">
                Detail Dokumen
            </h1>
            <p class="text-gray-500 text-xs">Informasi lengkap dokumen</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        
        {{-- HEADER BLUE --}}
        <div class="bg-gradient-to-r from-[#050C9C] to-blue-700 px-6 py-4">
            <h2 class="text-lg font-bold text-white">Detail Dokumen</h2>
        </div>

        <div class="p-6 space-y-4">
            
            {{-- Nomor Dokumen --}}
            <div class="bg-gray-50 rounded-xl p-4 border-l-4 border-[#050C9C]">
                <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">Nomor Dokumen</p>
                <p class="text-2xl font-bold text-gray-800">{{ $dokumen->nomor_dokumen ?? '01' }}</p>
            </div>

            {{-- Judul --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2">Judul Dokumen</label>
                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-200">
                    <p class="text-base font-bold text-gray-800">{{ $dokumen->judul ?? 'Riwayat Pengajaran' }}</p>
                </div>
            </div>

            {{-- Tanggal Upload --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Upload</label>
                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-200">
                    <p class="text-sm font-semibold text-gray-800">
                        {{ \Carbon\Carbon::parse($dokumen->tanggal_terbit ?? now())->translatedFormat('d F Y') }}
                    </p>
                </div>
            </div>

            {{-- Kategori --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2">Kategori Dokumen</label>
                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-200">
                    @php
                        $kategoriNama = $dokumen->kategori?->nama_kategori ?? 'RPS';
                        $badgeClass = match($kategoriNama) {
                            'Surat Keputusan' => 'bg-blue-100 text-blue-700 border-2 border-blue-300',
                            'Surat Tugas' => 'bg-blue-100 text-blue-700 border-2 border-blue-300',
                            'Riwayat Pengajaran' => 'bg-green-100 text-green-700 border-2 border-green-300',
                            'RPS', 'Rencana Pembelajaran Semester' => 'bg-blue-100 text-blue-700 border-2 border-blue-300',
                            'BKD', 'Buku Kerja Dosen' => 'bg-orange-100 text-orange-700 border-2 border-orange-300',
                            'SKP' => 'bg-pink-100 text-pink-700 border-2 border-pink-300',
                            default => 'bg-gray-100 text-gray-700 border-2 border-gray-300'
                        };
                    @endphp
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-bold {{ $badgeClass }}">
                        {{ $kategoriNama }}
                    </span>
                </div>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2">Deskripsi Dokumen</label>
                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-200">
                    <p class="text-sm text-gray-700 leading-relaxed">
                        {{ $dokumen->deskripsi ?? 'Tidak ada deskripsi.' }}
                    </p>
                </div>
            </div>

            {{-- Versi --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2">Versi Dokumen</label>
                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-200 flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-[#050C9C] to-blue-700 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    @php
                        $versiTerbaru = $dokumen->versi()->latest('nomor_versi')->first();
                    @endphp
                    <p class="text-xl font-bold text-gray-800">v{{ $versiTerbaru?->nomor_versi ?? 1 }}</p>
                </div>
            </div>

            {{-- Button Download (BLUE) --}}
            <div class="pt-2">
                <button class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-[#050C9C] to-blue-700 text-white rounded-xl font-bold text-sm hover:from-blue-700 hover:to-[#050C9C] transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download Dokumen
                </button>
            </div>

        </div>

    </div>

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tu/detail-dokumen.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/tu/detail-dokumen.js') }}"></script>
@endpush
