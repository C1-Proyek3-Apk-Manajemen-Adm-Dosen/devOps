<!-- resources/views/components/tu/upload-notification-success-tu.blade.php -->

<div id="successNotificationModal" class="hidden fixed inset-0 backdrop-blur-sm bg-white/30 z-50 flex items-center justify-center transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
        
        <!-- Checkmark Animation -->
        <div class="flex justify-center mb-6">
            <div class="relative">
                <svg class="w-24 h-24" viewBox="0 0 52 52">
                    <circle class="stroke-green-200" cx="26" cy="26" r="25" fill="none" stroke-width="2"/>
                    <circle class="stroke-green-500 animate-[dash_0.6s_ease-in-out_forwards]" cx="26" cy="26" r="25" fill="none" stroke-width="2" stroke-dasharray="157" stroke-dashoffset="157" style="animation: dash 0.6s ease-in-out forwards;"/>
                    <path class="stroke-green-500 animate-[checkmark_0.3s_0.6s_ease-in-out_forwards]" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M14.1 27.2l7.1 7.2 16.7-16.8" stroke-dasharray="48" stroke-dashoffset="48" style="animation: checkmark 0.3s 0.6s ease-in-out forwards;"/>
                </svg>
            </div>
        </div>

        <!-- Success Text -->
        <h2 class="text-2xl font-bold text-gray-900 text-center mb-2">Selamat!</h2>
        <p class="text-gray-600 text-center mb-6">Dokumen berhasil diunggah</p>

        <!-- OK Button -->
        <button type="button" onclick="closeSuccessNotification()" class="w-full bg-[#050C9C] hover:bg-[#040a7a] text-white font-semibold py-3 px-6 rounded-xl transition duration-200 shadow-lg hover:shadow-xl">
            OK
        </button>
    </div>
</div>

<style>
@keyframes dash {
    to {
        stroke-dashoffset: 0;
    }
}

@keyframes checkmark {
    to {
        stroke-dashoffset: 0;
    }
}
</style>

<script src="{{ asset('js/tu/upload-notification-success-tu.js') }}"></script>