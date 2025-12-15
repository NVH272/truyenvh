@extends('layouts.admin')

@section('title', 'Duyệt truyện mới')
@section('header', 'Quản lý Truyện')

@section('content')
<div class="min-h-screen bg-slate-900 p-6">
    <div class="max-w-6xl mx-auto space-y-8">

        {{-- Section Header & Stats --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Duyệt Truyện Mới</h2>
                <p class="text-slate-400 text-sm mt-1">Danh sách các truyện đang chờ kiểm duyệt và công khai.</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 flex items-center gap-3 shadow-sm">
                    <div class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></div>
                    <span class="text-slate-400 text-sm">Chờ duyệt:</span>
                    <span class="text-white font-bold font-mono text-lg">{{ $comics->total() }}</span>
                </div>

                {{-- Nút lịch sử duyệt truyện --}}
                <a href="{{ route('admin.comics.review_history') }}"
                    class="px-4 py-2 text-sm font-medium text-emerald-300 bg-slate-800 border border-emerald-500/50 rounded-lg hover:bg-emerald-500/10 hover:text-emerald-200 transition-all shadow-sm flex items-center gap-2">
                    <i class="fas fa-history"></i>
                    <span>Lịch sử duyệt truyện</span>
                </a>

                <a href="{{ route('admin.comics.index') }}"
                    class="px-4 py-2 text-sm font-medium text-slate-300 bg-slate-800 border border-slate-700 rounded-lg hover:bg-slate-700 hover:text-white transition-all shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Quay lại
                </a>
            </div>
        </div>

        {{-- Alerts Area --}}
        <div class="space-y-4">
            @if(session('success'))
            <div class="relative overflow-hidden bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-4 flex items-center gap-3 animate-fade-in-down">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500"></div>
                <div class="bg-emerald-500/20 p-2 rounded-full text-emerald-400">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <h4 class="font-bold text-emerald-400 text-sm">Thành công</h4>
                    <p class="text-emerald-400/80 text-xs">{{ session('success') }}</p>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="relative overflow-hidden bg-red-500/10 border border-red-500/20 rounded-xl p-4 flex items-center gap-3 animate-fade-in-down">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-red-500"></div>
                <div class="bg-red-500/20 p-2 rounded-full text-red-400">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <h4 class="font-bold text-red-400 text-sm">Có lỗi xảy ra</h4>
                    <p class="text-red-400/80 text-xs">{{ session('error') }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Main Content List --}}
        @if($comics->isEmpty())
        <div class="bg-slate-800/50 border border-slate-700/50 border-dashed rounded-3xl p-12 text-center">
            <div class="w-20 h-20 mx-auto bg-slate-800 rounded-full flex items-center justify-center mb-6 shadow-inner">
                <i class="fas fa-check-double text-3xl text-emerald-500/50"></i>
            </div>
            <h3 class="text-white font-bold text-lg mb-2">Đã hoàn thành!</h3>
            <p class="text-slate-400 text-sm max-w-sm mx-auto">Hiện tại không còn truyện nào cần duyệt. Bạn có thể quay lại trang quản lý chính.</p>
        </div>
        @else
        <div class="grid grid-cols-1 gap-6">
            @foreach($comics as $comic)
            <div class="group bg-slate-800 border border-slate-700/60 rounded-2xl overflow-hidden hover:border-slate-600 hover:shadow-xl hover:shadow-slate-900/20 transition-all duration-300">
                <div class="flex flex-col md:flex-row">

                    {{-- Cột Trái: Ảnh bìa --}}
                    <div class="md:w-48 bg-slate-900 relative shrink-0">
                        <div class="aspect-[2/3] md:h-full w-full">
                            <img src="{{ $comic->cover_url }}"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                alt="{{ $comic->title }}">
                        </div>
                        <div class="absolute top-2 left-2">
                            <span class="px-2 py-1 bg-black/60 backdrop-blur-md border border-white/10 text-white text-[10px] uppercase font-bold tracking-wider rounded">
                                ID: #{{ $comic->id }}
                            </span>
                        </div>
                    </div>

                    {{-- Cột Phải: Nội dung --}}
                    <div class="flex-1 p-6 flex flex-col justify-between">
                        <div>
                            {{-- Tiêu đề & Badges --}}
                            <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-blue-400 transition-colors">
                                        {{ $comic->title }}
                                    </h3>
                                    @if($comic->categories->count() > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($comic->categories->take(5) as $category)
                                        <span class="px-2.5 py-1 bg-slate-700/50 text-slate-300 text-xs rounded-md border border-slate-600/50">
                                            {{ $category->name }}
                                        </span>
                                        @endforeach
                                        @if($comic->categories->count() > 5)
                                        <span class="px-2 py-1 text-slate-500 text-xs">+{{ $comic->categories->count() - 5 }}</span>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Grid thông tin chi tiết --}}
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5 p-4 bg-slate-900/30 rounded-xl border border-slate-700/30">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400">
                                        <i class="fas fa-pen-nib text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase tracking-wider text-slate-500 font-bold">Tác giả</p>
                                        <p class="text-sm text-slate-200 font-medium truncate">{{ $comic->author ?? 'Chưa cập nhật' }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-orange-500/10 flex items-center justify-center text-orange-400">
                                        <i class="fas fa-user-circle text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase tracking-wider text-slate-500 font-bold">Người đăng</p>
                                        <p class="text-sm text-slate-200 font-medium truncate">{{ $comic->creator->name ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-400">
                                        <i class="fas fa-clock text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase tracking-wider text-slate-500 font-bold">Thời gian gửi</p>
                                        <p class="text-sm text-slate-200 font-medium">{{ $comic->created_at->format('H:i - d/m/Y') }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Mô tả --}}
                            @if($comic->description)
                            <div class="mb-5">
                                <p class="text-sm text-slate-400 leading-relaxed line-clamp-2 hover:line-clamp-none transition-all cursor-default">
                                    {{ $comic->description }}
                                </p>
                            </div>
                            @endif
                        </div>

                        {{-- Action Bar --}}
                        <div class="pt-5 border-t border-slate-700/50 flex flex-col xl:flex-row items-stretch xl:items-center gap-4">
                            {{-- Approve Button --}}
                            <form action="{{ route('admin.comics.approve', $comic) }}" method="POST" class="shrink-0">
                                @csrf
                                <button type="submit"
                                    class="w-full xl:w-auto h-10 px-6 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold rounded-lg shadow-lg shadow-emerald-900/20 flex items-center justify-center gap-2 transition-all transform hover:-translate-y-0.5">
                                    <i class="fas fa-check-circle"></i> <span>Duyệt ngay</span>
                                </button>
                            </form>

                            {{-- Divider on Desktop --}}
                            <div class="hidden xl:block w-px h-8 bg-slate-700"></div>

                            {{-- Reject Form --}}
                            <form action="{{ route('admin.comics.reject', $comic) }}" method="POST"
                                onsubmit="return confirm('Bạn có chắc chắn muốn từ chối truyện này?');"
                                class="flex-1 flex gap-2">
                                @csrf
                                <div class="relative flex-1 group/input">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-comment-alt text-slate-500 group-focus-within/input:text-red-400 transition-colors"></i>
                                    </div>
                                    <input type="text"
                                        name="reason"
                                        placeholder="Nhập lý do từ chối (nếu có)..."
                                        maxlength="1000"
                                        class="w-full h-10 pl-10 pr-4 bg-slate-900/50 border border-slate-600 text-sm text-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 placeholder-slate-500 transition-all">
                                </div>
                                <button type="submit"
                                    class="h-10 px-4 bg-slate-800 border border-slate-600 hover:bg-red-500/10 hover:border-red-500/50 hover:text-red-400 text-slate-400 text-sm font-medium rounded-lg transition-all flex items-center gap-2 whitespace-nowrap">
                                    <span>Từ chối</span> <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination Styling --}}
        <div class="mt-8 px-2">
            {{ $comics->links() }}
        </div>
        @endif
    </div>
</div>
@endsection