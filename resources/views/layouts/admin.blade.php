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

    <!-- Fonts: Chakra Petch (Tiêu đề) & Nunito (Nội dung) -->
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;500;600;700&family=Nunito:wght@400;600;700&family=Share+Tech+Mono&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            /* Dễ đọc hơn */
            background-color: #0f172a;
            /* Slate 900 */
            color: #cbd5e1;
            /* Slate 300 */
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

        /* Scrollbar mảnh, đẹp hơn */
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

        /* Card Style: Sạch sẽ, ít hiệu ứng thừa */
        .dashboard-card {
            background-color: #1e293b;
            /* Slate 800 */
            border: 1px solid #334155;
            border-radius: 0.75rem;
            /* Rounded-xl */
            transition: transform 0.2s, border-color 0.2s;
        }

        .dashboard-card:hover {
            border-color: #d97706;
            /* Orange hover */
            transform: translateY(-2px);
        }

        /* Nav Item Styles */
        .nav-item {
            transition: all 0.2s;
            border-left: 3px solid transparent;
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

        /* Table Styles Clean */
        .clean-table th {
            font-family: 'Chakra Petch', sans-serif;
            font-weight: 600;
            color: #94a3b8;
            border-bottom: 1px solid #334155;
            background-color: #1e293b;
        }

        .clean-table td {
            border-bottom: 1px solid #1e293b;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .clean-table tr:last-child td {
            border-bottom: none;
        }

        /* Input styles for admin forms */
        .admin-input {
            background-color: #1e293b;
            border: 1px solid #334155;
            color: white;
            transition: all 0.2s;
        }

        .admin-input:focus {
            outline: none;
            border-color: #d97706;
            ring: 1px #d97706;
        }
    </style>
</head>

<body class="antialiased h-screen flex overflow-hidden bg-slate-900">

    <!-- SIDEBAR (Fixed Height, Scrollable Menu) -->
    <aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col h-screen z-20 shadow-2xl shrink-0">

        <!-- 1. Logo (Fixed) -->
        <div class="h-16 flex items-center px-6 border-b border-slate-800 shrink-0 bg-slate-900">
            <div class="text-2xl font-bold tracking-wider text-white brand-font flex items-center gap-2">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ asset('storage/logo/logoDark.png') }}" alt="TruyenVH Logo" class="h-8">
                </a>
            </div>
        </div>

        <!-- 2. Scrollable Content (User Info + Menu) -->
        <div class="flex-1 overflow-y-auto custom-scroll py-4">

            <!-- User Info (Compact) -->
            <div class="px-4 mb-6">
                <div class="bg-slate-800 rounded-lg p-3 flex items-center gap-3 border border-slate-700">
                    <img src="https://ui-avatars.com/api/?name=H+K&background=ea580c&color=fff" class="w-10 h-10 rounded-full border-2 border-slate-600">
                    <div class="overflow-hidden">
                        <p class="text-sm font-bold text-white truncate brand-font">Hououin Kyouma</p>
                        <p class="text-xs text-orange-500 font-mono-tech">Role: Mad Scientist</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="space-y-1 px-2">
                <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 mt-2">Tổng quan</p>

                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-r-lg">
                    <i class="fas fa-chart-pie w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="nav-item text-slate-400 flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-r-lg">
                    <i class="fas fa-bell w-5 text-center"></i>
                    <span>Thông báo</span>
                    <span class="ml-auto bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold">3</span>
                </a>

                <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 mt-6">Quản lý Truyện</p>

                <a href="#" class="nav-item text-slate-400 flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-r-lg">
                    <i class="fas fa-book w-5 text-center"></i>
                    <span>Danh sách Truyện</span>
                </a>
                <a href="#" class="nav-item text-slate-400 flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-r-lg">
                    <i class="fas fa-file-upload w-5 text-center"></i>
                    <span>Đăng truyện mới</span>
                </a>
                <a href="#" class="nav-item text-slate-400 flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-r-lg">
                    <i class="fas fa-list w-5 text-center"></i>
                    <span>Quản lý Chapter</span>
                </a>

                <!-- Link đến Categories Index -->
                <a href="{{ route('admin.categories.index') }}" class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : 'text-slate-400' }} flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-r-lg">
                    <i class="fas fa-tags w-5 text-center"></i>
                    <span>Thể loại / Tags</span>
                </a>

                <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 mt-6">Người dùng & Hệ thống</p>

                <a href="#" class="nav-item text-slate-400 flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-r-lg">
                    <i class="fas fa-users w-5 text-center"></i>
                    <span>Thành viên</span>
                </a>
                <a href="#" class="nav-item text-slate-400 flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-r-lg">
                    <i class="fas fa-comment-dots w-5 text-center"></i>
                    <span>Bình luận</span>
                </a>
                <a href="#" class="nav-item text-slate-400 flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-r-lg">
                    <i class="fas fa-cog w-5 text-center"></i>
                    <span>Cài đặt hệ thống</span>
                </a>

                <!-- Demo scroll: Thêm mục giả để test cuộn -->
                <div class="h-24"></div>
            </nav>
        </div>

        <!-- 3. Footer (Logout) (Fixed) -->
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <div class="p-4 border-t border-slate-800 bg-slate-900 shrink-0">
                <button class="w-full flex items-center justify-center gap-2 bg-slate-800 hover:bg-red-600 text-slate-300 hover:text-white py-2.5 rounded-lg border border-slate-700 hover:border-red-500 transition-all text-sm font-bold shadow-sm">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </button>
            </div>
        </form>

    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col relative overflow-hidden bg-[#0f172a]">

        <!-- Top Header -->
        <header class="h-16 bg-slate-900/90 backdrop-blur-sm border-b border-slate-800 flex items-center justify-between px-8 z-10 sticky top-0 shrink-0">

            <!-- Breadcrumb / Title -->
            <div class="flex items-center gap-4">
                <button class="md:hidden text-slate-400 hover:text-white"><i class="fas fa-bars text-xl"></i></button>

                <!-- Tiêu đề động cho từng trang -->
                <h2 class="text-xl font-bold text-white brand-font tracking-wide">@yield('header', 'Dashboard')</h2>

                <!-- Divergence Number (Tinh tế hơn) -->
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

            <!-- Hiển thị thông báo (nếu có) -->
            @if(session('success'))
            <div class="mb-6 bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-lg text-sm flex items-center animate-fade-in">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
            @endif

            <!-- Nội dung động của từng trang sẽ được render ở đây -->
            @yield('content')

            <div class="mt-8 text-center opacity-30 pb-4">
                <p class="text-[10px] uppercase font-bold tracking-[0.3em] font-mono-tech">El Psy Kongroo</p>
            </div>
        </div>
    </main>

</body>

</html>