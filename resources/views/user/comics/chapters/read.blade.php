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
    let maxProgress = 0; // Lưu progress cao nhất để hiển thị trên UI

    let lastSavedProgress = @if(isset($currentProgress)) {{ $currentProgress }} @else 0 @endif;
    let sessionMaxProgress = 0; // Bắt đầu từ 0 mỗi session để có thể lưu progress thấp hơn
    let hasScrolledDown = false; // Đánh dấu đã scroll xuống trong session này

    // Function để tính progress hiện tại dựa trên số ảnh đã xem
    function getCurrentProgress() {
        const images = document.querySelectorAll('.chapter-image');
        if (images.length === 0) return 0;

        const viewportBottom = window.innerHeight + window.pageYOffset;
        let viewedCount = 0;

        images.forEach(function(img) {
            const imgTop = img.getBoundingClientRect().top + window.pageYOffset;
            if (imgTop < viewportBottom) {
                viewedCount++;
            }
        });

        return Math.min(100, Math.round((viewedCount / images.length) * 100));
    }

    // Function để cập nhật maxProgress cho thanh tiến trình (chỉ tăng, không giảm)
    function updateMaxProgress(newProgress) {
        if (newProgress > maxProgress) {
            maxProgress = newProgress;
            return true;
        }
        return false;
    }

    // Function để lưu progress vào database
    function saveProgressToDatabase(progress) {
        if (isNavigating) return;

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
                        progress: progress
                    })
                }).then(() => {
                    lastSavedProgress = progress;
                    console.log('Progress saved:', progress);
                }).catch(err => console.error('Error saving reading history:', err));
            }
        }, 1000);
    }

    window.addEventListener('scroll', function() {
        const currentProgress = getCurrentProgress();
        
        // Cập nhật maxProgress cho thanh tiến trình (chỉ tăng)
        updateMaxProgress(currentProgress);

        // Theo dõi progress cao nhất trong session
        if (currentProgress > sessionMaxProgress) {
            sessionMaxProgress = currentProgress;
            hasScrolledDown = true; // Đã scroll xuống trong session này
        }

        // Logic lưu vào database:
        // 1. Lưu khi progress TĂNG so với lần lưu trước (đọc tiếp)
        // 2. KHÔNG lưu khi progress GIẢM trong cùng session (scroll lên xem lại)
        // 3. Lưu khi đạt 100%
        const progressIncreased = currentProgress > lastSavedProgress && Math.abs(currentProgress - lastSavedProgress) >= 3;
        const isComplete = currentProgress === 100;
        
        // CHỈ lưu khi progress tăng hoặc đạt 100%
        const shouldSave = progressIncreased || isComplete;

        if (shouldSave) {
            saveProgressToDatabase(currentProgress);
        }
    });

    // Lắng nghe event từ reader.blade.php khi thanh tiến trình thay đổi
    window.addEventListener('progressUpdated', function(event) {
        const currentProgress = getCurrentProgress();
        
        // Cập nhật maxProgress cho UI
        updateMaxProgress(currentProgress);
        
        // Cập nhật sessionMaxProgress
        if (currentProgress > sessionMaxProgress) {
            sessionMaxProgress = currentProgress;
        }
        
        // Chỉ lưu khi progress tăng
        if (currentProgress > lastSavedProgress && Math.abs(currentProgress - lastSavedProgress) >= 3) {
            saveProgressToDatabase(currentProgress);
        } else if (currentProgress === 100) {
            saveProgressToDatabase(currentProgress);
        }
    });

    // Lưu lịch sử khi click vào các nút chuyển chapter
    document.addEventListener('DOMContentLoaded', function() {
        const chapterLinks = document.querySelectorAll('a[href*="/comics/{{ $comic->id }}/chapter-"]');

        chapterLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                const isCurrentChapter = link.href.includes('/chapter-{{ $chapter->chapter_number }}');
                const isNewTab = e.ctrlKey || e.metaKey || e.shiftKey || (e.button && e.button === 1);

                if (!isCurrentChapter && !isNewTab) {
                    e.preventDefault();
                    isNavigating = true;

                    // LUÔN lưu sessionMaxProgress (progress cao nhất trong session này)
                    // Cho phép lưu cả khi sessionMaxProgress < lastSavedProgress
                    
                    fetch("{{ route('reading-history.store') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken
                        },
                        body: JSON.stringify({
                            chapter_id: currentChapterId,
                            progress: sessionMaxProgress
                        })
                    }).finally(() => {
                        setTimeout(function() {
                            window.location.href = link.href;
                        }, 150);
                    });
                } else if (!isCurrentChapter && isNewTab) {
                    fetch("{{ route('reading-history.store') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken
                        },
                        body: JSON.stringify({
                            chapter_id: currentChapterId,
                            progress: sessionMaxProgress
                        })
                    }).catch(err => console.error('Error saving:', err));
                }
            });
        });
    });

    // Khởi tạo maxProgress và sessionMaxProgress
    @if(isset($currentProgress))
    maxProgress = {{ $currentProgress }}; // Dùng cho thanh tiến trình UI (chỉ tăng)
    sessionMaxProgress = 0; // Session mới bắt đầu từ 0 để cho phép lưu progress thấp hơn
    lastSavedProgress = {{ $currentProgress }}; // Progress đã lưu trong DB
    @else
    document.addEventListener('DOMContentLoaded', function() {
        const initialProgress = getCurrentProgress();
        maxProgress = initialProgress;
        sessionMaxProgress = 0; // Bắt đầu từ 0
    });
    @endif

    // Lưu lịch sử khi rời trang (beforeunload)
    window.addEventListener('beforeunload', function() {
        if (!isNavigating) {
            // LUÔN lưu sessionMaxProgress (progress cao nhất trong session này)
            // CHO PHÉP lưu cả khi sessionMaxProgress < lastSavedProgress
            // Ví dụ: 
            // - Lần 1: đọc đến 50% → lưu 50%
            // - Lần 2: load lại, chỉ đọc đến 30% → sessionMaxProgress = 30% → lưu 30%
            
            const formData = new FormData();
            formData.append('chapter_id', currentChapterId);
            formData.append('progress', sessionMaxProgress);
            formData.append('_token', csrfToken);

            fetch("{{ route('reading-history.store') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken
                },
                body: formData,
                keepalive: true
            });
        }
    });
    @endif
</script>
@endpush
{{-- KHUNG BÌNH LUẬN --}}
    <div class="max-w-4xl mx-auto mt-12 pb-20 px-4">
        {{-- Truyền biến $chapter vào component comment --}}
        @include('user.comics.partials.comments.index', [
            'comic' => $comic,
            'comments' => $comments, // Biến này lấy từ ReadChapterController
            'chapter' => $chapter,    // <--- QUAN TRỌNG: Truyền biến này để form biết ID chapter
            'theme' => 'dark'  // <--- Kích hoạt giao diện tối
        ])
    </div>
@endsection