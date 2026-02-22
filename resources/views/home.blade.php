@extends('layouts.app')

@section('title', 'TruyenVH - Đọc Truyện Tranh Online')

@section('content')

<style>
    /* Tùy chỉnh thanh cuộn ngang cho các hàng truyện */
    .scrolling-wrapper {
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: transparent transparent;
        transition: scrollbar-color 0.3s ease;
    }

    /* QUAN TRỌNG: Ngăn trình duyệt hiểu nhầm việc nhấp vào truyện là đang muốn "kéo thả file/link" */
    .scrolling-wrapper a,
    .scrolling-wrapper img {
        -webkit-user-drag: none;
        -khtml-user-drag: none;
        -moz-user-drag: none;
        -o-user-drag: none;
        user-drag: none;
    }

    /* Scrollbar ngang: luôn hiện mờ, khi hover thì đậm hơn để dễ nhìn */
    .scrolling-wrapper {
        scrollbar-color: #cbd5e1 transparent;
    }

    .scrolling-wrapper::-webkit-scrollbar {
        height: 6px;
        width: 6px;
    }

    .scrolling-wrapper::-webkit-scrollbar-track {
        background: transparent;
    }

    .scrolling-wrapper::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 10px;
        transition: background-color 0.3s ease;
    }

    .scrolling-wrapper:hover::-webkit-scrollbar-thumb {
        background-color: #94a3b8;
    }
</style>

