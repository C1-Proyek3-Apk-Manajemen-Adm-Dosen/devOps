@extends('layouts.app')

@section('title', 'Portofolio Dosen')

@section('content')
    <div class="container mx-auto p-6">
        @if ($profil && $biodata)
            <!-- SUDAH ADA PROFIL -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex items-start gap-6">
                    <!-- Foto Profil -->
                    <div class="flex-shrink-0">
                        @if ($profil->foto_profil)
                            <img src="{{ Storage::url($profil->foto_profil) }}"
                                class="w-32 h-32 rounded-full object-cover border-4 border-[#050C9C]">
                        @else
                            <div class="w-32 h-32 rounded-full bg-[#050C9C] flex items-center justify-center text-5xl text-white">
                                👤
                            </div>
                        @endif
                    </div>

                    <!-- Info Dosen -->
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-800">{{ $biodata['nama_lengkap'] ?? 'N/A' }}</h1>
                        <p class="text-lg text-purple-600 mt-1">{{ $biodata['jabatan_fungsional'] ?? '-' }}</p>

                        <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
                            <div>
                                <span class="text-gray-500">Jenis Kelamin:</span>
                                <span class="font-medium ml-2">{{ $biodata['jenis_kelamin'] ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Status:</span>
                                <span class="font-medium ml-2">{{ $biodata['status_aktivitas'] ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Perguruan Tinggi:</span>
                                <span class="font-medium ml-2">{{ $biodata['perguruan_tinggi'] ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Program Studi:</span>
                                <span class="font-medium ml-2">{{ $biodata['program_studi'] ?? '-' }}</span>
                            </div>
                        </div>

                        @if ($profil->bio)
                            <div class="mt-4 p-3 bg-purple-50 rounded-lg">
                                <p class="text-gray-700 italic">{{ $profil->bio }}</p>
                            </div>
                        @endif

                        <!-- Statistik -->
                        <div class="grid grid-cols-3 gap-4 mt-6">
                            <div class="bg-blue-50 p-4 rounded-lg text-center">
                                <p class="text-3xl font-bold text-blue-600">{{ $biodata['jumlah_penelitian'] ?? 0 }}</p>
                                <p class="text-sm text-gray-600 mt-1">Penelitian</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg text-center">
                                <p class="text-3xl font-bold text-green-600">{{ $biodata['jumlah_publikasi'] ?? 0 }}</p>
                                <p class="text-sm text-gray-600 mt-1">Publikasi</p>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg text-center">
                                <p class="text-3xl font-bold text-purple-600">{{ $biodata['jumlah_pengabdian'] ?? 0 }}</p>
                                <p class="text-sm text-gray-600 mt-1">Pengabdian</p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3 mt-6">
                            <a href="{{ $profil->pddikti_url }}" target="_blank"
                                class="text-[#050C9C] hover:underline text-sm">
                                Lihat di PDDikti
                            </a>
                            <button onclick="openEditModal()" class="text-[#050C9C] hover:underline text-sm">
                                Edit Bio & Foto
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($profil)
            <!-- ADA PROFIL TAPI BELUM VERIFIKASI -->
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded">
                <p class="text-yellow-800">Profil Anda perlu diverifikasi</p>
            </div>
        @else
            <!-- BELUM ADA PROFIL -->
            <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                <div class="text-red-500 mb-4 flex justify-center">
                    <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-width="2" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2" />
                        <circle cx="8" cy="7" r="4" stroke-width="2" />
                        <path stroke-linecap="round" stroke-width="2" d="M15 9l6 6m0-6l-6 6" />
                    </svg>
                </div>

                <h2 class="text-2xl font-bold text-gray-800 mb-4">Belum Ada Data</h2>
                <p class="text-gray-600 mb-6">
                    Hubungkan akun Anda dengan profil di database PDDikti untuk menampilkan portofolio
                </p>

                <a href="{{ route('dosen.portofolio.search') }}"
                    class="inline-block bg-[#050C9C] text-white px-8 py-3 rounded-lg hover:bg-[#0815d9] transition-colors font-semibold">
                    Cari & Hubungkan Profil PDDikti
                </a>
            </div>

        @endif
    </div>
@endsection
