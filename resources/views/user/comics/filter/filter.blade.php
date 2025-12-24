@extends('layouts.app')

@section('title', 'Kết quả tìm kiếm')

@section('content')

{{-- Config & Custom CSS --}}
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    brand: {
                        blue: '#2563eb', // Royal Blue
                        light: '#eff6ff', // Light Blue bg
                    }
                },
                boxShadow: {
                    'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                    'glow': '0 0 15px rgba(37, 99, 235, 0.2)',
                }
            }
        }
    }
</script>
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

{{-- CONTENT BODY --}}
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">

        {{-- LEFT COLUMN: KẾT QUẢ TÌM KIẾM (Chiếm 3/4) --}}
        <div class="w-full lg:w-3/4">
            {{-- Header Section --}}
            <div class="flex flex-col sm:flex-row justify-between items-end mb-6 pb-4 border-b border-slate-200 gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-800 uppercase mb-1 tracking-tight">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Kết Quả</span> Tìm Kiếm
                    </h1>
                    <p class="text-xs text-slate-500 font-medium">
                        Hiển thị {{ $comics->count() }} / {{ $comics->total() }} truyện phù hợp
                    </p>
                </div>
            </div>

            {{-- Grid Comics: 5 Cột (lg:grid-cols-5) --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4 md:gap-5">
                @forelse($comics as $comic)
                <div class="group relative flex flex-col transition-all duration-300 hover:-translate-y-1">

                    {{-- Stretched link phủ toàn bộ thẻ --}}
                    <a href="{{ route('user.comics.show', $comic->slug) }}"
                        class="absolute inset-0 z-30"
                        aria-label="{{ $comic->title }}"></a>

                    {{-- Cover Image Wrapper (GIỮ KÍCH THƯỚC: aspect-[2/3]) --}}
                    <div class="relative aspect-[2/3] rounded-lg overflow-hidden shadow-sm border border-slate-200
                group-hover:shadow-md group-hover:border-brand-blue/50 transition-all duration-300">
                        <img src="{{ $comic->cover_url }}" alt="{{ $comic->title }}"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">

                        {{-- Badge: Chapter (Top Left) --}}
                        <div class="absolute top-1.5 left-1.5 pointer-events-none z-20">
                            <span class="px-1.5 py-0.5 text-[9px] font-bold bg-black/70 text-white rounded backdrop-blur-sm shadow-sm">
                                {{ $comic->chapter_count ?? 0 }} chương
                            </span>
                        </div>

                        {{-- Badge: Status (Top Right) (dạng chữ như mẫu) --}}
                        <div class="absolute top-1.5 right-1.5 pointer-events-none z-20">
                            @if($comic->status === 'ongoing')
                            <span class="px-1.5 py-0.5 text-[9px] font-bold bg-blue-600/90 text-white rounded shadow-sm">
                                Đang tiến hành
                            </span>
                            @elseif($comic->status === 'completed')
                            <span class="px-1.5 py-0.5 text-[9px] font-bold bg-green-600/90 text-white rounded shadow-sm">
                                Hoàn thành
                            </span>
                            @else
                            <span class="px-1.5 py-0.5 text-[9px] font-bold bg-yellow-600/90 text-white rounded shadow-sm">
                                Tạm dừng
                            </span>
                            @endif
                        </div>

                        {{-- Overlay Stats (Bottom on Image - vệt đen mỏng đủ đọc chữ) --}}
                        <div class="absolute inset-x-0 bottom-0 pt-3 pb-1.5 px-2 bg-gradient-to-t from-black/80 via-black/40 to-transparent flex justify-between items-end text-[10px] text-white/90 pointer-events-none z-20">
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
                   group-hover:text-brand-blue transition-colors min-h-[2.6em]"
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
                        <div class="text-[11px] text-slate-500 truncate" title="Tác giả">
                            <i class="fas fa-user-edit text-[9px] mr-1"></i>
                            {{ $comic->author ?? 'Đang cập nhật' }}
                        </div>
                    </div>
                </div>

                @empty
                <div class="col-span-full text-center text-slate-400 py-10">
                    Không tìm thấy truyện phù hợp.
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="flex justify-center mt-10">
                <!-- <div class="flex items-center bg-white rounded-full px-2 py-1.5 border border-slate-200 shadow-sm gap-1">
                    <a href="#" class="w-7 h-7 flex items-center justify-center rounded-full text-slate-400 hover:bg-slate-100 transition-all"><i class="fas fa-chevron-left text-[10px]"></i></a>
                    <a href="#" class="w-7 h-7 flex items-center justify-center rounded-full bg-brand-blue text-white font-bold text-xs shadow-sm">1</a>
                    <a href="#" class="w-7 h-7 flex items-center justify-center rounded-full text-slate-500 hover:bg-slate-100 hover:text-brand-blue transition-all text-xs font-medium">2</a>
                    <a href="#" class="w-7 h-7 flex items-center justify-center rounded-full text-slate-500 hover:bg-slate-100 hover:text-brand-blue transition-all text-xs font-medium">3</a>
                    <span class="w-7 h-7 flex items-center justify-center text-slate-300 font-bold text-[10px]">...</span>
                    <a href="#" class="w-7 h-7 flex items-center justify-center rounded-full text-slate-400 hover:bg-slate-100 transition-all"><i class="fas fa-chevron-right text-[10px]"></i></a>
                </div> -->
                {{ $comics->links() }}
            </div>
        </div>

        {{-- RIGHT COLUMN: SIDEBAR FILTER (STICKY) --}}
        <div class="w-full lg:w-1/4">
            <div class="sticky top-24 space-y-6">

                {{-- Filter Box --}}
                <div class="bg-white/90 backdrop-blur-xl p-5 rounded-xl border border-slate-200 shadow-lg relative overflow-hidden">

                    {{-- Heading --}}
                    <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                        <div class="w-1 h-5 bg-brand-blue rounded-full shadow-sm"></div>
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Bộ Lọc</h3>
                    </div>

                    {{-- Search Name --}}
                    <form method="GET" action="{{ route('user.comics.filter') }}">
                        <div class="group relative mb-4">
                            <label class="text-[11px] font-bold text-slate-400 uppercase mb-1.5 block ml-1 tracking-wide">
                                Tìm kiếm
                            </label>

                            <div class="relative">
                                <input
                                    type="text"
                                    name="q"
                                    value="{{ request('q') }}"
                                    placeholder="Nhập tên truyện..."
                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg
                                    px-3 py-2 pl-9 text-xs text-slate-700 font-medium
                                    focus:bg-white focus:border-brand-blue focus:ring-1 focus:ring-brand-blue
                                    outline-none transition-all placeholder-slate-400">

                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2
                                text-slate-400 text-xs group-focus-within:text-brand-blue transition-colors"></i>
                            </div>
                        </div>
                    </form>

                    {{-- Sort Dropdown --}}
                    <div class="relative mb-4">
                        <label class="text-[11px] font-bold text-slate-400 uppercase mb-1.5 block ml-1 tracking-wide">
                            Sắp xếp
                        </label>
                        <form method="GET" action="{{ route('user.comics.filter') }}">
                            <div class="relative group">
                                <select name="sort" id="sort-select"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-700 font-medium
                                    focus:bg-white focus:border-brand-blue focus:ring-1 focus:ring-brand-blue outline-none appearance-none
                                    cursor-pointer transition-all hover:border-slate-300">

                                    {{-- Lượt xem --}}
                                    <option value="views_desc" {{ request('sort') == 'views_desc' ? 'selected' : '' }}>
                                        Lượt xem (Cao → Thấp)
                                    </option>
                                    <option value="views_asc" {{ request('sort') == 'views_asc'  ? 'selected' : '' }}>
                                        Lượt xem (Thấp → Cao)
                                    </option>

                                    {{-- Đánh giá --}}
                                    <option value="rating_desc" {{ request('sort') == 'rating_desc' ? 'selected' : '' }}>
                                        Đánh giá (Cao → Thấp)
                                    </option>
                                    <option value="rating_asc" {{ request('sort') == 'rating_asc'  ? 'selected' : '' }}>
                                        Đánh giá (Thấp → Cao)
                                    </option>

                                    {{-- Số chương --}}
                                    <option value="chapters_desc" {{ request('sort') == 'chapters_desc' ? 'selected' : '' }}>
                                        Số chương (Nhiều → Ít)
                                    </option>
                                    <option value="chapters_asc" {{ request('sort') == 'chapters_asc'  ? 'selected' : '' }}>
                                        Số chương (Ít → Nhiều)
                                    </option>
                                </select>

                                <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                    <i class="fas fa-chevron-down text-slate-400 text-[10px]"></i>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Tags Section --}}
                    <div class="mb-5">
                        <div class="flex items-center justify-between mb-2 ml-1">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Thể loại</label>
                            @if(request()->has('categories') && count(request('categories')) > 0)
                            <a href="{{ request()->fullUrlWithQuery(['categories' => null]) }}" class="text-[10px] text-red-500 hover:text-red-600 hover:underline transition-colors font-medium cursor-pointer">
                                <i class="fas fa-times mr-0.5"></i> Xóa
                            </a>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-1.5">
                            @forelse($categories as $category)
                            @php
                            $selected = request()->input('categories', []);
                            $active = in_array($category->slug, $selected);

                            $newSelected = $active
                            ? array_values(array_diff($selected, [$category->slug]))
                            : array_values(array_unique([...$selected, $category->slug]));

                            $url = request()->fullUrlWithQuery(['categories' => $newSelected]);
                            @endphp

                            <a href="{{ $url }}"
                                class="relative px-2.5 py-1.5 rounded-md text-[11px] font-semibold border transition-all duration-200 select-none
                                {{-- Logic CSS: Active chỉ highlight viền và chữ --}}
                                {{ $active 
                                        ? 'bg-brand-blue/5 border-brand-blue text-brand-blue' 
                                        : 'bg-white border-slate-200 text-slate-500 hover:border-brand-blue/50 hover:text-brand-blue' 
                                }}">

                                {{ $category->name }}
                            </a>

                            @empty
                            <span class="text-xs text-slate-400 italic pl-1">Đang cập nhật...</span>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sortSelect = document.getElementById('sort-select');
        if (!sortSelect) return;

        sortSelect.addEventListener('change', function() {
            const params = new URLSearchParams(window.location.search);
            if (params.get('sort') === this.value) return;
            this.form.submit();
        });
    });
</script>