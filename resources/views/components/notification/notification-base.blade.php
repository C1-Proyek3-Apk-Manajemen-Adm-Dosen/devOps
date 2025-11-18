<div x-data="{ open: false }" class="relative" x-cloak>
    
    <!-- Icon Bell -->
    <button @click="open = !open" class="relative text-[#050C9C] focus:outline-none">
        <i class="fa-solid fa-bell text-xl"></i>

        @if ($data->count() > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 rounded-full w-3 h-3"></span>
        @endif
    </button>

    <!-- DROPDOWN -->
    <div x-show="open"
         @click.away="open = false"
         x-transition
         class="absolute right-0 mt-3 w-80 bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 z-50">

        <div class="p-3 border-b font-semibold text-gray-700">Notifikasi</div>

        <ul class="max-h-72 overflow-y-auto">
            @forelse ($data as $notif)
                <li class="px-3 py-2 hover:bg-gray-50 text-sm border-b">
                    <div class="flex items-start gap-2">
                        <div class="text-[#050C9C] mt-1">
                            <i class="fa-solid fa-file-circle-check"></i>
                        </div>
                        <div>
                            <p class="text-gray-700">
                                <strong>{{ $notif->pemberiAkses->nama_lengkap ?? 'Pengguna' }}</strong>
                                memberi akses dokumen
                                <span class="font-semibold text-[#050C9C]">
                                    “{{ $notif->dokumen->judul ?? 'Dokumen' }}”
                                </span>
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-3 py-3 text-gray-500 text-sm text-center">Belum ada notifikasi</li>
            @endforelse
        </ul>

        <!-- Tombol lihat semua -->
        <a href="{{ $route }}"
            class="block text-center text-[#050C9C] font-medium text-sm py-2 hover:bg-gray-50 transition">
            Lihat semua
        </a>

    </div>
</div>
