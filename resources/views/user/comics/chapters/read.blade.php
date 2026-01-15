@extends('layouts.reader')

@section('title', $comic->title . ' - Chapter ' . $chapter->chapter_number)

{{-- ĐẨY NỘI DUNG VÀO HEADER CỦA LAYOUT --}}
@section('reader_header_content')
{{-- Bên trái --}}
<div class="flex items-center gap-4 overflow-hidden">
    <div class="logo-container h-16 flex items-center border-b border-slate-800 shrink-0 bg-slate-900 overflow-hidden whitespace-nowrap relative">
        <a href="{{ route('home') }}" class="logo-wrapper">

            <!-- Logo Full -->
            <div class="logo-full">
                <img src="{{ asset('storage/logo/logoMiniDark.png') }}" alt="TruyenVH" class="h-8 shrink-0">
            </div>

        </a>
    </div>
    <div class="flex flex-col">
        <h1 class="text-sm font-bold text-white truncate max-w-[200px] md:max-w-md uppercase tracking-wide">
            <a href="{{ route('user.comics.show', $comic->slug) }}" class="text-white hover:text-blue-400">{{ $comic->title }}</a>
        </h1>
        <span class="text-xs text-gray-500 font-medium">
            Chapter {{ $chapter->chapter_number }}
        </span>
    </div>
</div>

{{-- Bên phải --}}
<div class="flex items-center gap-2">

    {{-- CHAPTER TRƯỚC --}}
    @if($prevChapter)
    <a href="{{ route('user.comics.chapters.read', [
                'comic' => $comic->id,
                'chapter_number' => $prevChapter->chapter_number
            ]) }}"
        class="hidden md:flex items-center justify-center w-10 h-10 rounded-lg bg-gray-800
                border border-gray-700
                hover:bg-blue-600 hover:border-blue-600 hover:text-white
                transition-all text-gray-300">
        <i class="fas fa-chevron-left"></i>
    </a>
    @else
    <div
        class="hidden md:flex items-center justify-center w-10 h-10 rounded-lg
                border border-gray-800 text-gray-600
                opacity-50 cursor-not-allowed">
        <i class="fas fa-chevron-left"></i>
    </div>
    @endif

    {{-- DROPDOWN CHỌN CHAPTER --}}
    <div class="relative">
        <button
            class="flex items-center gap-2 px-3 py-2 rounded-lg h-10
                    bg-gray-800 border border-gray-700
                    hover:border-gray-500
                    text-sm font-bold text-white transition-colors"
            onclick="document.getElementById('chapter-dropdown').classList.toggle('hidden')">
            <span>Chap {{ $chapter->chapter_number }}</span>
            <i class="fas fa-chevron-down text-xs text-gray-400"></i>
        </button>

        {{-- MENU --}}
        <div id="chapter-dropdown"
            class="hidden absolute right-0 mt-2 w-48 max-h-64 overflow-y-auto
                    bg-[#1a1a1a] border border-gray-700 rounded-xl shadow-xl z-50">

            @foreach($comic->chapters()->orderByDesc('chapter_number')->get() as $c)
            <a href="{{ route('user.comics.chapters.read', [
                        'comic' => $comic->id,
                        'chapter_number' => $c->chapter_number
                    ]) }}"
                class="block px-4 py-2 text-sm
                          {{ $c->id === $chapter->id
                                ? 'bg-blue-600 text-white font-bold'
                                : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                Chapter {{ $c->chapter_number }}
            </a>
            @endforeach

        </div>
    </div>

    {{-- CHAPTER SAU --}}
    @if($nextChapter)
    <a href="{{ route('user.comics.chapters.read', [
                'comic' => $comic->id,
                'chapter_number' => $nextChapter->chapter_number
            ]) }}"
        class="hidden md:flex items-center justify-center w-10 h-10 rounded-lg bg-gray-800
                border border-gray-700
                hover:bg-blue-600 hover:border-blue-600 hover:text-white
                transition-all text-gray-300">
        <i class="fas fa-chevron-right"></i>
    </a>
    @else
    <div
        class="hidden md:flex items-center justify-center w-10 h-10 rounded-lg
                border border-gray-800 text-gray-600
                opacity-50 cursor-not-allowed">
        <i class="fas fa-chevron-right"></i>
    </div>
    @endif

