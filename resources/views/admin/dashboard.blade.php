@extends('layouts.app')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/override.css') }}">
@endpush
@section('title', 'Dashboard Administrator - SiDoRa')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-[#050C9C]">Dashboard Administrator</h1>
        <p class="text-gray-500">Selamat datang, {{ Auth::user()->nama_lengkap }} 👋</p>
    </div>

    {{-- Statistik Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Total Pengguna --}}
        <div class="bg-white/40 backdrop-blur-lg p-6 rounded-2xl shadow border border-white/30">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-[#050C9C] font-semibold">Total Pengguna</h3>
                    <p class="text-4xl font-extrabold text-[#050C9C] mt-2">{{ $totalUsers }}</p>
                </div>
                <div class="bg-blue-100 text-[#050C9C] p-3 rounded-xl">
                    <i class="fa-solid fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Total TU --}}
        <div class="bg-white/40 backdrop-blur-lg p-6 rounded-2xl shadow border border-white/30">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-[#050C9C] font-semibold">Total TU</h3>
                    <p class="text-4xl font-extrabold text-[#050C9C] mt-2">{{ $totalTU }}</p>
                </div>
                <div class="bg-blue-100 text-[#050C9C] p-3 rounded-xl">
                    <i class="fa-solid fa-user-tie text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Total Dosen --}}
        <div class="bg-white/40 backdrop-blur-lg p-6 rounded-2xl shadow border border-white/30">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-[#050C9C] font-semibold">Total Dosen</h3>
                    <p class="text-4xl font-extrabold text-[#050C9C] mt-2">{{ $totalDosen }}</p>
                </div>
                <div class="bg-blue-100 text-[#050C9C] p-3 rounded-xl">
                    <i class="fa-solid fa-chalkboard-user text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Total Koordinator --}}
        <div class="bg-white/40 backdrop-blur-lg p-6 rounded-2xl shadow border border-white/30">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-[#050C9C] font-semibold">Total Koordinator</h3>
                    <p class="text-4xl font-extrabold text-[#050C9C] mt-2">{{ $totalKoordinator }}</p>
                </div>
                <div class="bg-blue-100 text-[#050C9C] p-3 rounded-xl">
                    <i class="fa-solid fa-user-gear text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart Section --}}
    <div class="bg-white/40 backdrop-blur-lg p-6 rounded-2xl shadow border border-white/30">
        <h3 class="text-lg font-semibold text-[#050C9C] mb-4">Aktivitas Pembuatan Akun (30 Hari Terakhir)</h3>
        <canvas id="userChart" height="100"></canvas>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-gradient-to-br from-[#050C9C] to-blue-500 text-white p-6 rounded-2xl shadow">
        <h3 class="text-lg font-semibold mb-2">Quick Action</h3>
        <p class="text-sm text-blue-100 mb-4">Buat akun pengguna baru dengan cepat</p>
        <a href="{{ route('admin.users.create') }}"
            class="inline-block bg-white text-[#050C9C] font-semibold py-2 px-6 rounded-xl hover:bg-blue-100 transition">
            <i class="fa-solid fa-user-plus mr-2"></i> Buat Akun Baru
        </a>
    </div>
</div>

{{-- Chart.js Script --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartLabels = @json($chartLabels);
    const chartData = @json($chartData);

    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('userChart');

        if (ctx) {
            // Data dari controller

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Akun Dibuat',
                        data: chartData,
                        borderColor: '#050C9C',
                        backgroundColor: 'rgba(5, 12, 156, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#050C9C',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: {
                                    size: 12
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 11
                                },
                                maxRotation: 45,
                                minRotation: 45
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection