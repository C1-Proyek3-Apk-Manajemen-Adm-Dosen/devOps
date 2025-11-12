@extends('layouts.app')

@section('title', 'Riwayat Upload TU - SiDoRa')

@push('styles')
  @vite('resources/css/tu/riwayat.css')
@endpush

@section('content')
<div class="p-8" id="riwayatBox">
    {{-- Bar atas: Judul & Search (kanan) --}}
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
        <select name="cat"
                class="border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#050C9C]">
            <option value="">Semua Kategori</option>
            <option value="st" @selected(request('cat')==='st')>Surat Tugas (ST)</option>
            <option value="sk" @selected(request('cat')==='sk')>Surat Keputusan (SK)</option>
            <option value="rp" @selected(request('cat')==='rp')>Riwayat Pengajaran</option>
        </select>
        <button class="px-4 py-2 rounded-xl bg-[#050C9C] text-white hover:bg-[#001070] transition">
            Terapkan
        </button>
    </form>

    {{-- 📋 Tabel Riwayat --}}
    <div class="bg-white rounded-2xl shadow overflow-hidden border border-gray-100">
        <table class="cards text-sm w-full">
            {{-- kunci lebar kolom --}}
            <colgroup>
            <col style="width:80px">   {{-- NO --}}
            <col>                      {{-- NAMA DOKUMEN --}}
            <col style="width:180px">  {{-- KATEGORI --}}
            <col style="width:180px">  {{-- TANGGAL UPLOAD --}}
            <col style="width:140px">  {{-- DOSEN --}}
            <col style="width:100px">  {{-- AKSI --}}
        </colgroup>

            <thead>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">NO</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">NAMA DOKUMEN</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">KATEGORI</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">TANGGAL UPLOAD</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">DOSEN</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">AKSI</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($docs as $i => $d)
                    @php
                        $alias = $d->alias
                          ?? (strtolower($d->nama_kategori ?? '') ?
                              (str_contains(strtolower($d->nama_kategori),'tugas') ? 'st'
                              : (str_contains(strtolower($d->nama_kategori),'keputusan') ? 'sk'
                              : (str_contains(strtolower($d->nama_kategori),'rps') ? 'rp' : 'none')))
                              : 'none');
                    @endphp

                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4 text-gray-700 text-center font-medium">
                            {{ $docs->firstItem() + $i }}
                        </td>

                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#050C9C] to-[#0818d4] flex items-center justify-center shadow-md flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="leading-tight">
                                    <div class="text-gray-900 font-semibold text-sm">{{ $d->judul }}</div>
                                    @if(!empty($d->nomor_dokumen))
                                      <div class="text-xs text-gray-500 mt-0.5">{{ $d->nomor_dokumen }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td class="px-4 py-4">
                            <span class="chip chip--{{ $alias }} inline-block px-3 py-1 rounded-full text-xs font-semibold">
                                {{ $d->nama_kategori ?? 'Tidak Ada Kategori' }}
                            </span>
                        </td>

                        <td class="px-4 py-4 text-gray-700 text-sm">
                            {{ \Carbon\Carbon::parse($d->created_at)->locale('id')->translatedFormat('d F Y') }}
                        </td>

                        <td class="px-4 py-4 text-gray-700 text-sm text-center">
                            {{ $recipientsMap[$d->dokumen_id] ?? '-' }}
                        </td>

                        <td class="px-4 py-4 text-center">
                            <a href="{{ route('tu.dokumen.show', $d->dokumen_id) }}"
                               class="text-[#050C9C] hover:underline font-medium text-sm">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Belum ada unggahan dokumen.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-gray-100">
            {{ $docs->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
  @vite('resources/js/tu/riwayat.js')
@endpush