@extends('layouts.app')

@section('title', 'Tambah Pengguna')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    {{-- Back Button --}}
    <div>
        <a href="{{ route('admin.users.index') }}"
            class="inline-flex items-center gap-2 text-[#050C9C] hover:text-[#040a7a] font-semibold transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali ke Daftar Pengguna
        </a>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-[#050C9C] to-blue-600 px-8 py-6">
            <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                <i class="fa-solid fa-user-plus"></i>
                Tambah Pengguna Baru
            </h2>
            <p class="text-blue-100 mt-1">Buat akun baru untuk TU, Dosen, atau Koordinator</p>
        </div>

        {{-- Error Messages --}}
        @if ($errors->any())
        <div class="mx-8 mt-6 bg-red-50 border border-red-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation text-red-600 text-xl mt-0.5"></i>
                <div class="flex-1">
                    <h4 class="font-semibold text-red-800 mb-2">Terdapat kesalahan pada form:</h4>
                    <ul class="list-disc pl-5 space-y-1 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.users.store') }}" class="p-8 space-y-6">
            @csrf

            {{-- Nama Lengkap --}}
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text"
                        name="nama_lengkap"
                        value="{{ old('nama_lengkap') }}"
                        class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#050C9C] focus:border-transparent outline-none transition @error('nama_lengkap') border-red-500 @enderror"
                        placeholder="Masukkan nama lengkap"
                        required>
                </div>
                @error('nama_lengkap')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#050C9C] focus:border-transparent outline-none transition @error('email') border-red-500 @enderror"
                        placeholder="nama@polban.ac.id"
                        required>
                </div>
                <p class="mt-1.5 text-xs text-gray-500 flex items-center gap-1.5">
                    <i class="fa-solid fa-info-circle"></i>
                    Email harus menggunakan domain @polban.ac.id
                </p>
                @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Role --}}
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">
                    Role <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <i class="fa-solid fa-user-tag absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                    <select name="role"
                        class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#050C9C] focus:border-transparent outline-none transition appearance-none bg-white @error('role') border-red-500 @enderror"
                        required>
                        <option value="">Pilih Role</option>
                        <option value="tu" @selected(old('role')==='tu' )>TU (Tata Usaha)</option>
                        <option value="dosen" @selected(old('role')==='dosen' )>Dosen</option>
                        <option value="koordinator" @selected(old('role')==='koordinator' )>Koordinator</option>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                </div>
                @error('role')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">
                    Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="password"
                        name="password"
                        id="password"
                        class="w-full pl-12 pr-12 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#050C9C] focus:border-transparent outline-none transition @error('password') border-red-500 @enderror"
                        placeholder="Minimal 8 karakter"
                        required>
                    <button type="button"
                        onclick="togglePassword('password', this)"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                <p class="mt-1.5 text-xs text-gray-500 flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-halved"></i>
                    Gunakan kombinasi huruf, angka, dan simbol untuk keamanan
                </p>
                @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">
                    Konfirmasi Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        class="w-full pl-12 pr-12 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#050C9C] focus:border-transparent outline-none transition @error('password_confirmation') border-red-500 @enderror"
                        placeholder="Ketik ulang password"
                        required>
                    <button type="button"
                        onclick="togglePassword('password_confirmation', this)"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                @error('password_confirmation')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <button type="submit"
                    class="flex-1 bg-gradient-to-r from-[#050C9C] to-blue-600 hover:from-[#040a7a] hover:to-blue-700 text-white font-semibold py-3.5 rounded-xl transition shadow-lg flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Pengguna
                </button>
                <a href="{{ route('admin.users.index') }}"
                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3.5 rounded-xl transition text-center flex items-center justify-center gap-2">
                    <i class="fa-solid fa-xmark"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>

    {{-- Info Box --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
        <div class="flex items-start gap-4">
            <div class="bg-blue-100 text-blue-600 p-3 rounded-xl">
                <i class="fa-solid fa-lightbulb text-xl"></i>
            </div>
            <div>
                <h4 class="font-semibold text-blue-900 mb-2">Tips Keamanan</h4>
                <ul class="text-sm text-blue-800 space-y-1.5">
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-blue-600 mt-0.5"></i>
                        <span>Password harus minimal 8 karakter</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-blue-600 mt-0.5"></i>
                        <span>Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-blue-600 mt-0.5"></i>
                        <span>Hindari menggunakan informasi pribadi yang mudah ditebak</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endsection