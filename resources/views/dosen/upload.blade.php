{{-- resources/views/dosen/upload.blade.php --}}

@extends('layouts.app')

@section('title', 'Upload Dokumen - SiDoRa')

@section('content')
    <div class="p-6 max-w-6xl mx-auto">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Upload Dokumen</h2>
            <p class="text-gray-600">Lengkapi form di bawah untuk mengupload dokumen baru</p>
        </div>

        {{-- Alert sukses (Hidden untuk trigger modal) --}}
        @if (session('success'))
            <div class="alert-success hidden">{{ session('success') }}</div>
        @endif

        {{-- Alert error --}}
        @if (session('error'))
            <div class="alert-error mb-6 p-4 rounded-xl bg-red-100 text-red-700 border border-red-300 flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        @endif

        {{-- Validasi error --}}
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-100 text-red-700 border border-red-300">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <p class="font-medium mb-2">Terdapat beberapa kesalahan:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="form-card bg-white rounded-2xl shadow-xl p-8">
            <form id="uploadForm" method="POST" action="{{ route('dosen.dokumen.upload.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    
                    {{-- Kolom kiri --}}
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-gray-800">Judul Dokumen <span class="text-red-500">*</span></label>
                            <input type="text" name="judul" value="{{ old('judul') }}" class="input-field w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#050C9C] focus:ring-2 focus:ring-[#050C9C]/20 outline-none transition" placeholder="Masukkan judul dokumen" required>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2 text-gray-800">Nomor Dokumen</label>
                            <input type="text" name="nomor_dokumen" value="{{ old('nomor_dokumen') }}" class="input-field w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#050C9C] focus:ring-2 focus:ring-[#050C9C]/20 outline-none transition" placeholder="Contoh: 001/SK/2025">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2 text-gray-800">Tanggal Terbit <span class="text-red-500">*</span></label>
                            <input type="text" name="tanggal_terbit" id="tanggalTerbit" value="{{ old('tanggal_terbit') }}" placeholder="Pilih atau ketik tanggal (dd/mm/yyyy)" class="input-field w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#050C9C] focus:ring-2 focus:ring-[#050C9C]/20 outline-none transition" required>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2 text-gray-800">Kategori Dokumen <span class="text-red-500">*</span></label>
                            <select name="kategori_id" class="custom-select input-field w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#050C9C] focus:ring-2 focus:ring-[#050C9C]/20 outline-none transition appearance-none bg-white" required>
                                <option value="" disabled selected>Pilih kategori dokumen</option>
                                @foreach ($kategoris as $kategori)
                                    @if (in_array($kategori->nama_kategori, ['RPS','BKD','SKP','Bukti Pengajaran']))
                                        <option value="{{ $kategori->kategori_id }}">
                                            {{ $kategori->nama_kategori }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Kolom kanan --}}
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-gray-800">Upload File <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" id="fileInput" class="hidden" required>
                                <div id="fileUploadArea" class="file-upload-area w-full px-4 py-3 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 transition cursor-pointer">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center flex-1">
                                            <svg class="w-8 h-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                            <div class="flex-1">
                                                <span id="fileLabel" class="text-gray-500 text-sm block truncate">Klik untuk pilih file</span>
                                                <span class="text-xs text-gray-400">PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (Max: 20MB)</span>
                                            </div>
                                        </div>
                                        <button type="button" class="px-5 py-2 bg-[#050C9C] hover:bg-[#040a7a] text-white text-sm rounded-lg transition font-medium flex-shrink-0 ml-3" onclick="document.getElementById('fileInput').click()">Pilih File</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2 text-gray-800">Deskripsi <span class="text-red-500">*</span></label>
                            <textarea name="deskripsi" rows="5" class="input-field w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#050C9C] focus:ring-2 focus:ring-[#050C9C]/20 outline-none transition resize-none" placeholder="Tulis deskripsi singkat dokumen..." required>{{ old('deskripsi') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2 text-gray-800">Hak Akses <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div id="hakAksesDropdown" class="input-field w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#050C9C] focus:ring-2 focus:ring-[#050C9C]/20 outline-none transition appearance-none bg-white cursor-pointer flex items-center justify-between">
                                    <span id="hakAksesLabel" class="text-gray-500 text-sm">Pilih pengguna yang dapat mengakses</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-gray-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                                
                                <div id="hakAksesMenu" class="hidden absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl max-h-64 overflow-y-auto">
                                    <div class="p-3">
                                        <div class="mb-3">
                                            <input type="text" id="searchUser" placeholder="Cari pengguna..." class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#050C9C]">
                                        </div>
                                        
                                        <label class="flex items-center px-3 py-2.5 hover:bg-blue-50 rounded-lg cursor-pointer border-b border-gray-100 mb-2">
                                            <input type="checkbox" id="selectAllUsers" class="w-4 h-4 text-[#050C9C] border-gray-300 rounded focus:ring-[#050C9C] focus:ring-2">
                                            <span class="ml-3 text-sm font-semibold text-gray-800">Pilih Semua Pengguna</span>
                                        </label>
                                        
                                        @foreach ($users as $user)
                                            <label class="user-checkbox flex items-center px-3 py-2.5 hover:bg-gray-50 rounded-lg cursor-pointer transition" data-username="{{ strtolower($user->name) }}" data-useremail="{{ strtolower($user->email) }}">
                                                <input type="checkbox" name="owner_user_id[]" value="{{ $user->id }}" class="hak-akses-checkbox w-4 h-4 text-[#050C9C] border-gray-300 rounded focus:ring-[#050C9C] focus:ring-2">
                                                <div class="ml-3 flex-1">
                                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="hakAksesValidation" required>
                            <p class="mt-2 text-xs text-gray-500">Pilih minimal 1 pengguna yang dapat mengakses dokumen ini</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center pt-6 border-t border-gray-200">
                    <button type="submit" class="btn-primary w-full md:w-1/2 px-8 py-3 rounded-xl bg-[#050C9C] hover:bg-[#040a7a] text-white font-semibold transition shadow-lg shadow-[#050C9C]/20 hover:shadow-xl flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Upload Dokumen
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Success --}}
    @include('components.dosen.upload-notification-success')

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="{{ asset('css/dosen/upload-dokumen-dosen.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    @vite('resources/js/dosen/upload-dokumen-dosen.js')
    @vite('resources/js/dosen/upload-notification-success-dosen.js')

<script>
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeSuccessNotification();
});

document.addEventListener('click', (e) => {
    const modal = document.getElementById('successNotificationModal');
    if (modal && e.target === modal) closeSuccessNotification();
});
</script>
@endpush