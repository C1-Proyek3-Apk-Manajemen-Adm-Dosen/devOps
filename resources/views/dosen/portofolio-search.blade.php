@extends('layouts.app')

@section('title', 'Cari Profil PDDikti')

@section('content')
<div class="container mx-auto p-6 max-w-4xl">
    <!-- Header -->
    <div class="bg-[#050C9C] rounded-lg shadow-lg p-6 mb-6 text-white">
        <h1 class="text-3xl font-bold mb-2">Cari Profil PDDikti</h1>
        <p class="text-white">Hubungkan akun Anda dengan profil di database PDDikti Kemdikbud</p>
    </div>

    <!-- Form Pencarian -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <form id="searchForm" class="space-y-4">
            @csrf
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">
                    Masukkan Nama Lengkap Anda
                </label>
                <input 
                    type="text" 
                    id="nama" 
                    name="nama" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    placeholder="Contoh: Ade Chandra Nugraha"
                    required
                    minlength="3"
                >
                <p class="mt-2 text-sm text-gray-500">
                    Tips: Masukkan nama lengkap sesuai dengan yang terdaftar di PDDikti
                </p>
            </div>

            <button 
                type="submit" 
                id="btnSearch"
                class="w-full bg-[#050C9C] text-white py-3 px-6 rounded-lg hover:bg-[#0815d9] transition-colors font-semibold flex items-center justify-center">
                <span id="btnText">Cari di PDDikti</span>
                <span id="btnLoader" class="hidden">
                    <svg class="animate-spin h-5 w-5 mr-2 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Mencari...
                </span>
            </button>
        </form>
    </div>

    <!-- Hasil Pencarian -->
    <div id="resultSection" class="hidden">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <span class="text-2xl mr-2">✅</span> Hasil Pencarian
            </h2>
            <div id="resultContainer" class="space-y-3"></div>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="hidden bg-white rounded-lg shadow-lg p-12 text-center">
        <svg class="animate-spin h-12 w-12 mx-auto text-purple-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600">Sedang mencari di database PDDikti...</p>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="hidden bg-white rounded-lg shadow-lg p-12 text-center">
        <div class="text-6xl mb-4">🔍</div>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ditemukan</h3>
        <p class="text-gray-500 mb-4">Profil tidak ditemukan di PDDikti. Coba gunakan nama lengkap yang berbeda.</p>
        <button onclick="location.reload()" class="text-purple-600 hover:underline">
            ↻ Coba Lagi
        </button>
    </div>

    <!-- Error State -->
    <div id="errorState" class="hidden bg-red-50 border-l-4 border-red-500 p-4 rounded">
        <div class="flex items-start">
            <span class="text-2xl mr-3">⚠️</span>
            <div>
                <h3 class="font-semibold text-red-800">Terjadi Kesalahan</h3>
                <p id="errorMessage" class="text-red-600 text-sm mt-1"></p>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('searchForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const nama = document.getElementById('nama').value;
    const btnSearch = document.getElementById('btnSearch');
    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');
    
    // Reset states
    document.getElementById('resultSection').classList.add('hidden');
    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('errorState').classList.add('hidden');
    document.getElementById('loadingState').classList.remove('hidden');
    
    // Disable button
    btnSearch.disabled = true;
    btnText.classList.add('hidden');
    btnLoader.classList.remove('hidden');
    
    try {
        const response = await fetch('{{ route("dosen.portofolio.search-pddikti") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ nama })
        });
        
        const result = await response.json();
        
        document.getElementById('loadingState').classList.add('hidden');
        
        if (result.success && result.data.length > 0) {
            showResults(result.data);
        } else {
            document.getElementById('emptyState').classList.remove('hidden');
        }
        
    } catch (error) {
        document.getElementById('loadingState').classList.add('hidden');
        document.getElementById('errorState').classList.remove('hidden');
        document.getElementById('errorMessage').textContent = error.message;
    } finally {
        // Enable button
        btnSearch.disabled = false;
        btnText.classList.remove('hidden');
        btnLoader.classList.add('hidden');
    }
});

function showResults(data) {
    const container = document.getElementById('resultContainer');
    container.innerHTML = '';
    
    data.forEach((dosen, index) => {
        const card = document.createElement('div');
        card.className = 'border border-gray-200 rounded-lg p-4 hover:border-purple-500 hover:shadow-md transition-all cursor-pointer';
        card.innerHTML = `
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h3 class="font-semibold text-lg text-gray-800">${dosen.nama}</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        ${dosen.program_studi || 'Program Studi tidak tersedia'}
                    </p>
                    <p class="text-sm text-gray-500">
                        ${dosen.perguruan_tinggi || 'Perguruan Tinggi tidak tersedia'}
                    </p>
                </div>
                <button 
                    onclick="importProfile('${dosen.detail_url}', '${dosen.nama}')"
                    class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors text-sm font-medium">
                    Pilih
                </button>
            </div>
        `;
        container.appendChild(card);
    });
    
    document.getElementById('resultSection').classList.remove('hidden');
}

async function importProfile(detailUrl, nama) {
    if (!confirm(`Yakin ingin menghubungkan akun Anda dengan profil:\n\n${nama}?`)) {
        return;
    }
    
    const loadingOverlay = document.createElement('div');
    loadingOverlay.id = 'importLoading';
    loadingOverlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    loadingOverlay.innerHTML = `
        <div class="bg-white rounded-lg p-8 max-w-sm mx-4">
            <svg class="animate-spin h-12 w-12 mx-auto text-purple-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-center text-gray-700 font-semibold">Mengimpor data dari PDDikti...</p>
            <p class="text-center text-gray-500 text-sm mt-2">Mohon tunggu sebentar</p>
        </div>
    `;
    document.body.appendChild(loadingOverlay);
    
    try {
        const response = await fetch('{{ route("dosen.portofolio.import") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                detail_url: detailUrl,
                nama: nama
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('✅ ' + result.message);
            window.location.href = '{{ route("dosen.portofolio") }}';
        } else {
            alert('❌ ' + result.message);
        }
        
    } catch (error) {
        alert('❌ Terjadi kesalahan: ' + error.message);
    } finally {
        document.getElementById('importLoading')?.remove();
    }
}
</script>
@endsection