<div class="space-y-10">

    {{-- SECTION 1: HERO SLIDER (LIGHT MODE - MIMI STYLE) --}}
    <section class="relative group rounded-3xl overflow-hidden shadow-xl border border-slate-200 bg-white h-[400px] md:h-[450px]">

        {{-- Container chứa các slides --}}
        <div id="hero-slider" class="w-full h-full relative">
            {{-- Slides sẽ được Javascript render vào đây --}}
        </div>

        {{-- Nút điều hướng (Style Light Mode: Trắng, bóng đổ) --}}
        <button type="button" onclick="changeSlide(-1)"
            class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/80 backdrop-blur-sm hover:bg-white text-slate-700 hover:text-blue-600 rounded-full flex items-center justify-center shadow-lg border border-slate-100 transition-all z-30 opacity-0 group-hover:opacity-100 duration-300 hover:scale-110">
            <i class="fas fa-chevron-left text-lg"></i>
        </button>
        <button type="button" onclick="changeSlide(1)"
            class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/80 backdrop-blur-sm hover:bg-white text-slate-700 hover:text-blue-600 rounded-full flex items-center justify-center shadow-lg border border-slate-100 transition-all z-30 opacity-0 group-hover:opacity-100 duration-300 hover:scale-110">
            <i class="fas fa-chevron-right text-lg"></i>
        </button>

        {{-- Dots navigation --}}
        <div id="slider-dots" class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2 z-30"></div>
    </section>

    {{-- SCRIPT XỬ LÝ SLIDER --}}
    <script>
        const sliderData = @json($sliderData);
        let currentIndex = 0;
        const sliderContainer = document.getElementById('hero-slider');
        const dotsContainer = document.getElementById('slider-dots');
        let autoPlayInterval;

        function renderSlider() {
            if (!sliderData || sliderData.length === 0) return;

            sliderContainer.innerHTML = '';
            dotsContainer.innerHTML = '';

            sliderData.forEach((item, index) => {
                // A. Tạo Slide Item
                const slide = document.createElement('div');
                // Transition effect: Fade in/out
                slide.className = `absolute inset-0 transition-opacity duration-700 ease-in-out ${index === 0 ? 'opacity-100 z-20' : 'opacity-0 z-10 pointer-events-none'}`;
                slide.id = `slide-${index}`;

                // Tạo danh sách HTML cho Genres (Thể loại)
                const genresHtml = item.genres && item.genres.length > 0 ?
                    item.genres.map(g => `<span class="px-2.5 py-1 rounded-md bg-white/60 border border-slate-200 text-slate-600 text-xs font-semibold shadow-sm">${g}</span>`).join('') :
                    '';

                slide.innerHTML = `
                <div class="absolute inset-0 z-0">
                    <img src="${item.img}" class="w-full h-full object-cover opacity-90">
                </div>

                <div class="absolute inset-0 z-10 bg-gradient-to-r from-white/80 via-white/50 to-transparent/10"></div>

                <div class="absolute inset-0 z-20 flex items-center p-6 md:p-12 gap-8 md:gap-12">
                    
                    <div class="hidden md:block flex-shrink-0 w-[240px] h-[340px] relative group/poster">
                        <div class="absolute inset-0 bg-blue-600 rounded-xl rotate-3 opacity-20 group-hover/poster:rotate-6 transition-transform duration-500"></div>
                        <img src="${item.img}" alt="${item.title}" 
                             class="w-full h-full object-cover rounded-xl shadow-2xl relative z-10 transform group-hover/poster:-translate-y-2 transition-transform duration-500 border border-slate-100">
                        
                        <div class="absolute -top-3 -left-3 z-20 bg-red-600 text-white text-sm font-extrabold px-3 py-1.5 rounded-lg shadow-lg rotate-[-10deg]">
                            #${index + 1}
                        </div>
                    </div>

                    <div class="flex-1 flex flex-col justify-center max-w-2xl space-y-4">
                        
                        <h2 class="text-3xl md:text-5xl font-extrabold text-slate-800 leading-tight drop-shadow-sm line-clamp-2">
                            <a href="${item.url}" class="hover:text-blue-600 transition-colors">
                                ${item.title}
                            </a>
                        </h2>

                        <div class="flex flex-wrap gap-2">
                            ${genresHtml}
                        </div>

                        <p class="text-slate-600 text-sm md:text-base leading-relaxed line-clamp-3 md:line-clamp-4 bg-white/50 p-3 rounded-lg border border-slate-100/50 backdrop-blur-sm">
                            ${item.desc}
                        </p>

                        <div class="pt-2">
                            <a href="${item.url}" 
                               class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 transition-all hover:-translate-y-1 hover:shadow-blue-500/50">
                                <i class="fas fa-book-open"></i>
                                <span>Đọc Ngay</span>
                            </a>
                        </div>
                    </div>
                </div>
            `;
                sliderContainer.appendChild(slide);

                // B. Tạo Dot
                const dot = document.createElement('button');
                // Dot style: Thanh dài khi active, tròn khi inactive
                dot.className = `h-2 rounded-full transition-all duration-500 ${index === 0 ? 'w-8 bg-blue-600' : 'w-2 bg-slate-300 hover:bg-slate-400'}`;
                dot.onclick = () => goToSlide(index);
                dotsContainer.appendChild(dot);
            });
        }

        function showSlide(index) {
            const slides = sliderContainer.children;
            const dots = dotsContainer.children;

            for (let i = 0; i < slides.length; i++) {
                // Xử lý Slide
                if (i === index) {
                    slides[i].classList.remove('opacity-0', 'pointer-events-none');
                    slides[i].classList.add('opacity-100');
                } else {
                    slides[i].classList.remove('opacity-100');
                    slides[i].classList.add('opacity-0', 'pointer-events-none');
                }

                // Xử lý Dot (Active là thanh dài, Inactive là chấm tròn)
                if (i === index) {
                    dots[i].classList.remove('w-2', 'bg-slate-300', 'hover:bg-slate-400');
                    dots[i].classList.add('w-8', 'bg-blue-600');
                } else {
                    dots[i].classList.remove('w-8', 'bg-blue-600');
                    dots[i].classList.add('w-2', 'bg-slate-300', 'hover:bg-slate-400');
                }
            }
        }

        function changeSlide(direction) {
            currentIndex += direction;
            if (currentIndex >= sliderData.length) currentIndex = 0;
            if (currentIndex < 0) currentIndex = sliderData.length - 1;
            showSlide(currentIndex);
            resetAutoPlay();
        }

        function goToSlide(index) {
            currentIndex = index;
            showSlide(currentIndex);
            resetAutoPlay();
        }

        function startAutoPlay() {
            autoPlayInterval = setInterval(() => {
                changeSlide(1);
            }, 7500); // 5 giây
        }

        function resetAutoPlay() {
            clearInterval(autoPlayInterval);
            startAutoPlay();
        }

        document.addEventListener('DOMContentLoaded', () => {
            renderSlider();
            startAutoPlay();
        });
    </script>

    {{-- SECTION 2: TOP THỊNH HÀNH (DESIGN Y HỆT GENRE CARD) --}}
    <section>
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-200">
            <h2 class="text-xl md:text-2xl font-extrabold text-blue-700 uppercase flex items-center gap-2">
                <i class="fas fa-fire text-red-500"></i> Top thịnh hành
            </h2>
            <div class="flex gap-2">
                <button type="button" onclick="scrollSection('trending-list', -300)" class="w-8 h-8 rounded border border-gray-300 hover:bg-blue-50 text-gray-500 hover:text-blue-600 transition flex items-center justify-center"><i class="fas fa-chevron-left"></i></button>
                <button type="button" onclick="scrollSection('trending-list', 300)" class="w-8 h-8 rounded border border-gray-300 hover:bg-blue-50 text-gray-500 hover:text-blue-600 transition flex items-center justify-center"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>

        <div id="trending-list" class="flex gap-4 overflow-x-auto scrolling-wrapper pb-4">
            @foreach($trendingComics as $index => $comic)
            {{-- Card giống hệt Genre: w-[140px] --}}
            <div class="flex-none w-[140px] group relative bg-transparent rounded-md overflow-hidden transition-all duration-300 hover:-translate-y-1">

                <a href="{{ route('user.comics.show', $comic->slug) }}" class="absolute inset-0 z-10" aria-label="{{ $comic->title }}"></a>

                {{-- Ảnh bìa aspect-[2/3] --}}
                <div class="relative aspect-[2/3] rounded-md overflow-hidden shadow-md border border-slate-700/50 group-hover:shadow-lg group-hover:border-blue-500/50 transition-all">
                    <img src="{{ $comic->cover_url }}" alt="{{ $comic->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">

                    {{-- Rank Badge (Chỉ khác biệt phần này là có thêm số thứ tự) --}}
                    @php
                    $rankClass = match($index) {
                    0 => 'bg-red-600',
                    1 => 'bg-orange-500',
                    2 => 'bg-yellow-500',
                    default => 'bg-gray-800/80'
                    };
                    @endphp
                    <span class="absolute top-0 right-0 {{ $rankClass }} text-white px-2 py-0.5 text-[10px] font-bold rounded-bl-md shadow-sm z-20">#{{ $index + 1 }}</span>

                    {{-- Stats Overlay --}}
                    <div class="absolute inset-x-0 bottom-0 pt-3 pb-1.5 px-2 bg-gradient-to-t from-black/80 via-black/40 to-transparent flex justify-between items-end text-[10px] text-white/90 pointer-events-none z-20">
                        <span class="flex items-center gap-1"><i class="fas fa-eye text-[9px]"></i> {{ number_format($comic->views) }}</span>
                        <span class="flex items-center gap-1"><i class="fas fa-heart text-red-400 text-[9px]"></i> {{ number_format($comic->follows) }}</span>
                    </div>
                </div>

                {{-- Content Below --}}
                <div class="mt-2 space-y-1 relative z-20">
                    <h3 class="text-[13px] font-bold text-slate-800 line-clamp-2 leading-snug group-hover:text-blue-600 transition-colors" title="{{ $comic->title }}">{{ $comic->title }}</h3>
                    <div class="text-[11px] text-slate-500">{{ $comic->chapter_count }} chương</div>
                    {{-- Rating (read-only) --}}
                    @php
                    $avgRating = $comic->rating_avg ?? $comic->rating ?? 0;
                    $ratingCount = $comic->reviews_count ?? $comic->rating_count ?? 0;
                    @endphp
                    <div class="flex items-center gap-0.5 text-[10px]">
                        <x-rating-stars :rating="$avgRating" class="shrink-0" />
                        <span class="text-slate-500 ml-1">({{ number_format($avgRating, 1) }} • {{ $ratingCount }})</span>
                    </div>
                    {{-- Author --}}
                    <div class="text-[11px] text-slate-500 truncate" title="Tác giả">
                        <i class="fas fa-user-edit text-[9px] mr-1"></i> {{ $comic->authors_list ?? 'Đang cập nhật' }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    {{-- SECTION 3: MỚI CẬP NHẬT + SIDEBAR --}}
    <div class="grid grid-cols-12 gap-8">
        {{-- SECTION: MỚI CẬP NHẬT --}}
        <div class="col-span-12 lg:col-span-9">
            <div class="flex items-center justify-between mb-4 border-b border-gray-200 pb-2">
                <h3 class="text-xl font-extrabold text-blue-700 uppercase flex items-center gap-2">
                    <i class="fas fa-clock text-blue-500"></i> Mới cập nhật
                </h3>
                <a href="{{ route('home') }}" class="text-xs font-semibold text-gray-400 hover:text-blue-600">
                    Xem tất cả <i class="fas fa-angle-double-right"></i>
                </a>
            </div>

            {{-- Grid 30 items --}}
            <div id="updates-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-x-4 gap-y-6">
                @foreach($newUpdateComics as $comic)
                <div class="group relative bg-transparent rounded-md overflow-hidden transition-all duration-300 hover:-translate-y-1">

                    <a href="{{ route('user.comics.show', $comic->slug) }}" class="absolute inset-0 z-10" aria-label="{{ $comic->title }}"></a>

                    {{-- Ảnh bìa --}}
                    <div class="relative aspect-[2/3] rounded-md overflow-hidden shadow-md border border-slate-700/50 group-hover:shadow-lg group-hover:border-blue-500/50 transition-all">
                        <img src="{{ $comic->cover_url }}" alt="{{ $comic->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">

                        {{-- Badge UP --}}
                        <div class="absolute top-1.5 left-1.5 pointer-events-none z-20">
                            <span class="px-1.5 py-0.5 text-[9px] font-bold bg-green-600/90 text-white rounded shadow-sm">UP</span>
                        </div>

                        {{-- Stats Overlay --}}
                        <div class="absolute inset-x-0 bottom-0 pt-3 pb-1.5 px-2 bg-gradient-to-t from-black/80 via-black/40 to-transparent flex justify-between items-end text-[10px] text-white/90 pointer-events-none z-20">
                            <span class="flex items-center gap-1"><i class="fas fa-eye text-[9px]"></i> {{ number_format($comic->views) }}</span>
                            <span class="flex items-center gap-1"><i class="fas fa-heart text-red-400 text-[9px]"></i> {{ number_format($comic->follows) }}</span>
                        </div>
                    </div>

                    {{-- Thông tin --}}
                    <div class="mt-2 space-y-1 relative z-20">
                        <h3 class="text-[13px] font-bold text-slate-800 line-clamp-2 leading-snug group-hover:text-blue-600 transition-colors" title="{{ $comic->title }}">
                            {{ $comic->title }}
                        </h3>
                        <div class="flex justify-between items-center">
                            <div class="text-[11px] text-slate-500">{{ $comic->chapter_count }} chương</div>
                            {{-- SỬA Ở ĐÂY: Dùng last_chapter_at thay vì updated_at --}}
                            <div class="text-[10px] text-gray-400 italic">
                                {{ \Carbon\Carbon::parse($comic->last_chapter_at)->diffForHumans(null, true, true) }}
                            </div>
                        </div>
                        <div class="text-[11px] text-slate-500 truncate">
                            <i class="fas fa-user-edit text-[9px] mr-1"></i> {{ $comic->authors_list ?? 'N/A' }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- RIGHT SIDEBAR --}}
        <aside class="col-span-12 lg:col-span-3 space-y-6">

            {{-- TOP LƯỢT XEM --}}
            @include('user.comics.partials.topview')

            {{-- TOP THEO DÕI --}}
            <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
                {{-- HEADER --}}
                <div class="px-5 py-3 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 text-base border-l-4 border-blue-600 pl-3 uppercase">
                        Top Theo Dõi
                    </h3>
                    {{-- Tabs --}}
                    <div class="flex gap-1">
                        <button type="button" onclick="switchTab('day')" id="tab-day"
                            class="text-[10px] px-2 py-0.5 bg-blue-600 text-white rounded-full tab-btn transition-colors">
                            Ngày
                        </button>
                        <button type="button" onclick="switchTab('week')" id="tab-week"
                            class="text-[10px] px-2 py-0.5 bg-gray-200 text-gray-500 rounded-full hover:bg-gray-300 tab-btn transition-colors">
                            Tuần
                        </button>
                        <button type="button" onclick="switchTab('month')" id="tab-month"
                            class="text-[10px] px-2 py-0.5 bg-gray-200 text-gray-500 rounded-full hover:bg-gray-300 tab-btn transition-colors">
                            Tháng
                        </button>
                    </div>
                </div>

                {{-- LIST --}}
                <div id="sidebar-list" class="divide-y divide-gray-100">
                    @foreach($topFollowComics as $index => $comic)
                    <div class="flex items-center gap-3 p-3 hover:bg-gray-50 transition-colors cursor-pointer group">

                        {{-- Thứ hạng --}}
                        <span class="w-6 h-6 flex-shrink-0 flex items-center justify-center rounded-full
                        {{ $index < 3 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }}
                        text-xs font-bold">
                            {{ $index + 1 }}
                        </span>

                        {{-- Ảnh bìa (giữ nguyên tỷ lệ và kích thước như mẫu bạn đưa) --}}
                        <div class="w-12 h-16 flex-shrink-0 rounded overflow-hidden border border-gray-200 shadow-sm">
                            <a href="{{ route('user.comics.show', $comic->slug) }}" class="block w-full h-full">
                                <img src="{{ $comic->cover_url }}"
                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                                    alt="{{ $comic->title }}">
                            </a>
                        </div>

                        {{-- Thông tin --}}
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-gray-800 group-hover:text-blue-600 truncate transition-colors">
                                <a href="{{ route('user.comics.show', $comic->slug) }}">
                                    {{ $comic->title }}
                                </a>
                            </h4>

                            <div class="flex justify-between items-center mt-1.5">
                                {{-- Số chương --}}
                                <span class="text-[10px] bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded">
                                    {{ $comic->chapter_count }} chương
                                </span>

                                {{-- Lượt theo dõi --}}
                                <span class="text-xs text-gray-500 flex items-center gap-1">
                                    <i class="fas fa-heart text-red-500 text-[10px]"></i>
                                    {{ number_format($comic->follows) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>

    {{-- SECTION 4: CÁC HÀNG TRUYỆN THEO THỂ LOẠI --}}
    @foreach($genreSections as $section)
    <section class="mt-4">
        <div class="flex items-center justify-between mb-4 pb-2 border-b-2 border-blue-100">
            <h2 class="text-lg md:text-xl font-extrabold text-blue-700 uppercase">
                {{ $section['label'] }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('user.comics.filter', ['categories[0]' => $section['slug']]) }}" class="text-xs font-semibold text-gray-400 hover:text-blue-600">
                    Xem tất cả <i class="fas fa-angle-double-right"></i>
                </a>
            </div>
        </div>

        <div id="genre-{{ $section['slug'] }}"
            class="flex gap-4 overflow-x-auto scrolling-wrapper pb-2">
            @forelse($section['comics'] as $comic)
            <div class="flex-none w-[140px] group relative bg-transparent rounded-md overflow-hidden transition-all duration-300 hover:-translate-y-1">

                {{-- Stretched link phủ toàn bộ thẻ --}}
                <a href="{{ route('user.comics.show', $comic->slug) }}" class="absolute inset-0 z-10" aria-label="{{ $comic->title }}"></a>

                {{-- Cover Image Wrapper --}}
                <div class="relative aspect-[2/3] rounded-md overflow-hidden shadow-md border border-slate-700/50 group-hover:shadow-lg group-hover:border-blue-500/50 transition-all">
                    <img src="{{ $comic->cover_url }}" alt="{{ $comic->title }}"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">

                    {{-- Badge: Status (Top Left) --}}
                    <div class="absolute top-1.5 left-1.5 pointer-events-none z-20">
                        @if($comic->status === 'ongoing')
                        <span class="px-1.5 py-0.5 text-[9px] font-bold bg-blue-600/90 text-white rounded shadow-sm">Đang tiến hành</span>
                        @elseif($comic->status === 'completed')
                        <span class="px-1.5 py-0.5 text-[9px] font-bold bg-green-600/90 text-white rounded shadow-sm">Hoàn thành</span>
                        @else
                        <span class="px-1.5 py-0.5 text-[9px] font-bold bg-yellow-600/90 text-white rounded shadow-sm">Tạm dừng</span>
                        @endif
                    </div>

                    {{-- Overlay Stats (Bottom on Image - bỏ nền đen, chỉ giữ text) --}}
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
                <div class="mt-2 space-y-1 relative z-20">
                    <h3 class="text-[13px] font-bold text-slate-800 dark:text-slate-200 line-clamp-2 leading-snug group-hover:text-blue-600 transition-colors"
                        title="{{ $comic->title }}">
                        {{ $comic->title }}
                    </h3>

                    {{-- Chapter count (dưới tên truyện) --}}
                    <div class="text-[11px] text-slate-500">
                        {{ $comic->chapter_count ?? 0 }} chương
                    </div>

                    {{-- Rating (read-only) --}}
                    @php
                    $avgRating = $comic->rating_avg ?? $comic->rating ?? 0;
                    $ratingCount = $comic->reviews_count ?? $comic->rating_count ?? 0;
                    @endphp
                    <div class="flex items-center gap-0.5 text-[10px]">
                        <x-rating-stars :rating="$avgRating" class="shrink-0" />
                        <span class="text-slate-500 ml-1">({{ number_format($avgRating, 1) }} • {{ $ratingCount }})</span>
                    </div>

                    {{-- Author --}}
                    <div class="text-[11px] text-slate-500 truncate" title="Tác giả">
                        <i class="fas fa-user-edit text-[9px] mr-1"></i> {{ $comic->authors_list ?? 'Đang cập nhật' }}
                    </div>
                </div>
            </div>
            @empty
            <p class="text-xs text-gray-400 italic">
                Chưa có truyện cho thể loại này.
            </p>
            @endforelse
        </div>
    </section>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
    // ====== HÀM SCROLL CHUNG ======
    function scrollSection(id, distance) {
        const container = document.getElementById(id);
        if (!container) return;
        container.scrollBy({
            left: distance,
            behavior: 'smooth'
        });
    }
    window.scrollSection = scrollSection;

    // ====== TAB SIDEBAR ======
    function switchTab(tab) {
        document.querySelectorAll('.tab-btn').forEach(function(btn) {
            btn.className =
                'flex-1 py-1 text-[11px] font-bold rounded text-center text-gray-500 hover:text-gray-700 tab-btn transition';
        });
        const activeBtn = document.getElementById('tab-' + tab);
        if (activeBtn) {
            activeBtn.className =
                'flex-1 py-1 text-[11px] font-bold rounded text-center bg-white shadow-sm text-blue-600 tab-btn transition';
        }

        var sidebarList = document.getElementById('sidebar-list');
        if (!sidebarList) return;
        sidebarList.style.opacity = '0.5';
        setTimeout(function() {
            if (sidebarList) sidebarList.style.opacity = '1';
        }, 200);
    }
    window.switchTab = switchTab;

    // expose slider controls
    window.changeSlide = changeSlide;

    // ========================================================
    // CUỘN CHUỘT NGANG CHO CÁC HÀNG TRUYỆN (chỉ khi có overflow + chuột đang ở trong hàng)
    // - Chuột ngoài hàng truyện hoặc hàng không dài → cuộn trang lên/xuống bình thường.
    // - Chuột đưa vào hàng truyện và hàng có scrollbar ngang → lăn wheel = cuộn ngang hàng đó.
    // ========================================================
    (function initHorizontalWheelScroll() {
        document.addEventListener('wheel', function(e) {
            var slider = e.target && e.target.closest && e.target.closest('.scrolling-wrapper');
            if (!slider) return;
            if (Math.abs(e.deltaY) <= Math.abs(e.deltaX)) return;
            var canScrollH = slider.scrollWidth > slider.clientWidth;
            if (!canScrollH) return;
            e.preventDefault();
            e.stopPropagation();
            slider.scrollLeft += e.deltaY;
        }, { passive: false, capture: true });
    })();
</script>
@endpush