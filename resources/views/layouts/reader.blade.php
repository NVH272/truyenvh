<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TruyenVH')</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/logo/logoMini.png') }}">

    {{-- Assets --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }

        /* Ẩn thanh cuộn mặc định để đẹp hơn trên nền đen */
        .reader-mode::-webkit-scrollbar {
            width: 8px;
        }

        .reader-mode::-webkit-scrollbar-track {
            background: #121212;
        }

        .reader-mode::-webkit-scrollbar-thumb {
            background: #333;
            border-radius: 4px;
        }

        .reader-mode::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .tooltip {
            transition: opacity 0.3s ease, transform 0.3s ease;
            transition-delay: 0.3s;
        }

        .group:hover .tooltip {
            opacity: 1;
            transform: translateX(0);
        }

        .group:not(:hover) .tooltip {
            transition-delay: 0s;
        }
    </style>
</head>

@php
// Kiểm tra trang hiện tại có phải là trang đọc truyện không
$isReaderPage = request()->routeIs('user.comics.chapters.read') || request()->routeIs('chapters.show');
@endphp

<body class="{{ $isReaderPage ? 'bg-[#121212] overflow-x-hidden reader-mode' : 'bg-gray-100 text-slate-800' }}">

    {{-- ========================================== --}}
    {{-- KHUNG HEADER CHO TRANG ĐỌC (READER MODE)   --}}
    {{-- ========================================== --}}
    @if($isReaderPage)
    {{-- 1. HEADER CHÍNH --}}
    <div id="reader-header"
        class="fixed top-0 left-0 right-0 z-[100] bg-[#1a1a1a]/95 backdrop-blur-md border-b border-gray-800 shadow-xl transition-transform duration-300 ease-in-out transform translate-y-0">

        {{-- Nội dung header --}}
        <div class="max-w-7xl mx-auto px-4 h-14 md:h-16 flex items-center justify-between">
            @yield('reader_header_content')
        </div>

        {{-- Thanh tiến trình --}}
        <div class="w-full h-1 bg-gray-800 absolute bottom-0 left-0">
            <div id="progress-bar" class="h-full bg-blue-600 w-0 transition-all duration-100 ease-out"></div>
        </div>
    </div>

    {{-- 2. NÚT TOGGLE HEADER (MŨI TÊN TRÊN) --}}
    <button id="toggle-header"
        class="fixed right-4 z-[99] w-10 h-10
        bg-white/80 hover:bg-gray-200
        backdrop-blur-md rounded-full
        flex items-center justify-center
        text-gray-700 hover:text-gray-900
        border border-black/10
        transition-all duration-300
        shadow-lg group"
        style="top: 80px;">
        <i id="toggle-icon" class="fas fa-chevron-up text-sm transition-transform duration-300"></i>
    </button>
    @endif

    {{-- ========================================== --}}
    {{-- NÚT SCROLL TO TOP (MỚI THÊM)               --}}
    {{-- ========================================== --}}
    {{-- Nút này nằm ngoài @if($isReaderPage) để có thể dùng chung cho cả trang thường nếu muốn --}}
    <button id="scroll-to-top"
        onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
        class="group fixed bottom-4 right-4 z-[99] w-10 h-10
        bg-white/90 hover:bg-white
        backdrop-blur-md rounded-full
        flex items-center justify-center
        text-slate-600 hover:text-slate-900 hover:shadow-lg hover:-translate-y-1
        border border-slate-200
        shadow-md transition-all duration-300 ease-out transform
        translate-y-20 opacity-0 pointer-events-none">

        <i class="fas fa-arrow-up text-xs"></i>

        {{-- TOOLTIP MINIMALIST --}}
        {{-- Vị trí: Bên trái nút --}}
        <span class="tooltip absolute right-full mr-1 top-1/2 -translate-y-1/2 w-max
                 bg-slate-800/90 backdrop-blur-sm text-white text-[10px] font-medium tracking-wide
                 px-2.5 py-1 rounded-lg shadow-sm
                 opacity-0 translate-x-2 invisible
                 group-hover:opacity-100 group-hover:translate-x-0 group-hover:visible
                 transition-all duration-300 ease-out pointer-events-none">
            Lên đầu trang
        </span>

    </button>


    {{-- ========================================== --}}
    {{-- MAIN CONTENT                               --}}
    {{-- ========================================== --}}
    <main class="{{ $isReaderPage ? 'w-full min-h-screen pt-16' : 'container mx-auto px-4 py-6 min-h-screen' }}">
        @yield('content')
    </main>


    {{-- ========================================== --}}
    {{-- SCRIPTS                                    --}}
    {{-- ========================================== --}}
    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- CÁC BIẾN CHO HEADER (READER MODE) ---
            const header = document.getElementById('reader-header');
            const toggleBtn = document.getElementById('toggle-header');
            const toggleIcon = document.getElementById('toggle-icon');
            const progressBar = document.getElementById('progress-bar');

            // --- CÁC BIẾN CHO SCROLL TO TOP ---
            const scrollToTopBtn = document.getElementById('scroll-to-top');
            let lastScrollTop = 0; // Biến lưu vị trí cuộn trước đó để xác định hướng

            // Biến trạng thái Header
            let isPinned = false;

            // Biến lưu progress cao nhất đã đạt được (cho thanh tiến trình)
            let maxProgress = 0;
            
            // Function tính progress dựa trên số ảnh đã xem
            function calculateImageBasedProgress() {
                const images = document.querySelectorAll('.chapter-image');
                if (images.length === 0) return 0;
                
                const viewportBottom = window.innerHeight + window.pageYOffset;
                let viewedCount = 0;
                
                images.forEach(function(img) {
                    const imgTop = img.getBoundingClientRect().top + window.pageYOffset;
                    const imgBottom = imgTop + img.offsetHeight;
                    
                    // Nếu ảnh đã scroll qua (top của ảnh < bottom của viewport)
                    if (imgTop < viewportBottom) {
                        viewedCount++;
                    }
                });
                
                return Math.min(100, Math.round((viewedCount / images.length) * 100));
            }

            // Hàm cập nhật giao diện Header
            function updateHeaderInterface(show, atTop = false) {
                if (!header) return; // Nếu không phải trang reader thì bỏ qua

                if (show) {
                    header.classList.remove('-translate-y-full');
                    if (atTop) {
                        toggleBtn.classList.add('hidden');
                    } else {
                        toggleBtn.classList.remove('hidden');
                        toggleBtn.style.top = "80px";
                        toggleIcon.classList.remove('fa-chevron-down');
                        toggleIcon.classList.add('fa-chevron-up');
                    }
                } else {
                    header.classList.add('-translate-y-full');
                    toggleBtn.classList.remove('hidden');
                    toggleBtn.style.top = "20px";
                    toggleIcon.classList.remove('fa-chevron-up');
                    toggleIcon.classList.add('fa-chevron-down');
                }
            }

            // 1. Sự kiện Click nút Toggle Header
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isHidden = header.classList.contains('-translate-y-full');
                    if (isHidden) {
                        isPinned = true;
                        updateHeaderInterface(true, false);
                    } else {
                        isPinned = false;
                        updateHeaderInterface(false);
                    }
                });
            }

            // 2. Sự kiện Scroll (Xử lý chung cho cả Header và ScrollToTop)
            window.addEventListener('scroll', function() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

                // --- A. LOGIC THANH TIẾN TRÌNH (dựa trên số ảnh đã xem, chỉ tăng không giảm) ---
                if (progressBar) {
                    const currentProgress = calculateImageBasedProgress();
                    // Chỉ cập nhật nếu progress hiện tại lớn hơn progress cao nhất
                    if (currentProgress > maxProgress) {
                        maxProgress = currentProgress;
                    }
                    // Luôn hiển thị progress cao nhất đã đạt được
                    progressBar.style.width = maxProgress + '%';
                }

                // --- B. LOGIC ẨN/HIỆN HEADER ---
                if (header) {
                    if (scrollTop <= 10) {
                        updateHeaderInterface(true, true);
                    } else {
                        if (isPinned) {
                            updateHeaderInterface(true, false);
                        } else {
                            updateHeaderInterface(false);
                        }
                    }
                }

                // --- C. LOGIC NÚT SCROLL TO TOP (MỚI) ---
                if (scrollToTopBtn) {
                    // Logic: Hiện khi cuộn xuống quá 300px VÀ đang thực hiện thao tác cuộn xuống
                    // Ẩn khi cuộn lên HOẶC đang ở đầu trang (< 300px)

                    if (scrollTop > 1000 && scrollTop > lastScrollTop) {
                        // Đang cuộn xuống và đã qua 300px -> HIỆN
                        scrollToTopBtn.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
                    } else {
                        // Đang cuộn lên hoặc ở gần đầu trang -> ẨN
                        scrollToTopBtn.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none');
                    }
                }

                // Cập nhật vị trí cuộn để dùng cho lần sau
                lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
            }, {
                passive: true
            });

            // Khởi tạo trạng thái ban đầu
            if (window.pageYOffset <= 10 && header) {
                updateHeaderInterface(true, true);
            }
        });
    </script>

</body>

</html>