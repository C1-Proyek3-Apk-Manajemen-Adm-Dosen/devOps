<div id="share-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg p-8 transform transition-all">
        <!-- Header dengan icon -->
        <div class="flex items-start gap-4 mb-6">
            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-[#050C9C] to-[#0A1D56] rounded-2xl flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900 mb-1">
                    Share Document
                </h2>
                <p class="text-sm text-gray-500 font-medium" id="share-doc-title"></p>
            </div>
            <button type="button" 
                    onclick="closeShareModal()"
                    class="flex-shrink-0 w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Info box -->
        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 mb-6">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-[#050C9C] flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-[#050C9C] mb-1">Cara menggunakan link</p>
                    <p class="text-xs text-gray-600">Link ini dapat dibagikan kepada siapa saja untuk mengakses dokumen. Link akan expired dalam 7 hari.</p>
                </div>
            </div>
        </div>

        <!-- Document Link -->
        <label class="block text-sm font-semibold text-gray-700 mb-2">
            Document Link
        </label>

        <div class="relative mb-6">
            <input type="text"
                   id="share-link-input"
                   readonly
                   class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 pr-24 text-sm bg-gray-50 focus:outline-none focus:border-[#050C9C] transition-colors font-mono text-gray-700">
            <button type="button"
                    onclick="copyShareLink()"
                    class="absolute right-2 top-1/2 -translate-y-1/2 px-4 py-2 text-sm font-semibold rounded-lg bg-gradient-to-r from-[#050C9C] to-[#0A1D56] text-white hover:shadow-lg transition-all duration-200 hover:scale-105 active:scale-95">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Copy
                </span>
            </button>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <div class="flex items-center gap-2 text-xs text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <span>Link terenkripsi & aman</span>
            </div>
            <button type="button"
                    onclick="closeShareModal()"
                    class="px-5 py-2 text-sm font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors">
                Close
            </button>
        </div>
    </div>
</div>
