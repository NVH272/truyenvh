@extends('layouts.app')

@section('title', 'Tủ truyện yêu thích')

@section('content')
{{-- Sử dụng max-w-[1920px] để mở rộng không gian cho 7-8 cột --}}
<div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8 py-6 min-h-screen">

    {{-- HEADER: Tối giản, thanh lịch, tinh tế --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 pb-6">
        <div class="flex items-center gap-4">
            {{-- Icon đại diện (Nhẹ nhàng với màu pastel) --}}
            <div class="w-12 h-12 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center shadow-sm border border-rose-100/50 shrink-0">
                <i class="fas fa-heart text-xl"></i>
            </div>

            {{-- Nội dung text --}}
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight">
                    Truyện đang theo dõi
                </h1>
                <p class="text-slate-500 text-sm mt-0.5">
                    Cập nhật những chương mới nhất từ tủ truyện của bạn.
                </p>
            </div>
        </div>

        {{-- Badge thống kê --}}
        @if(isset($comics) && $comics->total() > 0)
        <div class="inline-flex items-center justify-center px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm font-medium text-slate-600 shadow-sm self-start md:self-auto">
            Đang theo dõi: <span class="text-rose-600 font-bold ml-1.5">{{ $comics->total() }}</span>
        </div>
        @endif
    </div>

    @if($comics->isEmpty())
    {{-- EMPTY STATE: Sạch sẽ, viền dashed thân thiện --}}
    <div class="flex flex-col items-center justify-center py-24 px-4 bg-slate-50/50 border border-slate-200 border-dashed rounded-2xl text-center max-w-3xl mx-auto mt-10">
        <div class="w-20 h-20 bg-white text-slate-300 rounded-full flex items-center justify-center text-4xl mb-5 shadow-sm border border-slate-100">
            <i class="fas fa-book-open"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">Tủ truyện của bạn đang trống</h3>
        <p class="text-slate-500 max-w-md mx-auto mb-8 text-sm leading-relaxed">
            Bạn chưa lưu bộ truyện nào cả. Hãy tìm kiếm và lưu lại những tác phẩm yêu thích để không bỏ lỡ chương mới nhé!
        </p>
        <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-semibold text-white bg-slate-800 rounded-xl hover:bg-slate-900 transition-all shadow-sm hover:shadow-md group">
            Khám phá truyện ngay
            <i class="fas fa-arrow-right ml-2 text-xs group-hover:translate-x-1 transition-transform"></i>
        </a>
    </div>
    @else
    {{-- COMIC GRID: 8 CỘT (2XL) --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-[repeat(8,minmax(0,1fr))] gap-3 gap-y-6">
        @foreach($comics as $comic)

        {{-- CARD START: ĐƯỢC GIỮ NGUYÊN HOÀN TOÀN THEO YÊU CẦU --}}
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
            <div class="mt-2 space-y-0.5 relative z-20 pointer-events-none px-0.5">
                <h3 class="text-[13px] font-bold text-slate-700 line-clamp-2 leading-snug
                                group-hover:text-rose-600 transition-colors"
                    title="{{ $comic->title }}">
                    {{ $comic->title }}
                </h3>

                {{-- Chapter count (dưới tên truyện) --}}
                <div class="text-[11px] text-slate-500 -mt-0.5">
                    {{ $comic->chapter_count ?? 0 }} chương
                </div>

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
                    {{ $comic->authors_list ?? 'Đang cập nhật' }}
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