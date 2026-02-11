@extends('layouts.admin')

@section('title', 'Thống kê & Báo cáo')
@section ('header', 'Thống kê & Báo cáo')

@section('content')
<div class="space-y-6">

    {{-- KPI TỔNG QUAN --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-slate-800/70 border border-slate-700/60 rounded-xl p-4 shadow-lg">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-400">
                    <i class="fas fa-book"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-xs font-medium uppercase">Truyện</p>
                    <p class="text-xl font-bold text-white">{{ number_format($totalComics) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-slate-800/70 border border-slate-700/60 rounded-xl p-4 shadow-lg">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <i class="fas fa-list-alt"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-xs font-medium uppercase">Chương</p>
                    <p class="text-xl font-bold text-white">{{ number_format($totalChapters) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-slate-800/70 border border-slate-700/60 rounded-xl p-4 shadow-lg">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-500/20 flex items-center justify-center text-amber-400">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-xs font-medium uppercase">Người dùng</p>
                    <p class="text-xl font-bold text-white">{{ number_format($totalUsers) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-slate-800/70 border border-slate-700/60 rounded-xl p-4 shadow-lg">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-rose-500/20 flex items-center justify-center text-rose-400">
                    <i class="fas fa-eye"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-xs font-medium uppercase">Lượt xem</p>
                    <p class="text-xl font-bold text-white">{{ number_format($totalViews) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-slate-800/70 border border-slate-700/60 rounded-xl p-4 shadow-lg">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-violet-500/20 flex items-center justify-center text-violet-400">
                    <i class="fas fa-heart"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-xs font-medium uppercase">Theo dõi</p>
                    <p class="text-xl font-bold text-white">{{ number_format($totalFollows) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-slate-800/70 border border-slate-700/60 rounded-xl p-4 shadow-lg">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-cyan-500/20 flex items-center justify-center text-cyan-400">
                    <i class="fas fa-comments"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-xs font-medium uppercase">Bình luận</p>
                    <p class="text-xl font-bold text-white">{{ number_format($totalComments) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- BIỂU ĐỒ THEO THỜI GIAN (7 ngày) --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="bg-slate-800/70 border border-slate-700/60 rounded-xl p-5 shadow-lg">
            <h2 class="font-semibold text-slate-200 mb-4">Lượt đọc 7 ngày</h2>
            <div class="relative h-56">
                <canvas id="viewsByDayChart"></canvas>
            </div>
        </div>
        <div class="bg-slate-800/70 border border-slate-700/60 rounded-xl p-5 shadow-lg">
            <h2 class="font-semibold text-slate-200 mb-4">Người dùng mới</h2>
            <div class="relative h-56">
                <canvas id="usersByDayChart"></canvas>
            </div>
        </div>
        <div class="bg-slate-800/70 border border-slate-700/60 rounded-xl p-5 shadow-lg">
            <h2 class="font-semibold text-slate-200 mb-4">Bình luận mới</h2>
            <div class="relative h-56">
                <canvas id="commentsByDayChart"></canvas>
            </div>
        </div>
    </div>

    {{-- TOP TRUYỆN & TRẠNG THÁI --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-slate-800/70 border border-slate-700/60 rounded-xl p-5 shadow-lg">
            <h2 class="font-semibold text-slate-200 mb-4">Top truyện theo lượt đọc (phiên đọc)</h2>
            <div class="relative h-64">
                <canvas id="topComicsChart"></canvas>
            </div>
        </div>
        <div class="bg-slate-800/70 border border-slate-700/60 rounded-xl p-5 shadow-lg">
            <h2 class="font-semibold text-slate-200 mb-4">Top truyện theo lượt xem</h2>
            <div class="relative h-64">
                <canvas id="topComicsByViewsChart"></canvas>
            </div>
        </div>
        <div class="bg-slate-800/70 border border-slate-700/60 rounded-xl p-5 shadow-lg">
            <h2 class="font-semibold text-slate-200 mb-4">Top truyện theo theo dõi</h2>
            <div class="relative h-64">
                <canvas id="topComicsByFollowsChart"></canvas>
            </div>
        </div>
        <div class="bg-slate-800/70 border border-slate-700/60 rounded-xl p-5 shadow-lg">
            <h2 class="font-semibold text-slate-200 mb-4">Truyện theo trạng thái</h2>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="comicsByStatusChart"></canvas>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Màu nền cho Chart (dark)
Chart.defaults.color = '#94a3b8';
Chart.defaults.borderColor = 'rgba(71, 85, 105, 0.5)';
Chart.defaults.font.family = "'Nunito', sans-serif";

const chartLabels = @json($chartLabels);

/* --- LƯỢT ĐỌC 7 NGÀY --- */
(function() {
    const ctx = document.getElementById('viewsByDayChart').getContext('2d');
    const g = ctx.createLinearGradient(0, 0, 0, 250);
    g.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
    g.addColorStop(1, 'rgba(59, 130, 246, 0)');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Lượt đọc',
                data: @json($viewsByDayData),
                borderColor: '#3b82f6',
                backgroundColor: g,
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
})();

/* --- NGƯỜI DÙNG MỚI --- */
(function() {
    const ctx = document.getElementById('usersByDayChart').getContext('2d');
    const g = ctx.createLinearGradient(0, 0, 0, 250);
    g.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
    g.addColorStop(1, 'rgba(16, 185, 129, 0)');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Người dùng mới',
                data: @json($usersByDayData),
                borderColor: '#10b981',
                backgroundColor: g,
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
})();

/* --- BÌNH LUẬN MỚI --- */
(function() {
    const ctx = document.getElementById('commentsByDayChart').getContext('2d');
    const g = ctx.createLinearGradient(0, 0, 0, 250);
    g.addColorStop(0, 'rgba(6, 182, 212, 0.4)');
    g.addColorStop(1, 'rgba(6, 182, 212, 0)');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Bình luận',
                data: @json($commentsByDayData),
                borderColor: '#06b6d4',
                backgroundColor: g,
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
})();

/* --- TOP TRUYỆN (lượt đọc / phiên đọc) --- */
new Chart(document.getElementById('topComicsChart'), {
    type: 'bar',
    data: {
        labels: @json($topComicsTitles),
        datasets: [{
            label: 'Lượt đọc',
            data: @json($topComics->pluck('reading_histories_count')),
            backgroundColor: ['rgba(59, 130, 246, 0.7)', 'rgba(16, 185, 129, 0.7)', 'rgba(245, 158, 11, 0.7)', 'rgba(239, 68, 68, 0.7)', 'rgba(139, 92, 246, 0.7)'],
            borderColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
            borderWidth: 1,
            borderRadius: 6
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true }
        }
    }
});

/* --- TOP TRUYỆN THEO LƯỢT XEM --- */
new Chart(document.getElementById('topComicsByViewsChart'), {
    type: 'bar',
    data: {
        labels: @json($topComicsByViewsTitles),
        datasets: [{
            label: 'Lượt xem',
            data: @json($topComicsByViews->pluck('views')),
            backgroundColor: ['rgba(239, 68, 68, 0.7)', 'rgba(249, 115, 22, 0.7)', 'rgba(245, 158, 11, 0.7)', 'rgba(34, 197, 94, 0.7)', 'rgba(59, 130, 246, 0.7)'],
            borderColor: ['#ef4444', '#f97316', '#f59e0b', '#22c55e', '#3b82f6'],
            borderWidth: 1,
            borderRadius: 6
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true }
        }
    }
});

/* --- TOP TRUYỆN THEO THEO DÕI --- */
new Chart(document.getElementById('topComicsByFollowsChart'), {
    type: 'bar',
    data: {
        labels: @json($topComicsByFollowsTitles),
        datasets: [{
            label: 'Theo dõi',
            data: @json($topComicsByFollows->pluck('follows')),
            backgroundColor: ['rgba(236, 72, 153, 0.7)', 'rgba(168, 85, 247, 0.7)', 'rgba(59, 130, 246, 0.7)', 'rgba(20, 184, 166, 0.7)', 'rgba(34, 197, 94, 0.7)'],
            borderColor: ['#ec4899', '#a855f7', '#3b82f6', '#14b8a6', '#22c55e'],
            borderWidth: 1,
            borderRadius: 6
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true }
        }
    }
});

/* --- TRUYỆN THEO TRẠNG THÁI (Doughnut) --- */
new Chart(document.getElementById('comicsByStatusChart'), {
    type: 'doughnut',
    data: {
        labels: @json($comicsByStatusLabels),
        datasets: [{
            data: @json($comicsByStatusData),
            backgroundColor: ['rgba(59, 130, 246, 0.8)', 'rgba(16, 185, 129, 0.8)', 'rgba(245, 158, 11, 0.8)'],
            borderColor: ['#3b82f6', '#10b981', '#f59e0b'],
            borderWidth: 2,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>
@endpush
