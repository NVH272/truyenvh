<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TruyenVH - Đọc Truyện Tranh Online')</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/logo/logoMini.png') }}">

    <!-- Tailwind & FontAwesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">

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
    </style>
</head>

<body class="text-slate-800 relative">

    <!-- === LỚP BACKGROUND STEINS;GATE === -->
    <div class="sg-background-wrapper">
        <div class="gear gear-1"></div>
        <div class="gear gear-2"></div>
        <div class="gear gear-3"></div>
    </div>

    <!-- HEADER -->
    <header class="bg-white/90 shadow-sm sticky top-0 z-50 backdrop-blur-sm border-b border-gray-200">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
            <!-- Left: Logo & Nav -->
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ asset('storage/logo/logoLight.png') }}" alt="TruyenVH Logo" class="h-8">
                </a>

                <nav class="hidden lg:flex items-center gap-6 font-bold text-sm text-gray-600 uppercase">
                    <a href="#" class="hover:text-blue-600 transition">Lịch sử</a>
                    <a href="{{ route('user.comics.followed') }}" class="hover:text-blue-600 transition">Theo dõi</a>
                    <div class="group relative cursor-pointer py-4">
                        <span class="hover:text-blue-600 flex items-center gap-1">Thể loại <i class="fas fa-caret-down"></i></span>
                        <div class="absolute top-full left-0 w-56 bg-white shadow-xl rounded-lg hidden group-hover:grid grid-cols-2 gap-2 p-3 border border-gray-100 z-50">
                            <a href="#" class="hover:text-blue-600 text-xs font-semibold">Action</a>
                            <a href="#" class="hover:text-blue-600 text-xs font-semibold">Comedy</a>
                            <a href="#" class="hover:text-blue-600 text-xs font-semibold">Manhwa</a>
                            <a href="#" class="hover:text-blue-600 text-xs font-semibold">Xem tất cả</a>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Right: Search & User -->
            <div class="flex items-center gap-3">

                <!-- SEARCH BAR -->
                <form action="{{ route('user.comics.search') }}" method="GET" class="relative hidden md:block group">
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
                        text-gray-400 group-hover:text-blue-600 transition">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                <!-- DARK MODE BUTTON (same height/shape as userMenuBtn avatar size) -->
                <button class="relative w-10 h-10 bg-white/80 backdrop-blur-md border border-slate-200/60 
                   rounded-full shadow-sm hover:shadow-lg hover:shadow-blue-500/10 
                   hover:border-blue-300 hover:bg-white text-slate-500 hover:text-blue-600 
                   flex items-center justify-center transition-all duration-300 group">
                    <i class="fas fa-moon transform group-hover:-rotate-12 transition-transform duration-300"></i>
                </button>

                <!-- LOGIN BUTTON (đồng bộ chiều cao, bo tròn, texture) -->
                @guest
                <a href="{{ route('login.form') }}"
                    class="group flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-full text-sm font-bold 
                    shadow-md hover:shadow-xl hover:shadow-blue-600/20 transition-all duration-300 hover:-translate-y-0.5">
                    <span>Đăng nhập</span>
                    <i class="fas fa-arrow-right text-xs opacity-70 group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-300"></i>
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
                            <i class="fas fa-cog mr-2"></i>Quản trị Dashboard
                        </a>
                        <div class="border-t border-gray-200"></div>
                        @endif

                        {{-- MY COMICS (ADMIN + POSTER) --}}
                        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'poster')
                        <a href="{{ route('user.my-comics.index') }}"
                            class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-book mr-2"></i>Danh sách truyện của bạn
                        </a>
                        <div class="border-t border-gray-200"></div>
                        @endif

                        {{-- PROFILE --}}
                        <a href="{{ route('user.profile.index') }}"
                            class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user mr-2"></i>Trang cá nhân
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
    <main class="container mx-auto px-4 py-6 space-y-10 min-h-screen flex flex-col">

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

    </main>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-slate-400 pt-12 pb-6 mt-0 border-t-4 border-gray-800 relative z-10">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 border-b border-slate-800 pb-8">
                <div class="col-span-1 md:col-span-2">
                    <span class="text-2xl font-extrabold text-white font-sg tracking-widest">TruyenVH</span>
                    <p class="mt-4 text-sm leading-relaxed">Website đọc truyện tranh online miễn phí chất lượng cao.</p>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4">Liên hệ</h4>
                    <div class="flex gap-4 text-xl">
                        <a href="https://www.facebook.com/viethoang272/" class="hover:text-blue-500 transition"><i class="fab fa-facebook"></i></a>
                        <a href="https://www.instagram.com/viethoang272/" class="hover:text-pink-500 transition"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.discordapp.com/users/842034711269212200" class="hover:text-[#4752C4] transition"><i class="fab fa-discord"></i></a>
                    </div>
                </div>
            </div>
            <div class="text-center text-xs mt-6 font-sg opacity-70">
                Copyright © 2025 TruyenVH.
            </div>
        </div>
    </footer>
</body>

</html>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('userMenuBtn');
        const menu = document.getElementById('userDropdown');
        const wrap = document.getElementById('userMenu');

        // CLICK mở/đóng
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.classList.toggle('opacity-100');
            menu.classList.toggle('visible');
            menu.classList.toggle('opacity-0');
            menu.classList.toggle('invisible');
        });

        // CLICK RA NGOÀI → ĐÓNG
        document.addEventListener('click', () => {
            menu.classList.add('opacity-0', 'invisible');
            menu.classList.remove('opacity-100', 'visible');
        });

        // Ngăn đóng khi click bên trong menu
        menu.addEventListener('click', (e) => e.stopPropagation());
    });
</script>