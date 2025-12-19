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
                        Hiển thị 35 truyện phù hợp nhất
                    </p>
                </div>
                <div class="bg-white px-3 py-1.5 rounded-full border border-slate-200 shadow-sm">
                    <span class="text-[10px] text-slate-400">Tổng</span>
                    <span class="text-brand-blue font-bold text-sm ml-1">14,379</span>
                </div>
            </div>

            {{-- Grid Comics: 5 Cột (lg:grid-cols-5) --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4 md:gap-5">
                @php
                // Mock Data 35 items (7 hàng x 5 cột)
                $baseComics = [
                (object)['slug' => 'inuyasha', 'title' => 'Inuyasha (Khuyển Dạ Xoa)', 'cover_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9c/Inuyashavolume1.jpg', 'chapter_count' => 558, 'status' => 'completed', 'views' => 976000, 'follows' => 1200, 'rating_avg' => 4.5],
                (object)['slug' => 'isekai-nonbiri', 'title' => 'Cuộc Sống Nhàn Nhã Ở Thế Giới Khác', 'cover_url' => 'https://m.media-amazon.com/images/I/81xVw9+N7rL._AC_UF1000,1000_QL80_.jpg', 'chapter_count' => 89, 'status' => 'ongoing', 'views' => 292000, 'follows' => 3400, 'rating_avg' => 5],
                (object)['slug' => 'nu-tho-san', 'title' => 'Phương Pháp Tán Tỉnh Của Nữ Thợ Săn', 'cover_url' => 'https://i.pinimg.com/736x/2b/86/e5/2b86e5c5450417643261642861c85317.jpg', 'chapter_count' => 45, 'status' => 'ongoing', 'views' => 5720000, 'follows' => 15000, 'rating_avg' => 3.5],
                (object)['slug' => 'the-live', 'title' => 'The Live (Cuộc Sống Mới)', 'cover_url' => 'https://m.media-amazon.com/images/I/61r56D-c+LL._AC_UF1000,1000_QL80_.jpg', 'chapter_count' => 120, 'status' => 'ongoing', 'views' => 1110000, 'follows' => 8900, 'rating_avg' => 4],
                (object)['slug' => 'tensei-ken', 'title' => 'Chuyển Sinh Thành Kiếm', 'cover_url' => 'https://upload.wikimedia.org/wikipedia/en/3/36/Reincarnated_as_a_Sword_light_novel_volume_1_cover.jpg', 'chapter_count' => 67, 'status' => 'pause', 'views' => 322000, 'follows' => 2100, 'rating_avg' => 4.8],
                ];

                $comicsData = [];
                for($i=0; $i<7; $i++) { // Lặp 7 lần để tạo 7 hàng (5x7=35)
                    foreach($baseComics as $c) {
                    $clone=clone $c;
                    $clone->chapter_count += rand(1, 10); // Random chút số liệu
                    $comicsData[] = $clone;
                    }
                    }
                    @endphp

                    @foreach($comicsData as $comic)
                    <div class="group relative flex flex-col">
                        {{-- Stretched link --}}
                        <a href="{{ route('user.comics.show', $comic->slug) }}" class="absolute inset-0 z-30" aria-label="{{ $comic->title }}"></a>

                        {{-- Cover Image --}}
                        <div class="relative aspect-[2/3] rounded-lg overflow-hidden shadow-sm border border-slate-200 group-hover:shadow-md group-hover:border-brand-blue/50 transition-all duration-300">
                            <img src="{{ $comic->cover_url }}" alt="{{ $comic->title }}"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">

                            {{-- Badges --}}
                            <div class="absolute top-1.5 left-1.5 pointer-events-none z-20">
                                <span class="px-1 py-0.5 text-[9px] font-bold bg-black/60 text-white rounded backdrop-blur-sm border border-white/10">
                                    {{ $comic->chapter_count }} ch
                                </span>
                            </div>
                            <div class="absolute top-1.5 right-1.5 pointer-events-none z-20">
                                @if($comic->status === 'ongoing')
                                <div class="w-2 h-2 rounded-full bg-blue-500 shadow-[0_0_5px_rgba(59,130,246,0.8)]"></div>
                                @elseif($comic->status === 'completed')
                                <div class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_5px_rgba(34,197,94,0.8)]"></div>
                                @else
                                <div class="w-2 h-2 rounded-full bg-yellow-500 shadow-[0_0_5px_rgba(234,179,8,0.8)]"></div>
                                @endif
                            </div>

                            {{-- Overlay Stats --}}
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent pt-6 pb-1.5 px-2 flex justify-between items-end text-[9px] text-white/90 pointer-events-none z-20 font-medium">
                                <span class="flex items-center gap-0.5"><i class="fas fa-eye text-[8px]"></i> {{ number_format($comic->views/1000, 1) }}K</span>
                                <span class="flex items-center gap-0.5"><i class="fas fa-heart text-red-400 text-[8px]"></i> {{ number_format($comic->follows) }}</span>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="mt-2 space-y-0.5 relative z-20 pointer-events-none px-0.5">
                            <h3 class="text-[13px] font-bold text-slate-700 line-clamp-2 leading-snug group-hover:text-brand-blue transition-colors min-h-[2.6em]" title="{{ $comic->title }}">
                                {{ $comic->title }}
                            </h3>
                            <div class="flex items-center gap-0.5 text-yellow-400 text-[9px]">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= round($comic->rating_avg) ? '' : 'text-slate-200' }}"></i>
                                    @endfor
                                    <span class="text-slate-400 ml-1">{{ number_format($comic->rating_avg, 1) }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
            </div>

            {{-- Pagination --}}
            <div class="flex justify-center mt-10">
                <div class="flex items-center bg-white rounded-full px-2 py-1.5 border border-slate-200 shadow-sm gap-1">
                    <a href="#" class="w-7 h-7 flex items-center justify-center rounded-full text-slate-400 hover:bg-slate-100 transition-all"><i class="fas fa-chevron-left text-[10px]"></i></a>
                    <a href="#" class="w-7 h-7 flex items-center justify-center rounded-full bg-brand-blue text-white font-bold text-xs shadow-sm">1</a>
                    <a href="#" class="w-7 h-7 flex items-center justify-center rounded-full text-slate-500 hover:bg-slate-100 hover:text-brand-blue transition-all text-xs font-medium">2</a>
                    <a href="#" class="w-7 h-7 flex items-center justify-center rounded-full text-slate-500 hover:bg-slate-100 hover:text-brand-blue transition-all text-xs font-medium">3</a>
                    <span class="w-7 h-7 flex items-center justify-center text-slate-300 font-bold text-[10px]">...</span>
                    <a href="#" class="w-7 h-7 flex items-center justify-center rounded-full text-slate-400 hover:bg-slate-100 transition-all"><i class="fas fa-chevron-right text-[10px]"></i></a>
                </div>
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
                    <div class="group relative mb-4">
                        <label class="text-[11px] font-bold text-slate-400 uppercase mb-1.5 block ml-1 tracking-wide">Tìm kiếm</label>
                        <div class="relative">
                            <input type="text"
                                name="keyword"
                                value="{{ request('keyword') }}"
                                placeholder="Nhập tên truyện..."
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 pl-9 text-xs text-slate-700 font-medium 
                                focus:bg-white focus:border-brand-blue focus:ring-1 focus:ring-brand-blue outline-none transition-all placeholder-slate-400">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs group-focus-within:text-brand-blue transition-colors"></i>
                        </div>
                    </div>

                    {{-- Sort Dropdown --}}
                    <div class="relative mb-4">
                        <label class="text-[11px] font-bold text-slate-400 uppercase mb-1.5 block ml-1 tracking-wide">Sắp xếp</label>
                        <div class="relative group">
                            <select name="sort" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-700 font-medium 
                            focus:bg-white focus:border-brand-blue focus:ring-1 focus:ring-brand-blue outline-none appearance-none cursor-pointer transition-all hover:border-slate-300">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Mới cập nhật</option>
                                <option value="views" {{ request('sort') == 'views' ? 'selected' : '' }}>Xem nhiều nhất</option>
                                <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Đánh giá cao</option>
                                <option value="chapters" {{ request('sort') == 'chapters' ? 'selected' : '' }}>Số chương</option>
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                <i class="fas fa-chevron-down text-slate-400 text-[10px]"></i>
                            </div>
                        </div>
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

                    {{-- Search Button --}}
                    <button type="submit" class="w-full bg-slate-900 hover:bg-brand-blue text-white font-bold py-2.5 rounded-lg uppercase tracking-wide text-xs transition-all duration-200 shadow-md hover:shadow-lg hover:-translate-y-0.5 active:scale-[0.98] flex items-center justify-center gap-2">
                        <i class="fas fa-filter text-[10px]"></i>
                        <span>Áp dụng</span>
                    </button>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection