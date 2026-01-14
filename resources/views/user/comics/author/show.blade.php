@extends('layouts.app')

@section('title', 'Truyện của tác giả ' . $author)

@section('content')
{{-- Sử dụng max-w-[1920px] để mở rộng khung hình tối đa, giúp 7 cột không bị bé --}}
<div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- HEADER: TÁC GIẢ --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col md:flex-row items-center gap-6 mb-8 relative overflow-hidden">

        {{-- Decorative Background (Optional: Họa tiết chìm làm nền giúp trang trông "đầy" hơn) --}}
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-blue-50 rounded-full blur-2xl opacity-50 pointer-events-none"></div>

        {{-- Icon đại diện (Avatar Tác giả) --}}
        <div class="shrink-0 relative group z-10">
            {{-- Vòng tròn trang trí xoay nhẹ khi hover --}}
            <div class="absolute inset-0 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-2xl blur opacity-20 group-hover:opacity-30 transition-opacity duration-500"></div>

            <div class="relative w-16 h-16 md:w-20 md:h-20 rounded-2xl bg-gradient-to-br from-slate-800 to-slate-900 flex items-center justify-center shadow-lg">
                {{-- Icon bút máy tượng trưng cho tác giả --}}
                <i class="fas fa-pen-nib text-2xl md:text-3xl text-white"></i>
            </div>
        </div>

        {{-- Thông tin chính --}}
        <div class="text-center md:text-left flex-1 z-10">

            {{-- Tiêu đề chính: Nhấn mạnh TÊN TÁC GIẢ --}}
            <div class="mb-1">
                <span class="block text-xs font-medium text-slate-800 uppercase tracking-wide mb-0.5">Truyện của tác giả</span>
                <h1 class="text-3xl md:text-4xl font-black text-slate-800 tracking-tight leading-none">
                    {{ $author }}
                </h1>
            </div>

            {{-- Mô tả thống kê --}}
            <p class="text-slate-500 text-sm flex items-center justify-center md:justify-start gap-1.5 mt-2">
                <span>Hiện có</span>
                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-800 font-bold border border-slate-200 text-xs">
                    {{ $comics->total() }}
                </span>
                <span>đầu truyện được đăng tải trên hệ thống.</span>
            </p>
        </div>
    </div>

    {{-- COMIC GRID: 7 COLUMNS --}}
    {{-- Chú ý class: 2xl:grid-cols-[repeat(7,minmax(0,1fr))] để chia đúng 7 cột --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-[repeat(7,minmax(0,1fr))] gap-3 gap-y-6">
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

                {{-- Badge: Chapter (Top Left) --}}
                <div class="absolute top-1.5 left-1.5 pointer-events-none z-20">
                    <span class="px-1.5 py-0.5 text-[9px] font-bold bg-black/70 text-white rounded backdrop-blur-sm shadow-sm">
                        {{ $comic->chapter_count ?? 0 }} chương
                    </span>
                </div>

                {{-- Badge: Status (Top Right) --}}
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
            <div class="mt-2 space-y-1 relative z-20 pointer-events-none px-0.5">
                <h3 class="text-[13px] font-bold text-slate-700 line-clamp-2 leading-snug
                        group-hover:text-blue-600 transition-colors min-h-[2.6em]"
                    title="{{ $comic->title }}">
                    {{ $comic->title }}
                </h3>

                {{-- Rating (read-only) --}}
                @php
                $avgRating = (float)($comic->rating_avg ?? $comic->rating ?? 0);
                $ratingCount = (int)($comic->reviews_count ?? $comic->rating_count ?? 0);
                $rounded = round($avgRating);
                @endphp

                <div class="flex items-center gap-0.5 text-yellow-500 text-[10px]">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $i <= $rounded ? '' : 'text-slate-300' }}"></i>
                        @endfor
                        <span class="text-slate-500 ml-1">({{ number_format($avgRating, 1) }} • {{ $ratingCount }})</span>
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
    <div class="mt-8 flex justify-center">
        {{ $comics->links() }}
    </div>
    @endif

</div>
@endsection