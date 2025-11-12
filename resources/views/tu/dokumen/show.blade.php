@extends('layouts.app')

@section('title', 'Detail Dokumen - SiDoRa')

@section('content')
<div class="p-8 space-y-6 font-sans">

  {{-- Link kembali --}}
  <div>
    <a href="{{ route('tu.riwayat') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Kembali ke Riwayat</a>
  </div>

  {{-- Banner judul --}}
  <div class="rounded-2xl bg-[#050C9C] text-white text-center font-semibold py-3 shadow-sm">
    Detail Dokumen
  </div>

  {{-- Card detail --}}
  <div class="rounded-2xl border border-gray-100 bg-white shadow p-8">
    <div class="flex items-start justify-between">
      <div class="space-y-5">

        {{-- Nomor Dokumen --}}
        <div>
          <div class="text-sm text-gray-500">Nomor Dokumen</div>
          <div class="text-base font-semibold text-gray-900">{{ $dokumen->nomor_dokumen ?? '—' }}</div>
        </div>

        {{-- Judul Dokumen --}}
        <div>
          <div class="text-sm text-gray-500">Judul Dokumen</div>
          <div class="text-base font-semibold text-gray-900">{{ $dokumen->judul }}</div>
        </div>

        {{-- Tanggal Upload --}}
        <div>
          <div class="text-sm text-gray-500">Tanggal Upload</div>
          <div class="text-base font-semibold text-gray-900">
            {{ \Carbon\Carbon::parse($dokumen->created_at)->locale('id')->translatedFormat('d F Y') }}
          </div>
        </div>

        {{-- Kategori Dokumen --}}
        <div>
          <div class="text-sm text-gray-500">Kategori Dokumen</div>
          <div class="text-base font-semibold text-gray-900">{{ $dokumen->nama_kategori ?? '-' }}</div>
        </div>

        {{-- Deskripsi Dokumen --}}
        @if(!empty($dokumen->deskripsi))
          <div>
            <div class="text-sm text-gray-500">Deskripsi Dokumen</div>
            <p class="text-sm text-gray-700 leading-relaxed">
              {{ $dokumen->deskripsi }}
            </p>
          </div>
        @endif

        {{-- Versi Dokumen --}}
        @if(!empty($latest))
          <div>
            <div class="text-sm text-gray-500">Versi Dokumen</div>
            <div class="text-base font-semibold text-gray-900">v{{ $latest->nomor_versi }}</div>
          </div>
        @endif
      </div>

      {{-- Badge status kanan atas --}}
      @php
        $status = strtoupper($dokumen->status ?? 'DRAFT');
        $badge = match($status) {
          'APPROVED' => 'bg-blue-100 text-blue-700',
          'REJECTED' => 'bg-red-100 text-red-700',
          default    => 'bg-gray-100 text-gray-600'
        };
      @endphp
      <span class="ml-4 shrink-0 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $badge }}">
        {{ $status }}
      </span>
    </div>

    {{-- Tombol Unduh --}}
    <div class="mt-8 flex justify-end">
      @if(!empty($latest?->file_path))
        <a href="{{ $latest->file_path }}"
           class="inline-flex items-center gap-2 rounded-xl bg-[#050C9C] text-white px-5 py-2.5 hover:bg-[#001070] transition shadow-sm">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 12v9m0-9l-3 3m3-3l3 3M12 3v9" />
          </svg>
          Unduh
        </a>
      @endif
    </div>
  </div>

  {{-- Riwayat Versi --}}
  <div class="bg-white rounded-2xl shadow p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Versi</h2>
    <div class="overflow-hidden rounded-xl border border-gray-100">
      <table class="min-w-full text-sm border-separate border-spacing-y-1">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold">
          <tr class="text-left">
            <th class="px-6 py-3">Versi</th>
            <th class="px-6 py-3">Tanggal Dokumen</th>
            <th class="px-6 py-3">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($versi as $v)
            <tr class="hover:bg-gray-50 transition">
              <td class="px-6 py-3 text-base font-semibold text-gray-900">v{{ $v->nomor_versi }}</td>
              <td class="px-6 py-3 text-base font-semibold text-gray-900">
                @if(!empty($v->tanggal_dokumen))
                  {{ \Carbon\Carbon::parse($v->tanggal_dokumen)->format('d M Y') }}
                @else
                  -
                @endif
              </td>
              <td class="px-6 py-3">
                @if(!empty($v->file_path))
                  <a href="{{ $v->file_path }}" class="text-[#050C9C] hover:underline font-medium">Buka/Unduh</a>
                @else
                  <span class="text-gray-400">-</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="px-6 py-8 text-center text-gray-500">Belum ada versi.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
