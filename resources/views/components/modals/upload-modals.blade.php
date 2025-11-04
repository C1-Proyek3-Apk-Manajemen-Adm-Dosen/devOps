<!-- ==================== MODAL UPLOAD RPS ==================== -->
<div id="modalRPS" class="hidden fixed inset-0 backdrop-blur-md bg-white/30 flex items-center justify-center z-50">
    <div class="bg-white rounded-3xl shadow-lg w-[90%] md:w-[600px] p-8 relative">
        <h2 class="text-2xl font-bold mb-6 text-gray-900">Upload Dokumen RPS</h2>

        <form>
            <!-- Judul -->
            <div class="mb-4">
                <label class="block font-semibold mb-1 text-gray-800">Judul Dokumen</label>
                <input type="text" class="w-full rounded-xl border border-gray-300 px-4 py-2 bg-[#E9EBF0]"
                    placeholder="Masukkan judul dokumen">
            </div>

            <!-- Pilih Dokumen -->
            <div class="mb-4">
                <label class="block font-semibold mb-1 text-gray-800">Pilih Dokumen</label>
                <div class="flex items-center gap-4 bg-[#E9EBF0] rounded-xl p-2">
                    <button type="button" id="customFileBtnRPS"
                        class="bg-purple-400 hover:bg-purple-500 text-white font-semibold px-5 py-2 rounded-xl transition">
                        Choose File
                    </button>
                    <span id="fileNameRPS" class="text-gray-800 font-semibold">No File Chosen</span>
                    <input type="file" id="realFileInputRPS" class="hidden" />
                </div>
            </div>

            <!-- Akses Dokumen -->
            <div class="mb-6">
                <label for="aksesRPS" class="block font-semibold mb-1 text-gray-800">
                    Pilih siapa yang dapat mengakses dokumen ini:
                </label>
                <input type="text" id="aksesRPS" name="aksesRPS" placeholder="contoh: alaa.khaizure@gmail.com"
                    class="w-full bg-[#E9EBF0] rounded-xl px-4 py-2 text-gray-800 font-medium placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400 transition" />
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-purple-400 hover:bg-purple-500 text-white font-semibold px-6 py-2 rounded-xl">
                    Submit
                </button>
            </div>
        </form>

        <button data-close-modal="modalRPS"
            class="absolute top-4 right-6 text-gray-500 hover:text-purple-600 text-xl">&times;</button>
    </div>
</div>

<!-- ==================== MODAL UPLOAD SKP ==================== -->
<div id="modalSKP" class="hidden fixed inset-0 backdrop-blur-md bg-white/30 flex items-center justify-center z-50">
    <div class="bg-white rounded-3xl shadow-lg w-[90%] md:w-[600px] p-8 relative">
        <h2 class="text-2xl font-bold mb-6 text-gray-900">Upload Dokumen SKP</h2>

        <form>
            <div class="mb-4">
                <label class="block font-semibold mb-1 text-gray-800">Judul Dokumen</label>
                <input type="text" class="w-full rounded-xl border border-gray-300 px-4 py-2 bg-[#E9EBF0]"
                    placeholder="Masukkan judul dokumen">
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1 text-gray-800">Pilih Dokumen</label>
                <div class="flex items-center gap-4 bg-[#E9EBF0] rounded-xl p-2">
                    <button type="button" id="customFileBtnSKP"
                        class="bg-purple-400 hover:bg-purple-500 text-white font-semibold px-5 py-2 rounded-xl transition">
                        Choose File
                    </button>
                    <span id="fileNameSKP" class="text-gray-800 font-semibold">No File Chosen</span>
                    <input type="file" id="realFileInputSKP" class="hidden" />
                </div>
            </div>

            <div class="mb-6">
                <label for="aksesSKP" class="block font-semibold mb-1 text-gray-800">
                    Pilih siapa yang dapat mengakses dokumen ini:
                </label>
                <input type="text" id="aksesSKP" name="aksesSKP" placeholder="contoh: alaa.khaizure@gmail.com"
                    class="w-full bg-[#E9EBF0] rounded-xl px-4 py-2 text-gray-800 font-medium placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400 transition" />
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-purple-400 hover:bg-purple-500 text-white font-semibold px-6 py-2 rounded-xl">
                    Submit
                </button>
            </div>
        </form>

        <button data-close-modal="modalSKP"
            class="absolute top-4 right-6 text-gray-500 hover:text-purple-600 text-xl">&times;</button>
    </div>