</div>
@endsection

@section('content')
{{-- NỀN TỐI BAO TRÙM TOÀN BỘ --}}
<div class="bg-[#121212] min-h-screen w-full relative font-sans text-gray-300">

    {{-- 2. KHUNG HIỂN THỊ ẢNH --}}
    {{-- Không cần padding-top lớn vì header sẽ tự ẩn khi đọc --}}
    <div class="pt-0 w-full mx-auto bg-black">
        <div class="flex flex-col items-center w-full gap-3" id="chapter-images-container">
            @foreach($chapter->pages as $p)
            {{-- Ảnh full width nhưng max-width hợp lý trên màn to --}}
            <img src="{{ $p->image_url }}"
                alt="Trang {{ $p->page_index }}"
                class="chapter-image w-full max-w-4xl h-auto block mx-auto select-none"
                loading="lazy"
                data-page-index="{{ $p->page_index }}">
            @endforeach
        </div>
    </div>

    {{-- 3. FOOTER ĐIỀU HƯỚNG RIÊNG --}}
    <div class="max-w-3xl mx-auto py-16 px-4 text-center space-y-8">
        <div class="space-y-2">
            <p class="text-gray-500 text-sm uppercase tracking-widest">Bạn đã đọc hết</p>
            <h3 class="text-xl font-bold text-white">Chapter {{ $chapter->chapter_number }}</h3>
        </div>
        <div class="grid grid-cols-2 gap-4">

            {{-- CHAPTER TRƯỚC --}}
            @if($prevChapter)
            <a href="{{ route('user.comics.chapters.read', ['comic' => $comic->id, 'chapter_number' => $prevChapter->chapter_number]) }}"
                class="group flex items-center justify-center gap-2 p-4 rounded-2xl
                    bg-[#1a1a1a] border border-gray-700
                    hover:border-blue-500 hover:bg-gray-800
                    transition-all text-gray-300 hover:text-white font-bold">
                <i class="fa-solid fa-angle-left"></i>
                Chapter trước
            </a>
            @else
            <div class="flex items-center justify-center gap-2 p-4 rounded-2xl
                    bg-[#1a1a1a] border border-gray-800
                    text-gray-600 font-bold opacity-50 cursor-not-allowed select-none">
                <i class="fa-solid fa-angle-left"></i>
                Chapter trước
            </div>
            @endif

            {{-- CHAPTER SAU --}}
            @if($nextChapter)
            <a href="{{ route('user.comics.chapters.read', ['comic' => $comic->id, 'chapter_number' => $nextChapter->chapter_number]) }}"
                class="group flex items-center justify-center gap-2 p-4 rounded-2xl
                    bg-[#1a1a1a] border border-gray-700
                    hover:border-blue-500 hover:bg-gray-800
                    transition-all text-gray-300 hover:text-white font-bold">
                Chapter sau
                <i class="fa-solid fa-angle-right"></i>
            </a>
            @else
            <div class="flex items-center justify-center gap-2 p-4 rounded-2xl
                    bg-[#1a1a1a] border border-gray-800
                    text-gray-600 font-bold opacity-50 cursor-not-allowed select-none">
                Chapter sau
                <i class="fa-solid fa-angle-right"></i>
            </div>
            @endif

        </div>

    </div>

</div>
@push('scripts')
<script>
    @if(auth()->check())
    const currentChapterId = {{ $chapter->id }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let saveTimeout = null;
    let isNavigating = false;
    let maxProgress = 0; // Lưu progress cao nhất đã đạt được

    // Function để tính progress hiện tại dựa trên số ảnh đã xem
    function getCurrentProgress() {
        const images = document.querySelectorAll('.chapter-image');
        if (images.length === 0) return 0;
        
        const viewportBottom = window.innerHeight + window.pageYOffset;
        let viewedCount = 0;
        
        images.forEach(function(img) {
            const imgTop = img.getBoundingClientRect().top + window.pageYOffset;
            // Nếu ảnh đã scroll qua (top của ảnh < bottom của viewport)
            if (imgTop < viewportBottom) {
                viewedCount++;
            }
        });
        
        return Math.min(100, Math.round((viewedCount / images.length) * 100));
    }

    // Function để cập nhật maxProgress cho thanh tiến trình (chỉ tăng, không giảm)
    function updateMaxProgress(newProgress) {
        // Chỉ cập nhật nếu progress mới lớn hơn progress cao nhất
        if (newProgress > maxProgress) {
            maxProgress = newProgress;
            return true; // Có thay đổi
        }
        return false; // Không thay đổi
    }

    // Function để tính progress và lưu lịch sử (lưu progress hiện tại, có thể tăng hoặc giảm)
    function saveReadingHistory() {
        if (isNavigating) return;
        
        const currentProgress = getCurrentProgress();
        // Cập nhật maxProgress cho thanh tiến trình (chỉ tăng)
        updateMaxProgress(currentProgress);
        
        // Lưu progress hiện tại vào database (có thể tăng hoặc giảm)
        fetch("{{ route('reading-history.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken
            },
            body: JSON.stringify({
                chapter_id: currentChapterId,
                progress: currentProgress // Lưu progress hiện tại, không phải maxProgress
            })
        }).catch(err => console.error('Error saving reading history:', err));
    }

    // Lưu lịch sử khi scroll (có delay)
    let lastSavedProgress = 0;
    window.addEventListener('scroll', function() {
        const currentProgress = getCurrentProgress();
        
        // Cập nhật maxProgress cho thanh tiến trình (chỉ tăng)
        updateMaxProgress(currentProgress);
        
        // Chỉ lưu vào database nếu progress thay đổi đáng kể (ít nhất 5% hoặc khác với lần lưu trước)
        if (Math.abs(currentProgress - lastSavedProgress) >= 5 || currentProgress === 100) {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                if (!isNavigating) {
                    fetch("{{ route('reading-history.store') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken
                        },
                        body: JSON.stringify({
                            chapter_id: currentChapterId,
                            progress: currentProgress // Lưu progress hiện tại
                        })
                    });
                    lastSavedProgress = currentProgress;
                }
            }, 1500);
        }
    });

    // Lưu lịch sử khi click vào các nút chuyển chapter
    document.addEventListener('DOMContentLoaded', function() {
        // Tìm tất cả các link chuyển chapter
        const chapterLinks = document.querySelectorAll('a[href*="/comics/{{ $comic->id }}/chapter-"]');
        
        chapterLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                // Chỉ xử lý nếu không phải chapter hiện tại và không phải Ctrl/Cmd click (mở tab mới)
                const isCurrentChapter = link.href.includes('/chapter-{{ $chapter->chapter_number }}');
                const isNewTab = e.ctrlKey || e.metaKey || e.shiftKey || (e.button && e.button === 1);
                
                if (!isCurrentChapter && !isNewTab) {
                    e.preventDefault();
                    isNavigating = true;
                    
                    // Lưu lịch sử trước khi chuyển trang
                    saveReadingHistory();
                    
                    // Đợi một chút để đảm bảo request được gửi đi
                    setTimeout(function() {
                        window.location.href = link.href;
                    }, 150);
                } else if (!isCurrentChapter && isNewTab) {
                    // Nếu mở tab mới, vẫn lưu lịch sử nhưng không prevent default
                    saveReadingHistory();
                }
            });
        });
    });

    // Lấy progress hiện tại từ server khi load trang (nếu có)
    @if(isset($currentProgress))
    maxProgress = {{ $currentProgress }};
    @else
    // Nếu chưa có progress, khởi tạo từ progress hiện tại
    document.addEventListener('DOMContentLoaded', function() {
        const initialProgress = getCurrentProgress();
        maxProgress = initialProgress;
    });
    @endif

    // Lưu lịch sử khi rời trang (beforeunload) - backup cho trường hợp đóng tab
    window.addEventListener('beforeunload', function() {
        if (!isNavigating) {
            const currentProgress = getCurrentProgress();
            if (currentProgress > 0) {
                // Lưu progress hiện tại (có thể tăng hoặc giảm)
                const formData = new FormData();
                formData.append('chapter_id', currentChapterId);
                formData.append('progress', currentProgress);
                formData.append('_token', csrfToken);
                
                // Gửi bằng fetch với keepalive thay vì sendBeacon để có thể dùng headers
                fetch("{{ route('reading-history.store') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: formData,
                    keepalive: true // Đảm bảo request được gửi ngay cả khi trang đang đóng
                });
            }
        }
    });
    @endif
</script>
@endpush

@endsection