<div id="uploadVersiModal" 
     class="fixed inset-0 z-50 hidden overflow-y-auto"
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true">
    
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" 
         id="modalBackdrop"
         onclick="closeUploadVersiModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div id="modalContent" 
             class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all opacity-0 scale-95">
            
            <div class="bg-gradient-to-r from-[#050C9C] to-blue-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white" id="modal-title">Upload Versi Baru</h3>
                            <p class="text-xs text-white/70">Unggah versi terbaru dokumen</p>
                        </div>
                    </div>
                    <button type="button" 
                            onclick="closeUploadVersiModal()"
                            class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <form id="uploadVersiForm" 
                  method="POST" 
                  action="{{ route('dosen.upload-versi', $dokumen->dokumen_id) }}" 
                  enctype="multipart/form-data">
                @csrf
                
                <div class="p-6 space-y-5">
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">Dokumen</p>
                                <p class="text-sm font-bold text-gray-800 truncate">{{ $dokumen->judul ?? 'Judul Dokumen' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">Nomor</p>
                                <p class="text-sm font-semibold text-gray-700">{{ $dokumen->nomor_dokumen ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">Versi Aktif</p>
                                @php
                                    $versiAktif = $dokumen->versi()->latest('nomor_versi')->first();
                                    $nomorVersiAktif = $versiAktif?->nomor_versi ?? 1;
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    v{{ $nomorVersiAktif }}
                                </span>
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">Versi Baru</p>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    v{{ $nomorVersiAktif + 1 }} 
                                </span>
                                <input type="hidden" name="nomor_versi" value="{{ $nomorVersiAktif + 1 }}">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            File Baru <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="file" 
                                   name="file" 
                                   id="fileInputVersi" 
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                   class="hidden" 
                                   required>
                            <div id="fileUploadAreaVersi" 
                                 onclick="document.getElementById('fileInputVersi').click()"
                                 class="w-full px-4 py-6 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 hover:border-[#050C9C] hover:bg-blue-50/30 transition-all cursor-pointer">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center" id="uploadIconVersi">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                    </div>
                                    <div class="text-center">
                                        <p id="fileLabelVersi" class="text-sm font-medium text-gray-600">
                                            Klik untuk pilih file
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (Max: 20MB)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Catatan Perubahan <span class="text-gray-400 font-normal">(Opsional)</span>
                        </label>
                        <textarea name="catatan_perubahan" 
                                  id="catatanPerubahan"
                                  rows="3" 
                                  class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#050C9C] focus:ring-2 focus:ring-[#050C9C]/20 outline-none transition resize-none text-sm"
                                  placeholder="Contoh: Revisi sesuai 02/BKD/25"></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row gap-3">
                    <button type="button" 
                            onclick="closeUploadVersiModal()"
                            class="flex-1 px-5 py-2.5 bg-white text-gray-700 border border-gray-300 rounded-xl font-semibold text-sm hover:bg-gray-100 transition-all duration-200">
                        Batal
                    </button>
                    <button type="submit" 
                            id="btnSubmitVersi"
                            class="flex-1 px-5 py-2.5 bg-gradient-to-r from-[#050C9C] to-blue-700 text-white rounded-xl font-semibold text-sm hover:from-blue-700 hover:to-[#050C9C] transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Upload Versi v{{ $nomorVersiAktif + 1 }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>