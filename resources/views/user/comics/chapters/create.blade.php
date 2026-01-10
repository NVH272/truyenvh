@extends('layouts.app')

@section('title', 'Upload Chapter - ' . $comic->title)
@section('header', 'Upload Chapter mới')

@section('content')
<div class="py-10 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-8">

    {{-- HEADER --}}
    <div class="flex flex-col gap-1">
        <h1 class="text-2xl font-black text-slate-800 tracking-tight">Upload Chapter Mới</h1>
        <p class="text-sm text-slate-500">Thêm nội dung mới cho truyện <span class="font-bold text-blue-600">{{ $comic->title }}</span></p>
    </div>

    {{-- ERROR DISPLAY --}}
    @if ($errors->any())
    <div class="p-4 bg-red-50 border border-red-100 rounded-xl text-red-600 text-sm animate-fade-in shadow-sm" role="alert">
        <div class="flex items-start gap-3">
            <i class="fas fa-exclamation-circle mt-0.5 text-lg"></i>
            <div>
                <span class="font-bold block mb-1">Đã xảy ra lỗi:</span>
                <ul class="list-disc list-inside space-y-1 opacity-90 marker:text-red-400">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    {{-- SUCCESS MESSAGE --}}
    @if (session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-700 text-sm animate-fade-in shadow-sm" role="alert">
        <div class="flex items-center gap-3 font-bold">
            <i class="fas fa-check-circle text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    {{-- LAYOUT GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- LEFT COLUMN: MAIN FORM --}}
        <div class="lg:col-span-2 space-y-6">
            <form action="{{ route('user.comics.chapters.store', $comic) }}" method="POST" enctype="multipart/form-data" id="chapter-upload-form">
                @csrf

                {{-- SECTION 1: COMIC SELECTION --}}
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-6 group">
                    <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-600/20">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800">Truyện đang chọn</h3>
                                <p class="text-xs text-slate-500">Chọn truyện để upload</p>
                            </div>
                        </div>
                        <div class="w-full sm:max-w-xs">
                            <select class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 font-medium focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all cursor-pointer hover:border-slate-400"
                                onchange="if(this.value) window.location.href=this.value;">
                                @foreach($myComics as $c)
                                <option value="{{ route('user.comics.chapters.create', ['comic' => $c->id]) }}" @selected((int)$c->id === (int)$comic->id)>
                                    {{ $c->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Selected Comic Preview --}}
                    <div class="p-6 flex items-start gap-5">
                        <img src="{{ $comic->cover_url }}" alt="{{ $comic->title }}"
                            class="w-24 h-36 object-cover rounded-lg border border-slate-200 shadow-sm shrink-0">
                        <div class="space-y-2">
                            <h4 class="font-bold text-xl text-slate-800 leading-tight">{{ $comic->title }}</h4>
                            <div class="flex items-center gap-2 text-sm text-slate-500">
                                <i class="fas fa-pen-nib text-slate-400"></i>
                                <span>{{ $comic->author ?? 'Tác giả chưa cập nhật' }}</span>
                            </div>
                            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                {{ $comic->chapters_count ?? 0 }} chương hiện có
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: CHAPTER INFO --}}
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                            <i class="fas fa-list-ol text-sm"></i>
                        </div>
                        <h3 class="font-bold text-slate-800">Thông tin Chapter</h3>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            {{-- Chapter Number --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700">Số thứ tự <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <i class="fas fa-hashtag text-xs"></i>
                                    </div>
                                    <input type="number" name="chapter_number" id="chapter_number"
                                        value="{{ old('chapter_number', $nextChapterNumber) }}"
                                        min="1" required
                                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-900 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all font-medium placeholder-slate-400">
                                </div>
                                <p class="text-xs text-blue-600 font-medium mt-1"><i class="fas fa-magic mr-1"></i>Gợi ý tiếp theo: {{ $nextChapterNumber }}</p>
                            </div>

                            {{-- Chapter Title --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700">Tên chương (Tùy chọn)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <i class="fas fa-heading text-xs"></i>
                                    </div>
                                    <input type="text" name="title" id="chapter_title"
                                        value="{{ old('title') }}"
                                        placeholder="VD: Trận chiến cuối cùng"
                                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-900 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all placeholder-slate-400">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: UPLOAD ZIP --}}
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">
                            <i class="fas fa-cloud-upload-alt text-sm"></i>
                        </div>
                        <h3 class="font-bold text-slate-800">Tải lên nội dung</h3>
                    </div>

                    <div class="p-6">
                        <div class="relative w-full group">
                            <div class="relative w-full min-h-[240px] bg-slate-50 rounded-xl border-2 border-dashed border-slate-300 hover:border-blue-500 hover:bg-blue-50/30 transition-all duration-300 flex flex-col items-center justify-center cursor-pointer">

                                {{-- Placeholder State --}}
                                <div id="zip-placeholder" class="text-center p-8 transition-all duration-300">
                                    <div class="w-20 h-20 mx-auto bg-white rounded-full flex items-center justify-center mb-4 shadow-sm border border-slate-200 group-hover:scale-110 group-hover:border-blue-200 transition-transform duration-300">
                                        <i class="fas fa-file-archive text-3xl text-slate-400 group-hover:text-blue-500 transition-colors"></i>
                                    </div>
                                    <h4 class="text-base font-bold text-slate-700 mb-1 group-hover:text-blue-600">Kéo thả file ZIP vào đây</h4>
                                    <p class="text-sm text-slate-500 mb-4">hoặc nhấn để chọn từ máy tính</p>
                                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-slate-200 text-slate-600 text-xs font-semibold">
                                        Max size: 100MB
                                    </div>
                                </div>

                                {{-- Preview State --}}
                                <div id="zip-preview" class="hidden text-center p-8 w-full h-full flex flex-col items-center justify-center animate-fade-in">
                                    <div class="w-16 h-16 mx-auto bg-emerald-100 rounded-2xl flex items-center justify-center mb-3 shadow-inner">
                                        <i class="fas fa-file-zipper text-3xl text-emerald-600"></i>
                                    </div>
                                    <p class="text-base font-bold text-slate-800 mb-1 break-all px-4" id="zip-file-name"></p>
                                    <p class="text-sm text-slate-500 font-mono bg-slate-100 px-2 py-0.5 rounded" id="zip-file-size"></p>
                                    <button type="button" onclick="clearZipFile()" class="mt-4 px-4 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors flex items-center gap-2">
                                        <i class="fas fa-trash-alt"></i> Xóa & Chọn lại
                                    </button>
                                </div>

                                {{-- Hidden Input --}}
                                <input type="file" name="zip_file" id="zip_file"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                    accept=".zip" required
                                    onchange="previewZipFile(this)">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ACTION BUTTONS GROUP (ĐÃ CHỈNH SỬA GỌN GÀNG) --}}
                <div class="flex flex-col-reverse sm:flex-row gap-3 pt-2 justify-end">
                    {{-- Back Button --}}
                    <a href="{{ route('user.comics.show', $comic) }}"
                        class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-600 font-bold hover:bg-slate-50 hover:text-slate-800 transition-all shadow-sm flex items-center justify-center gap-2 text-sm">
                        <i class="fas fa-arrow-left"></i>
                        <span>Quay lại</span>
                    </a>

                    {{-- Submit Button (Compact) --}}
                    <button type="submit" id="submit-btn" disabled
                        class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2 text-sm disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none">
                        <span>Đăng tải Chapter</span>
                    </button>
                </div>

            </form>
        </div>

        {{-- RIGHT COLUMN: GUIDE --}}
        <div class="space-y-6">
            {{-- Info Card --}}
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-6 shadow-sm sticky top-24">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center shadow-md">
                        <i class="fas fa-lightbulb text-sm"></i>
                    </div>
                    <h3 class="font-bold text-blue-900 text-lg">Lưu ý quan trọng</h3>
                </div>

                <div class="space-y-4 text-sm text-slate-600">
                    <p>Để đảm bảo chapter hiển thị tốt nhất, vui lòng tuân thủ quy định:</p>

                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0"></i>
                            <span>Nén tất cả ảnh vào <strong>1 file .zip</strong> duy nhất.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0"></i>
                            <span>Đặt tên ảnh theo số thứ tự: <strong>1.jpg, 2.jpg...</strong> để sắp xếp đúng.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0"></i>
                            <span>Hỗ trợ định dạng: <strong>jpg, jpeg, png, webp</strong>.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-exclamation-triangle text-orange-500 mt-0.5 shrink-0"></i>
                            <span>File không quá <strong>100MB</strong>.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function previewZipFile(input) {
        const file = input.files && input.files[0];
        const preview = document.getElementById('zip-preview');
        const placeholder = document.getElementById('zip-placeholder');
        const container = input.closest('.relative')?.querySelector('.border-dashed');
        const fileName = document.getElementById('zip-file-name');
        const fileSize = document.getElementById('zip-file-size');
        const submitBtn = document.getElementById('submit-btn');

        if (!file) {
            if (submitBtn) submitBtn.disabled = true;
            return;
        }

        // Kiểm tra định dạng (Extension check)
        if (!file.name.toLowerCase().endsWith('.zip')) {
            alert('Chỉ chấp nhận file định dạng .zip!');
            clearZipFile();
            return;
        }

        // Kiểm tra kích thước (100MB limit)
        const maxSize = 100 * 1024 * 1024;
        if (file.size > maxSize) {
            alert('File quá lớn! Giới hạn tối đa là 100MB.');
            clearZipFile();
            return;
        }

        // Hiển thị preview
        if (fileName) fileName.textContent = file.name;
        if (fileSize) fileSize.textContent = formatFileSize(file.size);

        if (preview) preview.classList.remove('hidden');
        if (placeholder) placeholder.classList.add('hidden');

        // Đổi style viền khi có file
        if (container) {
            container.classList.remove('border-slate-300', 'border-dashed');
            container.classList.add('border-emerald-500', 'border-solid', 'bg-emerald-50/20');
        }

        // Enable submit button khi file hợp lệ
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.removeAttribute('disabled');
        }
    }

    function clearZipFile() {
        const input = document.getElementById('zip_file');
        const preview = document.getElementById('zip-preview');
        const placeholder = document.getElementById('zip-placeholder');
        const container = input.parentElement.querySelector('.border-2'); // Fix selector logic
        const submitBtn = document.getElementById('submit-btn');

        input.value = ''; // Reset input

        preview.classList.add('hidden');
        placeholder.classList.remove('hidden');

        // Reset style viền
        // Note: Cần select lại đúng element container vì logic class bên trên thay đổi class
        const wrapper = document.querySelector('#zip-placeholder').parentElement;
        wrapper.classList.add('border-slate-300', 'border-dashed');
        wrapper.classList.remove('border-emerald-500', 'border-solid', 'bg-emerald-50/20');

        submitBtn.disabled = true;
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Disable submit button initially & Handle Form Submit
    document.addEventListener('DOMContentLoaded', function() {
        const submitBtn = document.getElementById('submit-btn');
        const zipFile = document.getElementById('zip_file');

        // Initial check
        if (!zipFile.files || zipFile.files.length === 0) {
            submitBtn.disabled = true;
        }

        // Form Submit Handler
        document.getElementById('chapter-upload-form').addEventListener('submit', function(e) {
            const zipFile = document.getElementById('zip_file');
            if (!zipFile.files || zipFile.files.length === 0) {
                e.preventDefault();
                alert('Vui lòng chọn file ZIP trước khi đăng!');
                return false;
            }

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin text-lg"></i><span class="ml-2">Đang xử lý...</span>';
            submitBtn.classList.remove('bg-gradient-to-r', 'hover:-translate-y-0.5'); // Remove hover effects
            submitBtn.classList.add('bg-slate-400', 'cursor-wait');
        });
    });
</script>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeIn 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
</style>
@endsection