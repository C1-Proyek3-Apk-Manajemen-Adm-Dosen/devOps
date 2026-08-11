<div id="duplicateFileModal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 opacity-0 transition-opacity duration-300">

    <div id="duplicateFileModalContent"
        class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md transform scale-95 opacity-0 transition-all duration-300">

        <h2 class="text-lg font-semibold text-gray-800 mb-3">File Sudah Ada</h2>

        <p class="text-gray-600 mb-6">
            File dengan nama yang sama sudah ada.  
            Kirim file lain atau tekan <b>Update Versi</b> untuk mengganti versi file.
        </p>

        <div class="flex justify-end gap-3">
            <button id="cancelDuplicateUpload"
                class="px-4 py-2 rounded-lg bg-gray-300 hover:bg-gray-400 text-gray-800">
                Batalkan
            </button>

            <button id="confirmUpdateVersion"
                class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white">
                Update Versi
            </button>
        </div>
    </div>
</div>
