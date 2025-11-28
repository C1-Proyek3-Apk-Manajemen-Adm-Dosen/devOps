<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<div id="modalEditHakAkses" 
     class="fixed inset-0 z-50 hidden overflow-y-auto"
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true">
    
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" 
         id="modalBackdrop"
         onclick="closeEditHakAksesModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div id="modalContent" 
             class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all opacity-0 scale-95">
            
            <div class="bg-gradient-to-r from-[#050C9C] to-blue-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white" id="modal-title">Edit Hak Akses Dokumen</h3>
                            <p class="text-xs text-white/70">Kelola akses pengguna ke dokumen</p>
                        </div>
                    </div>
                    <button type="button" 
                            onclick="closeEditHakAksesModal()"
                            class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="overflow-y-auto max-h-[calc(90vh-180px)] px-6 py-6 space-y-6">
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Judul Dokumen
                    </label>
                    <div class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#050C9C] to-[#0818d4] flex items-center justify-center shadow-md flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800" id="modalDokumenJudul">-</p>
                                <p class="text-xs text-gray-500" id="modalDokumenNomor">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        Tambah Hak Akses
                    </label>
                    
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 border-2 border-dashed border-gray-300 rounded-xl p-4">
                        <form id="addAccessForm" class="space-y-4">
                            
                            <div class="relative">
                                <label class="block text-xs font-medium text-gray-600 mb-2">
                                    Pilih Pengguna
                                </label>
                                
                                <div id="hakAksesDropdownTrigger" class="input-field w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#050C9C] focus:ring-2 focus:ring-[#050C9C]/20 outline-none transition appearance-none bg-white cursor-pointer flex items-center justify-between">
                                    <span id="hakAksesLabel" class="text-gray-500 text-sm truncate">Pilih pengguna...</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-gray-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                                
                                <div id="hakAksesMenu" class="hidden absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl max-h-64 overflow-y-auto">
                                    <div class="p-3">
                                        <div class="mb-3 sticky top-0 bg-white z-10 pb-2 border-b border-gray-100">
                                            <input type="text" id="searchUser" placeholder="Cari nama atau email..." class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#050C9C] focus:ring-1 focus:ring-[#050C9C]">
                                        </div>
                                        
                                        <label class="flex items-center px-3 py-2.5 hover:bg-blue-50 rounded-lg cursor-pointer border-b border-gray-100 mb-2 bg-gray-50">
                                            <input type="checkbox" id="selectAllUsers" class="w-4 h-4 text-[#050C9C] border-gray-300 rounded focus:ring-[#050C9C] focus:ring-2 transition">
                                            <span class="ml-3 text-sm font-bold text-gray-700">Pilih Semua Pengguna</span>
                                        </label>
                                        
                                        <div id="userListContainer">
                                        </div>
                                        
                                        <div id="noUserFound" class="hidden py-4 text-center text-sm text-gray-500">
                                            Pengguna tidak ditemukan.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-2">
                                    Jenis Akses
                                </label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="permission" value="READ" class="peer sr-only" checked>
                                        <div class="px-3 py-2.5 bg-white border-2 border-gray-300 rounded-lg text-center text-xs font-semibold text-gray-600 transition-all duration-200 peer-checked:border-[#050C9C] peer-checked:bg-[#050C9C] peer-checked:text-white peer-checked:shadow-md group-hover:border-[#050C9C]/50">
                                            READ
                                        </div>
                                    </label>
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="permission" value="COMMENT" class="peer sr-only">
                                        <div class="px-3 py-2.5 bg-white border-2 border-gray-300 rounded-lg text-center text-xs font-semibold text-gray-600 transition-all duration-200 peer-checked:border-[#050C9C] peer-checked:bg-[#050C9C] peer-checked:text-white peer-checked:shadow-md group-hover:border-[#050C9C]/50">
                                            COMMENT
                                        </div>
                                    </label>
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="permission" value="EDIT" class="peer sr-only">
                                        <div class="px-3 py-2.5 bg-white border-2 border-gray-300 rounded-lg text-center text-xs font-semibold text-gray-600 transition-all duration-200 peer-checked:border-[#050C9C] peer-checked:bg-[#050C9C] peer-checked:text-white peer-checked:shadow-md group-hover:border-[#050C9C]/50">
                                            EDIT
                                        </div>
                                    </label>
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="permission" value="OWNER" class="peer sr-only">
                                        <div class="px-3 py-2.5 bg-white border-2 border-gray-300 rounded-lg text-center text-xs font-semibold text-gray-600 transition-all duration-200 peer-checked:border-[#050C9C] peer-checked:bg-[#050C9C] peer-checked:text-white peer-checked:shadow-md group-hover:border-[#050C9C]/50">
                                            OWNER
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <button type="button" 
                                    id="addAccessBtn"
                                    class="w-full py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-xl hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span id="addBtnText">Tambah Akses</span>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="px-3 bg-white text-gray-500 font-medium">Daftar Akses</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        Pengguna yang memiliki akses
                    </label>
                    
                    <div id="accessList" class="space-y-2 max-h-64 overflow-y-auto pr-2">
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row gap-3 justify-end">
                <button type="button"
                        onclick="closeEditHakAksesModal()"
                        class="px-6 py-2.5 bg-white border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>