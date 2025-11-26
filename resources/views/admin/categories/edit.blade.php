@extends('layouts.admin')

@section('title', 'Chỉnh sửa Thể loại')
@section('header', 'Cập nhật Dữ liệu')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Navigation & Quick Actions -->
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center text-sm text-slate-400 hover:text-orange-500 transition group">
            <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> Quay lại danh sách
        </a>

        <!-- Delete Button -->
        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thể loại này?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-xs font-bold text-red-500 border border-red-500/30 bg-red-500/10 px-3 py-1.5 rounded hover:bg-red-500 hover:text-white transition flex items-center gap-2">
                <i class="fas fa-trash-alt"></i> Xóa Thể loại
            </button>
        </form>
    </div>

    <!-- Main Form Card -->
    <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl overflow-hidden relative">
        <!-- Orange Stripe Indicator -->
        <div class="absolute top-0 left-0 w-1 h-full bg-orange-500"></div>

        <div class="border-b border-slate-700 px-6 py-4 bg-slate-900/30 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-white brand-font text-lg">Chỉnh sửa: <span class="text-orange-500">{{ $category->name }}</span></h3>
                <p class="text-xs text-slate-500 mt-1 font-mono-tech">ID: #{{ str_pad($category->id, 3, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>

        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Row 1: Name & Slug -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name Input -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider">Tên Thể loại</label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" class="admin-input w-full px-4 py-2.5 rounded-lg text-sm bg-slate-900/50 border-slate-600 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 text-white transition" required>
                </div>

                <!-- Slug Input -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider">Slug (URL)</label>
                    <input type="text" name="slug" value="{{ old('slug', $category->slug) }}" class="admin-input w-full px-4 py-2.5 rounded-lg text-sm font-mono-tech bg-slate-900/50 border-slate-600 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 text-orange-400 transition" required>
                </div>
            </div>

            <!-- Row 2: Description -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-300 uppercase tracking-wider">Mô tả</label>
                <textarea name="description" rows="4" class="admin-input w-full px-4 py-2.5 rounded-lg text-sm bg-slate-900/50 border-slate-600 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 text-white transition resize-none">{{ old('description', $category->description) }}</textarea>
            </div>

            <!-- Row 3: Status -->
            <div class="space-y-3">
                <label class="text-xs font-bold text-slate-300 uppercase tracking-wider">Trạng thái hiển thị</label>
                <div class="flex items-center gap-4 p-3 bg-slate-900/30 rounded-lg border border-slate-700/50">

                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative flex items-center">
                            <input type="radio" name="status" value="1" class="peer sr-only" {{ old('status', $category->status ?? 1) == 1 ? 'checked' : '' }}>
                            <div class="w-5 h-5 border-2 border-slate-600 rounded-full peer-checked:border-green-500 peer-checked:bg-green-500 transition flex items-center justify-center">
                                <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100 transition"></div>
                            </div>
                        </div>
                        <span class="text-sm font-medium text-slate-400 group-hover:text-white transition">Công khai</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative flex items-center">
                            <input type="radio" name="status" value="0" class="peer sr-only" {{ old('status', $category->status ?? 1) == 0 ? 'checked' : '' }}>
                            <div class="w-5 h-5 border-2 border-slate-600 rounded-full peer-checked:border-slate-500 peer-checked:bg-slate-500 transition flex items-center justify-center">
                                <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100 transition"></div>
                            </div>
                        </div>
                        <span class="text-sm font-medium text-slate-400 group-hover:text-white transition">Ẩn / Nháp</span>
                    </label>

                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-6 border-t border-slate-700 flex items-center gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-lg shadow-blue-900/30 transition transform active:scale-95 flex items-center">
                    <i class="fas fa-check mr-2"></i> Cập nhật
                </button>
                <a href="{{ route('admin.categories.index') }}" class="bg-transparent border border-slate-600 hover:bg-slate-700 text-slate-300 hover:text-white px-6 py-2.5 rounded-lg text-sm font-bold transition">
                    Hủy bỏ
                </a>
            </div>
        </form>
    </div>
</div>
@endsection