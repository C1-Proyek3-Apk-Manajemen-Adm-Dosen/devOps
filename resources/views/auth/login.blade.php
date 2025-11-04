<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SiDoRa</title>

    {{-- Import CSS dan JS --}}
    @vite(['resources/css/app.css', 'resources/js/togglePassword.js'])

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-200 via-white to-blue-100 px-4">
    <div class="flex flex-col md:flex-row w-full md:w-10/12 max-w-6xl items-center md:items-stretch gap-10 md:gap-0">
        <!-- Left Side -->
        <div class="w-full md:w-1/2 text-center md:text-left md:pr-16 flex flex-col justify-center">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-4 md:mb-6">
                Streamline Your <br class="hidden md:block"> Workflow
            </h1>
            <p class="text-gray-600 text-base md:text-lg max-w-md mx-auto md:mx-0">
                Manajemen dokumen menjadi lebih cepat, aman, dan transparan.
            </p>
        </div>

        <!-- Right Side (Card Login) -->
        <div class="w-full md:w-1/2 bg-white rounded-3xl shadow-lg p-10 md:p-14 flex flex-col justify-center">
            <h2 class="text-2xl md:text-3xl font-bold mb-6 text-gray-900 text-center md:text-left">Log in</h2>
            <form action="#" method="POST" class="space-y-5">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" placeholder="Masukkan email"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-gray-100 focus:bg-white focus:ring-2 focus:ring-purple-400 focus:outline-none transition">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="Masukkan password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-gray-100 focus:bg-white focus:ring-2 focus:ring-purple-400 focus:outline-none transition">
                        <button type="button" id="togglePassword"
                            class="absolute inset-y-0 right-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542
                                    7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="submit"
                    class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 rounded-xl transition duration-200">
                    Log In
                </button>
            </form>
        </div>
    </div>
</body>

</html>
