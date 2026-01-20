@extends('layouts.admin')

@section('title', 'Thống kê & Báo cáo')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-800">
            📊 Thống kê & Báo cáo
        </h1>
    </div>

    {{-- GRID BIỂU ĐỒ --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- BIỂU ĐỒ LƯỢT ĐỌC THEO NGÀY --}}
        <div class="bg-white rounded-lg shadow p-5">
            <h2 class="font-semibold text-slate-700 mb-4">
                📈 Lượt đọc 7 ngày gần nhất
            </h2>
            <div class="relative h-64">
                <canvas id="viewsByDayChart"></canvas>
            </div>
        </div>

        {{-- BIỂU ĐỒ NGƯỜI DÙNG MỚI --}}
        <div class="bg-white rounded-lg shadow p-5">
            <h2 class="font-semibold text-slate-700 mb-4">
                👤 Người dùng mới
            </h2>
            <div class="relative h-64">
                <canvas id="usersByDayChart"></canvas>
            </div>
        </div>

        {{-- BIỂU ĐỒ TOP TRUYỆN --}}
        <div class="bg-white rounded-lg shadow p-5 xl:col-span-2">
            <h2 class="font-semibold text-slate-700 mb-4">
                📚 Top truyện nhiều lượt đọc
            </h2>
            <div class="relative h-80">
                <canvas id="topComicsChart"></canvas>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    /* =========================
           LƯỢT ĐỌC THEO NGÀY
       ==========================*/
    const viewsCtx = document.getElementById('viewsByDayChart').getContext('2d');

    // Tạo gradient màu cho đẹp
    const gradientViews = viewsCtx.createLinearGradient(0, 0, 0, 400);
    gradientViews.addColorStop(0, 'rgba(59, 130, 246, 0.5)'); // Blue
    gradientViews.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

    new Chart(viewsCtx, {
        type: 'line',
        data: {
            // SỬA LỖI: Xóa khoảng trắng giữa - và >
            labels: @json($viewsByDay->pluck('date')),
            datasets: [{
                label: 'Lượt đọc',
                data: @json($viewsByDay->pluck('total')),
                borderColor: '#3b82f6', // Màu xanh dương
                backgroundColor: gradientViews,
                borderWidth: 2,
                tension: 0.4, // Làm mềm đường cong
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    /* =========================
           NGƯỜI DÙNG MỚI
       ==========================*/
    const usersCtx = document.getElementById('usersByDayChart').getContext('2d');

    const gradientUsers = usersCtx.createLinearGradient(0, 0, 0, 400);
    gradientUsers.addColorStop(0, 'rgba(16, 185, 129, 0.5)'); // Green
    gradientUsers.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

    new Chart(usersCtx, {
        type: 'line',
        data: {
            labels: @json($usersByDay->pluck('date')),
            datasets: [{
                label: 'Người dùng mới',
                data: @json($usersByDay->pluck('total')),
                borderColor: '#10b981',
                backgroundColor: gradientUsers,
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    /* =========================
           TOP TRUYỆN
       ==========================*/
    new Chart(document.getElementById('topComicsChart'), {
        type: 'bar',
        data: {
            // SỬA LỖI: Xóa khoảng trắng
            labels: @json($topComics->pluck('title')),
            datasets: [{
                label: 'Lượt đọc',
                data: @json($topComics->pluck('reading_histories_count')),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush