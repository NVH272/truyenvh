@extends('layouts.admin')

@section('title', 'Danh sách Truyện')
@section('header', 'Quản lý Truyện')

@section('content')
<style>
    /* Custom Scrollbar cho bảng */
    .custom-scrollbar::-webkit-scrollbar {
        height: 6px;
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #1e293b;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #475569;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }

    /* Animation xuất hiện nhẹ */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeIn 0.4s ease-out forwards;
    }
</style>

<div class="p-6 space-y-8 animate-fade-in">

    {{-- 2. FILTER BAR (GLASS EFFECT) --}}
    <div class="relative rounded-2xl bg-slate-900/70 backdrop-blur-md border border-slate-700 shadow-xl">

        {{-- Filter header nhỏ --}}
        <div class="flex items-center justify-between px-4 pt-3 pb-2 text-[11px] uppercase tracking-[0.18em] text-slate-400">
            <span class="flex items-center gap-2">
                <i class="fas fa-sliders-h text-[10px] text-orange-400"></i>
                <span>Bộ lọc truyện</span>
            </span>
            <span class="hidden md:inline text-slate-500">
                Tổng:
                <span class="text-slate-200 font-semibold">{{ $comics->total() }}</span>
                truyện
            </span>
        </div>

        <div class="border-t border-slate-700/60 mt-1"></div>

        <form method="GET" class="flex flex-col lg:flex-row gap-2 p-3">

            {{-- Search Input --}}
            <div class="relative flex-grow group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-slate-500 group-focus-within:text-orange-500 transition-colors"></i>
                </div>
                <input type="text" name="search" value="{{ $search }}"
                    class="block w-full pl-10 pr-3 py-2.5 bg-slate-900/70 border border-slate-700/80 rounded-xl text-slate-200 placeholder-slate-500
                           focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500/70 transition-all text-sm font-medium"
                    placeholder="Nhập tên truyện, tác giả...">
            </div>

            {{-- Status Filter --}}
            <div class="min-w-[200px] relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-filter text-slate-500 group-focus-within:text-orange-500 transition-colors"></i>
                </div>
                <select name="status"
                    onchange="this.form.submit()"
                    class="block w-full pl-10 pr-8 py-2.5 bg-slate-900/70 border border-slate-700/80 rounded-xl text-slate-200
                           focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500/70 transition-all text-sm font-medium
                           appearance-none cursor-pointer">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="ongoing" @selected($status==='ongoing' )>Đang tiến hành</option>
                    <option value="completed" @selected($status==='completed' )>Đã hoàn thành</option>
                    <option value="dropped" @selected($status==='dropped' )>Tạm dừng</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-500">
                    <i class="fas fa-chevron-down text-xs"></i>
                </div>
            </div>

            {{-- NÚT THÊM TRUYỆN MỚI --}}
            <a href="{{ route('admin.comics.create') }}"
                class="px-6 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-orange-500 to-red-600 rounded-xl shadow-lg
                      hover:shadow-orange-500/30 hover:-translate-y-0.5 transition-all whitespace-nowrap flex items-center justify-center gap-2">
                <i class="fas fa-plus text-xs"></i>
                Thêm Truyện Mới
            </a>

            {{-- NÚT TRUYỆN ĐANG CHỜ DUYỆT --}}
            <a href="{{ route('admin.comics.pending') }}"
                class="px-6 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-orange-500 to-red-600 rounded-xl shadow-lg
                      hover:shadow-orange-500/30 hover:-translate-y-0.5 transition-all whitespace-nowrap flex items-center justify-center gap-2 relative">
                <i class="fas fa-clock text-xs"></i>
                <span>Truyện chờ duyệt</span>
                @if(isset($pendingCount) && $pendingCount > 0)
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center shadow-lg">
                    {{ $pendingCount > 9 ? '9+' : $pendingCount }}
                </span>
                @endif
            </a>
        </form>
    </div>

    {{-- 3. DATA TABLE --}}
    <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700/50 rounded-2xl shadow-2xl overflow-hidden flex flex-col">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-slate-400 border-b border-slate-700 bg-slate-900/50">
                        <th class="px-6 py-4 font-semibold w-16 text-center">ID</th>
                        <th class="px-6 py-4 font-semibold min-w-[300px]">Thông tin truyện</th>
                        <th class="px-6 py-4 font-semibold">Tác giả</th>
                        <th class="px-6 py-4 font-semibold text-center">Trạng thái</th>
                        <th class="px-6 py-4 font-semibold text-center">Thống kê</th>
                        <th class="px-6 py-4 font-semibold text-center w-32">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50 text-sm">
                    @forelse($comics as $comic)
                    <tr class="group hover:bg-slate-700/30 transition-colors duration-200">
                        {{-- ID --}}
                        <td class="px-6 py-4 text-center text-slate-500 font-mono">
                            #{{ $comic->id }}
                        </td>

                        {{-- Info --}}
                        <td class="px-6 py-4">
                            <div class="flex items-start gap-4">
                                <div class="relative flex-shrink-0 w-16 h-24 rounded-lg overflow-hidden shadow-md
                                            group-hover:shadow-orange-500/20 group-hover:ring-2 ring-orange-500/50 transition-all duration-300">
                                    <img src="{{ $comic->cover_url }}" alt="Cover"
                                        class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                                </div>
                                <div class="flex flex-col justify-center h-24">
                                    <h3 class="text-base font-bold text-slate-100 group-hover:text-orange-400 transition-colors line-clamp-2 leading-tight mb-1">
                                        {{ $comic->title }}
                                    </h3>
                                    <div class="flex items-center gap-3 text-xs text-slate-400">
                                        <span class="bg-slate-700/50 px-2 py-0.5 rounded text-slate-300">
                                            {{ $comic->chapter_count ?? 0 }} chương
                                        </span>
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-star text-yellow-500 text-[10px]"></i>
                                            <span>{{ number_format($comic->rating, 1) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Author --}}
                        <td class="px-6 py-4">
                            <span class="text-slate-300 font-medium">{{ $comic->author ?? 'Đang cập nhật' }}</span>
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4 text-center">
                            @php
                            $statusClasses = [
                            'ongoing' => 'bg-blue-500/10 text-blue-400 border-blue-500/20 shadow-blue-500/10',
                            'completed' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 shadow-emerald-500/10',
                            'dropped' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20 shadow-yellow-500/10',
                            ];
                            $statusLabels = [
                            'ongoing' => 'Đang tiến hành',
                            'completed' => 'Đã hoàn thành',
                            'dropped' => 'Tạm dừng',
                            ];
                            $currentStatus = $comic->status ?? 'dropped';
                            @endphp
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold border shadow-lg {{ $statusClasses[$currentStatus] ?? $statusClasses['dropped'] }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-current mr-2 animate-pulse"></span>
                                {{ $statusLabels[$currentStatus] ?? 'Không rõ' }}
                            </span>
                        </td>

                        {{-- Stats --}}
                        <td class="px-6 py-4">
                            <div class="flex flex-col items-center gap-1">
                                <div class="flex items-center gap-2 text-slate-300" title="Lượt xem">
                                    <i class="far fa-eye text-xs text-indigo-400"></i>
                                    <span class="font-mono font-semibold">{{ number_format($comic->views ?? 0) }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-slate-400" title="Theo dõi">
                                    <i class="far fa-heart text-xs text-rose-400"></i>
                                    <span class="font-mono text-xs">{{ number_format($comic->follows ?? 0) }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.comics.edit', $comic) }}"
                                    class="p-2 rounded-lg bg-slate-700/50 text-blue-400 hover:bg-blue-500 hover:text-white transition-all shadow hover:shadow-blue-500/30"
                                    title="Chỉnh sửa">
                                    <i class="fas fa-pen text-sm"></i>
                                </a>

                                <form action="{{ route('admin.comics.destroy', $comic) }}" method="POST"
                                    onsubmit="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn xóa truyện này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2 rounded-lg bg-slate-700/50 text-red-400 hover:bg-red-500 hover:text-white transition-all shadow hover:shadow-red-500/30"
                                        title="Xóa truyện">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-500">
                            @if(request('search') || request('status'))
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-search-minus text-4xl mb-3 text-slate-600"></i>
                                <p class="mb-1">
                                    Không tìm thấy truyện nào phù hợp với điều kiện lọc hiện tại.
                                </p>
                                @if(request('search'))
                                <p class="text-sm">
                                    Từ khóa:
                                    "<span class="font-semibold">{{ request('search') }}</span>"
                                </p>
                                @endif
                            </div>
                            @else
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-book-open text-4xl mb-3 text-slate-600"></i>
                                <p>Chưa có truyện nào trong hệ thống.</p>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-slate-700/50 bg-slate-900/30 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="text-xs text-slate-400 font-medium">
                Hiển thị
                <span class="text-slate-200">{{ $comics->firstItem() ?? 0 }}</span>
                -
                <span class="text-slate-200">{{ $comics->lastItem() ?? 0 }}</span>
                của
                <span class="text-slate-200">{{ $comics->total() }}</span>
                truyện
            </div>

            <div class="flex items-center gap-1">
                @if ($comics->onFirstPage())
                <span class="px-3 py-1 rounded-md bg-slate-800 text-slate-600 cursor-not-allowed border border-slate-700">
                    <i class="fas fa-chevron-left text-xs"></i>
                </span>
                @else
                <a href="{{ $comics->previousPageUrl() }}"
                    class="px-3 py-1 rounded-md bg-slate-800 text-slate-300 hover:bg-slate-700 border border-slate-700 transition">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
                @endif

                @foreach ($comics->getUrlRange(max(1, $comics->currentPage() - 1), min($comics->lastPage(), $comics->currentPage() + 1)) as $page => $url)
                <a href="{{ $url }}"
                    class="px-3 py-1 text-sm font-bold rounded-md border transition
                          {{ $page == $comics->currentPage()
                                ? 'bg-orange-600 border-orange-600 text-white shadow-lg shadow-orange-600/20'
                                : 'bg-slate-800 border-slate-700 text-slate-300 hover:bg-slate-700' }}">
                    {{ $page }}
                </a>
                @endforeach

                @if ($comics->hasMorePages())
                <a href="{{ $comics->nextPageUrl() }}"
                    class="px-3 py-1 rounded-md bg-slate-800 text-slate-300 hover:bg-slate-700 border border-slate-700 transition">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
                @else
                <span class="px-3 py-1 rounded-md bg-slate-800 text-slate-600 cursor-not-allowed border border-slate-700">
                    <i class="fas fa-chevron-right text-xs"></i>
                </span>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection