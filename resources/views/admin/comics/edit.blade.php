@extends('layouts.admin')

@section('title', 'Chỉnh sửa Truyện')
@section('header', 'Chỉnh sửa Truyện')

@section('content')
<div class="p-6 max-w-5xl mx-auto space-y-6">

    {{-- THÔNG BÁO LỖI --}}
    @if ($errors->any())
    <div class="border border-red-500/40 bg-red-500/10 text-red-300 text-sm rounded-xl p-4 mb-4">
        <div class="font-semibold mb-2 flex items-center gap-2">
            <i class="fas fa-triangle-exclamation"></i>
            Có lỗi xảy ra, vui lòng kiểm tra lại:
        </div>
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- FORM CARD --}}
    <div class="bg-slate-900/80 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden">

        {{-- HEADER --}}
        <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fas fa-edit text-blue-400"></i>
                    Chỉnh sửa truyện: <span class="text-slate-200">{{ $comic->title }}</span>
                </h2>
                <p class="text-xs text-slate-400 mt-1">
                    Cập nhật thông tin truyện trong hệ thống.
                </p>
            </div>

            <a href="{{ route('admin.comics.index') }}"
                class="text-xs px-3 py-1.5 rounded-lg border border-slate-600 text-slate-300 hover:bg-slate-800 transition flex items-center gap-1">
                <i class="fas fa-arrow-left text-[10px]"></i>
                Quay lại danh sách
            </a>
        </div>

        {{-- FORM --}}
        <form action="{{ route('admin.comics.update', $comic) }}"
            method="POST"
            enctype="multipart/form-data"
            class="px-6 py-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- GRID 2 CỘT --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- CỘT TRÁI --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- TIÊU ĐỀ & SLUG --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">
                                Tên truyện <span class="text-red-400">*</span>
                            </label>
                            <input type="text" name="title"
                                value="{{ old('title', $comic->title) }}"
                                class="w-full px-3 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-slate-100 text-sm
                                          focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">
                                Đường dẫn (slug)
                            </label>
                            <input type="text" name="slug"
                                value="{{ old('slug', $comic->slug) }}"
                                class="w-full px-3 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-slate-100 text-sm
                                          focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500"
                                placeholder="Giữ nguyên nếu không muốn đổi">
                        </div>
                    </div>

                    {{-- TÁC GIẢ & TRẠNG THÁI --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">
                                Tác giả
                            </label>
                            <input type="text" name="author"
                                value="{{ old('author', $comic->author) }}"
                                class="w-full px-3 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-slate-100 text-sm
                                          focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">
                                Trạng thái <span class="text-red-400">*</span>
                            </label>
                            @php $oldStatus = old('status', $comic->status); @endphp
                            <select name="status"
                                class="w-full px-3 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-slate-100 text-sm
                                           focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                                <option value="ongoing" @selected($oldStatus==='ongoing' )>Đang tiến hành</option>
                                <option value="completed" @selected($oldStatus==='completed' )>Đã hoàn thành</option>
                                <option value="dropped" @selected($oldStatus==='dropped' )>Tạm dừng</option>
                            </select>
                        </div>
                    </div>

                    {{-- MÔ TẢ --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">
                            Mô tả
                        </label>
                        <textarea name="description" rows="5"
                            class="w-full px-3 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-slate-100 text-sm
                                         focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500 resize-y">{{ old('description', $comic->description) }}</textarea>
                    </div>

                    {{-- CHAPTER & NGÀY PHÁT HÀNH --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">
                                Số chương hiện có
                            </label>
                            <input type="number" name="chapter_count"
                                value="{{ old('chapter_count', $comic->chapter_count) }}"
                                min="0"
                                class="w-full px-3 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-slate-100 text-sm
                                          focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">
                                Ngày phát hành
                            </label>
                            <input type="date" name="published_at"
                                value="{{ old('published_at', optional($comic->published_at)->format('Y-m-d')) }}"
                                class="w-full px-3 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-slate-100 text-sm
                                          focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                        </div>
                    </div>
                </div>

                {{-- CỘT PHẢI --}}
                <div class="space-y-5">

                    {{-- ẢNH BÌA --}}
                    <div class="bg-slate-900 border border-slate-700 rounded-xl p-4">
                        <h3 class="text-xs font-semibold text-slate-300 uppercase mb-3 flex items-center gap-2">
                            <i class="fas fa-image text-slate-400"></i>
                            Ảnh bìa
                        </h3>

                        <div class="flex flex-col items-center gap-3">
                            <div class="w-32 h-44 rounded-lg bg-slate-800 border border-slate-700 overflow-hidden flex items-center justify-center">
                                <img id="cover-preview"
                                    src="{{ $comic->cover_url }}"
                                    class="w-full h-full object-cover"
                                    alt="Cover hiện tại">
                            </div>

                            <label for="cover_image"
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-600 text-slate-200 text-xs cursor-pointer hover:bg-slate-800 transition">
                                <i class="fas fa-upload text-[11px]"></i>
                                Thay ảnh bìa
                            </label>
                            <input type="file" name="cover_image" id="cover_image" class="hidden" accept="image/*"
                                onchange="previewCover(this)">
                            <p class="text-[10px] text-slate-500 text-center mt-1">
                                Nếu không chọn ảnh mới, hệ thống sẽ giữ ảnh hiện tại.
                            </p>
                        </div>
                    </div>

                    {{-- THỂ LOẠI --}}
                    <div class="bg-slate-900 border border-slate-700 rounded-xl p-4">
                        <h3 class="text-xs font-semibold text-slate-300 uppercase mb-3 flex items-center gap-2">
                            <i class="fas fa-tags text-slate-400"></i>
                            Thể loại <span class="text-red-400">*</span>
                        </h3>

                        <div class="max-h-56 overflow-y-auto space-y-2 pr-1">
                            @php
                            $oldCategories = collect(old('category_ids', $selectedCategoryIds ?? []));
                            @endphp
                            @foreach($categories as $category)
                            <label class="flex items-center gap-2 text-xs text-slate-200 cursor-pointer">
                                <input type="checkbox"
                                    name="category_ids[]"
                                    value="{{ $category->id }}"
                                    class="rounded border-slate-600 bg-slate-800 text-orange-500 focus:ring-orange-500"
                                    @checked($oldCategories->contains($category->id))>
                                <span>{{ $category->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- BUTTONS --}}
            <div class="flex justify-between items-center pt-4 border-t border-slate-700">
                <a href="{{ route('admin.comics.index') }}"
                    class="px-4 py-2.5 rounded-lg text-xs font-semibold border border-slate-600 text-slate-300 hover:bg-slate-800 transition">
                    Hủy
                </a>

                <button type="submit"
                    class="px-5 py-2.5 rounded-lg text-xs font-bold bg-gradient-to-r from-blue-500 to-indigo-600 text-white
                               shadow-lg hover:shadow-blue-500/40 hover:-translate-y-0.5 transition">
                    <i class="fas fa-save mr-1.5 text-[11px]"></i>
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewCover(input) {
        const file = input.files && input.files[0];
        const img = document.getElementById('cover-preview');

        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection