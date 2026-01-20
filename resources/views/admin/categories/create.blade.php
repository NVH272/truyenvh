@extends('layouts.admin')

@section('title', 'Thêm Thể loại Mới')
@section('header', 'Khởi tạo Dữ liệu')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Navigation Back -->
    <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center text-sm text-slate-400 hover:text-orange-500 mb-6 transition group">
        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> Quay lại danh sách
    </a>

    <!-- Error Messages -->
    @if ($errors->any())
    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm" role="alert">
        <div class="flex items-center gap-2 font-bold mb-2">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Vui lòng kiểm tra các lỗi sau:</span>
        </div>
        <ul class="list-disc list-inside space-y-1 opacity-90 pl-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if (session('error'))
    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm" role="alert">
        <div class="flex items-center gap-2 font-bold">
            <i class="fas fa-exclamation-triangle"></i>
            <span>{{ session('error') }}</span>
        </div>
    </div>
    @endif

    <!-- Main Form Card -->
    <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl overflow-hidden">
        <div class="border-b border-slate-700 px-6 py-4 bg-slate-900/30">
            <h3 class="font-bold text-white brand-font text-lg">Thông tin Thể loại</h3>
            <p class="text-xs text-slate-500 mt-1">Điền đầy đủ thông tin bên dưới để tạo mới.</p>
        </div>

        <form action="{{ route('admin.categories.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Row 1: Name & Slug -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name Input -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider">Tên Thể loại <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="admin-input w-full px-4 py-2.5 rounded-lg text-sm bg-slate-900/50 border-slate-600 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 text-white placeholder-slate-600 transition {{ $errors->has('name') ? 'border-red-500' : '' }}" placeholder="Ví dụ: Isekai, Học đường..." required>
                    @error('name')
                    <p class="text-xs text-red-400 mt-1 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Slug Input -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider">Slug (URL)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" class="admin-input w-full px-4 py-2.5 rounded-lg text-sm font-mono-tech bg-slate-900/50 border-slate-600 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 text-orange-400 placeholder-slate-600 transition {{ $errors->has('slug') ? 'border-red-500' : '' }}" placeholder="vi-du-isekai-hoc-duong">
                    <p class="text-[10px] text-slate-500 italic">Để trống sẽ tự động tạo từ tên.</p>
                    @error('slug')
                    <p class="text-xs text-red-400 mt-1 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            <!-- Row 2: Description -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-300 uppercase tracking-wider">Mô tả</label>
                <textarea name="description" rows="4" class="admin-input w-full px-4 py-2.5 rounded-lg text-sm bg-slate-900/50 border-slate-600 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 text-white placeholder-slate-600 transition resize-none" placeholder="Nhập mô tả ngắn gọn về thể loại này..."></textarea>
            </div>

            <!-- Row 3: Status Toggle -->
            <div class="space-y-3">
                <label class="text-xs font-bold text-slate-300 uppercase tracking-wider">Trạng thái hiển thị</label>
                <div class="flex items-center gap-4 p-3 bg-slate-900/30 rounded-lg border border-slate-700/50">

                    <!-- Active Option -->
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative flex items-center">
                            <input type="radio" name="status" value="1" class="peer sr-only" checked>
                            <div class="w-5 h-5 border-2 border-slate-600 rounded-full peer-checked:border-green-500 peer-checked:bg-green-500 transition flex items-center justify-center">
                                <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100 transition"></div>
                            </div>
                        </div>
                        <span class="text-sm font-medium text-slate-400 group-hover:text-white transition">Công khai</span>
                    </label>

                    <!-- Inactive Option -->
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative flex items-center">
                            <input type="radio" name="status" value="0" class="peer sr-only">
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
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-lg shadow-orange-900/30 transition transform active:scale-95 flex items-center">
                    <i class="fas fa-save mr-2"></i> Lưu Dữ Liệu
                </button>
                <a href="{{ route('admin.categories.index') }}" class="bg-transparent border border-slate-600 hover:bg-slate-700 text-slate-300 hover:text-white px-6 py-2.5 rounded-lg text-sm font-bold transition">
                    Hủy bỏ
                </a>
            </div>
        </form>
    </div>
</div>
@endsection