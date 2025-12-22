@extends('layouts.app')

@section('title', 'Tủ truyện yêu thích')

@section('content')
<div class="bg-gray-50 min-h-screen pb-12">
    {{-- HEADER SECTION WITH GRADIENT BACKGROUND --}}
    <div class="bg-white border-b border-gray-200 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{-- Breadcrumb --}}
            <nav class="flex text-sm font-medium text-gray-500 mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ url('/') }}" class="hover:text-indigo-600 transition-colors flex items-center">
                            <i class="fas fa-home mr-2"></i> Trang chủ
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                            <span class="text-gray-800">Tủ truyện</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- Title & Stats --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3">
                        <span class="bg-red-100 text-red-600 p-2 rounded-lg">
                            <i class="fas fa-heart"></i>
                        </span>
                        Truyện Đang Theo Dõi
                    </h1>
                    <p class="mt-2 text-gray-500 text-sm">Danh sách các bộ truyện bạn đã lưu lại để đọc dần.</p>
                </div>

                <div class="flex items-center gap-2 bg-indigo-50 px-4 py-2 rounded-full border border-indigo-100">
                    <span class="text-indigo-600 font-bold text-lg">{{ $comics->total() }}</span>
                    <span class="text-indigo-900 text-sm font-medium">truyện trong tủ</span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($comics->isEmpty())
        {{-- EMPTY STATE MODERN --}}
        <div class="flex flex-col items-center justify-center bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center max-w-2xl mx-auto">
            <div class="relative mb-6">
                <div class="absolute inset-0 bg-red-100 rounded-full animate-ping opacity-75"></div>
                <div class="relative bg-red-50 text-red-500 w-24 h-24 rounded-full flex items-center justify-center text-4xl shadow-inner">
                    <i class="fas fa-heart-broken"></i>
                </div>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-3">Tủ truyện của bạn đang trống</h2>
            <p class="text-gray-500 mb-8 max-w-md mx-auto">
                Có vẻ như bạn chưa lưu bộ truyện nào. Hãy khám phá thư viện và nhấn nút <span class="font-bold text-red-500"><i class="fas fa-heart"></i> Theo dõi</span> để nhận thông báo khi có chap mới nhé!
            </p>
            <a href="{{ url('/') }}"
                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white text-base font-semibold rounded-full shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                <i class="fas fa-compass mr-2"></i> Khám phá ngay
            </a>
        </div>
        @else
        {{-- COMIC GRID --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-x-6 gap-y-10">
            @foreach($comics as $comic)
            <a href="{{ route('user.comics.show', $comic) }}"
                class="group relative flex flex-col bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 border border-transparent hover:border-gray-100 pb-3">

                {{-- Card Image Wrapper --}}
                <div class="relative w-full aspect-[2/3] rounded-t-xl overflow-hidden mb-3">

                    {{-- Image --}}
                    <img src="{{ $comic->cover_url }}"
                        alt="{{ $comic->title }}"
                        class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500 ease-in-out">

                    {{-- Overlay Gradient --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-70 group-hover:opacity-90 transition-opacity"></div>

                    {{-- Badge: Chapter (Top Left) --}}
                    <div class="absolute top-2 left-2 pointer-events-none z-20">
                        <span class="px-2 py-1 text-[10px] font-bold bg-black/70 text-white rounded-md backdrop-blur-sm shadow-sm">
                            {{ $comic->chapter_count ?? 0 }} chương
                        </span>
                    </div>

                    {{-- Badge: Status (Top Right - dạng chữ như mẫu) --}}
                    <div class="absolute top-2 right-2 pointer-events-none z-20">
                        @if($comic->status === 'ongoing')
                        <span class="px-2 py-1 text-[10px] font-bold bg-blue-600/90 text-white rounded-md shadow-sm">
                            Đang tiến hành
                        </span>
                        @elseif($comic->status === 'completed')
                        <span class="px-2 py-1 text-[10px] font-bold bg-green-600/90 text-white rounded-md shadow-sm">
                            Hoàn thành
                        </span>
                        @else
                        <span class="px-2 py-1 text-[10px] font-bold bg-yellow-600/90 text-white rounded-md shadow-sm">
                            Tạm dừng
                        </span>
                        @endif
                    </div>

                    {{-- Overlay Stats (Bottom) --}}
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/95 via-black/60 to-transparent pt-7 pb-2 px-3 flex justify-between items-end text-[11px] text-white/90 pointer-events-none z-20">
                        <span class="flex items-center gap-1">
                            <i class="fas fa-eye text-[10px]"></i>
                            {{ number_format($comic->views ?? 0) }}
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="fas fa-heart text-red-400 text-[10px]"></i>
                            {{ number_format($comic->follows ?? 0) }}
                        </span>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="px-3 flex flex-col gap-1.5">
                    {{-- Title --}}
                    <h3 class="font-bold text-gray-800 text-sm leading-tight line-clamp-2 min-h-[2.5em] group-hover:text-indigo-600 transition-colors"
                        title="{{ $comic->title }}">
                        {{ $comic->title }}
                    </h3>

                    {{-- Rating (sao + (avg • count)) --}}
                    @php
                    $avgRating = (float)($comic->rating_avg ?? $comic->rating ?? 0);
                    $ratingCount = (int)($comic->reviews_count ?? $comic->rating_count ?? 0);
                    $rounded = round($avgRating);
                    @endphp
                    <div class="flex items-center gap-0.5 text-yellow-500 text-[11px]">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $rounded ? '' : 'text-slate-300' }}"></i>
                            @endfor
                            <span class="text-gray-500 ml-1">({{ number_format($avgRating, 1) }} • {{ $ratingCount }})</span>
                    </div>

                    {{-- Author --}}
                    <div class="flex items-center text-xs text-gray-500">
                        <i class="fas fa-user-edit text-[10px] mr-1.5 w-3 text-center"></i>
                        <span class="truncate">{{ $comic->author ?? 'Đang cập nhật' }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        {{-- PAGINATION --}}
        <!-- <div class="mt-12 mb-8 flex justify-center">
            <div class="bg-white px-4 py-3 rounded-xl shadow-sm border border-gray-100">
                {{ $comics->links() }}
            </div>
        </div> -->
        @endif
    </div>
</div>
@endsection