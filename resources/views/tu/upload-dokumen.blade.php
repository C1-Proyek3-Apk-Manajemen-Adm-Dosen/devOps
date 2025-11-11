@extends('layouts.app')

@section('title', 'Upload Dokumen - SiDoRa')

@section('content')
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-6">Apa yang mau diupload?</h2>

        <!-- ====== Alert Upload Success / Error ====== -->
        @if (session('success'))
            <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-700 border border-green-300 flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">×</button>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-700 border border-red-300 flex items-center justify-between">
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">×</button>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-700 border border-red-300">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- ========================================== -->

        <!-- ===== Pilihan Card ===== -->
        <div class="flex flex-wrap gap-6">
            <!-- Card Surat Tugas -->
            <div onclick="openModal('Surat Tugas')"
                class="cursor-pointer w-60 p-5 bg-white rounded-2xl shadow-md hover:shadow-lg transition flex items-center justify-between">
                <span class="font-semibold text-gray-800">Surat Tugas</span>
                <div class="bg-blue-100 p-2 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="rgb(59,130,246)" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 12v8h16v-8M16 6l-4-4m0 0L8 6m4-4v16" />
                    </svg>
                </div>
            </div>

            <!-- Card Surat Keputusan -->
            <div onclick="openModal('Surat Keputusan')"
                class="cursor-pointer w-60 p-5 bg-white rounded-2xl shadow-md hover:shadow-lg transition flex items-center justify-between">
                <span class="font-semibold text-gray-800">Surat Keputusan</span>
                <div class="bg-blue-100 p-2 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="rgb(59,130,246)" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 12v8h16v-8M16 6l-4-4m0 0L8 6m4-4v16" />
                    </svg>
                </div>
            </div>

            <!-- Card Riwayat Pengajaran -->
            <div onclick="openModal('Riwayat Pengajaran')"
                class="cursor-pointer w-60 p-5 bg-white rounded-2xl shadow-md hover:shadow-lg transition flex items-center justify-between">
                <span class="font-semibold text-gray-800">Riwayat Pengajaran</span>
                <div class="bg-blue-100 p-2 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="rgb(59,130,246)" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 12v8h16v-8M16 6l-4-4m0 0L8 6m4-4v16" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Modal Upload TU -->
        @include('components.tu.upload-modals-tu')
    </div>

    {{-- optional: auto-hide success alert --}}
    <script>
        setTimeout(() => {
            const successAlert = document.querySelector('.bg-green-100');
            if (successAlert) successAlert.remove();
        }, 3000);
    </script>
@endsection
