@extends('layouts.app')

@section('title', 'Kết quả tìm kiếm: ' . ($q ?? ''))

@section('content')
<div class="bg-gray-50 min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- HEADER SECTION --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
                    <span class="p-2 bg-blue-100 text-blue-600 rounded-lg shadow-sm">
                        <i class="fas fa-search"></i>
                    </span>
                    Kết quả tìm kiếm
                </h1>
                <p class="text-gray-500 mt-2 text-base">
                    Từ khóa: <span class="font-bold text-blue-600">"{{ $q ?: '...' }}"</span>
                    <span class="mx-2 text-gray-300">|</span>
                    Tìm thấy <span class="font-bold text-gray-900">{{ $comics->total() }}</span> truyện
                </p>
            </div>

            <a href="{{ url('/') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm hover:shadow hover:border-blue-200">
                <i class="fas fa-arrow-left mr-2"></i> Về trang chủ
            </a>
        </div>

        @if($q === '')
        {{-- EMPTY QUERY STATE --}}
        <div class="flex flex-col items-center justify-center bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center text-3xl mb-4">
                <i class="fas fa-keyboard"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Nhập từ khóa để tìm kiếm</h3>
            <p class="text-gray-500 mt-1">Bạn có thể tìm theo tên truyện hoặc tên tác giả.</p>
        </div>
        @elseif($comics->isEmpty())
        {{-- NO RESULTS STATE --}}
        <div class="flex flex-col items-center justify-center bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center text-3xl mb-4">
                <i class="fas fa-search-minus"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Không tìm thấy truyện nào</h3>
            <p class="text-gray-500 mt-1">Thử tìm kiếm với từ khóa khác hoặc ngắn gọn hơn xem sao.</p>
        </div>
        @else
        {{-- GRID LAYOUT (Modern Design - Larger Grid) --}}
        {{-- Adjusted columns: lg:grid-cols-5 -> 4, xl:grid-cols-6 -> 5, 2xl:grid-cols-8 -> 6 --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-x-6 gap-y-10">
            @foreach($comics as $comic)
            <div class="group relative bg-transparent rounded-lg transition-all duration-300 hover:-translate-y-1">

                {{-- Stretched Link (Phủ toàn bộ thẻ để click vào đâu cũng được) --}}
                <a href="{{ route('user.comics.show', $comic->slug) }}" class="absolute inset-0 z-10" aria-label="{{ $comic->title }}"></a>

                {{-- Cover Image Wrapper --}}
                <div class="relative aspect-[2/3] rounded-lg overflow-hidden shadow-sm border border-gray-200 group-hover:shadow-md group-hover:border-blue-500/30 transition-all bg-gray-100">

                    {{-- Image --}}
                    <img src="{{ $comic->cover_url }}"
                        alt="{{ $comic->title }}"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">

                    {{-- Gradient Overlay (Làm tối phần dưới để text dễ đọc) --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-60 group-hover:opacity-80 transition-opacity pointer-events-none"></div>

                    {{-- Badge: Chapter (Top Left) --}}
                    <div class="absolute top-1.5 left-1.5 pointer-events-none z-20">
                        <span class="px-1.5 py-0.5 text-[9px] font-bold bg-black/60 backdrop-blur-sm text-white rounded shadow-sm border border-white/10">
                            {{ $comic->chapter_count ?? 0 }} chương
                        </span>
                    </div>

                    {{-- Badge: Status (Top Right) --}}
                    <div class="absolute top-1.5 right-1.5 pointer-events-none z-20">
                        @if($comic->status === 'completed')
                        <span class="px-1.5 py-0.5 text-[9px] font-bold bg-emerald-500 text-white rounded shadow-sm">Full</span>
                        @elseif($comic->status === 'ongoing')
                        <span class="px-1.5 py-0.5 text-[9px] font-bold bg-blue-500 text-white rounded shadow-sm">Đang ra</span>
                        @else
                        <span class="px-1.5 py-0.5 text-[9px] font-bold bg-gray-500 text-white rounded shadow-sm">Tạm ngưng</span>
                        @endif
                    </div>

                    {{-- Stats (Bottom Overlay) --}}
                    <div class="absolute bottom-0 left-0 right-0 p-2 flex justify-between items-end text-white/95 text-[10px] pointer-events-none z-20 font-medium">
                        <span class="flex items-center gap-1">
                            <i class="fas fa-eye text-sky-300"></i>
                            {{ number_format($comic->views ?? 0) }}
                        </span>
                        {{-- Hiển thị lượt theo dõi nếu có biến follows --}}
                        @if(isset($comic->follows))
                        <span class="flex items-center gap-1">
                            <i class="fas fa-heart text-red-400"></i>
                            {{ number_format($comic->follows) }}
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Content Below Image --}}
                <div class="mt-3 relative z-20 px-0.5">
                    {{-- Title --}}
                    <h3 class="text-sm font-bold text-gray-800 line-clamp-2 leading-snug group-hover:text-blue-600 transition-colors min-h-[1.5em]" title="{{ $comic->title }}">
                        {{ $comic->title }}
                    </h3>

                    {{-- Rating Stars --}}
                    <div class="flex items-center gap-1 mt-1.5">
                        <div class="flex items-center text-yellow-400 text-[11px]">
                            @for($i = 1; $i <= 5; $i++)
                                {{-- Dùng rating_avg nếu có, fallback về rating --}}
                                @if($i <=round($comic->rating_avg ?? $comic->rating ?? 0))
                                <i class="fas fa-star"></i>
                                @else
                                <i class="far fa-star text-gray-300"></i>
                                @endif
                                @endfor
                        </div>
                        <span class="text-[11px] text-gray-500 font-medium pt-0.5">
                            ({{ number_format($comic->rating_avg ?? $comic->rating ?? 0, 1) }})
                        </span>
                    </div>

                    {{-- Author --}}
                    <div class="flex items-center text-xs text-gray-500 mt-1 truncate">
                        <i class="fas fa-pen-nib text-[10px] mr-1.5 opacity-70"></i>
                        <span class="truncate">{{ $comic->author ?? 'Đang cập nhật' }}</span>
                    </div>
                </div>

            </div>
            @endforeach
        </div>

        {{-- PAGINATION --}}
        <div class="mt-12 flex justify-center">
            {{ $comics->links() }}
        </div>
        @endif
    </div>
</div>
@endsection