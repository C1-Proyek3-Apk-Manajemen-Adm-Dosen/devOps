<!-- Modal Upload Dokumen -->
<div id="modalUpload" class="hidden fixed inset-0 bg-gray-900/30 backdrop-blur-sm flex items-center justify-center z-50">

    <div class="bg-white rounded-2xl w-full max-w-lg p-6 relative shadow-xl">
        <!-- Tombol Close -->
        <button data-close-modal="modalUpload"
            class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl">&times;</button>

        <!-- Judul Modal -->
        <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Upload Dokumen</h2>

        <!-- Form Upload -->
        <form action="{{ route('dosen.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Judul Dokumen -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Judul Dokumen</label>
                <input type="text" name="judul" placeholder="Masukkan judul dokumen"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#050C9C] focus:outline-none"
                    required>
            </div>

            <!-- Kategori + Nomor Dokumen -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Kategori Dokumen -->
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Kategori Dokumen</label>
                    <select name="kategori"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#050C9C] focus:outline-none"
                        required>
                        <option value="" disabled selected>Pilih kategori</option>
                        <option value="Surat Keputusan">Surat Keputusan</option>
                        <option value="Surat Tugas">Surat Tugas</option>
                        <option value="RPS">RPS</option>
                        <option value="SKP">SKP</option>
                        <option value="Portofolio">Portofolio</option>
                    </select>
                </div>

                <!-- Nomor Dokumen -->
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nomor Dokumen</label>
                    <input type="text" name="nomor_dokumen" placeholder="Masukkan nomor dokumen"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#050C9C] focus:outline-none">
                </div>
            </div>

            <!-- Tanggal Terbit -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Tanggal Terbit</label>
                <input type="date" name="tanggal_terbit"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#050C9C] focus:outline-none"
                    required>
            </div>

            <!-- Upload File -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Upload File</label>
                <div class="flex items-center gap-3 border border-gray-300 rounded-lg px-4 py-2 bg-gray-50">
                    <button type="button" id="customFileBtnUpload"
                        class="bg-[#050C9C] text-white px-4 py-2 rounded-md hover:bg-[#3572EF] transition">Choose
                        File</button>
                    <input type="file" id="realFileInputUpload" name="file" class="hidden" required>
                    <span id="fileNameUpload" class="text-sm text-gray-500">No File Chosen</span>
                </div>
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="3" placeholder="Masukkan deskripsi singkat dokumen..."
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#050C9C] focus:outline-none"></textarea>
            </div>

            <!-- Tombol Submit -->
            <div class="text-center pt-2">
                <button type="submit"
                    class="bg-[#050C9C] text-white font-semibold px-6 py-2 rounded-full hover:bg-[#3572EF] transition">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>
