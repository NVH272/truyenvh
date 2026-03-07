@extends('layouts.app')

@section('title', 'Truyện Mới Cập Nhật')

@section('content')
<div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Header & Bộ lọc (Filter) --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-slate-800 uppercase mb-2 tracking-tight flex items-center gap-3">
                <i class="fas fa-clock text-brand-blue"></i>
                Truyện Mới Cập Nhật
            </h1>
            <p class="text-sm text-slate-500 font-medium">
                Danh sách các tác phẩm vừa ra mắt chương mới nhất
            </p>
        </div>

        {{-- Dropdown Lọc Trạng Thái (Status) --}}
        <form method="GET" action="{{ route('user.comics.recently-updated') }}" id="status-filter-form" class="w-full md:w-auto">
            <div class="relative group">
                <select name="status" id="status-select"
                    class="w-full md:w-48 bg-white border-2 border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 font-bold focus:border-brand-blue focus:ring-4 focus:ring-brand-blue/20 outline-none appearance-none cursor-pointer transition-all hover:border-slate-300 shadow-sm"
                    onchange="document.getElementById('status-filter-form').submit();">
                    <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>Tất cả trạng thái</option>
                    <option value="ongoing" {{ $statusFilter == 'ongoing' ? 'selected' : '' }}>Đang tiến hành</option>
                    <option value="completed" {{ $statusFilter == 'completed' ? 'selected' : '' }}>Đã hoàn thành</option>
                    <option value="dropped" {{ $statusFilter == 'dropped' ? 'selected' : '' }}>Tạm dừng</option>
                </select>
                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 group-hover:text-brand-blue transition-colors">
                    <i class="fas fa-chevron-down text-xs"></i>
                </div>
            </div>
        </form>
    </div>

    {{-- Grid Hiển Thị Truyện --}}
    @if($comics->isEmpty())
    <div class="text-center py-20 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
        <div class="text-slate-300 mb-3"><i class="fas fa-folder-open text-5xl"></i></div>
        <h3 class="text-lg font-bold text-slate-600">Không tìm thấy truyện nào</h3>
        <p class="text-sm text-slate-400 mt-1">Thử thay đổi bộ lọc trạng thái xem sao.</p>
    </div>
    @else
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-[repeat(8,minmax(0,1fr))] gap-3 gap-y-6">
        @foreach ($comics as $comic)

        <div class="group relative flex flex-col transition-all duration-300 hover:-translate-y-1">

            {{-- Stretched link phủ toàn bộ thẻ --}}
            <a href="{{ route('user.comics.show', $comic->slug) }}"
                class="absolute inset-0 z-30"
                aria-label="{{ $comic->title }}"></a>

            {{-- Cover Image Wrapper (GIỮ KÍCH THƯỚC: aspect-[2/3]) --}}
            <div class="relative aspect-[2/3] rounded-lg overflow-hidden shadow-sm border border-slate-200 bg-slate-100
                    group-hover:shadow-md group-hover:border-blue-500/50 transition-all duration-300">

                <img src="{{ $comic->cover_url }}" alt="{{ $comic->title }}"
                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                    loading="lazy">

                {{-- Badge: Status (Top Left) --}}
                <div class="absolute top-1.5 left-1.5 pointer-events-none z-20">
                    @if($comic->status === 'ongoing')
                    <span class="px-1.5 py-0.5 text-[9px] font-bold bg-blue-600/90 text-white rounded shadow-sm backdrop-blur-sm">
                        Đang tiến hành
                    </span>
                    @elseif($comic->status === 'completed')
                    <span class="px-1.5 py-0.5 text-[9px] font-bold bg-green-600/90 text-white rounded shadow-sm backdrop-blur-sm">
                        Hoàn thành
                    </span>
                    @else
                    <span class="px-1.5 py-0.5 text-[9px] font-bold bg-yellow-600/90 text-white rounded shadow-sm backdrop-blur-sm">
                        Tạm dừng
                    </span>
                    @endif
                </div>

                <!-- {{-- Badge: Thời gian cập nhật (Top Right) --}}
                @if(!empty($comic->last_chapter_at))
                <div class="absolute top-1.5 right-1.5 pointer-events-none z-20">
                    <span class="px-1.5 py-0.5 text-[9px] font-semibold bg-slate-900/80 text-white rounded shadow-sm backdrop-blur-sm">
                        <i class="far fa-clock mr-1"></i>
                        {{ \Carbon\Carbon::parse($comic->last_chapter_at)->diffForHumans(null, true, true) }}
                    </span>
                </div>
                @endif -->

                {{-- Overlay Stats (Bottom on Image) --}}
                <div class="absolute inset-x-0 bottom-0 pt-6 pb-1.5 px-2 bg-gradient-to-t from-black/80 via-black/40 to-transparent flex justify-between items-end text-[10px] text-white/90 pointer-events-none z-20">
                    <span class="flex items-center gap-1">
                        <i class="fas fa-eye text-[9px]"></i>
                        {{ number_format($comic->views ?? 0) }}
                    </span>
                    <span class="flex items-center gap-1">
                        <i class="fas fa-heart text-red-400 text-[9px]"></i>
                        {{ number_format($comic->follows ?? 0) }}
                    </span>
                </div>
            </div>

            {{-- Content Below Image --}}
            <div class="mt-2 space-y-0.5 relative z-20 pointer-events-none px-0.5">
                <h3 class="text-[13px] font-bold text-slate-700 line-clamp-2 leading-snug
                        group-hover:text-blue-600 transition-colors"
                    title="{{ $comic->title }}">
                    {{ $comic->title }}
                </h3>

                {{-- Chapter count (dưới tên truyện) --}}
                <div class="text-[11px] text-slate-500 -mt-0.5">
                    {{ $comic->chapter_count ?? 0 }} chương
                </div>

                {{-- Rating (read-only) --}}
                @php
                $avgRating = (float)($comic->rating_avg ?? $comic->rating ?? 0);
                $ratingCount = (int)($comic->reviews_count ?? $comic->rating_count ?? 0);
                @endphp

                <div class="flex items-center gap-0.5 text-[10px]">
                    <x-rating-stars :rating="$avgRating" />
                    <span class="text-slate-500 ml-1">({{ number_format($avgRating, 1) }} • {{ $ratingCount }})</span>
                </div>

                {{-- Author --}}
                <div class="text-[11px] text-slate-500 truncate pt-1" title="Tác giả">
                    <i class="fas fa-user-edit text-[9px] mr-1 text-slate-400"></i>
                    {{ $comic->authors_list ?? 'Đang cập nhật' }}
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Sử dụng lại file Pagination Custom cực xịn xò của bạn --}}
    <div class="mt-12 flex justify-center border-t border-slate-200 pt-8 pb-8">
        {{ $comics->links('vendor.pagination.custom') }}
    </div>
    @endif
</div>
@endsection