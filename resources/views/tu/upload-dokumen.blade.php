@extends('layouts.app')

@section('title', 'Upload Dokumen - SiDoRa')

@section('content')
    <div class="container mx-auto p-4">
        
        <h1 class="text-gray-700 text-2xl font-semibold mb-4">Upload Dokumen Baru (TU)</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Sukses!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form 
            action="{{ route('tu.upload.post') }}" 
            method="POST" 
            enctype="multipart/form-data"
            class="bg-white p-6 rounded-lg shadow-md"
        >
            @csrf

            <div class="mb-4">
                <label for="judul_dokumen" class="block text-gray-700 font-medium mb-2">Judul Dokumen:</label>
                <input 
                    type="text" 
                    id="judul_dokumen" 
                    name="judul_dokumen" 
                    value="{{ old('judul_dokumen') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-200"
                >
            </div>
            
            <div class="mb-6">
                <label for="file_upload" class="block text-gray-700 font-medium mb-2">Pilih File (PDF, Word, Excel, JPG, PNG):</label>
                <input 
                    type="file" 
                    id="file_upload" 
                    name="file_upload"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                >
                <p class="text-gray-500 text-sm mt-1">Maks: 10MB.</p>
            </div>

            <button 
                type="submit" 
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline"
            >
                Upload File
            </button>
        </form>
    </div>
@endsection