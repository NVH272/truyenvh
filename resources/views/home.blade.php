@extends('layouts.app')

@section('title', 'TruyenVH - Đọc Truyện Tranh Online')

@section('content')
<!-- SECTION 1: HERO SLIDER (Hoạt động) -->
<section class="relative group rounded-2xl overflow-hidden shadow-2xl h-[200px] md:h-[380px]">
    <!-- Slides Container -->
    <div id="hero-slider" class="w-full h-full relative">
        <!-- Javascript will inject slides here -->
    </div>

    <!-- Slider Controls -->
    <button onclick="changeSlide(-1)" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/20 backdrop-blur-md hover:bg-white/40 rounded-full flex items-center justify-center text-white transition z-20">
        <i class="fas fa-chevron-left"></i>
    </button>
    <button onclick="changeSlide(1)" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/20 backdrop-blur-md hover:bg-white/40 rounded-full flex items-center justify-center text-white transition z-20">
        <i class="fas fa-chevron-right"></i>
    </button>

    <!-- Dots -->
    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-20" id="slider-dots">
        <!-- JS will inject dots -->
    </div>
</section>

<!-- SECTION 2: TOP TRENDING (Carousel Hoạt động) -->
<section>
    <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-200">
        <h2 class="text-2xl font-extrabold text-blue-700 uppercase flex items-center gap-2">
            <i class="fas fa-fire text-red-500"></i> Top Thịnh Hành
        </h2>
        <div class="flex gap-2">
            <button onclick="scrollSection('trending-list', -300)" class="w-8 h-8 rounded border border-gray-300 hover:bg-blue-50 text-gray-500 hover:text-blue-600 transition flex items-center justify-center">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button onclick="scrollSection('trending-list', 300)" class="w-8 h-8 rounded border border-gray-300 hover:bg-blue-50 text-gray-500 hover:text-blue-600 transition flex items-center justify-center">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <div id="trending-list" class="flex gap-4 overflow-x-auto hide-scrollbar scrolling-wrapper pb-4">
        <!-- JS Will Render Trending Items Here -->
    </div>
</section>

<!-- SECTION 3: CONTENT & SIDEBAR -->
<div class="grid grid-cols-12 gap-8">

    <!-- LEFT: NEW UPDATES -->
    <div class="col-span-12 lg:col-span-9">
        <div class="flex items-center justify-between mb-4 border-b border-gray-200 pb-2">
            <h3 class="text-xl font-extrabold text-blue-700 uppercase flex items-center gap-2">
                <i class="fas fa-clock text-blue-500"></i> Mới Cập Nhật
            </h3>
            <a href="#" class="text-xs font-semibold text-gray-400 hover:text-blue-600">Xem tất cả <i class="fas fa-angle-double-right"></i></a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-x-4 gap-y-8" id="updates-grid">
            <!-- JS Will Render Update Items Here -->
        </div>

        <div class="mt-8 flex justify-center">
            <button class="px-8 py-2 bg-white border-2 border-blue-600 text-blue-600 font-bold rounded-full hover:bg-blue-600 hover:text-white transition shadow-sm">
                Xem thêm
            </button>
        </div>
    </div>

    <!-- RIGHT: SIDEBAR -->
    <aside class="col-span-12 lg:col-span-3 space-y-6">
        <!-- Top Followed Widget -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <h4 class="font-bold text-blue-700 uppercase text-sm mb-3">Top Theo Dõi</h4>
                <div class="flex bg-gray-100 p-1 rounded-lg">
                    <button onclick="switchTab('day')" class="flex-1 py-1 text-[11px] font-bold rounded text-center bg-white shadow-sm text-blue-600 tab-btn" id="tab-day">Ngày</button>
                    <button onclick="switchTab('week')" class="flex-1 py-1 text-[11px] font-bold rounded text-center text-gray-500 hover:text-gray-700 tab-btn" id="tab-week">Tuần</button>
                    <button onclick="switchTab('month')" class="flex-1 py-1 text-[11px] font-bold rounded text-center text-gray-500 hover:text-gray-700 tab-btn" id="tab-month">Tháng</button>
                </div>
            </div>

            <div class="p-2 space-y-2" id="sidebar-list">
                <!-- JS renders content -->
            </div>
        </div>

        <!-- History -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <h4 class="font-bold text-gray-700 uppercase text-sm mb-3 border-b pb-2">Lịch sử đọc</h4>
            <div class="text-center py-4 text-gray-400 text-xs">
                <i class="fas fa-history text-2xl mb-2 block"></i>
                Chưa có lịch sử nào
            </div>
        </div>
    </aside>
