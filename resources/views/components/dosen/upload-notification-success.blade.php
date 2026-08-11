@if (session('success'))
    <div class="alert-success hidden">{{ session('success') }}</div>

    <div id="successNotificationModal"
         class="fixed inset-0 z-[9999] bg-black/40 backdrop-blur-sm flex items-center justify-center
                opacity-0 hidden transition-opacity duration-300">

        <div id="modalContent"
             class="bg-white w-full max-w-md rounded-3xl p-8 shadow-2xl
                    transform scale-95 opacity-0 transition-all duration-300">

            <div class="flex flex-col items-center text-center">

                {{-- ICON --}}
                <div class="w-20 h-20 rounded-full bg-emerald-100 flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                {{-- TITLE --}}
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Selamat!</h2>
                <p class="text-gray-500 text-sm mb-6">Dokumen berhasil diunggah</p>

                {{-- BUTTON --}}
                <button onclick="window.closeSuccessNotification()"
                        class="w-full py-3 rounded-lg bg-[#050C9C] hover:bg-[#040a7a]
                               text-white font-semibold transition">
                    OK
                </button>

            </div>
        </div>
    </div>
@endif
