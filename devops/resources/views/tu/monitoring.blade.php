@extends('layouts.app')
@section('title', 'Monitoring Dokumen - SiDoRa')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6">

    <div class="mb-6 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 mb-1">
                Monitoring Dokumen TU
            </h1>
            <p class="text-gray-500 text-sm">Kelola dan pantau semua dokumen administrasi</p>
        </div>

        <div class="relative w-full lg:w-80">
            <input type="text" 
                   id="searchInput"
                   placeholder="Search" 
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#050C9C] focus:border-transparent transition-all duration-200 shadow-sm text-sm">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </div>

    <div class="w-full mb-6">
        <div class="bg-white shadow-md rounded-2xl p-2 flex flex-col sm:flex-row gap-2 overflow-x-auto">
            @foreach ($tabs as $key => $label)
                <a href="{{ route('tu.monitoring', ['tab' => $key]) }}"
                   class="flex-1 text-center py-3 px-4 rounded-xl font-semibold transition-all duration-300 text-sm whitespace-nowrap
                        {{ $tab == $key 
                             ? 'bg-[#050C9C] text-white shadow-lg' 
                             : 'text-gray-600 hover:bg-gray-50 hover:text-[#050C9C]' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-hidden">
            <div class="max-h-[400px] overflow-y-auto" style="scrollbar-width: thin; scrollbar-color: #050C9C #f1f5f9;">
                <table class="w-full text-gray-700">
                    <thead class="sticky top-0 z-10">
                        <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                            <th class="py-3 px-4 text-left text-[10px] font-bold text-gray-600 uppercase tracking-wider w-12">No</th>
                            <th class="py-3 px-4 text-left text-[10px] font-bold text-gray-600 uppercase tracking-wider">Nama Dokumen</th>
                            <th class="py-3 px-4 text-left text-[10px] font-bold text-gray-600 uppercase tracking-wider">Kategori</th>
                            <th class="py-3 px-4 text-left text-[10px] font-bold text-gray-600 uppercase tracking-wider">Tanggal Upload</th>
                            <th class="py-3 px-4 text-left text-[10px] font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($dokumens as $index => $d)
                        <tr class="hover:bg-blue-50/30 transition-all duration-200 group">
                            <td class="py-3 px-4 text-xs font-medium text-gray-500">
                                {{ $dokumens->firstItem() + $index }}
                            </td>

                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-[#050C9C] to-[#0818d4] flex items-center justify-center shadow-md flex-shrink-0">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm text-gray-800 group-hover:text-[#050C9C] transition-colors">
                                            {{ $d->judul ?? '-' }}
                                        </p>
                                        <p class="text-[10px] text-gray-500">{{ $d->nomor_dokumen ?? 'No. Dokumen' }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3 px-4">
                                @php
                                    $kategoriNama = $d->kategori?->nama_kategori ?? 'Tidak Ada Kategori';
                                    $badgeClass = match($kategoriNama) {
                                        'Surat Keputusan' => 'bg-purple-100 text-purple-700 border border-purple-200',
                                        'Surat Tugas' => 'bg-blue-100 text-blue-700 border border-blue-200',
                                        'Riwayat Pengajaran' => 'bg-green-100 text-green-700 border border-green-200',
                                        'RPS', 'Rencana Pembelajaran Semester' => 'bg-indigo-100 text-indigo-700 border border-indigo-200',
                                        'BKD', 'Buku Kerja Dosen' => 'bg-orange-100 text-orange-700 border border-orange-200',
                                        'SKP' => 'bg-pink-100 text-pink-700 border border-pink-200',
                                        default => 'bg-gray-100 text-gray-700 border border-gray-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $badgeClass }}">
                                    <span class="w-1 h-1 rounded-full bg-current"></span>
                                    {{ $kategoriNama }}
                                </span>
                            </td>

                            <td class="py-3 px-4 text-xs text-gray-600">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($d->tanggal_terbit)->translatedFormat('d M Y') }}
                                </div>
                            </td>

                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('tu.edit-hak-akses', $d->dokumen_id) }}" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#050C9C] text-white text-xs font-medium rounded-lg hover:bg-[#0818d4] transition-all duration-200 shadow-sm hover:shadow-md">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        Hak Akses
                                    </a>

                                    <a href="{{ route('tu.detail-dokumen', $d->dokumen_id) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 text-gray-600 text-xs font-medium rounded-lg hover:bg-gray-100 hover:text-[#050C9C] transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-14 h-14 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-gray-500 font-medium text-sm">Tidak ada dokumen</p>
                                    <p class="text-gray-400 text-xs">Dokumen akan muncul di sini setelah diupload</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-gray-50 px-4 sm:px-6 py-3 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
                <div class="flex items-center gap-2 text-xs text-gray-600">
                    <svg class="w-4 h-4 text-[#050C9C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="font-semibold text-gray-700">Total Dokumen:</span>
                    <span class="font-bold text-[#050C9C]">{{ $total }}</span>
                </div>
                
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <span class="text-xs text-gray-600 font-medium">
                        Halaman {{ $dokumens->currentPage() }} dari {{ $dokumens->lastPage() }}
                    </span>
                    
                    <div class="flex items-center gap-1.5">
                        @if ($dokumens->onFirstPage())
                            <span class="px-2.5 py-1.5 text-xs font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </span>
                        @else
                            <a href="{{ $dokumens->previousPageUrl() }}" 
                               class="px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#050C9C] hover:text-white hover:border-[#050C9C] transition-all duration-200 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </a>
                        @endif

                        <div class="hidden sm:flex items-center gap-1.5">
                            @foreach ($dokumens->getUrlRange(1, $dokumens->lastPage()) as $page => $url)
                                @if ($page == $dokumens->currentPage())
                                    <span class="px-3 py-1.5 text-xs font-bold text-white bg-[#050C9C] rounded-lg shadow-md">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" 
                                       class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#050C9C] hover:text-white hover:border-[#050C9C] transition-all duration-200 shadow-sm">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        </div>

                        @if ($dokumens->hasMorePages())
                            <a href="{{ $dokumens->nextPageUrl() }}" 
                               class="px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#050C9C] hover:text-white hover:border-[#050C9C] transition-all duration-200 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        @else
                            <span class="px-2.5 py-1.5 text-xs font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tu/monitoring.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/tu/monitoring.js') }}"></script>
@endpush
