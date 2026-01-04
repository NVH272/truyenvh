@extends('layouts.app')

@section('title', 'Upload Chapter - ' . $comic->title)
@section('header', 'Upload Chapter mới')

@section('content')
<div class="p-8 max-w-4xl mx-auto space-y-6">

    {{-- ERROR DISPLAY --}}
    @if ($errors->any())
    <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm animate-fade-in" role="alert">
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

    {{-- SUCCESS MESSAGE --}}
    @if (session('success'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm animate-fade-in" role="alert">
        <div class="flex items-center gap-2 font-bold">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    {{-- INFO CARD --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Hướng dẫn upload Chapter:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li>File ZIP phải chứa các ảnh trang truyện (jpg, jpeg, png, gif, webp)</li>
                    <li>Ảnh sẽ được tự động đánh số thứ tự: 1.jpg, 2.jpg, 3.jpg, ...</li>
                    <li>Kích thước file ZIP tối đa: 100MB</li>
                    <li>Ảnh có thể nằm trong thư mục con trong file ZIP</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- MAIN FORM --}}
    <form action="{{ route('user.chapters.store', $comic) }}" method="POST" enctype="multipart/form-data" id="chapter-upload-form">
        @csrf

        {{-- COMIC INFO --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center gap-3">
                <span class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-book text-sm"></i>
                </span>
                <h3 class="font-bold text-gray-800 text-base">Thông tin truyện</h3>
            </div>
            <div class="p-6">
                <div class="flex items-center gap-4">
                    <img src="{{ $comic->cover_url }}" alt="{{ $comic->title }}" class="w-20 h-28 object-cover rounded-lg border border-gray-200">
                    <div>
                        <h4 class="font-bold text-lg text-gray-800">{{ $comic->title }}</h4>
                        <p class="text-sm text-gray-600 mt-1">Tác giả: {{ $comic->author ?? 'Đang cập nhật' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- CHAPTER INFO --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center gap-3">
                <span class="w-9 h-9 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                    <i class="fas fa-list text-sm"></i>
                </span>
                <h3 class="font-bold text-gray-800 text-base">Thông tin Chapter</h3>
            </div>

            <div class="p-6 space-y-6">
                {{-- Chapter Number --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-gray-700">
                        Số Chapter <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-hashtag text-gray-400"></i>
                        </div>
                        <input type="number" name="chapter_number" id="chapter_number" 
                            value="{{ old('chapter_number', $nextChapterNumber) }}" 
                            min="1" required
                            class="w-full pl-10 pr-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 text-sm placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    </div>
                    <p class="text-xs text-gray-500">Chapter tiếp theo: {{ $nextChapterNumber }}</p>
                </div>

                {{-- Chapter Title --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-gray-700">
                        Tiêu đề Chapter (tùy chọn)
                    </label>
                    <input type="text" name="title" id="chapter_title" 
                        value="{{ old('title') }}"
                        placeholder="Ví dụ: Chapter 1 - Khởi đầu"
                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 text-sm placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    <p class="text-xs text-gray-500">Nếu để trống, hệ thống sẽ tự động đặt tên "Chapter {số}"</p>
                </div>
            </div>
        </div>

        {{-- ZIP FILE UPLOAD --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center gap-3">
                <span class="w-9 h-9 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
                    <i class="fas fa-file-archive text-sm"></i>
                </span>
                <h3 class="font-bold text-gray-800 text-base">Upload File ZIP</h3>
            </div>

            <div class="p-6">
                <div class="relative w-full group">
                    <div class="relative w-full min-h-[200px] bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 overflow-hidden flex flex-col items-center justify-center transition-all group-hover:border-blue-400 group-hover:bg-blue-50/30">
                        <div id="zip-placeholder" class="text-center p-6 transition-opacity duration-300">
                            <div class="w-16 h-16 mx-auto bg-white rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-md border border-gray-200">
                                <i class="fas fa-cloud-upload-alt text-2xl text-blue-500 group-hover:text-blue-600"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-700 mb-1">Nhấn để chọn file ZIP</p>
                            <p class="text-xs text-gray-500">hoặc kéo thả file vào đây</p>
                            <p class="text-xs text-gray-400 mt-2">Tối đa 100MB</p>
                        </div>
                        <div id="zip-preview" class="hidden text-center p-6">
                            <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-file-archive text-2xl text-green-600"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-700 mb-1" id="zip-file-name"></p>
                            <p class="text-xs text-gray-500" id="zip-file-size"></p>
                            <button type="button" onclick="clearZipFile()" class="mt-3 text-xs text-red-600 hover:text-red-700 underline">
                                Xóa file
                            </button>
                        </div>
                        <input type="file" name="zip_file" id="zip_file" 
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                            accept=".zip" 
                            required
                            onchange="previewZipFile(this)">
                    </div>
                </div>
            </div>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex gap-4">
            <a href="{{ route('user.comics.show', $comic) }}"
                class="flex-1 inline-flex justify-center items-center px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-all shadow-md hover:shadow-lg">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại
            </a>
            <button type="submit" id="submit-btn"
                class="flex-1 inline-flex justify-center items-center px-7 py-3 text-sm font-bold text-white bg-blue-600 rounded-lg shadow-lg shadow-blue-600/20 hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-upload mr-2"></i> Upload Chapter
            </button>
        </div>
    </form>
</div>

{{-- SCRIPT --}}
<script>
    function previewZipFile(input) {
        const file = input.files && input.files[0];
        const preview = document.getElementById('zip-preview');
        const placeholder = document.getElementById('zip-placeholder');
        const container = input.closest('.relative').querySelector('.border-dashed');
        const fileName = document.getElementById('zip-file-name');
        const fileSize = document.getElementById('zip-file-size');
        const submitBtn = document.getElementById('submit-btn');

        if (file) {
            // Kiểm tra định dạng
            if (!file.name.toLowerCase().endsWith('.zip')) {
                alert('Vui lòng chọn file ZIP!');
                input.value = '';
                return;
            }

            // Kiểm tra kích thước (100MB)
            const maxSize = 100 * 1024 * 1024; // 100MB in bytes
            if (file.size > maxSize) {
                alert('File ZIP không được vượt quá 100MB!');
                input.value = '';
                return;
            }

            // Hiển thị preview
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
            container.classList.remove('border-gray-300');
            container.classList.add('border-blue-500', 'border-solid');
            submitBtn.disabled = false;
        } else {
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
            container.classList.add('border-gray-300', 'border-dashed');
            container.classList.remove('border-blue-500', 'border-solid');
            submitBtn.disabled = true;
        }
    }

    function clearZipFile() {
        const input = document.getElementById('zip_file');
        input.value = '';
        previewZipFile(input);
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    // Disable submit button initially
    document.addEventListener('DOMContentLoaded', function() {
        const submitBtn = document.getElementById('submit-btn');
        const zipFile = document.getElementById('zip_file');
        
        if (!zipFile.files || zipFile.files.length === 0) {
            submitBtn.disabled = true;
        }

        // Handle form submission
        document.getElementById('chapter-upload-form').addEventListener('submit', function(e) {
            const zipFile = document.getElementById('zip_file');
            if (!zipFile.files || zipFile.files.length === 0) {
                e.preventDefault();
                alert('Vui lòng chọn file ZIP!');
                return false;
            }
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang upload...';
        });
    });
</script>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .animate-fade-in {
        animation: fadeIn 0.3s ease-out forwards;
    }
</style>
@endsection

