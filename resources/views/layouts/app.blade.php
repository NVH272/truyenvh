<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TruyenVH - Đọc Truyện Tranh Online')</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/logo/logoMini.png') }}">

    <!-- Tailwind Config (must be before Tailwind script) -->
    @stack('head-scripts')

    <!-- Tailwind & FontAwesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    @stack('styles')

    <style>
        /* --- BASE STYLES --- */
        body {
            font-family: 'Nunito', sans-serif;
            background-color: transparent;
            /* Để lộ background phía sau */
        }

        /* --- STEINS;GATE BACKGROUND (TĨNH - FIXED) --- */
        .sg-background-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: -50;
            /* Nằm dưới cùng */
            background-color: #e5e5e5;
            /* Màu giấy cũ */
            /* Lưới Grid */
            background-image:
                linear-gradient(rgba(0, 0, 0, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 0, 0, 0.1) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        /* Hiệu ứng tối 4 góc (Vignette) */
        .sg-background-wrapper::after {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle, transparent 50%, rgba(0, 0, 0, 0.4) 100%);
            z-index: -49;
        }

        /* Bánh răng trang trí */
        .gear {
            position: absolute;
            border: 4px dashed #333;
            border-radius: 50%;
            opacity: 0.15;
            z-index: -48;
        }

        .gear-1 {
            width: 300px;
            height: 300px;
            top: -50px;
            right: -50px;
            transform: rotate(15deg);
        }

        .gear-2 {
            width: 200px;
            height: 200px;
            top: 150px;
            right: 100px;
            transform: rotate(-20deg);
            border-width: 6px;
            border-style: dotted;
        }

        .gear-3 {
            width: 150px;
            height: 150px;
            bottom: 50px;
            left: 50px;
            transform: rotate(45deg);
            border-color: #555;
        }

        /* Font trang trí */
        .font-sg {
            font-family: 'Share Tech Mono', monospace;
        }

        /* Utilities */
        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* CSS cho scrollbar đẹp */
        .custom-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 4px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
    </style>
</head>

