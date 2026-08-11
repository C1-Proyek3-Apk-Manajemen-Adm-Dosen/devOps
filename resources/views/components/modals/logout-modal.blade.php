<div id="logoutModal" class="hidden fixed inset-0 bg-gray-900/50 flex items-center justify-center z-50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-96">
        <div class="flex items-center gap-3 mb-4">
            <div class="text-red-500 text-2xl">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h2 class="text-lg font-semibold text-gray-800">Logout Confirmation</h2>
        </div>

        <p class="text-gray-600 text-sm mb-6">
            Apakah kamu yakin ingin logout dari akun ini?
            Kamu akan kembali ke halaman login setelah logout.
        </p>

        <div class="flex justify-end gap-3">
            <button id="cancelLogout"
                class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 font-medium hover:bg-gray-300 transition">
                Batal
            </button>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-red-500 text-white font-medium hover:bg-red-600 transition">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
