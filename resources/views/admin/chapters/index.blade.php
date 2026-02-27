@extends('layouts.admin')

@section('title', 'Quản lý Chapter')
@section('header', 'Quản lý Chapter')

@section('content')
<div class="max-w-7xl mx-auto p-6 space-y-8">

    {{-- HEADER & FILTER SECTION --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-100 tracking-tight">
                Quản lý Chapter
            </h1>
            <p class="text-slate-400 text-sm mt-1">Chọn một bộ truyện để xem và quản lý danh sách chương.</p>
        </div>

        {{-- BỘ CÔNG CỤ: TÌM KIẾM + DROPDOWN CUSTOM --}}
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto z-50">

            {{-- 1. Ô Tìm kiếm --}}
            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="fas fa-search text-slate-500"></i>
                </div>
                <input type="text" id="search-comic-input" placeholder="Tìm tên truyện..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-slate-200 
                           focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 outline-none transition-all placeholder-slate-500 shadow-lg"
                    autocomplete="off">
            </div>

            {{-- 2. Custom Dropdown --}}
            <style>
                /* Thanh cuộn tinh tế cho Dropdown Custom */
                .admin-scrollbar {
                    scrollbar-width: thin;
                    scrollbar-color: #475569 transparent;
                }

                .admin-scrollbar::-webkit-scrollbar {
                    width: 6px;
                }

                .admin-scrollbar::-webkit-scrollbar-track {
                    background: rgba(0, 0, 0, 0.1);
                    border-radius: 8px;
                }

                .admin-scrollbar::-webkit-scrollbar-thumb {
                    background-color: #475569;
                    border-radius: 8px;
                }

                .admin-scrollbar::-webkit-scrollbar-thumb:hover {
                    background-color: #64748b;
                }
            </style>
            <div class="relative w-full sm:w-80" id="custom-comic-dropdown">
                <button type="button" id="dropdown-toggle-btn"
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-slate-200 
                           hover:border-slate-500 focus:ring-2 focus:ring-blue-500/50 outline-none transition-all shadow-lg text-left">
                    <span class="truncate pr-4 font-medium">
                        {{ $comic ? $comic->title : 'Danh sách truyện...' }}
                    </span>
                    <i class="fas fa-chevron-down text-xs text-slate-500 transition-transform duration-300" id="dropdown-icon"></i>
                </button>

                <div id="dropdown-menu"
                    class="absolute right-0 mt-2 w-full sm:w-[450px] bg-slate-800 border border-slate-700 rounded-xl shadow-2xl z-50 hidden opacity-0 transition-opacity duration-200 origin-top-right">

                    <div class="px-4 py-3 border-b border-slate-700 bg-slate-800/80 rounded-t-xl">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Danh sách truyện</span>
                    </div>

                    {{-- Danh sách giới hạn chiều cao max-h-64 (Khoảng 250px) và có scrollbar custom --}}
                    <ul class="max-h-64 overflow-y-auto admin-scrollbar py-2" id="comic-list">
                        @foreach ($comics as $c)
                        <li>
                            <a href="{{ route('admin.chapters.by-comic', $c) }}"
                                class="comic-item flex items-center px-4 py-2.5 text-sm text-slate-300 hover:bg-slate-700 hover:text-white transition-colors
                                      {{ isset($comic) && $comic->id === $c->id ? 'bg-blue-600/20 text-blue-400 font-bold border-l-4 border-blue-500' : 'border-l-4 border-transparent' }}"
                                data-title="{{ mb_strtolower($c->title) }}">
                                <span class="truncate">{{ $c->title }}</span>
                            </a>
                        </li>
                        @endforeach

                        {{-- Thông báo khi tìm không thấy --}}
                        <li id="no-results-msg" class="hidden px-4 py-6 text-sm text-slate-500 text-center">
                            <i class="fas fa-box-open text-3xl mb-2 opacity-50 block"></i>
                            Không tìm thấy truyện nào phù hợp
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

    {{-- SCRIPT XỬ LÝ DROPDOWN & TÌM KIẾM --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownBtn = document.getElementById('dropdown-toggle-btn');
            const dropdownMenu = document.getElementById('dropdown-menu');
            const dropdownIcon = document.getElementById('dropdown-icon');
            const searchInput = document.getElementById('search-comic-input');
            const comicItems = document.querySelectorAll('.comic-item');
            const noResultsMsg = document.getElementById('no-results-msg');

            // 1. Mở/Đóng dropdown
            dropdownBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleDropdown();
            });

            function toggleDropdown(forceOpen = false) {
                if (forceOpen || dropdownMenu.classList.contains('hidden')) {
                    dropdownMenu.classList.remove('hidden');
                    setTimeout(() => dropdownMenu.classList.remove('opacity-0'), 10);
                    dropdownIcon.classList.add('rotate-180');
                } else {
                    dropdownMenu.classList.add('opacity-0');
                    dropdownIcon.classList.remove('rotate-180');
                    setTimeout(() => dropdownMenu.classList.add('hidden'), 200);
                }
            }

            // 2. Click ra ngoài thì đóng
            document.addEventListener('click', function(e) {
                if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.classList.add('opacity-0');
                    dropdownIcon.classList.remove('rotate-180');
                    setTimeout(() => dropdownMenu.classList.add('hidden'), 200);
                }
            });

            // 3. Logic Tìm kiếm
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                let hasVisibleItems = false;

                // Nếu đang gõ mà dropdown đang đóng -> Tự động mở
                if (searchTerm.length > 0) {
                    toggleDropdown(true);
                }

                // Lọc danh sách
                comicItems.forEach(item => {
                    const title = item.dataset.title;
                    if (title.includes(searchTerm)) {
                        item.parentElement.style.display = 'block';
                        hasVisibleItems = true;
                    } else {
                        item.parentElement.style.display = 'none';
                    }
                });

                // Hiện thông báo trống
                if (!hasVisibleItems) {
                    noResultsMsg.classList.remove('hidden');
                } else {
                    noResultsMsg.classList.add('hidden');
                }
            });
        });
    </script>
    @endpush

    {{-- CONTENT SECTION --}}
    @if ($comic)
    <div class="bg-slate-800 rounded-2xl border border-slate-700/60 shadow-xl overflow-hidden relative z-0">

        {{-- NEW HEADER: COMIC INFO + IMAGE --}}
        <div class="p-6 border-b border-slate-700/60 bg-slate-800/50 flex items-center gap-5">
            {{-- Ảnh bìa truyện --}}
            <div class="shrink-0 w-16 h-24 rounded-lg overflow-hidden border border-slate-600/50 shadow-lg relative group">
                <img src="{{ $comic->cover_url }}" alt="{{ $comic->title }}"
                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
            </div>

            {{-- Thông tin --}}
            <div>
                <h3 class="text-xl font-bold text-white leading-tight mb-2">
                    {{ $comic->title }}
                </h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20">
                    <i class="fas fa-layer-group mr-1.5"></i>
                    Tổng: {{ $chapters->total() }} chương
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-900/50 text-slate-400 uppercase text-xs font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Chapter</th>
                        <th class="px-6 py-4 text-center">Lượt xem</th>
                        <th class="px-6 py-4 text-center">Ngày đăng</th>
                        <th class="px-6 py-4 text-right">Hành động</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-700/60">
                    @forelse ($chapters as $chapter)
                    <tr class="hover:bg-slate-700/30 transition-colors duration-150 group">
                        {{-- Cột Tên Chapter --}}
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-200 group-hover:text-blue-400 transition-colors">
                                Chapter {{ $chapter->chapter_number }}
                            </div>
                            @if($chapter->title)
                            <div class="text-xs text-slate-500 mt-0.5">{{ $chapter->title }}</div>
                            @endif
                        </td>

                        {{-- Cột Lượt xem --}}
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-slate-700/50 text-cyan-400 border border-slate-600/50">
                                <i class="far fa-eye mr-1.5"></i>
                                {{ number_format($chapter->views) }}
                            </span>
                        </td>

                        {{-- Cột Ngày đăng --}}
                        <td class="px-6 py-4 text-center text-slate-400 text-xs">
                            {{ $chapter->created_at ? $chapter->created_at->format('d/m/Y') : '--' }}
                        </td>

                        {{-- Cột Hành động --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Nút Xem --}}
                                <a href="{{ route('user.comics.chapters.read', ['comic' => $comic->id, 'chapter_number' => $chapter->chapter_number]) }}"
                                    target="_blank"
                                    class="p-2 rounded-lg text-slate-400 hover:text-blue-400 hover:bg-blue-500/10 transition-all duration-200"
                                    title="Xem chapter">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>

                                {{-- Nút Xóa --}}
                                <form action="{{ route('admin.chapters.destroy', ['comic' => $comic->id, 'chapter' => $chapter->id]) }}" method="POST"
                                    class="inline-block"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa Chapter {{ $chapter->chapter_number }} không?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-all duration-200"
                                        title="Xóa chapter">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-slate-700/50 rounded-full flex items-center justify-center mb-4 animate-pulse">
                                    <i class="fas fa-layer-group text-3xl text-slate-500"></i>
                                </div>
                                <h3 class="text-slate-300 font-medium text-lg">Chưa có dữ liệu</h3>
                                <p class="text-slate-500 text-sm mt-1">Truyện này hiện chưa có chapter nào được đăng tải.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-700 bg-slate-800 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-slate-400">

            {{-- Hiển thị thông tin --}}
            <span class="order-2 sm:order-1">
                Hiển thị
                <strong>{{ $chapters->firstItem() }}</strong> -
                <strong>{{ $chapters->lastItem() }}</strong>
                trong tổng số
                <strong>{{ $chapters->total() }}</strong> kết quả
            </span>

            {{-- Nút chuyển trang --}}
            <div class="flex gap-1 order-1 sm:order-2">

                {{-- Previous --}}
                <a href="{{ $chapters->previousPageUrl() }}"
                    class="px-3 py-1.5 rounded-md border border-slate-600 hover:bg-slate-700 hover:text-white transition 
                    {{ $chapters->onFirstPage() ? 'opacity-50 pointer-events-none' : '' }}">
                    <i class="fas fa-chevron-left"></i>
                </a>

                {{-- Numbered Pages --}}
                @foreach ($chapters->getUrlRange(1, $chapters->lastPage()) as $page => $url)
                <a href="{{ $url }}"
                    class="px-3 py-1.5 rounded-md border border-slate-600 hover:bg-slate-700 hover:text-white transition
                {{ $page == $chapters->currentPage() ? 'bg-orange-600 text-white font-bold border-none' : '' }}">
                    {{ $page }}
                </a>
                @endforeach

                {{-- Next --}}
                <a href="{{ $chapters->nextPageUrl() }}"
                    class="px-3 py-1.5 rounded-md border border-slate-600 hover:bg-slate-700 hover:text-white transition
                    {{ !$chapters->hasMorePages() ? 'opacity-50 pointer-events-none' : '' }}">
                    <i class="fas fa-chevron-right"></i>
                </a>

            </div>

        </div>
    </div>
    @else
    {{-- Empty State khi chưa chọn truyện --}}
    <div class="flex flex-col items-center justify-center py-20 bg-slate-800 rounded-2xl border border-slate-700/60 border-dashed">
        <div class="w-20 h-20 bg-slate-700/30 rounded-full flex items-center justify-center mb-4 animate-pulse">
            <i class="fas fa-book-open text-4xl text-slate-500"></i>
        </div>
        <h2 class="text-xl font-semibold text-slate-300">Chưa chọn truyện</h2>
        <p class="text-slate-500 mt-2 max-w-sm text-center">Vui lòng chọn một bộ truyện từ danh sách phía trên để bắt đầu quản lý các chapter.</p>
    </div>
    @endif
</div>
@endsection