@extends('layouts.admin')

@section('title', 'Duyệt truyện mới')
@section('header', 'Quản lý Truyện')

@section('content')
<div class="p-8 max-w-7xl mx-auto space-y-6">

    {{-- Thông báo --}}
    @if(session('success'))
    <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm animate-fade-in" role="alert">
        <div class="flex items-center gap-2 font-bold">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm animate-fade-in" role="alert">
        <div class="flex items-center gap-2 font-bold">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    </div>
    @endif

    {{-- Header Card --}}
    <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-700 bg-slate-800/50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-lg bg-orange-500/10 text-orange-500 flex items-center justify-center">
                    <i class="fas fa-clock text-sm"></i>
                </span>
                <div>
                    <h3 class="font-bold text-slate-100 text-base">Truyện chờ duyệt</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Tổng cộng: <span class="text-orange-400 font-semibold">{{ $comics->total() }}</span> truyện</p>
                </div>
            </div>
            <a href="{{ route('admin.comics.index') }}" 
                class="px-4 py-2 text-sm font-medium text-slate-300 bg-slate-700/50 border border-slate-600 rounded-lg hover:bg-slate-700 hover:text-white transition-all">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại
            </a>
        </div>

        <div class="p-6">
            @if($comics->isEmpty())
            <div class="text-center py-12">
                <div class="w-16 h-16 mx-auto bg-slate-700/50 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-inbox text-2xl text-slate-500"></i>
                </div>
                <p class="text-slate-400 text-sm">Hiện không có truyện nào đang chờ duyệt.</p>
            </div>
            @else
            <div class="space-y-4">
                @foreach($comics as $comic)
                <div class="bg-slate-900/50 border border-slate-700 rounded-xl p-5 hover:border-slate-600 transition-colors">
                    <div class="flex gap-4">
                        {{-- Ảnh bìa --}}
                        <div class="w-20 h-28 rounded-lg overflow-hidden flex-shrink-0 bg-slate-800 border border-slate-700">
                            <img src="{{ $comic->cover_url }}" class="w-full h-full object-cover" alt="{{ $comic->title }}">
                        </div>

                        {{-- Thông tin truyện --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start gap-4 mb-2">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-slate-100 font-bold text-base mb-1.5 truncate">
                                        {{ $comic->title }}
                                    </h4>
                                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-400 mb-2">
                                        <span>
                                            <i class="fas fa-user text-slate-500 mr-1"></i>
                                            Tác giả: <span class="text-slate-300">{{ $comic->author ?? 'Đang cập nhật' }}</span>
                                        </span>
                                        <span>
                                            <i class="fas fa-user-circle text-orange-500 mr-1"></i>
                                            Người gửi: <span class="text-orange-400 font-medium">{{ $comic->creator->name ?? 'N/A' }}</span>
                                        </span>
                                        <span>
                                            <i class="fas fa-calendar text-slate-500 mr-1"></i>
                                            Gửi: <span class="text-slate-300">{{ $comic->created_at->format('d/m/Y H:i') }}</span>
                                        </span>
                                    </div>
                                    
                                    {{-- Thể loại --}}
                                    @if($comic->categories->count() > 0)
                                    <div class="flex flex-wrap gap-1.5 mb-2">
                                        @foreach($comic->categories->take(5) as $category)
                                        <span class="px-2 py-0.5 bg-blue-500/10 text-blue-400 text-[10px] rounded-full border border-blue-500/20">
                                            {{ $category->name }}
                                        </span>
                                        @endforeach
                                        @if($comic->categories->count() > 5)
                                        <span class="px-2 py-0.5 bg-slate-700 text-slate-400 text-[10px] rounded-full">
                                            +{{ $comic->categories->count() - 5 }}
                                        </span>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Mô tả --}}
                            @if($comic->description)
                            <p class="text-xs text-slate-400 line-clamp-2 mb-3">
                                {{ $comic->description }}
                            </p>
                            @endif

                            {{-- Action Buttons --}}
                            <div class="flex items-center gap-3 flex-wrap">
                                <form action="{{ route('admin.comics.approve', $comic) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5">
                                        <i class="fas fa-check"></i> Phê duyệt
                                    </button>
                                </form>

                                <form action="{{ route('admin.comics.reject', $comic) }}" method="POST" 
                                    onsubmit="return confirm('Bạn có chắc chắn muốn từ chối truyện này?');" 
                                    class="inline-flex items-center gap-2 flex-1 min-w-0">
                                    @csrf
                                    <input type="text" 
                                        name="reason" 
                                        placeholder="Lý do từ chối (tùy chọn)" 
                                        maxlength="1000"
                                        class="flex-1 min-w-0 px-3 py-2 text-xs bg-slate-800 border border-slate-600 rounded-lg text-slate-200 placeholder-slate-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold rounded-lg bg-red-600 hover:bg-red-700 text-white transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5 ml-2">
                                        <i class="fas fa-times"></i> Từ chối
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $comics->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
