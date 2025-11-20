@extends('layouts.app')
@section('title', 'Edit Hak Akses - SiDoRa')

@section('content')
<div id="modalHakAkses" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 animate-fadeIn">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden animate-slideUp" onclick="event.stopPropagation()">
        
        <div class="bg-gradient-to-r from-[#050C9C] to-[#0818d4] px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-bold text-white">Edit Hak Akses Dokumen</h2>
            <a href="{{ route('dosen.dokumen') }}" class="text-white/80 hover:text-white transition-colors p-1 hover:bg-white/20 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </a>
        </div>

        <div class="overflow-y-auto max-h-[calc(90vh-180px)] px-6 py-6 space-y-6">
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Judul Dokumen
                </label>
                <div class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#050C9C] to-[#0818d4] flex items-center justify-center shadow-md flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $dokumen->judul ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $dokumen->nomor_dokumen ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    Tambah Hak Akses
                </label>
                
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 border-2 border-dashed border-gray-300 rounded-xl p-4">
                    <form id="addAccessForm" class="space-y-4">
                        
                        <div class="relative">
                            <label class="block text-xs font-medium text-gray-600 mb-2">
                                Pilih Pengguna
                            </label>
                            
                            <div id="hakAksesDropdownTrigger" class="input-field w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#050C9C] focus:ring-2 focus:ring-[#050C9C]/20 outline-none transition appearance-none bg-white cursor-pointer flex items-center justify-between">
                                <span id="hakAksesLabel" class="text-gray-500 text-sm truncate">Pilih pengguna...</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            
                            <div id="hakAksesMenu" class="hidden absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl max-h-64 overflow-y-auto">
                                <div class="p-3">
                                    <div class="mb-3 sticky top-0 bg-white z-10 pb-2 border-b border-gray-100">
                                        <input type="text" id="searchUser" placeholder="Cari nama atau email..." class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#050C9C] focus:ring-1 focus:ring-[#050C9C]">
                                    </div>
                                    
                                    <label class="flex items-center px-3 py-2.5 hover:bg-blue-50 rounded-lg cursor-pointer border-b border-gray-100 mb-2 bg-gray-50">
                                        <input type="checkbox" id="selectAllUsers" class="w-4 h-4 text-[#050C9C] border-gray-300 rounded focus:ring-[#050C9C] focus:ring-2 transition">
                                        <span class="ml-3 text-sm font-bold text-gray-700">Pilih Semua Pengguna</span>
                                    </label>
                                    
                                    <div id="userListContainer">
                                        @foreach($users ?? [] as $user)
                                            <label class="user-checkbox-item flex items-center px-3 py-2.5 hover:bg-gray-50 rounded-lg cursor-pointer transition group" data-search="{{ strtolower($user->nama_lengkap . ' ' . $user->email) }}">
                                                <input type="checkbox" name="user_ids[]" value="{{ $user->id_user }}" class="hak-akses-checkbox w-4 h-4 text-[#050C9C] border-gray-300 rounded focus:ring-[#050C9C] focus:ring-2">
                                                <div class="ml-3 flex-1">
                                                    <div class="text-sm font-medium text-gray-900 group-hover:text-[#050C9C] transition-colors">{{ $user->nama_lengkap ?? 'User' }}</div>
                                                    <div class="text-xs text-gray-500">{{ $user->email }} ({{ ucfirst($user->role) }})</div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    
                                    <div id="noUserFound" class="hidden py-4 text-center text-sm text-gray-500">
                                        Pengguna tidak ditemukan.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-2">
                                Jenis Akses
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="permission" value="READ" class="peer sr-only" checked>
                                    <div class="px-3 py-2.5 bg-white border-2 border-gray-300 rounded-lg text-center text-xs font-semibold text-gray-600 transition-all duration-200 peer-checked:border-[#050C9C] peer-checked:bg-[#050C9C] peer-checked:text-white peer-checked:shadow-md group-hover:border-[#050C9C]/50">
                                        READ
                                    </div>
                                </label>
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="permission" value="COMMENT" class="peer sr-only">
                                    <div class="px-3 py-2.5 bg-white border-2 border-gray-300 rounded-lg text-center text-xs font-semibold text-gray-600 transition-all duration-200 peer-checked:border-[#050C9C] peer-checked:bg-[#050C9C] peer-checked:text-white peer-checked:shadow-md group-hover:border-[#050C9C]/50">
                                        COMMENT
                                    </div>
                                </label>
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="permission" value="EDIT" class="peer sr-only">
                                    <div class="px-3 py-2.5 bg-white border-2 border-gray-300 rounded-lg text-center text-xs font-semibold text-gray-600 transition-all duration-200 peer-checked:border-[#050C9C] peer-checked:bg-[#050C9C] peer-checked:text-white peer-checked:shadow-md group-hover:border-[#050C9C]/50">
                                        EDIT
                                    </div>
                                </label>
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="permission" value="OWNER" class="peer sr-only">
                                    <div class="px-3 py-2.5 bg-white border-2 border-gray-300 rounded-lg text-center text-xs font-semibold text-gray-600 transition-all duration-200 peer-checked:border-[#050C9C] peer-checked:bg-[#050C9C] peer-checked:text-white peer-checked:shadow-md group-hover:border-[#050C9C]/50">
                                        OWNER
                                    </div>
                                </label>
                            </div>
                        </div>

                        <button type="button" 
                                id="addAccessBtn"
                                class="w-full py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-xl hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span id="addBtnText">Tambah Akses</span>
                        </button>
                    </form>
                </div>
            </div>

            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-3 bg-white text-gray-500 font-medium">Daftar Akses</span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    Orang yang memiliki akses
                </label>
                
                <div id="accessList" class="space-y-2 max-h-64 overflow-y-auto pr-2">
                    @forelse($dokumen->accessControls ?? [] as $access)
                    <div class="flex items-center justify-between bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl px-4 py-3 transition-all duration-200 group" data-user-id="{{ $access->granteeUser->id_user ?? 0 }}">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-md">
                                {{ strtoupper(substr($access->granteeUser->nama_lengkap ?? 'U', 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-sm text-gray-800">{{ $access->granteeUser->nama_lengkap ?? 'User' }}</p>
                                <p class="text-xs text-gray-500">{{ $access->granteeUser->email }} ({{ ucfirst($access->granteeUser->role ?? 'User') }})</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @php
                                $permClass = match($access->perm) {
                                    'READ' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    'COMMENT' => 'bg-green-100 text-green-700 border-green-200',
                                    'EDIT' => 'bg-orange-100 text-orange-700 border-orange-200',
                                    'OWNER' => 'bg-purple-100 text-purple-700 border-purple-200',
                                    default => 'bg-gray-100 text-gray-700 border-gray-200'
                                };
                            @endphp
                            <span class="px-3 py-1 {{ $permClass }} text-xs font-semibold rounded-full border">
                                {{ $access->perm }}
                            </span>
                            <button type="button" onclick="removeAccess({{ $access->granteeUser->id_user ?? 0 }}, '{{ $access->granteeUser->nama_lengkap ?? '' }}')" 
                                    class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-all duration-200 opacity-0 group-hover:opacity-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div id="emptyState" class="py-8 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <p class="text-gray-500 font-medium text-sm">Belum ada yang memiliki akses</p>
                        <p class="text-gray-400 text-xs">Tambahkan pengguna untuk memberikan akses</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row gap-3 justify-end">
            <a href="{{ route('dosen.dokumen') }}"
               class="px-6 py-2.5 bg-white border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 text-center">
                Tutup
            </a>
            <a href="{{ route('dosen.dokumen') }}"
               class="px-6 py-2.5 bg-gradient-to-r from-[#050C9C] to-[#0818d4] text-white font-semibold rounded-xl hover:from-[#0818d4] hover:to-[#050C9C] transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Simpan Perubahan
            </a>
        </div>
    </div>
</div>

<script>
    window.dokumenId = {{ $dokumen->dokumen_id ?? 0 }};
    window.csrfToken = '{{ csrf_token() }}';

    window.returnUrl = '{{ route('dosen.dokumen') }}';

    window.removeAccessRoute = '{{ route("dosen.hak-akses.remove", $dokumen->dokumen_id) }}';
    window.updateAccessRoute = '{{ route("dosen.update-hak-akses", $dokumen->dokumen_id) }}';
</script>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dosen/edit-hak-akses.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/dosen/edit-hak-akses.js') }}"></script>
@endpush
