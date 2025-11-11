@extends('layouts.app')

@section('title', 'Upload Dokumen - SiDoRa')

@section('content')
    <div class="p-8 bg-[#E9EBF0] rounded-3xl min-h-[85vh]">
        <!-- Judul Halaman -->
        <h2 class="text-2xl font-bold text-gray-800 mb-8">Apa yang mau diupload?</h2>

        <!-- GRID CARD -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- CARD: Upload File RPS -->
            <button data-modal-target="modalUpload"
                class="flex justify-between items-center bg-white hover:border-2 hover:border-[#050C9C]
                   transition-all duration-300 shadow-md hover:shadow-lg rounded-2xl px-6 py-6 w-full text-left">
                <div>
                    <p class="font-bold text-gray-900">Upload File</p>
                    <p class="text-gray-700 text-base font-semibold">RPS</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fa-solid fa-arrow-up-from-bracket text-[#050C9C] text-xl"></i>
                </div>
            </button>

            <!-- CARD: Upload File SKP -->
            <button data-modal-target="modalUpload"
                class="flex justify-between items-center bg-white hover:border-2 hover:border-[#050C9C] 
                   transition-all duration-300 shadow-md hover:shadow-lg rounded-2xl px-6 py-6 w-full text-left">
                <div>
                    <p class="font-bold text-gray-900">Upload File</p>
                    <p class="text-gray-700 text-base font-semibold">SKP</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fa-solid fa-arrow-up-from-bracket text-[#050C9C] text-xl"></i>
                </div>
            </button>

            <!-- CARD: Upload File BKD -->
            <button data-modal-target="modalUpload"
                class="flex justify-between items-center bg-white hover:border-2 hover:border-[#050C9C] 
                   transition-all duration-300 shadow-md hover:shadow-lg rounded-2xl px-6 py-6 w-full text-left">
                <div>
                    <p class="font-bold text-gray-900">Upload File</p>
                    <p class="text-gray-700 text-base font-semibold">BKD</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fa-solid fa-arrow-up-from-bracket text-[#050C9C] text-xl"></i>
                </div>
            </button>

            <!-- CARD: Bukti Pengajaran -->
            <button data-modal-target="modalUpload"
                class="flex justify-between items-center bg-white hover:border-2 hover:border-[#050C9C] 
                   transition-all duration-300 shadow-md hover:shadow-lg rounded-2xl px-6 py-6 w-full text-left">
                <div>
                    <p class="font-bold text-gray-900">Upload File</p>
                    <p class="text-gray-700 text-base font-semibold">Bukti Pengajaran</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fa-solid fa-arrow-up-from-bracket text-[#050C9C] text-xl"></i>
                </div>
            </button>
        </div>
    </div>

    <!-- INCLUDE SEMUA MODAL UPLOAD -->
    @include('components.modals.upload-modals-dosen')
@endsection
