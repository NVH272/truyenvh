@extends('layouts.app')

@section('title', 'TruyenVH - Đọc Truyện Tranh Online')

@section('content')
<div class="space-y-10">

    {{-- SECTION 1: HERO SLIDER --}}
    <section class="relative group rounded-2xl overflow-hidden shadow-2xl h-[200px] md:h-[360px]">
        <div id="hero-slider" class="w-full h-full relative"></div>

        <button type="button" onclick="changeSlide(-1)"
            class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/20 backdrop-blur-md hover:bg-white/40 rounded-full flex items-center justify-center text-white transition z-20">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button type="button" onclick="changeSlide(1)"
            class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/20 backdrop-blur-md hover:bg-white/40 rounded-full flex items-center justify-center text-white transition z-20">
            <i class="fas fa-chevron-right"></i>
        </button>

        <div id="slider-dots"
            class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-20">
        </div>
    </section>

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

        <div id="trending-list" class="flex gap-4 overflow-x-auto hide-scrollbar scrolling-wrapper pb-4">
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
                <div class="mt-2 space-y-1 relative z-20 pointer-events-none">
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
                        <i class="fas fa-user-edit text-[9px] mr-1"></i> {{ $comic->author ?? 'Đang cập nhật' }}
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
                    <div class="mt-2 space-y-1 relative z-20 pointer-events-none">
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
                            <i class="fas fa-user-edit text-[9px] mr-1"></i> {{ $comic->author ?? 'N/A' }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- RIGHT SIDEBAR --}}
        <aside class="col-span-12 lg:col-span-3 space-y-6">
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

            {{-- TOP LƯỢT XEM --}}
            @include('user.comics.partials.topview')
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
                <button type="button"
                    onclick="scrollSection('genre-{{ $section['slug'] }}', -300)"
                    class="w-6 h-6 rounded border hover:bg-blue-50 text-xs flex items-center justify-center">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button type="button"
                    onclick="scrollSection('genre-{{ $section['slug'] }}', 300)"
                    class="w-6 h-6 rounded border hover:bg-blue-50 text-xs flex items-center justify-center">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <div id="genre-{{ $section['slug'] }}"
            class="flex gap-4 overflow-x-auto hide-scrollbar scrolling-wrapper pb-2">
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
                <div class="mt-2 space-y-1 relative z-20 pointer-events-none">
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
                        <i class="fas fa-user-edit text-[9px] mr-1"></i> {{ $comic->author ?? 'Đang cập nhật' }}
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

