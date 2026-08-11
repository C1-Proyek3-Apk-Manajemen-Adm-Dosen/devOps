@extends('layouts.app')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/override.css') }}">
@endpush
@section('title', 'Manajemen Pengguna')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#050C9C]">Manajemen Pengguna</h1>
            <p class="text-gray-600 mt-1">Kelola akun TU, Dosen, dan Koordinator</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
            class="inline-flex items-center justify-center gap-2 bg-[#050C9C] hover:bg-[#040a7a] text-white px-6 py-3 rounded-xl font-semibold transition shadow-lg">
            <i class="fa-solid fa-user-plus"></i>
            Tambah Pengguna
        </a>
    </div>

    {{-- Success Alert --}}
    @if (session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-green-600 text-xl"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="text-green-600 hover:text-green-800 transition">
            <i class="fa-solid fa-times text-lg"></i>
        </button>
    </div>
    @endif

    {{-- Search & Filter --}}
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row gap-4">
            {{-- Search Input --}}
            <div class="flex-1">
                <div class="relative">
                    <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama atau email..."
                        class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#050C9C] focus:border-transparent outline-none transition">
                </div>
            </div>

            {{-- Role Filter --}}
            <div class="w-full md:w-48">
                <select name="role"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#050C9C] focus:border-transparent outline-none transition">
                    <option value="all">Semua Role</option>
                    <option value="tu" @selected(request('role')==='tu' )>TU</option>
                    <option value="dosen" @selected(request('role')==='dosen' )>Dosen</option>
                    <option value="koordinator" @selected(request('role')==='koordinator' )>Koordinator</option>
                </select>
            </div>

            {{-- Search Button --}}
            <button type="submit"
                class="bg-[#050C9C] hover:bg-[#040a7a] text-white px-6 py-3 rounded-xl font-semibold transition shadow-lg flex items-center justify-center gap-2">
                <i class="fa-solid fa-search"></i>
                <span class="hidden md:inline">Cari</span>
            </button>

            {{-- Reset Button --}}
            @if(request('search') || request('role'))
            <a href="{{ route('admin.users.index') }}"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-redo"></i>
                <span class="hidden md:inline">Reset</span>
            </a>
            @endif
        </form>
    </div>

    {{-- Users Table --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Pengguna
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Role
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        {{-- User Info --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#050C9C] to-blue-600 flex items-center justify-center text-white font-bold text-sm shadow">
                                    {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $user->nama_lengkap }}</p>
                                    <p class="text-xs text-gray-500">ID: {{ $user->id_user }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Email --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 text-sm text-gray-700">
                                <i class="fa-solid fa-envelope text-gray-400"></i>
                                {{ $user->email }}
                            </div>
                        </td>

                        {{-- Role Badge --}}
                        <td class="px-6 py-4">
                            @php
                            $roleConfig = [
                            'tu' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'TU'],
                            'dosen' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Dosen'],
                            'koordinator' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'label' => 'Koordinator'],
                            ];
                            $config = $roleConfig[$user->role] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => ucfirst($user->role)];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $config['bg'] }} {{ $config['text'] }}">
                                {{ $config['label'] }}
                            </span>
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4">
                            @if ($user->status)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                <i class="fa-solid fa-circle-check"></i>
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                <i class="fa-solid fa-circle-xmark"></i>
                                Nonaktif
                            </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Edit Button --}}
                                <a href="{{ route('admin.users.edit', $user->id_user) }}"
                                    class="inline-flex items-center justify-center w-9 h-9 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-lg transition"
                                    title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                {{-- Delete Button --}}
                                <form action="{{ route('admin.users.destroy', $user->id_user) }}"
                                    method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $user->nama_lengkap }}?')"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center justify-center w-9 h-9 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition"
                                        title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <i class="fa-solid fa-users-slash text-5xl mb-4 text-gray-300"></i>
                                <p class="text-lg font-semibold">Tidak ada data pengguna</p>
                                <p class="text-sm mt-1">Coba ubah filter atau tambahkan pengguna baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    {{-- Summary --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center gap-3">
        <i class="fa-solid fa-info-circle text-blue-600 text-xl"></i>
        <p class="text-sm text-blue-800">
            Menampilkan <span class="font-semibold">{{ $users->count() }}</span> dari <span class="font-semibold">{{ $users->total() }}</span> pengguna
        </p>
    </div>
</div>
@endsection