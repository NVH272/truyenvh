@extends('layouts.admin')

@section('title', 'Thêm Truyện Mới')
@section('header', 'Quản lý Truyện')

@section('content')
<div class="p-8 max-w-7xl mx-auto space-y-6">

    {{-- ERROR DISPLAY --}}
    @if ($errors->any())
    <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm animate-fade-in" role="alert">
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

    {{-- MAIN FORM --}}
    <form id="create-comic-form" action="{{ route('admin.comics.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- === ROW 1: THÔNG TIN TRUYỆN (Cùng chiều rộng với 2 card bên dưới) === --}}
        <div class="flex justify-center">
            <div class="w-full max-w-4xl">
                <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-700 bg-slate-800/50 flex items-center gap-3">
                        <span class="w-9 h-9 rounded-lg bg-blue-500/10 text-blue-500 flex items-center justify-center">
                            <i class="fas fa-book text-sm"></i>
                        </span>
                        <h3 class="font-bold text-slate-100 text-base">Thông tin truyện</h3>
                    </div>

                    <div class="p-7 space-y-6">
                        {{-- Title --}}
                        <div class="space-y-1.5">
                            <label class="block text-sm font-semibold text-slate-300">
                                Tên truyện <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="input-title" value="{{ old('title') }}"
                                class="w-full px-4 py-3 bg-slate-900 border border-slate-600 rounded-lg text-slate-100 text-sm placeholder-slate-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-sm"
                                placeholder="Nhập tên chính thức của truyện...">
                        </div>

                        {{-- Slug & Author --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <label class="block text-sm font-semibold text-slate-300">Slug (URL)</label>
                                <div class="flex rounded-lg shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-slate-600 bg-slate-700/50 text-slate-400 text-sm">
                                        /truyen/
                                    </span>
                                    <input type="text" name="slug" value="{{ old('slug') }}"
                                        class="flex-1 min-w-0 block w-full px-4 py-3 bg-slate-900 border border-slate-600 rounded-r-lg text-slate-100 text-sm placeholder-slate-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                                        placeholder="tu-dong-tao">
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-sm font-semibold text-slate-300">Tác giả</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-pen-nib text-slate-500"></i>
                                    </div>
                                    <input type="text" name="author" id="input-author" value="{{ old('author') }}"
                                        class="w-full pl-10 pr-4 py-3 bg-slate-900 border border-slate-600 rounded-lg text-slate-100 text-sm placeholder-slate-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                                        placeholder="Tên tác giả">
                                </div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="space-y-1.5">
                            <label class="block text-sm font-semibold text-slate-300">Tóm tắt nội dung</label>
                            <textarea name="description" id="input-description" rows="4"
                                class="w-full px-4 py-3 bg-slate-900 border border-slate-600 rounded-lg text-slate-100 text-sm placeholder-slate-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors resize-y leading-relaxed custom-scrollbar"
                                placeholder="Viết mô tả hấp dẫn cho truyện...">{{ old('description') }}</textarea>
                        </div>

                        <div class="border-t border-slate-700/50 my-4"></div>

                        {{-- Status, Chapter, Date --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div class="space-y-1.5">
                                <label class="block text-sm font-semibold text-slate-300">
                                    Trạng thái <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="status" id="input-status" class="w-full pl-4 pr-10 py-3 bg-slate-900 border border-slate-600 rounded-lg text-slate-100 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors appearance-none cursor-pointer">
                                        <option value="ongoing" @selected(old('status')==='ongoing' )>Đang tiến hành</option>
                                        <option value="completed" @selected(old('status')==='completed' )>Đã hoàn thành</option>
                                        <option value="dropped" @selected(old('status')==='dropped' )>Tạm dừng</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-500">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-sm font-semibold text-slate-300">Số chương</label>
                                <input type="number" name="chapter_count" id="input-chapter" value="{{ old('chapter_count', 0) }}" min="0"
                                    class="w-full px-4 py-3 bg-slate-900 border border-slate-600 rounded-lg text-slate-100 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-sm font-semibold text-slate-300">Ngày phát hành</label>
                                <input type="date" name="published_at" id="input-published-at"
                                    value="{{ old('published_at', now()->format('Y-m-d')) }}"
                                    class="date-light w-full px-4 py-3 bg-slate-900 border border-slate-600 rounded-lg text-slate-100 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

{{-- === ROW 2: ẢNH BÌA & THỂ LOẠI + NÚT === --}}
<div class="flex justify-center">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 w-full max-w-4xl">

        {{-- === CARD 2: ẢNH BÌA (Bên trái) === --}}
        <div class="w-full">
            <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-lg overflow-hidden flex flex-col h-full">
                <div class="px-5 py-3 border-b border-slate-700 bg-slate-800/50 flex-shrink-0">
                    <h3 class="font-bold text-slate-100 text-sm">Ảnh bìa truyện</h3>
                </div>

                <div class="p-5 flex flex-col justify-center items-center flex-1 min-h-0">
                    <div class="relative w-full max-w-xs mx-auto group">
                        <div class="relative w-full aspect-[2/3] bg-slate-900 rounded-xl border-2 border-dashed border-slate-600 overflow-hidden flex flex-col items-center justify-center transition-all group-hover:border-blue-500/50 group-hover:bg-slate-900/80 shadow-inner">
                            <div id="cover-placeholder" class="text-center p-4 transition-opacity duration-300">
                                <div class="w-12 h-12 mx-auto bg-slate-800 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-md">
                                    <i class="fas fa-cloud-upload-alt text-xl text-slate-500 group-hover:text-blue-500"></i>
                                </div>
                                <p class="text-xs font-medium text-slate-300">Nhấn để tải ảnh</p>
                                <p class="text-[10px] text-slate-500 mt-1">hoặc kéo thả</p>
                            </div>
                            <img id="cover-preview" src="#" class="absolute inset-0 w-full h-full object-cover hidden" alt="Preview">
                            <input type="file" name="cover_image" id="cover_image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/*" onchange="previewCover(this)">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- === CARD 3: THỂ LOẠI + NÚT (Bên phải - Cùng chiều rộng) === --}}
        <div class="w-full flex flex-col gap-6">
            {{-- Card Thể loại --}}
            <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-lg overflow-hidden flex flex-col flex-1">
                <div class="px-5 py-3 border-b border-slate-700 bg-slate-800/50 flex justify-between items-center flex-shrink-0">
                    <h3 class="font-bold text-slate-100 text-sm">Thể loại</h3>
                </div>

                <div class="p-4 bg-slate-900/50 flex flex-col flex-1 min-h-0 max-h-[500px]">
                    {{-- Selected Tags --}}
                    <div id="selected-tags-container" class="flex flex-wrap gap-1.5 mb-2 empty:hidden flex-shrink-0"></div>

                    {{-- Search --}}
                    <div class="mb-2 relative flex-shrink-0">
                        <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-500 text-[10px]"></i>
                        <input type="text" id="category-search" placeholder="Tìm thể loại..."
                            class="w-full pl-8 pr-2 py-2 bg-slate-900 border border-slate-700 rounded-lg text-sm text-slate-200 focus:outline-none focus:border-blue-500 placeholder-slate-600 transition-colors">
                    </div>

                    {{-- List với scroll --}}
                    <div class="overflow-y-auto custom-scrollbar space-y-1 pr-1 border-t border-slate-700/50 pt-2 flex-1 min-h-0 max-h-[350px]" id="category-list">
                        @foreach($categories as $category)
                        <label class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-800 cursor-pointer transition-colors group select-none">
                            <span class="text-sm text-slate-300 group-hover:text-white transition-colors category-name truncate flex-1">
                                {{ $category->name }}
                            </span>
                            <input type="checkbox"
                                name="category_ids[]"
                                value="{{ $category->id }}"
                                data-name="{{ $category->name }}"
                                class="category-checkbox w-3.5 h-3.5 rounded border-slate-600 bg-slate-700 text-blue-600 focus:ring-offset-0 focus:ring-blue-500 transition-all cursor-pointer ml-2 flex-shrink-0"
                                @checked(collect(old('category_ids', []))->contains($category->id))
                            onchange="updateTags()">
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-4 flex-shrink-0">
                <a href="{{ route('admin.comics.index') }}"
                    class="flex-1 inline-flex justify-center items-center px-6 py-3 text-sm font-medium text-slate-300 bg-slate-800 border border-slate-600 rounded-lg hover:bg-slate-700 hover:text-white transition-all shadow-md hover:shadow-lg">
                    <i class="fas fa-arrow-left mr-2"></i> Quay lại
                </a>
                <button type="submit" form="create-comic-form"
                    class="flex-1 inline-flex justify-center items-center px-7 py-3 text-sm font-bold text-white bg-blue-600 rounded-lg shadow-lg shadow-blue-600/20 hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-xl transition-all">
                    <i class="fas fa-save mr-2"></i> Lưu dữ liệu
                </button>
            </div>
        </div>
    </div>
    </form>
</div>

{{-- SCRIPT --}}
<script>
    // 1. Preview Ảnh Bìa
    function previewCover(input) {
        const file = input.files && input.files[0];
        const preview = document.getElementById('cover-preview');
        const placeholder = document.getElementById('cover-placeholder');
        const container = input.closest('.relative').querySelector('.border-dashed, .border-solid');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('opacity-0');
                container.classList.remove('border-dashed', 'border-slate-600');
                container.classList.add('border-solid', 'border-blue-500');
            };
            reader.readAsDataURL(file);
        } else {
            preview.classList.add('hidden');
            placeholder.classList.remove('opacity-0');
            container.classList.add('border-dashed', 'border-slate-600');
            container.classList.remove('border-solid', 'border-blue-500');
        }
    }

    // 2. Logic Thẻ (Tags)
    function updateTags() {
        const container = document.getElementById('selected-tags-container');
        const checkboxes = document.querySelectorAll('.category-checkbox:checked');

        container.innerHTML = '';

        if (checkboxes.length === 0) {
            container.classList.add('hidden');
            return;
        }
        container.classList.remove('hidden');

        checkboxes.forEach(cb => {
            const name = cb.getAttribute('data-name');
            const id = cb.value;

            const tag = document.createElement('div');
            tag.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-800 border border-blue-500/50 text-blue-300 text-xs font-medium shadow-sm animate-fade-in-scale';
            tag.innerHTML = `
                <span>${name}</span>
                <button type="button" onclick="removeTag('${id}')" class="text-blue-400 hover:text-red-400 transition-colors focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(tag);
        });
    }

    // Helper functions
    function removeTag(id) {
        const checkbox = document.querySelector(`.category-checkbox[value="${id}"]`);
        if (checkbox) {
            checkbox.checked = false;
            updateTags();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateTags();
        previewCover(document.getElementById('cover_image'));
    });

    document.getElementById('category-search').addEventListener('keyup', function() {
        const searchText = this.value.toLowerCase();
        const items = document.querySelectorAll('#category-list label');
        items.forEach(item => {
            const text = item.querySelector('.category-name').textContent.toLowerCase();
            item.style.display = text.includes(searchText) ? 'flex' : 'none';
        });
    });
</script>

<style>
    /* Animation & Scrollbar */
    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.9);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .animate-fade-in-scale {
        animation: fadeInScale 0.2s ease-out forwards;
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 5px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(30, 41, 59, 0.5);
        border-radius: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #475569;
        border-radius: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }

    /* Làm icon lịch sáng hơn */
    .date-light::-webkit-calendar-picker-indicator {
        filter: invert(1) brightness(1.5);
        opacity: 0.9;
    }

    .date-light:hover::-webkit-calendar-picker-indicator {
        opacity: 1;
        filter: invert(1) brightness(2);
    }
</style>
@endsection