@section('scripts')
<script>
    // ====== DỮ LIỆU TỪ LARAVEL ======
    const sliderData = @json($sliderData);
    const trendingData = @json($trendingData);
    const updatesData = @json($updatesData);
    const sidebarData = @json($sidebarData);

    // ====== SLIDER ======
    const sliderContainer = document.getElementById('hero-slider');
    const dotsContainer = document.getElementById('slider-dots');
    let currentSlideIndex = 0;

    function renderSlider() {
        if (!sliderContainer) return;

        sliderContainer.innerHTML = '';
        dotsContainer.innerHTML = '';

        sliderData.forEach((slide, index) => {
            const slideEl = document.createElement('div');
            slideEl.className =
                'absolute inset-0 transition-opacity duration-700 ease-in-out ' +
                (index === currentSlideIndex ? 'opacity-100 z-10' : 'opacity-0 z-0');

            slideEl.innerHTML = `
                <a href="${slide.url}" class="block w-full h-full">
                <img src="${slide.img}" class="w-full h-full object-cover object-top" alt="${slide.title}">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-6 md:p-12 w-full md:w-2/3 text-white">
                    <span class="bg-red-600 text-xs font-bold px-2 py-1 rounded mb-2 inline-block shadow-sm">${slide.badge ?? ''}</span>
                    <h2 class="text-3xl md:text-5xl font-extrabold mb-3 leading-tight drop-shadow-lg">${slide.title}</h2>
                    <p class="text-gray-200 line-clamp-2 drop-shadow-md text-sm md:text-base">${slide.desc ?? ''}</p>
                </div>
                </a>
            `;
            sliderContainer.appendChild(slideEl);

            const dot = document.createElement('button');
            dot.className =
                'w-3 h-3 rounded-full transition-all ' +
                (index === currentSlideIndex ? 'bg-blue-500 w-8' : 'bg-white/50 hover:bg-white');
            dot.onclick = function() {
                currentSlideIndex = index;
                renderSlider();
            };
            dotsContainer.appendChild(dot);
        });
    }

    function changeSlide(direction) {
        if (!sliderData.length) return;
        currentSlideIndex = (currentSlideIndex + direction + sliderData.length) % sliderData.length;
        renderSlider();
    }

    if (sliderData.length) {
        renderSlider();
        setInterval(function() {
            changeSlide(1);
        }, 5000);
    }

    // ====== TOP THỊNH HÀNH ======
    const trendingContainer = document.getElementById('trending-list');
    if (trendingContainer) {
        trendingData.forEach(function(item, index) {
            const rankClass =
                index === 0 ? 'rank-1' :
                index === 1 ? 'rank-2' :
                index === 2 ? 'rank-3' : 'rank-other';

            const wrapper = document.createElement('div');
            wrapper.className = 'flex-none w-[160px] md:w-[180px] group cursor-pointer relative';

            wrapper.innerHTML = `
                <a href="${item.url}" class="block">
                <div class="aspect-[2/3] rounded-lg overflow-hidden shadow-md relative mb-3">
                    <img src="${item.img}" class="w-full h-full object-cover" alt="${item.title}">
                    <span class="rank-text ${rankClass}">${index + 1}</span>
                </div>
                <h3 class="font-bold text-sm text-gray-800 line-clamp-1 group-hover:text-blue-600 transition">${item.title}</h3>
                <div class="flex justify-between text-xs text-gray-500 mt-1">
                    <span>Chap ${item.chap}</span>
                    <span><i class="fas fa-eye"></i> ${item.view}</span>
                </div>
                </a>
            `;
            trendingContainer.appendChild(wrapper);
        });
    }

    // ====== MỚI CẬP NHẬT ======
    const updatesContainer = document.getElementById('updates-grid');
    if (updatesContainer) {
        updatesData.forEach(function(item) {
            const card = document.createElement('div');
            card.className = 'group';

            let chapsHtml = '';
            item.chaps.forEach(function(chap) {
                chapsHtml += `
                    <div class="flex justify-between items-center text-xs">
                        <a href="${item.url}" class="bg-gray-100 hover:bg-blue-100 px-2 py-0.5 rounded text-gray-600 font-semibold transition">Chap ${chap.num}</a>
                        <span class="text-gray-400 italic text-[10px]">${chap.time}</span>
                    </div>
                `;
            });

            card.innerHTML = `
                <a href="${item.url}" class="block">
                <div class="relative rounded-lg overflow-hidden shadow-sm aspect-[2/3] mb-2 cursor-pointer">
                    ${item.isHot ? '<span class="absolute top-2 left-2 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow-sm z-10">HOT</span>' : ''}
                    <img src="${item.img}" class="w-full h-full object-cover" alt="${item.title}">
                    <div class="absolute bottom-0 w-full p-2 bg-gradient-to-t from-black/80 via-black/40 to-transparent flex justify-between text-white/90 text-[10px]">
                        <span><i class="fas fa-eye"></i> 15K</span>
                        <span><i class="fas fa-heart"></i> 200</span>
                    </div>
                </div>
                <h4 class="font-bold text-sm text-slate-800 line-clamp-1 group-hover:text-blue-600 transition cursor-pointer">${item.title}</h4>
                </a>
                <div class="mt-2 space-y-1">
                    ${chapsHtml}
                </div>
            `;
            updatesContainer.appendChild(card);
        });
    }

    // ====== SIDEBAR TOP THEO DÕI ======
    const sidebarContainer = document.getElementById('sidebar-list');
    if (sidebarContainer) {
        sidebarData.forEach(function(item, index) {
            const numColor = index < 3 ? 'text-blue-600' : 'text-gray-300';
            const row = document.createElement('div');
            row.className =
                'flex gap-3 items-center border-b border-gray-50 last:border-0 pb-2 cursor-pointer group';

            row.innerHTML = `
                <a href="${item.url}" class="flex gap-3 items-center w-full">
                <span class="text-xl font-black ${numColor} w-6 text-center italic">0${index + 1}</span>
                <div class="w-12 h-16 rounded overflow-hidden flex-shrink-0 shadow-sm">
                    <img src="${item.img}" class="w-full h-full object-cover" alt="${item.title}">
                </div>
                <div class="flex-1">
                    <h5 class="text-xs font-bold text-gray-700 line-clamp-1 group-hover:text-blue-600 transition">${item.title}</h5>
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-[10px] text-gray-500">Chap ${item.chap}</span>
                        <span class="text-[10px] text-gray-400"><i class="fas fa-eye"></i> ${item.view}</span>
                    </div>
                </div>
                </a>
            `;
            sidebarContainer.appendChild(row);
        });
    }

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

        if (!sidebarContainer) return;
        sidebarContainer.style.opacity = '0.5';
        setTimeout(function() {
            sidebarContainer.style.opacity = '1';
        }, 200);
    }
    window.switchTab = switchTab;

    // expose slider controls
    window.changeSlide = changeSlide;
</script>
@endsection