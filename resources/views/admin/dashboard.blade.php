@extends('layouts.admin')

@section('title', 'Bảng Điều Khiển Trung Tâm')
@section('header', 'Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-white brand-font mb-1">Chào mừng trở lại, Kyouma!</h1>
        <p class="text-slate-400 text-sm">Hệ thống hoạt động ổn định. Không phát hiện bất thường từ SERN.</p>
    </div>
    <div class="flex gap-3">
        <button class="bg-slate-800 hover:bg-slate-700 text-white px-4 py-2 rounded-lg border border-slate-600 text-sm font-medium transition shadow-sm">
            <i class="fas fa-download mr-2"></i> Báo cáo
        </button>
        <a href="{{ route('admin.categories.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-lg shadow-orange-900/20 transition">
            <i class="fas fa-plus mr-2"></i> Thêm mới
        </a>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Stat Card 1 -->
    <div class="dashboard-card p-5">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Tổng Truyện</p>
                <h3 class="text-3xl font-bold text-white mt-1 brand-font">1,248</h3>
            </div>
            <div class="p-2 bg-blue-500/10 rounded-lg text-blue-500">
                <i class="fas fa-book-open text-xl"></i>
            </div>
        </div>
        <div class="flex items-center text-xs text-green-400 font-bold">
            <i class="fas fa-arrow-up mr-1"></i> 12% <span class="text-slate-500 font-normal ml-1">so với tháng trước</span>
        </div>
    </div>

    <!-- Stat Card 2 -->
    <div class="dashboard-card p-5">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Lượt xem</p>
                <h3 class="text-3xl font-bold text-white mt-1 brand-font">85.3K</h3>
            </div>
            <div class="p-2 bg-orange-500/10 rounded-lg text-orange-500">
                <i class="fas fa-eye text-xl"></i>
            </div>
        </div>
        <div class="flex items-center text-xs text-green-400 font-bold">
            <i class="fas fa-arrow-up mr-1"></i> 5.3% <span class="text-slate-500 font-normal ml-1">so với hôm qua</span>
        </div>
    </div>

    <!-- Stat Card 3 -->
    <div class="dashboard-card p-5">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Thành viên</p>
                <h3 class="text-3xl font-bold text-white mt-1 brand-font">4,820</h3>
            </div>
            <div class="p-2 bg-purple-500/10 rounded-lg text-purple-500">
                <i class="fas fa-users text-xl"></i>
            </div>
        </div>
        <div class="flex items-center text-xs text-slate-400 font-bold">
            <i class="fas fa-minus mr-1"></i> 0% <span class="text-slate-500 font-normal ml-1">ổn định</span>
        </div>
    </div>

    <!-- Stat Card 4 -->
    <div class="dashboard-card p-5 border-red-900/50 bg-red-900/10">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-red-400 text-xs font-bold uppercase tracking-wider">Báo cáo lỗi</p>
                <h3 class="text-3xl font-bold text-white mt-1 brand-font">3</h3>
            </div>
            <div class="p-2 bg-red-500/10 rounded-lg text-red-500">
                <i class="fas fa-exclamation-triangle text-xl"></i>
            </div>
        </div>
        <div class="flex items-center text-xs text-red-400 font-bold">
            Cần xử lý ngay
        </div>
    </div>
</div>

<!-- Main Content Area: Table & Sidebar Widgets -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Table (Chiếm 2/3) -->
    <div class="lg:col-span-2 dashboard-card overflow-hidden">
        <div class="p-5 border-b border-slate-700 flex justify-between items-center bg-slate-800/50">
            <h3 class="font-bold text-lg text-white brand-font">Truyện Cập Nhật Gần Đây</h3>
            <a href="#" class="text-xs text-orange-500 hover:text-orange-400 font-bold uppercase tracking-wide">Xem tất cả</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left clean-table text-sm">
                <thead>
                    <tr>
                        <th class="px-6 py-4">Tên Truyện</th>
                        <th class="px-6 py-4">Chapter</th>
                        <th class="px-6 py-4">Trạng thái</th>
                        <th class="px-6 py-4 text-right">Tác vụ</th>
                    </tr>
                </thead>
                <tbody class="text-slate-300">
                    <tr class="hover:bg-slate-800/50 transition">
                        <td class="px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-10 bg-slate-700 rounded overflow-hidden flex-shrink-0">
                                    <img src="https://via.placeholder.com/32x40" class="w-full h-full object-cover">
                                </div>
                                <span class="font-bold text-white">Võ Luyện Đỉnh Phong</span>
                            </div>
                        </td>
                        <td class="px-6 font-mono-tech">Chap 3500</td>
                        <td class="px-6"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/10 text-green-500 border border-green-500/20">Active</span></td>
                        <td class="px-6 text-right">
                            <button class="text-slate-400 hover:text-blue-500 transition mr-2"><i class="fas fa-edit"></i></button>
                            <button class="text-slate-400 hover:text-red-500 transition"><i class="fas fa-trash-alt"></i></button>
                        </td>
                    </tr>
                    <!-- Thêm các dòng khác tương tự -->
                    <tr class="hover:bg-slate-800/50 transition">
                        <td class="px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-10 bg-slate-700 rounded overflow-hidden flex-shrink-0">
                                    <img src="https://via.placeholder.com/32x40" class="w-full h-full object-cover">
                                </div>
                                <span class="font-bold text-white">Toàn Trí Độc Giả</span>
                            </div>
                        </td>
                        <td class="px-6 font-mono-tech">Chap 188</td>
                        <td class="px-6"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/10 text-green-500 border border-green-500/20">Active</span></td>
                        <td class="px-6 text-right">
                            <button class="text-slate-400 hover:text-blue-500 transition mr-2"><i class="fas fa-edit"></i></button>
                            <button class="text-slate-400 hover:text-red-500 transition"><i class="fas fa-trash-alt"></i></button>
                        </td>
                    </tr>
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
            <div class="flex gap-3 text-xs">
                <span class="text-slate-500 font-mono-tech shrink-0">10:42 AM</span>
                <span class="text-slate-300">Admin <strong class="text-white">Kyouma</strong> updated <span class="text-orange-400">One Piece</span>.</span>
            </div>
            <div class="flex gap-3 text-xs">
                <span class="text-slate-500 font-mono-tech shrink-0">10:30 AM</span>
                <span class="text-slate-300">New user <strong class="text-white">Mayuri</strong> registered.</span>
            </div>
            <div class="flex gap-3 text-xs">
                <span class="text-slate-500 font-mono-tech shrink-0">09:15 AM</span>
                <span class="text-red-400">Error: Image upload failed (ID: #4092).</span>
            </div>
            <div class="flex gap-3 text-xs">
                <span class="text-slate-500 font-mono-tech shrink-0">08:00 AM</span>
                <span class="text-green-400">System backup completed successfully.</span>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-slate-700 text-center">
            <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold">El Psy Kongroo</p>
        </div>
    </div>

</div>
@endsection