<body class="text-slate-800 relative min-h-screen flex flex-col">

    <!-- === LỚP BACKGROUND STEINS;GATE === -->
    <div class="sg-background-wrapper">
        <div class="gear gear-1"></div>
        <div class="gear gear-2"></div>
        <div class="gear gear-3"></div>
    </div>

    <!-- HEADER -->
    <header class="bg-white/90 shadow-sm sticky top-0 z-50 backdrop-blur-sm border-b border-gray-200 shrink-0" style="will-change: transform; transform: translateZ(0);">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
            <!-- Left: Logo & Nav -->
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ asset('storage/logo/logoLight.png') }}" alt="TruyenVH Logo" class="h-8">
                </a>

                <nav class="hidden lg:flex items-center gap-6 font-bold text-sm text-gray-600 uppercase">
                    <a href="{{ route('user.comics.followed') }}" class="hover:text-blue-600 transition">Theo dõi</a>
                    <a href="{{ route('user.reading-history.index') }}" class="hover:text-blue-600 transition">Lịch sử đọc</a>
                    <div class="group relative cursor-pointer py-4">
                        <span class="hover:text-blue-600 flex items-center gap-1">Thể loại <i class="fas fa-caret-down"></i></span>
                        <div class="absolute top-full left-0 w-56 bg-white shadow-xl rounded-lg hidden group-hover:grid grid-cols-2 gap-2 p-3 border border-gray-100 z-50">
                            {{-- Top 3 categories --}}
                            @foreach($topCategories as $category)
                            <a href="{{ route('user.comics.filter', ['categories[0]' => $category->slug]) }}" class="hover:text-blue-600 text-xs font-semibold">
                                {{ $category->name }}
                            </a>
                            @endforeach
                            <a href="{{ route('user.comics.filter') }}" class="hover:text-blue-600 text-xs font-semibold">Xem tất cả</a>
                        </div>
                    </div>
                    <a href="#" class="hover:text-blue-600 transition">Tin tức</a>
                </nav>
            </div>

            <!-- Right: Search & User -->
            <div class="flex items-center gap-3">

                <!-- SEARCH BAR -->
                <form action="{{ route('user.comics.filter') }}" method="GET" class="relative hidden md:block group" id="header-search-form">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Tìm truyện..."
                        class="bg-white/90 backdrop-blur-sm border border-slate-200 rounded-full
                        pl-4 pr-10 py-2.5 text-sm w-64 shadow-sm
                        focus:outline-none focus:ring-2 focus:ring-blue-100/70
                        hover:border-blue-300 transition-all duration-300">

                    <button type="submit"
                        class="absolute right-3 top-1/2 -translate-y-1/2
                        text-gray-400 group-hover:text-blue-600 transition focus:outline-none">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                <!-- Notification Wrapper -->
                <div class="relative" id="notification-dropdown-wrapper">

                    <!-- Button Chuông -->
                    <button id="notification-btn" class="relative w-10 h-10 bg-white/80 backdrop-blur-md border border-slate-200/60 
                        rounded-full shadow-sm hover:shadow-lg hover:shadow-blue-500/10 
                        hover:border-blue-300 hover:bg-white text-slate-500 hover:text-blue-600 
                        flex items-center justify-center transition-all duration-300 group">

                        <i class="fas fa-bell transform group-hover:-rotate-12 transition-transform duration-300 text-lg"></i>

                        <!-- Chấm đỏ báo hiệu số lượng (Ẩn mặc định) -->
                        <span id="notification-count" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-sm border border-white">
                            0
                        </span>
                    </button>

                    <!-- Dropdown Content (Ẩn mặc định) -->
                    <div id="notification-dropdown" class="hidden absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden z-50 origin-top-right transition-all duration-200 transform scale-95 opacity-0">
                        <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                            <h3 class="font-bold text-gray-700">Thông báo</h3>
                            <button onclick="markAllAsRead()" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Đánh dấu đã đọc</button>
                        </div>

                        <!-- KHUNG CHỨA DANH SÁCH -->
                        <div id="notification-list" class="max-h-[400px] overflow-y-auto custom-scroll">
                            <div class="p-6 text-center text-gray-500 flex flex-col items-center">
                                <i class="fas fa-circle-notch fa-spin text-blue-500 text-2xl mb-2"></i>
                                <span class="text-xs">Đang tải...</span>
                            </div>
                        </div>

                        <div class="px-4 py-2 border-t border-gray-100 bg-gray-50 text-center">
                            <a href="#" class="text-xs text-gray-500 hover:text-blue-600 font-medium">Xem tất cả</a>
                        </div>
                    </div>
                </div>

                <!-- LOGIN BUTTON (đồng bộ chiều cao, bo tròn, texture) -->
                @guest
                <a href="{{ route('login.form') }}"
                    class="group flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-full text-sm font-bold 
                    shadow-md hover:shadow-xl hover:shadow-blue-600/20 transition-all duration-300 hover:-translate-y-0.5">
                    <span>Đăng nhập</span>
                    <i class="fas fa-arrow-right-to-bracket text-xs opacity-70 group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-300"></i>
                </a>
                @else

                {{-- DROPDOWN USER --}}
                <div class="relative group" id="userMenu">
                    <!-- AVATAR + BADGE WRAPPER -->
                    <button id="userMenuBtn"
                        class="group flex items-center gap-2 p-1 pl-3 bg-white/90 backdrop-blur-sm border border-slate-200 rounded-full shadow-sm hover:shadow-md hover:border-blue-300 hover:ring-2 hover:ring-blue-100/50 transition-all duration-300">

                        <!-- BADGE (ROLE) -->
                        <div class="flex flex-col items-end leading-none">
                            <span class="text-[10px] font-extrabold tracking-wider uppercase px-2 py-0.5 rounded-md border shadow-sm
                                @if(Auth::user()->role === 'admin')
                                    bg-blue-50 text-blue-700 border-blue-200
                                @elseif(Auth::user()->role === 'poster')
                                    bg-purple-50 text-purple-700 border-purple-200
                                @else
                                    bg-emerald-50 text-emerald-700 border-emerald-200
                                @endif
                            ">
                                {{ Auth::user()->role }}
                            </span>
                        </div>

                        <!-- AVATAR -->
                        <!-- Thêm relative để bọc avatar, giúp căn chỉnh đẹp hơn -->
                        <div class="relative w-9 h-9">
                            <img src="{{ Auth::user()->avatar_url }}"
                                class="w-full h-full rounded-full object-cover border-2 border-white shadow-sm"
                                alt="Avatar">
                    </button>

                    <!-- MENU DROPDOWN -->
                    <div id="userDropdown"
                        class="absolute right-0 mt-2 w-56 bg-white shadow-lg border border-gray-200 rounded-lg 
                        transition-all duration-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible">

                        {{-- EMAIL VERIFICATION --}}
                        @if(!Auth::user()->hasVerifiedEmail())
                        <div class="px-4 py-2 bg-yellow-50 border-b border-yellow-200">
                            <div class="flex items-center gap-2 text-xs text-yellow-800">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span class="font-semibold">Email chưa được xác thực</span>
                            </div>
                        </div>
                        <a href="{{ route('verification.notice') }}"
                            class="block px-4 py-2 text-sm font-semibold text-orange-600 hover:bg-orange-50 border-b border-gray-200">
                            <i class="fas fa-envelope mr-2"></i>Xác thực email
                        </a>
                        <div class="border-t border-gray-200"></div>
                        @endif

                        {{-- ADMIN --}}
                        @if(Auth::user()->role === 'admin')
                        <a href="{{ url('/admin') }}"
                            class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user-tie mr-2"></i>Quản trị Dashboard
                        </a>
                        <div class="border-t border-gray-200"></div>
                        @endif

                        {{-- MY COMICS (ADMIN + POSTER) --}}
                        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'poster')
                        <a href="{{ route('user.my-comics.index') }}"
                            class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-book mr-2"></i>Danh sách truyện của bạn
                        </a>
                        <a href="{{ route('poster.index') }}"
                            class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-pen-to-square mr-2"></i>Chương truyện của bạn
                        </a>
                        <div class="border-t border-gray-200"></div>
                        @endif

                        {{-- PROFILE --}}
                        <a href="{{ route('user.profile.index') }}"
                            class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user mr-2"></i>Trang cá nhân
                        </a>

                        {{-- SETTING --}}
                        <a href=""
                            class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-gear mr-2"></i>Cài đặt
                        </a>

                        {{-- LOGOUT --}}
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-2"></i>Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>

                @endguest

            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="flex-1 container mx-auto px-4 py-6 space-y-10 flex flex-col">

        {{-- Các trang AUTH và PROFILE không bị bọc khung trắng --}}
        @if (request()->routeIs([
        'login*',
        'register*',
        'password.*',
        'verification.*',
        'user.profile.*'
        ]))
        @yield('content')
        @else
        {{-- Các trang khác bị bọc trong khung trắng --}}
        <div class="bg-white/85 backdrop-blur-sm rounded-xl shadow-lg border border-gray-300 overflow-visible">
            <div class="p-6">
                @yield('content')
            </div>
        </div>
        @endif

        @include('user.live_chat.chat_box')

    </main>

    <!-- FOOTER -->
    <footer class="bg-[#1a1a1a] text-gray-400 py-12 relative overflow-hidden border-t border-gray-800 font-sans">

        <div class="container mx-auto px-4 relative z-10">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">

                <div class="lg:col-span-5 flex flex-col items-start">
                    <div class="flex items-center gap-3 mb-4">
                        <a href="{{ route('home') }}" class="flex items-center gap-2">
                            <img src="{{ asset('storage/logo/logoDark.png') }}" alt="TruyenVH Logo" class="h-8">
                        </a>
                    </div>

                    <p class="text-sm leading-relaxed text-gray-400 max-w-sm">
                        TruyenVH là website đọc truyện tranh online uy tín hàng đầu Việt Nam. Tất cả các truyện trên website đăng tải được TruyenVH biên dịch và tổng hợp từ nhiều nguồn trên Internet.
                    </p>
                </div>

                <div class="lg:col-span-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <ul class="space-y-3">
                        <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">Về chúng tôi</a></li>
                        <li><a href="{{ route('terms') }}" class="hover:text-white transition-colors">Điều khoản dịch vụ</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-white transition-colors">Chính sách bảo mật</a></li>
                    </ul>

                    <ul class="space-y-3">
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition-colors">Phương thức liên hệ</a></li>
                        <li><a href="{{ route('disclaimer') }}" class="hover:text-white transition-colors">Tuyên bố miễn trừ trách nhiệm</a></li>
                    </ul>
                </div>

                <div class="lg:col-span-2 flex flex-col lg:items-end items-start gap-4">
                    <a href="https://www.facebook.com/viethoang272/" target="_blank" class="group w-10 h-10 rounded-full bg-white text-black flex items-center justify-center hover:bg-gray-200 transition-all duration-300">
                        <i class="fab fa-facebook-f text-xl group-hover:text-blue-600"></i>
                    </a>

                    <a href="https://www.instagram.com/viethoang272/" target="_blank" class="group w-10 h-10 rounded-full bg-white text-black flex items-center justify-center hover:bg-gray-200 transition-all duration-300">
                        <i class="fab fa-instagram text-xl group-hover:text-pink-600"></i>
                    </a>

                    <a href="https://www.discordapp.com/users/842034711269212200" target="_blank" class="group w-10 h-10 rounded-full bg-white text-black flex items-center justify-center hover:bg-gray-200 transition-all duration-300">
                        <i class="fab fa-discord text-xl group-hover:text-[#5865F2]"></i>
                    </a>
                </div>
            </div>

            <div class="text-center pt-8 mt-4 border-t border-gray-800/50">
                <p class="text-sm text-gray-500">Copyright © 2025 TruyenVH. All rights reserved.</p>
            </div>
        </div>

        <img
            src="{{ asset('storage/backgrounds/nrt.png') }}"
            alt="Footer Art"
            class="absolute bottom-0 right-8 md:right-20 lg:right-30 z-20 pointer-events-none w-auto h-48 md:h-64 lg:h-[330px]">
    </footer>

    {{-- Scripts được push từ các view con (ví dụ: comments.blade.php) --}}
    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // ===== DROPDOWN USER =====
            const userBtn = document.getElementById('userMenuBtn');
            const userMenu = document.getElementById('userDropdown');
            const userWrap = document.getElementById('userMenu');

            if (userBtn && userMenu && userWrap) {
                // CLICK mở/đóng
                userBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    userMenu.classList.toggle('opacity-100');
                    userMenu.classList.toggle('visible');
                    userMenu.classList.toggle('opacity-0');
                    userMenu.classList.toggle('invisible');
                });

                // CLICK RA NGOÀI → ĐÓNG
                document.addEventListener('click', () => {
                    userMenu.classList.add('opacity-0', 'invisible');
                    userMenu.classList.remove('opacity-100', 'visible');
                });

                // Ngăn đóng khi click bên trong menu
                userMenu.addEventListener('click', (e) => e.stopPropagation());
            }

            // ===== THÔNG BÁO =====
            const notifyBtn = document.getElementById('notification-btn');
            const dropdown = document.getElementById('notification-dropdown');
            const list = document.getElementById('notification-list');
            const countBadge = document.getElementById('notification-count');
            let isOpen = false;

            if (notifyBtn && dropdown && list && countBadge) {
                // 1. Load số lượng thông báo khi vào trang (Chạy ngay lập tức)
                fetchNotifications(false);

                // 2. Sự kiện Click vào nút chuông
                notifyBtn.addEventListener('click', function(e) {
                    e.stopPropagation(); // Ngăn sự kiện lan ra ngoài
                    isOpen = !isOpen;

                    if (isOpen) {
                        // Hiện dropdown
                        dropdown.classList.remove('hidden');
                        // Hiệu ứng animation (optional)
                        setTimeout(() => {
                            dropdown.classList.remove('scale-95', 'opacity-0');
                            dropdown.classList.add('scale-100', 'opacity-100');
                        }, 10);

                        // Load nội dung HTML danh sách
                        fetchNotifications(true);
                    } else {
                        closeDropdown();
                    }
                });

                // 3. Click ra ngoài thì đóng
                document.addEventListener('click', function(e) {
                    if (dropdown && !dropdown.contains(e.target) && !notifyBtn.contains(e.target)) {
                        closeDropdown();
                    }
                });
            }

            function closeDropdown() {
                if (!dropdown) return;
                isOpen = false;
                dropdown.classList.remove('scale-100', 'opacity-100');
                dropdown.classList.add('scale-95', 'opacity-0');
                setTimeout(() => dropdown.classList.add('hidden'), 200);
            }

            // 4. Hàm gọi API lấy thông báo
            function fetchNotifications(loadHtml = false) {
                fetch("{{ route('notifications.get') }}", {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('Network response was not ok');
                        return res.json();
                    })
                    .then(data => {
                        // Cập nhật số lượng
                        if (data.unread_count > 0) {
                            countBadge.innerText = data.unread_count > 99 ? '99+' : data.unread_count;
                            countBadge.classList.remove('hidden');
                        } else {
                            countBadge.classList.add('hidden');
                        }

                        // Cập nhật danh sách nếu cần (khi mở dropdown)
                        if (loadHtml && list) {
                            list.innerHTML = data.html;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching notifications:', error);
                        if (loadHtml && list) {
                            list.innerHTML = `
                                <div class="p-6 text-center text-red-500 text-sm">
                                    Không tải được thông báo. Vui lòng thử lại sau.
                                </div>
                            `;
                        }
                    });
            }

            // 5. Hàm đánh dấu đã đọc (Global để gọi từ onclick trong HTML nếu cần)
            window.markAllAsRead = function() {
                fetch("{{ route('notifications.markRead') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    }
                }).then(() => {
                    if (!countBadge || !list) return;
                    countBadge.classList.add('hidden');
                    // Làm mờ các item chưa đọc trong list (nếu đang mở)
                    const unreadItems = list.querySelectorAll('.bg-blue-50\\/60'); // Class nền xanh bạn set ở view
                    unreadItems.forEach(item => {
                        item.classList.remove('bg-blue-50/60');
                        item.classList.add('bg-white', 'opacity-60');
                    });
                });
            }
        });
    </script>
</body>

</html>