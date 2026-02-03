@extends('layouts.admin')

@section('title', 'Bảng Điều Khiển Trung Tâm')
@section('header', 'Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-white brand-font mb-1">Chào mừng trở lại, {{ Auth::user()->name }}!</h1>
        <p class="text-slate-400 text-sm">Hệ thống hoạt động ổn định. Không phát hiện bất thường từ SERN.</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.reports.index') }}" class="bg-slate-800 hover:bg-slate-700 text-white px-4 py-2 rounded-lg border border-slate-600 text-sm font-medium transition shadow-sm">
            <i class="fas fa-chart-column mr-2"></i> Báo cáo
        </a>
        <a href="{{ route('admin.comics.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-lg shadow-orange-900/20 transition">
            <i class="fas fa-plus mr-2"></i> Thêm mới
        </a>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Stat Card 1: Tổng Truyện -->
    <div class="dashboard-card p-5">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Tổng Truyện</p>
                <h3 class="text-3xl font-bold text-white mt-1 brand-font">{{ number_format($totalComics) }}</h3>
            </div>
            <div class="p-2 bg-blue-500/10 rounded-lg text-blue-500">
                <i class="fas fa-book-open text-xl"></i>
            </div>
        </div>
        <div class="flex items-center text-xs {{ $comicsGrowth >= 0 ? 'text-green-400' : 'text-red-400' }} font-bold">
            <i class="fas fa-{{ $comicsGrowth >= 0 ? 'arrow-up' : 'arrow-down' }} mr-1"></i> {{ number_format(abs($comicsGrowth), 1) }}% <span class="text-slate-500 font-normal ml-1">so với tháng trước</span>
        </div>
    </div>

    <!-- Stat Card 2: Lượt xem -->
    <div class="dashboard-card p-5">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Lượt xem</p>
                <h3 class="text-3xl font-bold text-white mt-1 brand-font">{{ number_format($totalViews) }}</h3>
            </div>
            <div class="p-2 bg-orange-500/10 rounded-lg text-orange-500">
                <i class="fas fa-eye text-xl"></i>
            </div>
        </div>
        <div class="flex items-center text-xs text-green-400 font-bold">
            <!-- Số liệu giả định hoặc cần bảng analytics riêng -->
            <i class="fas fa-arrow-up mr-1"></i> {{ $viewsGrowth }}% <span class="text-slate-500 font-normal ml-1">tăng trưởng</span>
        </div>
    </div>

    <!-- Stat Card 3: Thành viên -->
    <div class="dashboard-card p-5">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Thành viên</p>
                <h3 class="text-3xl font-bold text-white mt-1 brand-font">{{ number_format($totalUsers) }}</h3>
            </div>
            <div class="p-2 bg-purple-500/10 rounded-lg text-purple-500">
                <i class="fas fa-users text-xl"></i>
            </div>
        </div>
        <div class="flex items-center text-xs {{ $usersGrowth >= 0 ? 'text-green-400' : 'text-red-400' }} font-bold">
            <i class="fas fa-{{ $usersGrowth >= 0 ? 'arrow-up' : 'arrow-down' }} mr-1"></i> {{ number_format(abs($usersGrowth), 1) }}% <span class="text-slate-500 font-normal ml-1">so với tháng trước</span>
        </div>
    </div>

    <!-- Stat Card 4: Báo cáo lỗi -->
    <div class="dashboard-card p-5 border-red-900/50 bg-red-900/10">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-red-400 text-xs font-bold uppercase tracking-wider">Báo cáo lỗi</p>
                <h3 class="text-3xl font-bold text-white mt-1 brand-font">{{ $pendingReports }}</h3>
            </div>
            <div class="p-2 bg-red-500/10 rounded-lg text-red-500">
                <i class="fas fa-exclamation-triangle text-xl"></i>
            </div>
        </div>
        <div class="flex items-center text-xs text-red-400 font-bold">
            {{ $pendingReports > 0 ? 'Cần xử lý ngay' : 'Hệ thống ổn định' }}
        </div>
    </div>
</div>

<!-- Main Content Area -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Table: Truyện Cập Nhật Gần Đây -->
    <div class="lg:col-span-2 dashboard-card overflow-hidden">
        <div class="p-5 border-b border-slate-700 flex justify-between items-center bg-slate-800/50">
            <h3 class="font-bold text-lg text-white brand-font">Truyện Cập Nhật Gần Đây</h3>
            <a href="{{ route('admin.comics.index') }}" class="text-xs text-orange-500 hover:text-orange-400 font-bold uppercase tracking-wide">Xem tất cả</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left clean-table text-sm">
                <thead>
                    <tr>
                        <th class="px-6 py-4">Tên Truyện</th>
                        <th class="px-6 py-4">Chapter Mới</th>
                        <th class="px-6 py-4">Trạng thái</th>
                        <th class="px-6 py-4 text-right">Tác vụ</th>
                    </tr>
                </thead>
                <tbody class="text-slate-300">
                    @forelse($recentComics as $comic)
                    <tr class="hover:bg-slate-800/50 transition">
                        <td class="px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-10 bg-slate-700 rounded overflow-hidden flex-shrink-0">
                                    <img src="{{ $comic->cover_url }}" class="w-full h-full object-cover" alt="Cover">
                                </div>
                                <span class="font-bold text-white truncate max-w-[150px]" title="{{ $comic->title }}">{{ $comic->title }}</span>
                            </div>
                        </td>
                        <td class="px-6 font-mono-tech text-orange-400">
                            {{ $comic->chapters->first() ? 'Chap ' . $comic->chapters->first()->chapter_number : 'Chưa có' }}
                        </td>
                        <td class="px-6">
                            @if($comic->status == 'ongoing')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/10 text-green-500 border border-green-500/20">Ongoing</span>
                            @elseif($comic->status == 'completed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-500/10 text-blue-500 border border-blue-500/20">Full</span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/10 text-red-500 border border-red-500/20">Dropped</span>
                            @endif
                        </td>
                        <td class="px-6 text-right">
                            <a href="{{ route('admin.comics.edit', $comic->id) }}" class="text-slate-400 hover:text-blue-500 transition mr-2"><i class="fas fa-edit"></i></a>
                            <!-- Nút xóa (cần form delete) -->
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-slate-500">Chưa có truyện nào được cập nhật.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Column (System Log) -->
    <div class="dashboard-card p-5 h-fit">
        <h3 class="font-bold text-white brand-font mb-4 flex items-center gap-2">
            <i class="fas fa-terminal text-sm text-slate-500"></i> System Log
        </h3>
        <div class="space-y-3">
            @forelse($systemLogs as $log)
            <div class="flex gap-3 text-xs">
                <span class="text-slate-500 font-mono-tech shrink-0">{{ $log['time']->format('H:i A') }}</span>
                <span class="text-slate-300">{!! $log['message'] !!}</span>
            </div>
            @empty
            <div class="text-xs text-slate-500 text-center">No recent activity.</div>
            @endforelse
        </div>
        <div class="mt-4 pt-4 border-t border-slate-700 text-center">
            <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold">El Psy Kongroo</p>
        </div>
    </div>

</div>
@endsection