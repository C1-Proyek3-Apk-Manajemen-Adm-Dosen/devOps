@if (session('success'))
    {{-- Elemen trigger untuk JS --}}
    <div class="alert-success hidden">{{ session('success') }}</div>

    {{-- Modal Notifikasi Sukses --}}
    <div id="successNotificationModal"
         class="fixed inset-0 z-[9999] bg-black/40 flex items-center justify-center
                opacity-0 hidden transition-opacity duration-300">

        <div id="modalContent"
             class="bg-white w-full max-w-md rounded-2xl p-6 md:p-7 shadow-xl
                    transform scale-95 opacity-0 transition-all duration-300">

            {{-- Header --}}
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <div>
                    <h2 class="text-lg font-bold text-gray-900">Dokumen Berhasil Diunggah!</h2>
                    <p class="text-sm text-gray-500">Dokumen berhasil diunggah.</p>
                </div>
            </div>

            <hr class="my-4 border-gray-200">

            {{-- Tombol --}}
            <div class="flex justify-end gap-3">
                <button onclick="window.closeSuccessNotification()"
                        class="px-5 py-2.5 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium
                               hover:bg-gray-200 transition">
                    Tutup
                </button>

                <a href="{{ route('dosen.dokumen') }}"
                   class="px-5 py-2.5 rounded-lg bg-[#050C9C] text-white text-sm font-medium
                          hover:bg-[#040a7a] transition">
                    Lihat Dokumen
                </a>
            </div>

        </div>
    </div>
@endif