</div>

<!-- SECTION 4: GENRE SECTION (Example) -->
<section>
    <div class="flex items-center justify-between mb-4 pb-2 border-b-2 border-purple-500">
        <h2 class="text-xl font-extrabold text-purple-700 uppercase">Truyện Con Gái <i class="fas fa-heart text-pink-500 ml-1"></i></h2>
        <div class="flex gap-2">
            <button onclick="scrollSection('manhwa-list', -300)" class="w-6 h-6 rounded border hover:bg-purple-50"><i class="fas fa-chevron-left text-xs"></i></button>
            <button onclick="scrollSection('manhwa-list', 300)" class="w-6 h-6 rounded border hover:bg-purple-50"><i class="fas fa-chevron-right text-xs"></i></button>
        </div>
    </div>
    <div id="manhwa-list" class="flex gap-4 overflow-x-auto hide-scrollbar scrolling-wrapper pb-2">
        <!-- JS Will Render -->
    </div>
</section>
@endsection

@section('scripts')
<!-- JAVASCRIPT LOGIC -->
<script>
    // --- 1. DỮ LIỆU MẪU (Giả lập Database từ Laravel) ---
    const sliderData = [{
            title: "Võ Luyện Đỉnh Phong",
            desc: "Vũ chi đỉnh, là cô độc, là tịch mịch...",
            img: "https://images.unsplash.com/photo-1541562232579-512a21360020?q=80&w=1000&auto=format&fit=crop",
            badge: "Hot Nhất"
        },
        {
            title: "Toàn Trí Độc Giả",
            desc: "Chỉ một mình tôi biết kết cục của thế giới này.",
            img: "https://images.unsplash.com/photo-1613376023733-0a73315d9b06?q=80&w=1000&auto=format&fit=crop",
            badge: "Siêu Phẩm"
        },
        {
            title: "Thanh Gươm Diệt Quỷ",
            desc: "Cuộc hành trình diệt quỷ cứu em gái.",
            img: "https://images.unsplash.com/photo-1578632767115-351597cf2477?q=80&w=1000&auto=format&fit=crop",
            badge: "Top View"
        }
    ];

    const trendingData = [{
            id: 1,
            title: "Solo Leveling",
            chap: 179,
            view: "1.2M",
            img: "https://m.media-amazon.com/images/M/MV5BMmMzYzlmZjMtMDM5ZS00MDE0LTgyZjYtYjliM2Y3NzdkZWEzXkEyXkFqcGdeQXVyMzgxODM4NjM@._V1_FMjpg_UX1000_.jpg"
        },
        {
            id: 2,
            title: "One Piece",
            chap: 1092,
            view: "2.1M",
            img: "https://m.media-amazon.com/images/M/MV5BODcwNWE3OTMtMDc3MS00NDFjLWE1OTAtNDU3NjgxODMxY2UyXkEyXkFqcGdeQXVyMzgxODM4NjM@._V1_FMjpg_UX1000_.jpg"
        },
        {
            id: 3,
            title: "Jujutsu Kaisen",
            chap: 236,
            view: "900K",
            img: "https://m.media-amazon.com/images/M/MV5BNjRiNmNjMWEtMzMyBS00NmIyLThjY2QtMTUyMzZkYzMyNzBiXkEyXkFqcGdeQXVyMzgxODM4NjM@._V1_FMjpg_UX1000_.jpg"
        },
        {
            id: 4,
            title: "Blue Lock",
            chap: 250,
            view: "850K",
            img: "https://upload.wikimedia.org/wikipedia/vi/thumb/e/e0/Chainsaw_man_vol_1.jpg/220px-Chainsaw_man_vol_1.jpg"
        },
        {
            id: 5,
            title: "Kingdom",
            chap: 780,
            view: "500K",
            img: "https://upload.wikimedia.org/wikipedia/vi/9/91/Kingdom_manga_vol_1.jpg"
        },
        {
            id: 6,
            title: "Naruto",
            chap: 700,
            view: "5M",
            img: "https://upload.wikimedia.org/wikipedia/en/9/94/NarutoCoverTankobon1.jpg"
        }
    ];

    const updatesData = Array(15).fill(null).map((_, i) => ({
        title: i % 2 === 0 ? "Ta Là Tà Đế" : "Đại Quản Gia Là Ma Hoàng",
        img: i % 2 === 0 ? "https://i.pinimg.com/736x/21/5d/93/215d9333333333333333333333333333.jpg" : "https://via.placeholder.com/200x300?text=Comic",
        chaps: [{
                num: 450,
                time: "10 phút"
            },
            {
                num: 449,
                time: "1 giờ"
            }
        ],
        isHot: i < 3
    }));

    // --- 2. LOGIC RENDER & SLIDER ---

    // Render Slider
    const sliderContainer = document.getElementById('hero-slider');
    const dotsContainer = document.getElementById('slider-dots');
    let currentSlideIndex = 0;

    function renderSlider() {
        sliderContainer.innerHTML = '';
        dotsContainer.innerHTML = '';

        sliderData.forEach((slide, index) => {
            // Create Slide Element
            const slideEl = document.createElement('div');
            slideEl.className = `absolute inset-0 transition-opacity duration-700 ease-in-out ${index === currentSlideIndex ? 'opacity-100 z-10' : 'opacity-0 z-0'}`;
            slideEl.innerHTML = `
                    <img src="${slide.img}" class="w-full h-full object-cover object-top" alt="${slide.title}">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-6 md:p-12 w-full md:w-2/3 text-white">
                        <span class="bg-red-600 text-xs font-bold px-2 py-1 rounded mb-2 inline-block shadow-sm">${slide.badge}</span>
                        <h2 class="text-3xl md:text-5xl font-extrabold mb-3 leading-tight drop-shadow-lg">${slide.title}</h2>
                        <p class="text-gray-200 line-clamp-2 drop-shadow-md text-sm md:text-base">${slide.desc}</p>
                    </div>
                `;
            sliderContainer.appendChild(slideEl);

            // Create Dot
            const dot = document.createElement('button');
            dot.className = `w-3 h-3 rounded-full transition-all ${index === currentSlideIndex ? 'bg-blue-500 w-8' : 'bg-white/50 hover:bg-white'}`;
            dot.onclick = () => {
                currentSlideIndex = index;
                renderSlider();
            };
            dotsContainer.appendChild(dot);
        });
    }

    function changeSlide(direction) {
        currentSlideIndex = (currentSlideIndex + direction + sliderData.length) % sliderData.length;
        renderSlider();
    }

    // Auto play slider
    setInterval(() => changeSlide(1), 5000);
    renderSlider();

    // --- 3. LOGIC RENDER LISTS ---

    // Render Trending Carousel
    const trendingContainer = document.getElementById('trending-list');
    trendingData.forEach((item, index) => {
        const rankClass = index === 0 ? 'rank-1' : (index === 1 ? 'rank-2' : (index === 2 ? 'rank-3' : 'rank-other'));
        const html = `
                <div class="flex-none w-[160px] md:w-[180px] group cursor-pointer comic-card relative">
                    <div class="aspect-[2/3] rounded-lg overflow-hidden shadow-md relative mb-3">
                        <img src="${item.img}" class="w-full h-full object-cover">
                        <span class="rank-text ${rankClass}">${index + 1}</span>
                    </div>
                    <h3 class="font-bold text-sm text-gray-800 line-clamp-1 group-hover:text-blue-600 transition">${item.title}</h3>
                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                        <span>Chap ${item.chap}</span>
                        <span><i class="fas fa-eye"></i> ${item.view}</span>
                    </div>
                </div>
            `;
        trendingContainer.innerHTML += html;
    });

    // Render Update Grid
    const updatesContainer = document.getElementById('updates-grid');
    updatesData.forEach(item => {
        const html = `
                <div class="group comic-card">
                    <div class="relative rounded-lg overflow-hidden shadow-sm aspect-[2/3] mb-2 cursor-pointer">
                        ${item.isHot ? '<span class="absolute top-2 left-2 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow-sm z-10">HOT</span>' : ''}
                        <img src="${item.img}" class="w-full h-full object-cover">
                        <div class="absolute bottom-0 w-full bg-gradient-to-t from-black/70 to-transparent p-2 flex justify-between text-white/90 text-[10px]">
                            <span><i class="fas fa-eye"></i> 15K</span>
                            <span><i class="fas fa-heart"></i> 200</span>
                        </div>
                    </div>
                    <h4 class="font-bold text-sm text-slate-800 line-clamp-1 group-hover:text-blue-600 transition cursor-pointer">${item.title}</h4>
                    <div class="mt-2 space-y-1">
                        ${item.chaps.map(chap => `
                            <div class="flex justify-between items-center text-xs">
                                <a href="#" class="bg-gray-100 hover:bg-blue-100 px-2 py-0.5 rounded text-gray-600 font-semibold transition">${chap.num}</a>
                                <span class="text-gray-400 italic text-[10px]">${chap.time}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        updatesContainer.innerHTML += html;
    });

    // Render Sidebar List
    const sidebarContainer = document.getElementById('sidebar-list');
    // Reuse trending data for sidebar simplicity
    trendingData.slice(0, 5).forEach((item, index) => {
        const numColor = index < 3 ? 'text-blue-600' : 'text-gray-300';
        const html = `
                <div class="flex gap-3 items-center border-b border-gray-50 last:border-0 pb-2 cursor-pointer group">
                    <span class="text-xl font-black ${numColor} w-6 text-center italic">0${index+1}</span>
                    <div class="w-12 h-16 rounded overflow-hidden flex-shrink-0 shadow-sm">
                        <img src="${item.img}" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <h5 class="text-xs font-bold text-gray-700 line-clamp-1 group-hover:text-blue-600 transition">${item.title}</h5>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-[10px] text-gray-500">Chap ${item.chap}</span>
                            <span class="text-[10px] text-gray-400"><i class="fas fa-eye"></i> ${item.view}</span>
                        </div>
                    </div>
                </div>
            `;
        sidebarContainer.innerHTML += html;
    });

    // Render Manhwa List (Example for bottom section)
    const manhwaContainer = document.getElementById('manhwa-list');
    for (let i = 0; i < 8; i++) {
        manhwaContainer.innerHTML += `
                <div class="flex-none w-[140px] group cursor-pointer comic-card">
                    <div class="aspect-[2/3] rounded-lg overflow-hidden shadow-md mb-2 relative">
                         <img src="https://via.placeholder.com/200x300/e9d5ff/6b21a8?text=Love" class="w-full h-full object-cover">
                         <div class="absolute top-2 right-2 bg-pink-500 text-white text-[9px] font-bold px-1 rounded">Full</div>
                    </div>
                    <h4 class="font-bold text-sm text-gray-800 line-clamp-1 group-hover:text-purple-600">Cô Vợ Ngọt Ngào</h4>
                    <span class="text-xs text-gray-400">Chap 100</span>
                </div>
             `;
    }

    // --- 4. HÀM SCROLL (CHO NÚT MŨI TÊN) ---
    function scrollSection(id, distance) {
        const container = document.getElementById(id);
        container.scrollBy({
            left: distance,
            behavior: 'smooth'
        });
    }

    // --- 5. HÀM TAB SIDEBAR ---
    function switchTab(tab) {
        // Reset styles
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.className = 'flex-1 py-1 text-[11px] font-bold rounded text-center text-gray-500 hover:text-gray-700 tab-btn transition';
        });
        // Active style
        const activeBtn = document.getElementById('tab-' + tab);
        activeBtn.className = 'flex-1 py-1 text-[11px] font-bold rounded text-center bg-white shadow-sm text-blue-600 tab-btn transition';

        // In a real app, you would fetch new data here.
        // visual feedback:
        sidebarContainer.style.opacity = '0.5';
        setTimeout(() => sidebarContainer.style.opacity = '1', 200);
    }
</script>

@endsection