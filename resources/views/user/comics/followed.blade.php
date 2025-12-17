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
            <a href="{{ route('user.comics.show', $comic) }}" class="group relative flex flex-col bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 border border-transparent hover:border-gray-100 pb-3">
                {{-- Card Image Wrapper --}}
                <div class="relative w-full aspect-[2/3] rounded-t-xl overflow-hidden mb-3">

                    {{-- Image --}}
                    <img src="{{ $comic->cover_url }}"
                        alt="{{ $comic->title }}"
                        class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500 ease-in-out">

                    {{-- Overlay Gradient --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-60 group-hover:opacity-80 transition-opacity"></div>

                    {{-- Badges Top --}}
                    <div class="absolute top-2 left-2 right-2 flex justify-between items-start">
                        {{-- Heart Icon (Indicator) --}}
                        <div class="bg-white/20 backdrop-blur-md text-white p-1.5 rounded-full shadow-lg border border-white/30">
                            <i class="fas fa-heart text-red-500 text-xs"></i>
                        </div>

                        @if($comic->status === 'completed')
                        <span class="bg-emerald-500/90 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded-md shadow-sm border border-emerald-400/50 uppercase tracking-wide">
                            Full
                        </span>
                        @endif
                    </div>

                    {{-- Hover Action: Read Now --}}
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10 pointer-events-none">
                        <span class="bg-white text-indigo-600 rounded-full px-4 py-2 font-bold text-xs shadow-lg transform scale-90 group-hover:scale-100 transition-transform">
                            <i class="fas fa-book-reader mr-1"></i> Đọc ngay
                        </span>
                    </div>

                    {{-- Info Bottom Overlay (Stats) --}}
                    <div class="absolute bottom-0 left-0 right-0 p-2 text-white">
                        <div class="flex items-center justify-between text-[10px] font-medium text-gray-200">
                            <span class="flex items-center gap-1">
                                <i class="fas fa-list-ol"></i> {{ $comic->chapter_count ?? 0 }} chap
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="fas fa-eye"></i> {{ number_format($comic->views ?? 0) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="px-3 flex flex-col gap-1.5">
                    {{-- Title --}}
                    <h3 class="font-bold text-gray-800 text-sm leading-tight line-clamp-2 min-h-[2.5em] group-hover:text-indigo-600 transition-colors" title="{{ $comic->title }}">
                        {{ $comic->title }}
                    </h3>

                    {{-- Author --}}
                    <div class="flex items-center text-xs text-gray-500">
                        <i class="fas fa-pen-nib text-[10px] mr-1.5 w-3 text-center"></i>
                        <span class="truncate">{{ $comic->author ?? 'Đang cập nhật' }}</span>
                    </div>

                    {{-- Rating Section --}}
                    <div class="flex items-center justify-between text-xs mt-0.5">
                        {{-- Average Rating --}}
                        <div class="flex items-center gap-1 text-yellow-500 font-bold bg-yellow-50 px-1.5 py-0.5 rounded border border-yellow-100">
                            <span>{{ number_format($comic->rating ?? 0, 1) }}</span>
                            <i class="fas fa-star text-[10px]"></i>
                        </div>

                        {{-- Reviews Count --}}
                        <span class="text-gray-400 text-[11px]">
                            ({{ number_format($comic->rating_count ?? 0) }} đánh giá)
                        </span>
                    </div>

                    {{-- Line Separator --}}
                    <div class="h-px bg-gray-100 my-1"></div>

                    {{-- Categories --}}
                    <div class="flex flex-wrap gap-1">
                        @foreach($comic->categories->take(2) as $cat)
                        <span class="text-[10px] px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded hover:bg-gray-200 transition-colors">
                            {{ $cat->name }}
                        </span>
                        @endforeach
                        @if($comic->categories->count() > 2)
                        <span class="text-[10px] text-gray-400 px-1">+{{ $comic->categories->count() - 2 }}</span>
                        @endif
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