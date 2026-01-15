@extends('layouts.app')

@section('title', 'Tủ truyện yêu thích')

@section('content')
{{-- Sử dụng max-w-[1920px] để mở rộng không gian cho 7 cột --}}
<div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8 py-6 min-h-screen">

    {{-- HEADER: CLEAN GLASSY (Đồng bộ với Empty State) --}}
    <div class="relative mb-8 p-[2px] rounded-[2rem] bg-gradient-to-br from-rose-100 via-white to-pink-50 shadow-sm">

        {{-- Lớp kính mờ bên trong --}}
        <div class="relative bg-white/60 backdrop-blur-xl rounded-[calc(2rem-2px)] p-6 md:p-8 flex flex-col md:flex-row items-center md:items-start gap-6">

            {{-- Icon đại diện (Gọn, nổi bật trên nền trắng) --}}
            <div class="shrink-0">
                <div class="w-16 h-16 md:w-20 md:h-20 bg-white rounded-2xl shadow-[0_8px_20px_-6px_rgba(244,63,94,0.2)] border border-rose-50 flex items-center justify-center group">
                    <i class="fas fa-heart text-3xl md:text-4xl bg-gradient-to-br from-rose-500 to-pink-600 bg-clip-text text-transparent group-hover:scale-110 transition-transform duration-300"></i>
                </div>
            </div>

            {{-- Nội dung text --}}
            <div class="flex-1 text-center md:text-left">
                <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 mb-2">
                    <h1 class="text-2xl md:text-3xl font-black text-slate-800 tracking-tight">
                        Truyện đang theo dõi
                    </h1>

                    {{-- Badge thống kê nhỏ gọn --}}
                    @if($comics->total() > 0)
                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-rose-50 border border-rose-100 text-rose-600 text-xs font-bold uppercase tracking-wide mx-auto md:mx-0">
                        {{ $comics->total() }} bộ truyện
                    </span>
                    @endif
                </div>

                <p class="text-slate-500 font-medium text-sm md:text-base leading-relaxed max-w-2xl">
                    Danh sách những bộ truyện bạn đang theo dõi. Mọi cập nhật chương mới sẽ hiển thị tại đây.
                </p>
            </div>
        </div>
    </div>

    @if($comics->isEmpty())
    {{-- EMPTY STATE: Glassy --}}
    <div class="relative p-[2px] rounded-[2.5rem] bg-gradient-to-br from-slate-100 via-white to-slate-50 shadow-lg mt-8 max-w-2xl mx-auto">
        <div class="bg-white/80 backdrop-blur-xl rounded-[2.4rem] p-10 text-center relative overflow-hidden">
            {{-- Icon và nội dung --}}
            <div class="w-20 h-20 bg-slate-50 rounded-2xl mx-auto flex items-center justify-center text-3xl text-slate-300 mb-5 shadow-sm border border-slate-100">
                <i class="far fa-folder-open"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-3">Tủ truyện đang trống</h3>
            <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold text-white bg-rose-600 rounded-xl hover:bg-rose-700 transition-colors shadow-lg shadow-rose-600/20">
                Tìm truyện để theo dõi &rarr;
            </a>
        </div>
    </div>
    @else
    {{-- COMIC GRID: 7 CỘT --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-[repeat(7,minmax(0,1fr))] gap-3 gap-y-6">
        @foreach($comics as $comic)
        {{-- CARD START: Code y hệt trang tác giả --}}
        <div class="group relative flex flex-col transition-all duration-300 hover:-translate-y-1">

            {{-- Stretched link --}}
            <a href="{{ route('user.comics.show', $comic->slug) }}"
                class="absolute inset-0 z-30"
                aria-label="{{ $comic->title }}"></a>

            {{-- Cover Image Wrapper --}}
            <div class="relative aspect-[2/3] rounded-lg overflow-hidden shadow-sm border border-slate-200 bg-slate-100
                            group-hover:shadow-md group-hover:border-rose-500/50 transition-all duration-300">

                <img src="{{ $comic->cover_url }}" alt="{{ $comic->title }}"
                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                    loading="lazy">

                {{-- Badge: Chapter --}}
                <div class="absolute top-1.5 left-1.5 pointer-events-none z-20">
                    <span class="px-1.5 py-0.5 text-[9px] font-bold bg-black/70 text-white rounded backdrop-blur-sm shadow-sm">
                        {{ $comic->chapter_count ?? 0 }} chương
                    </span>
                </div>

                {{-- Badge: Status --}}
                <div class="absolute top-1.5 right-1.5 pointer-events-none z-20">
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

                {{-- Overlay Stats --}}
                <div class="absolute inset-x-0 bottom-0 pt-6 pb-1.5 px-2 bg-gradient-to-t from-black/80 via-black/40 to-transparent flex justify-between items-end text-[10px] text-white/90 pointer-events-none z-20">
                    <span class="flex items-center gap-1">
                        <i class="fas fa-eye text-[9px]"></i>
                        {{ number_format($comic->views ?? 0) }}
                    </span>
                    <span class="flex items-center gap-1">
                        <i class="fas fa-heart text-rose-400 text-[9px]"></i>
                        {{ number_format($comic->follows ?? 0) }}
                    </span>
                </div>
            </div>

            {{-- Content Below Image --}}
            <div class="mt-2 space-y-1 relative z-20 pointer-events-none px-0.5">
                <h3 class="text-[13px] font-bold text-slate-700 line-clamp-2 leading-snug
                                group-hover:text-rose-600 transition-colors min-h-[2.6em]"
                    title="{{ $comic->title }}">
                    {{ $comic->title }}
                </h3>

                {{-- Rating (sao + (avg • count)) --}}
                @php
                $avgRating = (float)($comic->rating_avg ?? $comic->rating ?? 0);
                $ratingCount = (int)($comic->reviews_count ?? $comic->rating_count ?? 0);
                $rounded = round($avgRating);
                @endphp
                <div class="flex items-center gap-0.5 text-[11px]">
                    <x-rating-stars :rating="$avgRating" />
                    <span class="text-gray-500 ml-1">({{ number_format($avgRating, 1) }} • {{ $ratingCount }})</span>
                </div>

                {{-- Author --}}
                <div class="text-[11px] text-slate-500 truncate pt-1" title="Tác giả">
                    <i class="fas fa-user-edit text-[9px] mr-1 text-slate-400"></i>
                    {{ $comic->author ?? 'Đang cập nhật' }}
                </div>
            </div>
        </div>
        {{-- CARD END --}}
        @endforeach
    </div>

    {{-- PAGINATION --}}
    @if($comics->hasPages())
    <div class="mt-12 flex justify-center">
        {{ $comics->links() }}
    </div>
    @endif
    @endif
</div>
@endsection