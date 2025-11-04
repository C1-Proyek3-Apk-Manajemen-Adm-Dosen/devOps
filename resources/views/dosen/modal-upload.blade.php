<!-- MODAL UPLOAD RPS -->
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
                    <!-- Tombol Custom -->
                    <button type="button" id="customFileBtn"
                        class="bg-purple-400 hover:bg-purple-500 text-white font-semibold px-5 py-2 rounded-xl transition">
                        Choose File
                    </button>

                    <!-- Nama File -->
                    <span id="fileName" class="text-gray-800 font-semibold">No File Chosen</span>

                    <!-- Input File Asli (disembunyikan) -->
                    <input type="file" id="realFileInput" class="hidden" />
                </div>
            </div>



            <div class="mb-6">
                <label for="aksesDokumen" class="block font-semibold mb-1 text-gray-800">
                    Pilih siapa yang dapat mengakses dokumen ini:
                </label>

                <input type="text" id="aksesDokumen" name="aksesDokumen"
                    placeholder="contoh: alaa.khaizure@gmail.com"
                    class="w-full bg-[#E9EBF0] rounded-xl px-4 py-2 text-gray-800 font-medium placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400 transition" />
            </div>


            <div class="flex justify-end">
                <button type="submit"
                    class="bg-purple-400 hover:bg-purple-500 text-white font-semibold px-6 py-2 rounded-xl">Submit</button>
            </div>
        </form>

        <!-- Tombol Close -->
        <button data-close-modal="modalRPS"
            class="absolute top-4 right-6 text-gray-500 hover:text-purple-600 text-xl">&times;</button>
    </div>
</div>
