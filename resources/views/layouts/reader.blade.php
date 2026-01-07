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

    {{-- 2. NÚT TOGGLE (MŨI TÊN) --}}
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
    {{-- MAIN CONTENT                               --}}
    {{-- ========================================== --}}
    <main class="{{ $isReaderPage ? 'w-full min-h-screen pt-16' : 'container mx-auto px-4 py-6 min-h-screen' }}">
        @yield('content')
    </main>


    {{-- ========================================== --}}
    {{-- SCRIPTS                                    --}}
    {{-- ========================================== --}}
    @stack('scripts')

    @if($isReaderPage)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const header = document.getElementById('reader-header');
            const toggleBtn = document.getElementById('toggle-header');
            const toggleIcon = document.getElementById('toggle-icon');
            const progressBar = document.getElementById('progress-bar');

            // Biến trạng thái
            let isPinned = false; // False: Tự động ẩn, True: Dính cứng (Hiện)

            // Hàm cập nhật giao diện (Vị trí nút & Icon)
            function updateInterface(show) {
                if (show) {
                    // Hiện header
                    header.classList.remove('-translate-y-full');
                    // Nút chạy xuống dưới header
                    toggleBtn.style.top = "80px";
                    // Icon chuyển thành mũi tên lên (để bấm vào thì ẩn)
                    toggleIcon.classList.remove('fa-chevron-down');
                    toggleIcon.classList.add('fa-chevron-up');
                } else {
                    // Ẩn header
                    header.classList.add('-translate-y-full');
                    // Nút chạy lên sát mép trên
                    toggleBtn.style.top = "20px";
                    // Icon chuyển thành mũi tên xuống (để bấm vào thì hiện)
                    toggleIcon.classList.remove('fa-chevron-up');
                    toggleIcon.classList.add('fa-chevron-down');
                }
            }

            // 1. Xử lý sự kiện Click nút Toggle
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();

                    // Kiểm tra xem header đang ẩn hay hiện
                    const isHidden = header.classList.contains('-translate-y-full');

                    if (isHidden) {
                        // Đang ẩn -> Bấm để HIỆN và GHIM LẠI
                        isPinned = true;
                        updateInterface(true);
                    } else {
                        // Đang hiện -> Bấm để ẨN và BỎ GHIM
                        isPinned = false;
                        updateInterface(false);
                    }
                });
            }

            // 2. Xử lý sự kiện Scroll
            window.addEventListener('scroll', function() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

                // --- LOGIC TIẾN TRÌNH ĐỌC (Giữ nguyên) ---
                if (progressBar) {
                    const docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                    const scrolled = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
                    progressBar.style.width = scrolled + '%';
                }

                // --- LOGIC ẨN/HIỆN HEADER ---

                // Nếu đang GHIM (isPinned = true) -> Không làm gì cả (Header luôn hiện)
                if (isPinned) return;

                // Nếu KHÔNG GHIM:
                if (scrollTop <= 10) {
                    // Kéo lên sát đầu trang (Top <= 10px) -> Hiện Header
                    updateInterface(true);
                } else {
                    // Cuộn xuống bất kỳ đâu khác đầu trang -> Ẩn Header
                    updateInterface(false);
                }

            }, {
                passive: true
            });
        });
    </script>
    @endif

</body>

</html>