</div>

<!-- ==================== MODAL UPLOAD BKD ==================== -->
<div id="modalBKD" class="hidden fixed inset-0 backdrop-blur-md bg-white/30 flex items-center justify-center z-50">
    <div class="bg-white rounded-3xl shadow-lg w-[90%] md:w-[600px] p-8 relative">
        <h2 class="text-2xl font-bold mb-6 text-gray-900">Upload Dokumen BKD</h2>

        <form>
            <div class="mb-4">
                <label class="block font-semibold mb-1 text-gray-800">Judul Dokumen</label>
                <input type="text" class="w-full rounded-xl border border-gray-300 px-4 py-2 bg-[#E9EBF0]"
                    placeholder="Masukkan judul dokumen">
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1 text-gray-800">Pilih Dokumen</label>
                <div class="flex items-center gap-4 bg-[#E9EBF0] rounded-xl p-2">
                    <button type="button" id="customFileBtnBKD"
                        class="bg-purple-400 hover:bg-purple-500 text-white font-semibold px-5 py-2 rounded-xl transition">
                        Choose File
                    </button>
                    <span id="fileNameBKD" class="text-gray-800 font-semibold">No File Chosen</span>
                    <input type="file" id="realFileInputBKD" class="hidden" />
                </div>
            </div>

            <div class="mb-6">
                <label for="aksesBKD" class="block font-semibold mb-1 text-gray-800">
                    Pilih siapa yang dapat mengakses dokumen ini:
                </label>
                <input type="text" id="aksesBKD" name="aksesBKD" placeholder="contoh: alaa.khaizure@gmail.com"
                    class="w-full bg-[#E9EBF0] rounded-xl px-4 py-2 text-gray-800 font-medium placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400 transition" />
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-purple-400 hover:bg-purple-500 text-white font-semibold px-6 py-2 rounded-xl">
                    Submit
                </button>
            </div>
        </form>

        <button data-close-modal="modalBKD"
            class="absolute top-4 right-6 text-gray-500 hover:text-purple-600 text-xl">&times;</button>
    </div>
</div>

<!-- ==================== MODAL UPLOAD BUKTI PENGAJARAN ==================== -->
<div id="modalBukti" class="hidden fixed inset-0 backdrop-blur-md bg-white/30 flex items-center justify-center z-50">
    <div class="bg-white rounded-3xl shadow-lg w-[90%] md:w-[600px] p-8 relative">
        <h2 class="text-2xl font-bold mb-6 text-gray-900">Upload Bukti Pengajaran</h2>

        <form>
            <div class="mb-4">
                <label class="block font-semibold mb-1 text-gray-800">Judul Dokumen</label>
                <input type="text" class="w-full rounded-xl border border-gray-300 px-4 py-2 bg-[#E9EBF0]"
                    placeholder="Masukkan judul dokumen">
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1 text-gray-800">Pilih Dokumen</label>
                <div class="flex items-center gap-4 bg-[#E9EBF0] rounded-xl p-2">
                    <button type="button" id="customFileBtnBukti"
                        class="bg-purple-400 hover:bg-purple-500 text-white font-semibold px-5 py-2 rounded-xl transition">
                        Choose File
                    </button>
                    <span id="fileNameBukti" class="text-gray-800 font-semibold">No File Chosen</span>
                    <input type="file" id="realFileInputBukti" class="hidden" />
                </div>
            </div>

            <div class="mb-6">
                <label for="aksesBukti" class="block font-semibold mb-1 text-gray-800">
                    Pilih siapa yang dapat mengakses dokumen ini:
                </label>
                <input type="text" id="aksesBukti" name="aksesBukti"
                    placeholder="contoh: alaa.khaizure@gmail.com"
                    class="w-full bg-[#E9EBF0] rounded-xl px-4 py-2 text-gray-800 font-medium placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400 transition" />
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-purple-400 hover:bg-purple-500 text-white font-semibold px-6 py-2 rounded-xl">
                    Submit
                </button>
            </div>
        </form>

        <button data-close-modal="modalBukti"
            class="absolute top-4 right-6 text-gray-500 hover:text-purple-600 text-xl">&times;</button>
    </div>
</div>
