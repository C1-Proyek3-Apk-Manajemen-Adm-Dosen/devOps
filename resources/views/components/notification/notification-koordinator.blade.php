@php
use Illuminate\Support\Facades\Auth;
use App\Models\AccessControl;

// Ambil notifikasi untuk KAPRODI (koordinator)
$recentNotifikasi = AccessControl::with(['pemberiAkses', 'dokumen'])
    ->where('grantee_user_id', Auth::user()->id_user)
    ->orderByDesc('created_at')
    ->take(5)
    ->get();

// Route lihat semua
$routeLihatSemua = route('kaprodi.notifikasi');
@endphp

<div x-data="{ open: false }" class="relative z-50" x-cloak>
    <button @click="open = !open" class="relative text-[#050C9C] focus:outline-none">
        <i class="fa-solid fa-bell text-xl"></i>

        @if($recentNotifikasi->count() > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 rounded-full w-3 h-3"></span>
        @endif
    </button>

    <div x-show="open"
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-3 w-80 bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">

        <div class="p-3 border-b font-semibold text-gray-700">
            Notifikasi
        </div>

        <ul class="max-h-72 overflow-y-auto">
            @forelse ($recentNotifikasi as $notif)
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
                <li class="px-3 py-3 text-gray-500 text-sm text-center">
                    Belum ada notifikasi
                </li>
            @endforelse
        </ul>

        <a href="{{ $routeLihatSemua }}"
           class="block text-center text-[#050C9C] font-medium text-sm py-2 hover:bg-gray-50 transition">
            Lihat semua
        </a>
    </div>
</div>
