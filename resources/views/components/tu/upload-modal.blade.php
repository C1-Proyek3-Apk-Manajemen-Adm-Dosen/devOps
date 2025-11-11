<div id="uploadModal"
    class="hidden fixed inset-0 flex items-center justify-center z-50 
    bg-white/10 backdrop-blur-sm opacity-0 transition-opacity duration-300">


    <div
        class="bg-white/80 backdrop-blur-md w-full max-w-2xl p-8 rounded-2xl shadow-2xl relative border border-white/40">

        <button onclick="closeModal()"
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>

        <h3 id="modalTitle" class="text-2xl font-bold mb-6 text-gray-800">Upload Dokumen</h3>

        <form action="#" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <input type="hidden" name="jenis_dokumen" id="jenis_dokumen">

            <!-- Judul -->
            <div>
                <label class="block font-semibold mb-2 text-gray-700">Judul Dokumen</label>
                <input type="text" name="judul"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                    required>
            </div>

            <!-- File Upload -->
            <div>
                <label class="block font-semibold mb-2 text-gray-700">Pilih Dokumen</label>
                <div class="flex items-center gap-3">
                    <label
                        class="bg-[#050C9C] text-white px-5 py-2 rounded-lg cursor-pointer hover:bg-blue-600 transition">
                        Choose File
                        <input type="file" name="file" class="hidden" onchange="updateFileName(this)" required>
                    </label>
                    <span id="fileName" class="text-gray-600">No file chosen</span>
                </div>
            </div>

            <!-- Akses Dokumen -->
            <div>
                <label class="block font-semibold mb-2 text-gray-700">Pilih siapa yang dapat mengakses dokumen
                    ini:</label>
                <input type="text" value="{{ Auth::user()->email }}" readonly
                    class="w-full rounded-lg border-gray-300 bg-gray-50 text-gray-700 font-medium px-4 py-2 cursor-not-allowed focus:ring-0 focus:border-gray-300">
            </div>


            <!-- Submit -->
            <div class="flex justify-end">
                <button type="submit"
                    class="bg-[#050C9C] text-white px-6 py-2 rounded-lg hover:bg-blue-600 shadow-md transition">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>
