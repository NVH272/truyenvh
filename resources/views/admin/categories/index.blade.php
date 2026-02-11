@extends('layouts.admin')

@section('title', 'Danh sách Thể loại')
@section('header', 'Quản lý Thể loại')

@section('content')
<div class="space-y-6">
    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                
        <form action="{{ route('admin.categories.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 w-full md:w-auto" onsubmit="this.form.submit()">
        
        {{-- TÌM KIẾM --}}
        <div class="relative w-full md:w-72 group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-slate-500 group-focus-within:text-orange-500 transition-colors"></i>
                </div>

                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Tìm kiếm thể loại..."
                    class="admin-input w-full pl-10 pr-4 py-2 rounded-lg text-sm focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 block bg-slate-800 border-slate-700 text-white placeholder-slate-500 transition-all" 
                />
            </div>

            {{-- SẮP XẾP --}}
            <div class="relative">
                <select 
                    name="sort" 
                    onchange="this.form.submit();" 
                    class="appearance-none bg-slate-800 border border-slate-700 text-white text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full pl-3 pr-8 py-2 cursor-pointer transition-all hover:bg-slate-700">
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên (A - Z)</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Tên (Z - A)</option>
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Mới nhất (ID giảm dần)</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Cũ nhất (ID tăng dần)</option>          
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                    <i class="fas fa-chevron-down text-xs"></i>
                </div>
            </div>
        </form>

        {{-- NÚT THÊM THỂ LOẠI MỚI --}}
        <a href="{{ route('admin.categories.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-5 py-2 rounded-lg text-sm font-bold shadow-lg shadow-orange-900/20 transition flex items-center gap-2 transform active:scale-95">
            <i class="fas fa-plus"></i>
            <span class="whitespace-nowrap">Thêm Thể loại</span>
        </a>
    </div>

    <!-- Data Table Card -->
    <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <!-- Table Header -->
                <thead>
                    <!-- ĐÃ SỬA: Dùng brand-font thay vì font-mono-tech để không lỗi tiếng Việt -->
                    <tr class="bg-slate-900/50 border-b border-slate-700 text-slate-400 uppercase tracking-wider brand-font text-xs">
                        <th class="px-6 py-4 font-bold">STT</th>
                        <th class="px-6 py-4 font-bold">#ID</th>
                        <th class="px-6 py-4 font-bold">Tên Thể loại</th>
                        <th class="px-6 py-4 font-bold">Slug (Đường dẫn)</th>
                        <th class="px-6 py-4 font-bold text-center">Trạng thái</th>
                        <th class="px-6 py-4 font-bold text-right">Hành động</th>
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody class="divide-y divide-slate-700/50">
                    @forelse ($categories as $category)
                    <tr class="hover:bg-slate-700/30 transition group">
                        <!-- STT -->
                        <td class="px-6 py-4">
                            {{ $categories->firstItem() + $loop->index }}
                        </td>

                        <!-- ID -->
                        <td class="px-6 py-4 font-mono-tech text-slate-500">
                            #{{ str_pad($category->id, 3, '0', STR_PAD_LEFT) }}
                        </td>

                        <!-- Name + description -->
                        <td class="px-6 py-4">
                            <span class="font-bold text-white brand-font text-base">
                                {{ $category->name }}
                            </span>
                            @if (!empty($category->description))
                            <span class="text-xs text-slate-500 block mt-0.5 line-clamp-1">
                                {{ $category->description }}
                            </span>
                            @endif
                        </td>

                        <!-- Slug -->
                        <td class="px-6 py-4">
                            <code class="text-orange-400 bg-orange-400/10 px-2 py-1 rounded text-xs font-mono-tech">
                                {{ $category->slug ?? '---' }}
                            </code>
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 text-center">
                            @if ($category->is_active ?? 1)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-500/10 text-green-500 border border-green-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5 animate-pulse"></span>
                                Hiển thị
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-700 text-slate-400 border border-slate-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-500 mr-1.5"></span>
                                Ẩn
                            </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.categories.edit', $category->id) }}"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-700 text-slate-400 hover:bg-blue-600 hover:text-white transition"
                                title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('admin.categories.destroy', $category->id) }}"
                                method="POST"
                                class="inline-block"
                                onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-700 text-slate-400 hover:bg-red-600 hover:text-white transition"
                                    title="Xóa">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                            @if(request('q'))
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-search-minus text-4xl mb-3 text-slate-600"></i>
                                <p>
                                    Không tìm thấy thể loại nào phù hợp với từ khóa
                                    "<span class="font-semibold">{{ request('q') }}</span>"
                                </p>
                            </div>
                            @else
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-box-open text-4xl mb-3 text-slate-600"></i>
                                <p>Chưa có thể loại nào.</p>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-700 bg-slate-800 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-slate-400">

            {{-- Hiển thị thông tin --}}
            <span class="order-2 sm:order-1">
                Hiển thị
                <strong>{{ $categories->firstItem() }}</strong> -
                <strong>{{ $categories->lastItem() }}</strong>
                trong tổng số
                <strong>{{ $categories->total() }}</strong> kết quả
            </span>

            {{-- Nút chuyển trang --}}
            <div class="flex gap-1 order-1 sm:order-2">

                {{-- Previous --}}
                <a href="{{ $categories->previousPageUrl() }}"
                    class="px-3 py-1.5 rounded-md border border-slate-600 hover:bg-slate-700 hover:text-white transition 
            {{ $categories->onFirstPage() ? 'opacity-50 pointer-events-none' : '' }}">
                    <i class="fas fa-chevron-left"></i>
                </a>

                {{-- Numbered Pages --}}
                @foreach ($categories->getUrlRange(1, $categories->lastPage()) as $page => $url)
                <a href="{{ $url }}"
                    class="px-3 py-1.5 rounded-md border border-slate-600 hover:bg-slate-700 hover:text-white transition
                {{ $page == $categories->currentPage() ? 'bg-orange-600 text-white font-bold border-none' : '' }}">
                    {{ $page }}
                </a>
                @endforeach

                {{-- Next --}}
                <a href="{{ $categories->nextPageUrl() }}"
                    class="px-3 py-1.5 rounded-md border border-slate-600 hover:bg-slate-700 hover:text-white transition
            {{ !$categories->hasMorePages() ? 'opacity-50 pointer-events-none' : '' }}">
                    <i class="fas fa-chevron-right"></i>
                </a>

            </div>

        </div>

    </div>
</div>
@endsection