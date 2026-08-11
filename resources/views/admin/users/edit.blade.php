@extends('layouts.app')

@section('title', 'Edit Pengguna')

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
                <i class="fa-solid fa-user-pen"></i>
                Edit Pengguna
            </h2>
            <p class="text-blue-100 mt-1">Perbarui informasi akun {{ $user->nama_lengkap }}</p>
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
        <form method="POST" action="{{ route('admin.users.update', $user->id_user) }}" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            {{-- User ID Info --}}
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#050C9C] to-blue-600 flex items-center justify-center text-white font-bold text-lg shadow">
                        {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">ID Pengguna</p>
                        <p class="font-semibold text-gray-900">{{ $user->id_user }}</p>
                    </div>
                </div>
            </div>

            {{-- Nama Lengkap --}}
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text"
                        name="nama_lengkap"
                        value="{{ old('nama_lengkap', $user->nama_lengkap) }}"
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
                        value="{{ old('email', $user->email) }}"
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
                        <option value="tu" @selected(old('role', $user->role) === 'tu')>TU (Tata Usaha)</option>
                        <option value="dosen" @selected(old('role', $user->role) === 'dosen')>Dosen</option>
                        <option value="koordinator" @selected(old('role', $user->role) === 'koordinator')>Koordinator</option>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                </div>
                @error('role')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">
                    Status Akun <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-3 flex-1 p-4 border-2 rounded-xl cursor-pointer transition hover:bg-gray-50 @if(old('status', $user->status)) border-green-500 bg-green-50 @else border-gray-300 @endif">
                        <input type="radio"
                            name="status"
                            value="1"
                            @checked(old('status', $user->status) == 1)
                        class="w-5 h-5 text-green-600 focus:ring-2 focus:ring-green-500"
                        required>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-green-600"></i>
                                Aktif
                            </p>
                            <p class="text-xs text-gray-600 mt-0.5">Pengguna dapat login</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 flex-1 p-4 border-2 rounded-xl cursor-pointer transition hover:bg-gray-50 @if(!old('status', $user->status)) border-red-500 bg-red-50 @else border-gray-300 @endif">
                        <input type="radio"
                            name="status"
                            value="0"
                            @checked(old('status', $user->status) == 0)
                        class="w-5 h-5 text-red-600 focus:ring-2 focus:ring-red-500"
                        required>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fa-solid fa-circle-xmark text-red-600"></i>
                                Nonaktif
                            </p>
                            <p class="text-xs text-gray-600 mt-0.5">Pengguna tidak dapat login</p>
                        </div>
                    </label>
                </div>
                @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password Section --}}
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-key text-[#050C9C]"></i>
                    Ubah Password (Opsional)
                </h3>
                <p class="text-sm text-gray-600 mb-4">Kosongkan jika tidak ingin mengubah password</p>

                {{-- New Password --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">
                            Password Baru
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="password"
                                name="password"
                                id="password"
                                class="w-full pl-12 pr-12 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#050C9C] focus:border-transparent outline-none transition @error('password') border-red-500 @enderror"
                                placeholder="Minimal 8 karakter">
                            <button type="button"
                                onclick="togglePassword('password', this)"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">
                            Konfirmasi Password Baru
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                class="w-full pl-12 pr-12 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#050C9C] focus:border-transparent outline-none transition"
                                placeholder="Ketik ulang password baru">
                            <button type="button"
                                onclick="togglePassword('password_confirmation', this)"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <button type="submit"
                    class="flex-1 bg-gradient-to-r from-[#050C9C] to-blue-600 hover:from-[#040a7a] hover:to-blue-700 text-white font-semibold py-3.5 rounded-xl transition shadow-lg flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.users.index') }}"
                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3.5 rounded-xl transition text-center flex items-center justify-center gap-2">
                    <i class="fa-solid fa-xmark"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>

    {{-- Warning Box --}}
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
        <div class="flex items-start gap-4">
            <div class="bg-yellow-100 text-yellow-600 p-3 rounded-xl">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </div>
            <div>
                <h4 class="font-semibold text-yellow-900 mb-2">Peringatan</h4>
                <ul class="text-sm text-yellow-800 space-y-1.5">
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-exclamation text-yellow-600 mt-0.5"></i>
                        <span>Perubahan role akan mengubah hak akses pengguna di sistem</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-exclamation text-yellow-600 mt-0.5"></i>
                        <span>Menonaktifkan akun akan mencegah pengguna login ke sistem</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-exclamation text-yellow-600 mt-0.5"></i>
                        <span>Pastikan perubahan data sudah sesuai sebelum menyimpan</span>
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