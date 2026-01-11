<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TruyenVH Admin Dashboard')</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/logo/logoMini.png') }}">

    <!-- Tailwind & Icons -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;500;600;700&family=Nunito:wght@400;600;700&family=Share+Tech+Mono&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #0f172a;
            color: #cbd5e1;
        }

        h1,
        h2,
        h3,
        h4,
        .brand-font {
            font-family: 'Chakra Petch', sans-serif;
        }

        .font-mono-tech {
            font-family: 'Share Tech Mono', monospace;
        }

        /* Custom Scrollbar */
        .custom-scroll::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 10px;
        }

        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #d97706;
        }

        /* --- SMOOTH MINI SIDEBAR LOGIC --- */
        #sidebar {
            width: 17rem;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            flex-shrink: 0;
        }

        #sidebar.is-collapsed {
            width: 5.5rem;
        }

        /* --- NAV ITEMS --- */
        .nav-item {
            display: flex;
            align-items: center;
            height: 3rem;
            padding-left: 1.5rem;
            padding-right: 1rem;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
            white-space: nowrap;
            overflow: hidden;
        }

        .nav-item:hover {
            background-color: #1e293b;
            color: #fff;
        }

        .nav-item.active {
            background-color: #1e293b;
            border-left-color: #d97706;
            color: #d97706;
        }

        .nav-icon-wrapper {
            width: 1.5rem;
            height: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-right: 0.75rem;
            transition: margin 0.3s ease;
        }

        .sidebar-text {
            opacity: 1;
            transition: opacity 0.2s ease, transform 0.3s ease;
            transform: translateX(0);
        }

        /* --- COLLAPSED STATE --- */
        #sidebar.is-collapsed .nav-item {
            padding-left: 0;
            padding-right: 0;
            justify-content: center;
        }

        #sidebar.is-collapsed .nav-icon-wrapper {
            margin-right: 0;
        }

        #sidebar.is-collapsed .sidebar-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
            transform: translateX(10px);
        }

        .section-header {
            padding-left: 1.5rem;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            opacity: 1;
            max-height: 20px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        #sidebar.is-collapsed .section-header {
            opacity: 0;
            max-height: 0;
            margin-top: 0;
            margin-bottom: 0;
            padding-left: 0;
        }

        .user-card {
            transition: all 0.3s ease;
            padding: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        #sidebar.is-collapsed .user-card {
            justify-content: center;
            gap: 0;
            padding-left: 0;
            padding-right: 0;
        }

        .logo-container {
            transition: all 0.3s ease;
            padding-left: 1.5rem;
        }

        #sidebar.is-collapsed .logo-container {
            padding-left: 0;
            justify-content: center;
        }

        /* ============================================ */
        /* 🌟 ULTRA SMOOTH LOGO TRANSITION - BÁ ĐẠO 🌟 */
        /* ============================================ */

        /* Logo Container - Tối ưu để chứa cả 2 logo */
        .logo-wrapper {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            overflow: hidden;
        }

        /* Logo Full - Expanded State */
        .logo-full {
            position: absolute;
            left: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            opacity: 1;
            transform: translateX(0) scale(1);
            filter: blur(0px);
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            /* Elastic easing */
            pointer-events: auto;
            will-change: transform, opacity, filter;
        }

        /* Logo Mini - Hidden State */
        .logo-mini {
            position: absolute;
            left: 50%;
            transform: translateX(-50%) scale(0.3) rotate(-180deg);
            opacity: 0;
            filter: blur(10px);
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            /* Elastic easing */
            pointer-events: none;
            will-change: transform, opacity, filter;
        }

        /* ========================================== */
        /* COLLAPSED STATE - Logo Mini hiện lên */
        /* ========================================== */

        #sidebar.is-collapsed .logo-full {
            opacity: 0;
            transform: translateX(-30px) scale(0.7);
            filter: blur(8px);
            pointer-events: none;
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            /* Back easing */
        }

        #sidebar.is-collapsed .logo-mini {
            opacity: 1;
            transform: translateX(-50%) scale(1) rotate(0deg);
            filter: blur(0px);
            pointer-events: auto;
            transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            /* Elastic với delay dài hơn */
            transition-delay: 0.1s;
            /* Delay nhẹ để logo full kịp biến mất */
        }

        /* ========================================== */
        /* EXPANDING STATE - Logo Full hiện lại */
        /* ========================================== */

        #sidebar:not(.is-collapsed) .logo-full {
            transition-delay: 0.15s;
            /* Logo full xuất hiện sau khi mini đã nhỏ lại */
        }

        #sidebar:not(.is-collapsed) .logo-mini {
            transition-delay: 0s;
            /* Logo mini biến mất ngay lập tức */
        }

        /* ========================================== */
        /* THÊM GLOW EFFECT KHI CHUYỂN ĐỔI (OPTIONAL) */
        /* ========================================== */

        .logo-full img,
        .logo-mini img {
            transition: all 0.5s ease;
            filter: drop-shadow(0 0 0px rgba(217, 119, 6, 0));
        }

        #sidebar.is-collapsed .logo-mini img {
            animation: logoGlow 0.6s ease-in-out;
        }

        #sidebar:not(.is-collapsed) .logo-full img {
            animation: logoGlow 0.6s ease-in-out;
        }

        @keyframes logoGlow {
            0% {
                filter: drop-shadow(0 0 0px rgba(217, 119, 6, 0));
            }

            50% {
                filter: drop-shadow(0 0 20px rgba(217, 119, 6, 0.8));
            }

            100% {
                filter: drop-shadow(0 0 0px rgba(217, 119, 6, 0));
            }
        }

        /* ========================================== */
        /* PRELOAD STATE - Tránh FOUC */
        /* ========================================== */

        .sidebar-closed .logo-full {
            opacity: 0;
            transform: translateX(-30px) scale(0.7);
            filter: blur(8px);
            pointer-events: none;
        }

        .sidebar-closed .logo-mini {
            opacity: 1;
            transform: translateX(-50%) scale(1) rotate(0deg);
            filter: blur(0px);
            pointer-events: auto;
        }
    </style>

    <script>
        // Script chạy ngay lập tức để tránh FOUC
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            document.documentElement.classList.add('sidebar-closed');
        }
    </script>
    <style>
        /* CSS Override khi script trên hoạt động ngay lúc load trang */
        .sidebar-closed #sidebar {
            width: 5.5rem !important;
        }

        .sidebar-closed .sidebar-text {
            opacity: 0;
            width: 0;
            display: none;
        }

        .sidebar-closed .section-header {
            opacity: 0;
            max-height: 0;
            margin: 0;
            display: none;
        }

        .sidebar-closed .nav-item {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        .sidebar-closed .nav-icon-wrapper {
            margin-right: 0;
        }

        .sidebar-closed .user-card {
            justify-content: center;
            gap: 0;
        }

        .sidebar-closed .logo-container {
            padding-left: 0;
            justify-content: center;
        }
    </style>
</head>

<body class="antialiased h-screen flex overflow-hidden bg-slate-900">

    <!-- SIDEBAR -->
    <aside id="sidebar" class="bg-slate-900 border-r border-slate-800 flex flex-col h-screen z-20 shadow-2xl">

        <!-- 1. Logo -->
        <div class="logo-container h-16 flex items-center border-b border-slate-800 shrink-0 bg-slate-900 overflow-hidden whitespace-nowrap relative">
            <a href="{{ route('home') }}" class="logo-wrapper">

                <!-- Logo Full -->
                <div class="logo-full">
                    <img src="{{ asset('storage/logo/logoDark.png') }}" alt="TruyenVH" class="h-8 shrink-0">
                </div>

                <!-- Logo Mini -->
                <div class="logo-mini">
                    <img src="{{ asset('storage/logo/logoMiniDark.png') }}" alt="TruyenVH Mini" class="h-8 shrink-0">
                </div>

            </a>
        </div>

        <!-- 2. Scrollable Content -->
        <div class="flex-1 overflow-y-auto custom-scroll py-4 overflow-x-hidden">

            <!-- User Info -->
            <a href="{{ route('admin.dashboard') }}">
                <div class="px-4 mb-6 whitespace-nowrap transition-all duration-300">
                    <div class="user-card bg-slate-800 rounded-lg border border-slate-700 hover:border-orange-500/50">
                        <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name ?? 'Admin') . '&background=ea580c&color=fff' }}"
                            class="w-10 h-10 min-w-[2.5rem] min-h-[2.5rem] rounded-full border-2 border-slate-600 shrink-0 object-cover aspect-square shadow-sm bg-slate-700"
                            alt="User Avatar">

                        <div class="overflow-hidden sidebar-text">
                            <p class="text-sm font-bold text-white truncate brand-font">{{ auth()->user()->name ?? 'Quản trị viên' }}</p>
                            <p class="text-xs text-orange-500 font-mono-tech">{{ auth()->user()->email ?? 'Admin' }}</p>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Navigation -->
            <nav class="space-y-1 px-2 whitespace-nowrap">

                <p class="section-header text-[10px] font-bold text-slate-500 uppercase tracking-widest">Tổng quan</p>

                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} rounded-lg" title="Dashboard">
                    <div class="nav-icon-wrapper"><i class="fas fa-chart-pie text-lg"></i></div>
                    <span class="sidebar-text">Dashboard</span>
                </a>
                <a href="#" class="nav-item text-slate-400 rounded-lg" title="Thông báo">
                    <div class="nav-icon-wrapper"><i class="fas fa-bell text-lg"></i></div>
                    <span class="sidebar-text">Thông báo</span>
                    <span class="sidebar-text ml-auto bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold">3</span>
                </a>

                <p class="section-header text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-6">Quản lý Truyện</p>

                <a href="{{ route('admin.comics.index') }}" class="nav-item text-slate-400 rounded-lg" title="Danh sách Truyện">
                    <div class="nav-icon-wrapper"><i class="fas fa-book text-lg"></i></div>
                    <span class="sidebar-text">Danh sách Truyện</span>
                </a>
                <a href="{{ route('admin.chapters.index') }}" class="nav-item text-slate-400 rounded-lg" title="Quản lý Chapter">
                    <div class="nav-icon-wrapper"><i class="fas fa-list text-lg"></i></div>
                    <span class="sidebar-text">Quản lý Chapter</span>
                </a>

                <a href="{{ route('admin.categories.index') }}" class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : 'text-slate-400' }} rounded-lg" title="Thể loại / Tags">
                    <div class="nav-icon-wrapper"><i class="fas fa-tags text-lg"></i></div>
                    <span class="sidebar-text">Thể loại / Tags</span>
                </a>

                <p class="section-header text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-6">Hệ thống</p>

                <a href="{{ route('admin.users.index') }}" class="nav-item text-slate-400 rounded-lg" title="Thành viên">
                    <div class="nav-icon-wrapper"><i class="fas fa-users text-lg"></i></div>
                    <span class="sidebar-text">Thành viên</span>
                </a>
                <a href="{{ route('admin.violation.index') }}" class="nav-item text-slate-400 rounded-lg" title="Xử lý vi phạm">
                    <div class="nav-icon-wrapper"><i class="fas fa-comment-dots text-lg"></i></div>
                    <span class="sidebar-text">Quản lý bình luận</span>
                </a>
                <a href="#" class="nav-item text-slate-400 rounded-lg" title="Cài đặt hệ thống">
                    <div class="nav-icon-wrapper"><i class="fas fa-cog text-lg"></i></div>
                    <span class="sidebar-text">Cài đặt hệ thống</span>
                </a>

                <div class="h-24"></div>
            </nav>
        </div>

        <!-- 3. Footer (Logout) -->
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <div class="p-4 border-t border-slate-800 bg-slate-900 shrink-0 whitespace-nowrap">
                <button class="logout-btn w-full flex items-center justify-start nav-item bg-slate-800 hover:bg-red-600 text-slate-300 hover:text-white rounded-lg border border-slate-700 hover:border-red-500 transition-all font-bold shadow-sm overflow-hidden" title="Đăng xuất">
                    <div class="nav-icon-wrapper"><i class="fas fa-sign-out-alt text-lg"></i></div>
                    <span class="sidebar-text">Đăng xuất</span>
                </button>
            </div>
        </form>

    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col relative overflow-hidden bg-[#0f172a]">

        <!-- Top Header -->
        <header class="h-16 bg-slate-900/90 backdrop-blur-sm border-b border-slate-800 flex items-center justify-between px-6 z-10 sticky top-0 shrink-0">

            <div class="flex items-center gap-4">
                <!-- Nút 3 gạch (Hamburger) -->
                <button id="sidebar-toggle" class="text-slate-400 hover:text-white transition-transform active:scale-95 p-2 rounded hover:bg-slate-800 focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <!-- Tiêu đề trang -->
                <h2 class="text-xl font-bold text-white brand-font tracking-wide">@yield('header', 'Dashboard')</h2>

                <!-- Divergence Number -->
                <div class="hidden md:flex items-center gap-2 px-3 py-1 bg-black/40 rounded border border-slate-700 ml-4">
                    <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                    <span class="text-orange-500 font-mono-tech font-bold text-sm tracking-widest">1.048596</span>
                </div>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-6">
                <!-- Search -->
                <div class="relative hidden md:block">
                    <input type="text" placeholder="Tìm kiếm nhanh..." class="bg-slate-800 border border-slate-700 text-sm rounded-full pl-10 pr-4 py-1.5 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all w-64 text-slate-200">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                </div>

                <!-- Notifications -->
                <button class="text-slate-400 hover:text-white relative transition">
                    <i class="far fa-bell text-xl"></i>
                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
                    </span>
                </button>
            </div>
        </header>

        <!-- Scrollable Dashboard Content -->
        <div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-8 relative">
            @if(session('success'))
            <div class="mb-6 bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-lg text-sm flex items-center animate-fade-in">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
            @endif

            @yield('content')

            <div class="mt-8 text-center opacity-30 pb-4">
                <p class="text-[10px] uppercase font-bold tracking-[0.3em] font-mono-tech">El Psy Kongroo</p>
            </div>
        </div>
    </main>

    <!-- SCRIPT XỬ LÝ TOGGLE SIDEBAR + LOCALSTORAGE -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebar-toggle');

            function applyState(collapsed) {
                if (collapsed) {
                    sidebar.classList.add('is-collapsed');
                    document.documentElement.classList.add('sidebar-closed');
                } else {
                    sidebar.classList.remove('is-collapsed');
                    document.documentElement.classList.remove('sidebar-closed');
                }
            }

            const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            applyState(isCollapsed);

            toggleBtn.addEventListener('click', function() {
                const willCollapse = !sidebar.classList.contains('is-collapsed');
                sidebar.classList.toggle('is-collapsed');
                document.documentElement.classList.toggle('sidebar-closed');
                localStorage.setItem('sidebar-collapsed', willCollapse);
            });
        });
    </script>

</body>

</html>