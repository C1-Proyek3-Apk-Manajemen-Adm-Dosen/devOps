<!-- resources/views/tu/upload-modals-tu.blade.php -->
<div id="uploadModal"
    class="hidden fixed inset-0 flex items-center justify-center z-50 bg-black/40 backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl p-8 relative">

        <!-- Header dengan Tombol Tutup -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Upload Dokumen RPS</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Form Upload -->
        <form id="uploadForm" method="POST" action="{{ route('tu.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Grid 2 Kolom -->
            <div class="grid grid-cols-2 gap-6 mb-6">
                
                <!-- Kolom Kiri -->
                <div class="space-y-4">
                    <!-- Judul Dokumen -->
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">Judul Dokumen</label>
                        <input type="text" name="judul"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#050C9C] focus:ring-2 focus:ring-[#050C9C]/20 outline-none transition"
                            placeholder="Masukkan judul dokumen" required>
                    </div>

                    <!-- Nomor Dokumen -->
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">Nomor Dokumen</label>
                        <input type="text" name="nomor_dokumen"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#050C9C] focus:ring-2 focus:ring-[#050C9C]/20 outline-none transition"
                            placeholder="Masukkan nomor dokumen">
                    </div>

                    <!-- Tanggal Terbit -->
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">Tanggal Terbit</label>
                        <input type="text" name="tanggal_terbit" placeholder="dd/mm/yyyy"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#050C9C] focus:ring-2 focus:ring-[#050C9C]/20 outline-none transition"
                            pattern="\d{2}/\d{2}/\d{4}" title="Gunakan format dd/mm/yyyy" required>
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">Pilih Kategori</label>
                        <select name="kategori_id"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#050C9C] focus:ring-2 focus:ring-[#050C9C]/20 outline-none transition appearance-none bg-white"
                            required>
                            <option value="" disabled selected>Pilih kategori dokumen</option>
                            @foreach ($kategoris as $kategori)
                                <option value="{{ $kategori->kategori_id }}">{{ $kategori->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="space-y-4">
                    <!-- Upload File dengan Design Baru -->
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">Upload File</label>
                        <div class="relative">
                            <input type="file" name="file" accept=".pdf,.doc,.docx,.jpg,.png" id="fileInput"
                                class="hidden" required>
                            <div onclick="document.getElementById('fileInput').click()"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 transition cursor-pointer flex items-center justify-between">
                                <span class="text-gray-500 text-sm truncate" id="fileLabel">No File Choosen</span>
                                <button type="button"
                                    class="px-4 py-2 bg-[#050C9C] hover:bg-[#040a7a] text-white text-sm rounded-lg transition font-medium flex-shrink-0 ml-2">
                                    Choose File
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">Deskripsi</label>
                        <textarea name="deskripsi" rows="4"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#050C9C] focus:ring-2 focus:ring-[#050C9C]/20 outline-none transition resize-none"
                            placeholder="Tulis deskripsi singkat dokumen..." required></textarea>
                    </div>

                    <!-- Owner User -->
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">Pilih owner yang harus mengerjakan dokumen ini</label>
                        <select name="owner_user_id"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#050C9C] focus:ring-2 focus:ring-[#050C9C]/20 outline-none transition appearance-none bg-white"
                            required>
                            <option value="" disabled selected>Pilih pemilik dokumen</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="pt-2">
                <button type="submit"
                    class="w-full py-3.5 rounded-xl bg-[#050C9C] hover:bg-[#040a7a] text-white font-semibold transition shadow-lg shadow-[#050C9C]/20 hover:shadow-xl">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Script Modal -->
<script>
    function openModal() {
        const modal = document.getElementById('uploadModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('opacity-100'), 10);
    }

    function closeModal() {
        const modal = document.getElementById('uploadModal');
        modal.classList.remove('opacity-100');
        setTimeout(() => modal.classList.add('hidden'), 200);
    }

    // Update file label saat file dipilih
    document.getElementById('fileInput').addEventListener('change', function(e) {
        const label = document.getElementById('fileLabel');
        if (e.target.files.length > 0) {
            label.textContent = e.target.files[0].name;
            label.classList.remove('text-gray-500');
            label.classList.add('text-gray-900', 'font-medium');
        } else {
            label.textContent = 'No File Choosen';
            label.classList.add('text-gray-500');
            label.classList.remove('text-gray-900', 'font-medium');
        }
    });
</script>

<style>
    /* Custom styling untuk select arrow */
    select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1.25rem;
        padding-right: 2.5rem;
    }
</style>