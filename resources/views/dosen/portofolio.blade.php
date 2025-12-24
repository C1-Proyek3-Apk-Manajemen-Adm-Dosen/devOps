@extends('layouts.app')

@section('title', 'Portofolio Dosen')

@section('content')
<div class="container mx-auto p-6 max-w-7xl">
    @if ($profil && $biodata)
    {{-- HEADER PROFIL --}}
    <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-2xl shadow-2xl p-8 mb-6 text-white">
        <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
            {{-- Foto Profil --}}
            <div class="flex-shrink-0">
                @if ($profil->foto_profil)
                <img src="{{ Storage::url($profil->foto_profil) }}"
                    class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg">
                @else
                <div class="w-32 h-32 rounded-full bg-white/20 backdrop-blur flex items-center justify-center text-6xl">
                    👤
                </div>
                @endif
            </div>

            {{-- Info Utama --}}
            <div class="flex-1">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-4xl font-bold mb-2">{{ $biodata['nama_lengkap'] ?? 'N/A' }}</h1>
                        <p class="text-xl opacity-90 mb-1">{{ $biodata['jabatan_fungsional'] ?? 'Dosen' }}</p>
                        <p class="text-lg opacity-75">{{ $biodata['program_studi'] ?? '-' }}</p>
                    </div>

                    @if (!$profil->is_verified)
                    <span class="bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-sm font-semibold">
                        Belum Verifikasi
                    </span>
                    @else
                    <span class="bg-green-400 text-green-900 px-3 py-1 rounded-full text-sm font-semibold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Terverifikasi
                    </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                        </svg>
                        <span class="text-sm">{{ $biodata['perguruan_tinggi'] ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
                            <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
                        </svg>
                        <span class="text-sm">NIDN: {{ $profil->nidn ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm">{{ $biodata['jenis_kelamin'] ?? '-' }}</span>
                    </div>
                </div>

                @if ($profil->bio)
                <div class="mt-4 p-4 bg-white/10 backdrop-blur rounded-lg">
                    <p class="text-sm leading-relaxed">{{ $profil->bio }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- STATISTIK KARYA --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium mb-1">Penelitian</p>
                    <p class="text-4xl font-bold text-blue-600">{{ $biodata['jumlah_penelitian'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 p-4 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium mb-1">Publikasi</p>
                    <p class="text-4xl font-bold text-green-600">{{ $biodata['jumlah_publikasi'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 p-4 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium mb-1">Pengabdian</p>
                    <p class="text-4xl font-bold text-purple-600">{{ $biodata['jumlah_pengabdian'] ?? 0 }}</p>
                </div>
                <div class="bg-purple-100 p-4 rounded-full">
                    <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- TABS PORTOFOLIO --}}
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <div class="border-b border-gray-200 mb-6">
            <nav class="flex space-x-8" x-data="{ activeTab: 'pendidikan' }">
                <button @click="activeTab = 'pendidikan'"
                    :class="activeTab === 'pendidikan' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    📚 Riwayat Pendidikan
                </button>
                <button @click="activeTab = 'penelitian'"
                    :class="activeTab === 'penelitian' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    🔬 Penelitian ({{ count($biodata['penelitian']) }})
                </button>
                <button @click="activeTab = 'publikasi'"
                    :class="activeTab === 'publikasi' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    📄 Publikasi ({{ count($biodata['publikasi']) }})
                </button>
                <button @click="activeTab = 'pengabdian'"
                    :class="activeTab === 'pengabdian' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    🤝 Pengabdian ({{ count($biodata['pengabdian']) }})
                </button>
                <button @click="activeTab = 'hki'"
                    :class="activeTab === 'hki' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    ©️ HKI/Paten ({{ count($biodata['hki']) }})
                </button>
            </nav>
        </div>

        {{-- Tab Content --}}
        <div x-data="{ activeTab: 'pendidikan' }">
            {{-- Riwayat Pendidikan --}}
            <div x-show="activeTab === 'pendidikan'" class="space-y-4">
                @forelse ($biodata['riwayat_pendidikan'] ?? [] as $pend)
                <div class="border-l-4 border-blue-500 bg-blue-50 p-4 rounded-r-lg hover:bg-blue-100 transition-colors">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-semibold text-lg text-gray-800">{{ $pend['perguruan_tinggi'] ?? '-' }}</p>
                            <p class="text-gray-600 mt-1">{{ $pend['gelar'] ?? '-' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                                {{ $pend['jenjang'] ?? '-' }}
                            </span>
                            <p class="text-gray-500 text-sm mt-2">{{ $pend['tahun'] ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-8">Tidak ada data riwayat pendidikan</p>
                @endforelse
            </div>

            {{-- Penelitian --}}
            <div x-show="activeTab === 'penelitian'" class="space-y-3">
                @forelse ($biodata['penelitian'] ?? [] as $index => $item)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 bg-blue-100 text-blue-600 w-10 h-10 rounded-full flex items-center justify-center font-bold">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-800 mb-1">{{ $item['judul'] ?? 'N/A' }}</h4>
                            @if (!empty($item['jenis']))
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">{{ $item['jenis'] }}</span>
                            @endif
                            <p class="text-sm text-gray-500 mt-2">Tahun: {{ $item['tahun'] ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-8">Belum ada data penelitian</p>
                @endforelse
            </div>

            {{-- Publikasi --}}
            <div x-show="activeTab === 'publikasi'" class="space-y-3">
                @forelse ($biodata['publikasi'] ?? [] as $index => $item)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 bg-green-100 text-green-600 w-10 h-10 rounded-full flex items-center justify-center font-bold">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-800 mb-1">{{ $item['judul'] ?? 'N/A' }}</h4>
                            @if (!empty($item['jenis']))
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">{{ $item['jenis'] }}</span>
                            @endif
                            <p class="text-sm text-gray-500 mt-2">Tahun: {{ $item['tahun'] ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-8">Belum ada data publikasi</p>
                @endforelse
            </div>

            {{-- Pengabdian --}}
            <div x-show="activeTab === 'pengabdian'" class="space-y-3">
                @forelse ($biodata['pengabdian'] ?? [] as $index => $item)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 bg-purple-100 text-purple-600 w-10 h-10 rounded-full flex items-center justify-center font-bold">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-800 mb-1">{{ $item['judul'] ?? 'N/A' }}</h4>
                            @if (!empty($item['jenis']))
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">{{ $item['jenis'] }}</span>
                            @endif
                            <p class="text-sm text-gray-500 mt-2">Tahun: {{ $item['tahun'] ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-8">Belum ada data pengabdian masyarakat</p>
                @endforelse
            </div>

            {{-- HKI --}}
            <div x-show="activeTab === 'hki'" class="space-y-3">
                @forelse ($biodata['hki'] ?? [] as $index => $item)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 bg-orange-100 text-orange-600 w-10 h-10 rounded-full flex items-center justify-center font-bold">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-800 mb-1">{{ $item['judul'] ?? 'N/A' }}</h4>
                            @if (!empty($item['jenis']))
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">{{ $item['jenis'] }}</span>
                            @endif
                            <p class="text-sm text-gray-500 mt-2">Tahun: {{ $item['tahun'] ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-8">Belum ada data HKI/Paten</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex flex-wrap gap-4 justify-between items-center">
            <div class="flex gap-3">
                <a href="{{ $profil->pddikti_url }}" target="_blank"
                    class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                        <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                    </svg>
                    Lihat di PDDikti
                </a>

                <button onclick="refreshData()"
                    class="inline-flex items-center gap-2 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors font-medium">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                    </svg>
                    Refresh Data
                </button>
            </div>

            @if (!$profil->is_verified)
            <button onclick="verifyProfile()"
                class="inline-flex items-center gap-2 bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors font-medium">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Verifikasi Profil
            </button>
            @endif
        </div>

        <p class="text-sm text-gray-500 mt-4">
            <strong>Terakhir diperbarui:</strong> {{ $profil->last_scraped_at?->diffForHumans() ?? 'Belum pernah' }}
        </p>
    </div>

    @else
    {{-- BELUM ADA PROFIL --}}
    <div class="bg-white rounded-2xl shadow-2xl p-12 text-center">
        <div class="text-red-500 mb-6 flex justify-center">
            <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>

        <h2 class="text-3xl font-bold text-gray-800 mb-4">Belum Ada Profil PDDikti</h2>
        <p class="text-gray-600 mb-8 max-w-2xl mx-auto">
            Hubungkan akun Anda dengan profil di database PDDikti Kemdikbud untuk menampilkan portofolio lengkap Anda.
            Sistem akan mengambil data penelitian, publikasi, dan pengabdian masyarakat secara otomatis.
        </p>

        <a href="{{ route('dosen.portofolio.search') }}"
            class="inline-flex items-center gap-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white px-8 py-4 rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all transform hover:scale-105 font-semibold text-lg shadow-lg">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
            Cari & Hubungkan Profil PDDikti
        </a>
    </div>
    @endif
</div>

<script>
    async function refreshData() {
        if (!confirm('Refresh data dari PDDikti? Data manual Anda tidak akan hilang.')) return;

        const btn = event.target;
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memperbarui...';

        try {
            const response = await fetch('{{ route("dosen.portofolio.refresh") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            });

            let data;
            try {
                data = await response.json();
            } catch (e) {
                data = {};
            }

            if (response.ok) {
                alert(data.message || 'Data berhasil diperbarui.');
                location.reload();
            } else {
                alert(data.message || 'Gagal memperbarui data.');
            }
        } catch (err) {
            alert('Terjadi kesalahan: ' + (err.message || err));
        } finally {
            try {
                btn.disabled = false;
                btn.innerHTML = btn.getAttribute('data-original') || 'Refresh Data';
            } catch (e) {}
        }
    }

    async function verifyProfile() {
        if (!confirm('Tandai profil ini sebagai terverifikasi?')) return;

        const btn = (typeof event !== 'undefined' && event.target) ? event.target : document.activeElement;
        btn.setAttribute('data-original', btn.innerHTML || 'Verifikasi Profil');
        btn.disabled = true;
        btn.innerHTML = 'Memverifikasi...';

        try {
            const response = await fetch('{{ route("dosen.portofolio.verify") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            });

            let data;
            try {
                data = await response.json();
            } catch (e) {
                data = {};
            }

            if (response.ok) {
                alert(data.message || 'Profil berhasil diverifikasi.');
                location.reload();
            } else {
                alert(data.message || 'Gagal memverifikasi profil.');
            }
        } catch (err) {
            alert('Terjadi kesalahan: ' + (err.message || err));
        } finally {
            try {
                btn.disabled = false;
                btn.innerHTML = btn.getAttribute('data-original') || 'Verifikasi Profil';
            } catch (e) {}
        }
    }
</script>

@endsection