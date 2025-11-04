<nav class="w-full bg-white flex items-center justify-between flex-wrap px-4 md:px-6 py-3">
    <!-- Left Section: Logo + Tombol Sidebar -->
    <div class="flex items-center gap-2 flex-shrink-0">
        <!-- Tombol Toggle Sidebar (muncul hanya di mobile) -->
        <button id="toggleSidebar" class="md:hidden mr-2 text-purple-600">
            <i class="fas fa-bars text-2xl"></i>
        </button>


        <!-- Logo -->
        <div class="flex flex-col">
            <h1 class="text-2xl md:text-4xl font-bold text-purple-600 leading-none">SiDoRa</h1>
            <p class="hidden md:block text-xs text-gray-500 leading-none">Sistem Dokumeen & Arsip Dosen</p>
        </div>
    </div>

    <!-- Middle Section: Search + Notification -->
    <div class="flex items-center gap-3 flex-1 justify-center order-last md:order-none w-full md:w-auto mt-3 md:mt-0">
        <input type="text" placeholder="Search"
            class="w-full md:w-80 border border-gray-300 rounded-full px-5 py-2 text-sm focus:ring-2 focus:ring-purple-400 focus:outline-none transition" />

        <!-- Notifikasi bulat -->
        <button
            class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                class="w-5 h-5 text-purple-600">
                <path
                    d="M22.086 14.672A3.685 3.685 0 0 1 21 12.05V9A9 9 0 0 0 3 9v3.05a3.685 3.685 0 0 1-1.086 2.622A3.121 3.121 0 0 0 4.121 20H7.1a5 5 0 0 0 9.8 0h2.98a3.121 3.121 0 0 0 2.207-5.328ZM12 22a3 3 0 0 1-2.816-2h5.632A3 3 0 0 1 12 22Zm7.879-4H4.121a1.121 1.121 0 0 1-.793-1.914A5.672 5.672 0 0 0 5 12.05V9a7 7 0 0 1 14 0v3.05a5.672 5.672 0 0 0 1.672 4.036A1.121 1.121 0 0 1 19.879 18Z">
                </path>
            </svg>
        </button>
    </div>

    <!-- Right Section: Profile -->
    <div class="flex items-center gap-3 flex-shrink-0">
        <!-- Bulatan Profil -->
        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                fill="currentColor" class="text-gray-700">
                <g fill="#000" transform="translate(-140 -2159)">
                    <path
                        d="M100.563 2017H87.438c-.706 0-1.228-.697-.961-1.338 1.236-2.964 4.14-4.662 7.523-4.662 3.384 0 6.288 1.698 7.524 4.662.267.641-.255 1.338-.961 1.338m-10.646-12c0-2.206 1.832-4 4.083-4 2.252 0 4.083 1.794 4.083 4s-1.831 4-4.083 4c-2.251 0-4.083-1.794-4.083-4m14.039 11.636c-.742-3.359-3.064-5.838-6.119-6.963 1.619-1.277 2.563-3.342 2.216-5.603-.402-2.623-2.63-4.722-5.318-5.028-3.712-.423-6.86 2.407-6.86 5.958 0 1.89.894 3.574 2.289 4.673-3.057 1.125-5.377 3.604-6.12 6.963-.27 1.221.735 2.364 2.01 2.364h15.892c1.276 0 2.28-1.143 2.01-2.364">
                    </path>
                </g>
            </svg>
        </div>

        <!-- Sapaan -->
        <div class="hidden sm:flex flex-col leading-tight">
            <p class="text-xs text-gray-400">Hi,</p>
            <p class="text-sm font-semibold text-purple-700">
                {{-- sementara statis, nanti bisa ganti ke: {{ Auth::user()->role }} {{ Auth::user()->name }} --}}
                TU <span class="text-gray-700">Lia Rahmawati</span>
            </p>
        </div>
    </div>
</nav>
