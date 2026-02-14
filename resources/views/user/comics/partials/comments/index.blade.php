@php
// Mặc định là light theme nếu không truyền vào
$isDark = isset($theme) && $theme === 'dark';

// Định nghĩa các class CSS động
$bgClass = $isDark ? 'bg-[#1a1a1a]' : 'bg-white';
$textClass = $isDark ? 'text-gray-300' : 'text-gray-700';
$borderClass = $isDark ? 'border-gray-800' : 'border-gray-100';
$headingClass = $isDark ? 'text-blue-400' : 'text-blue-600';
$subTextClass = $isDark ? 'text-gray-500' : 'text-gray-500';
$inputBgClass = $isDark ? 'bg-[#252525] focus-within:bg-[#2a2a2a] focus-within:ring-blue-900' : 'bg-gray-100 focus-within:bg-white focus-within:ring-blue-100';
$inputTextClass = $isDark ? 'text-gray-200 placeholder-gray-500' : 'text-gray-800 placeholder-gray-500';
$bubbleBgClass = $isDark ? 'bg-[#252525]' : 'bg-gray-100';
$nameClass = $isDark ? 'text-gray-200' : 'text-gray-800';
$selectBgClass = $isDark ? 'bg-[#1a1a1a] border-gray-700 text-gray-300' : 'bg-white border-gray-300';
@endphp

{{-- 2. BÌNH LUẬN --}}
<div id="comments-section" class="{{ $bgClass }} rounded-lg shadow-sm">

    <div id="banned-words-data"
        data-banned='@json($bannedWords ?? [])'
        class="hidden"></div>

    <div class="px-6 py-4 border-b {{ $borderClass }} flex items-center justify-between">
        <h2 class="text-lg font-bold {{ $headingClass }} flex items-center gap-2">
            <i class="fas fa-comments"></i>
            <span>Bình luận</span>
            <span class="text-sm font-normal {{ $subTextClass }}">({{ $totalCommentsCount ?? 0 }})</span>
        </h2>

        {{-- Filter Dropdown --}}
        <div class="relative">
            <select id="comment-filter"
                class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm bg-white cursor-pointer hover:border-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                <option value="latest" {{ ($commentFilter ?? request('filter', 'latest')) === 'latest' ? 'selected' : '' }}>Mới nhất</option>
                <option value="oldest" {{ ($commentFilter ?? request('filter', 'latest')) === 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                <option value="popular" {{ ($commentFilter ?? request('filter', 'latest')) === 'popular' ? 'selected' : '' }}>Nổi bật</option>
            </select>
        </div>
    </div>

    <div class="p-6">
        @auth
        {{-- Input Comment --}}
        <div class="flex gap-3 mb-8">
            <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=random' }}"
                class="w-10 h-10 rounded-full object-cover flex-shrink-0"
                alt="{{ auth()->user()->name }}">

            <div class="js-comment-error hidden text-xs text-red-600 mt-1 ml-3 flex items-center gap-1.5 bg-red-50 border border-red-200 rounded-lg px-3 py-2 max-w-md relative"></div>

            <form method="POST"
                action="{{ route('comments.store', $comic) }}"
                class="flex-1 js-comment-form"
                data-comment-form="main">
                @csrf

                {{-- THÊM ĐOẠN NÀY --}}
                @if(isset($chapter))
                <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
                @endif
                {{-- HẾT --}}

                {{-- KHUNG NHẬP COMMENT --}}
                <div class="relative flex items-center bg-gray-100 rounded-[20px] px-3 py-2 shadow-sm focus-within:bg-white focus-within:shadow-md focus-within:ring-2 focus-within:ring-blue-100 transition-all">

                    {{-- Textarea --}}
                    <textarea name="content"
                        rows="1"
                        style="min-height: 36px; max-height: 120px;"
                        class="flex-1 bg-transparent border-none p-0 px-2 text-sm text-gray-800 placeholder-gray-500 focus:outline-none focus:ring-0 transition-all resize-none js-comment-textarea overflow-hidden leading-relaxed py-1"
                        placeholder="Viết bình luận..."
                        oninput="this.style.height = 'auto'; this.style.height = (this.scrollHeight) + 'px';">{{ old('content') }}</textarea>

                    {{-- Button --}}
                    <button type="submit"
                        class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full text-blue-600 hover:bg-gray-200 transition-colors opacity-80 hover:opacity-100 focus:outline-none ml-1"
                        title="Gửi bình luận">
                        <i class="fas fa-paper-plane text-sm transform rotate-12 translate-x-[-2px] translate-y-[1px]"></i>
                    </button>
                </div>

            </form>

            @push('scripts')
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const textareas = document.querySelectorAll('.js-comment-textarea');
                    textareas.forEach(tx => {
                        if (tx.value !== '') {
                            tx.style.height = 'auto';
                            tx.style.height = (tx.scrollHeight) + 'px';
                        }
                    });
                });
            </script>
            @endpush

        </div>

        {{-- Comment List --}}
        @if($comments->count() > 0)
        <div class="space-y-6" id="comments-container">
            @foreach($comments as $comment)
            <div class="flex gap-3 js-comment-item group scroll-mt-28"
                id="comment-{{ $comment->id }}"
                data-comment-id="{{ $comment->id }}" data-is-reply="0"
                data-timestamp="{{ $comment->created_at->timestamp }}"
                data-likes="{{ $comment->likes_count ?? 0 }}">
                {{-- Avatar --}}
                <img src="{{ $comment->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($comment->user->name).'&background=random' }}"
                    class="w-10 h-10 rounded-full object-cover flex-shrink-0"
                    alt="{{ $comment->user->name }}">

                <div class="flex-1 min-w-0">
                    {{-- Bubble comment + dấu ... --}}
                    <div class="flex items-center gap-2 group/comment">

                        {{-- Bubble comment --}}
                        <div class="{{ $bubbleBgClass }} rounded-2xl px-4 py-2.5 inline-block max-w-md js-bubble">
                            <div class="font-semibold {{ $nameClass }} text-sm mb-0.5">
                                {{ $comment->user->name }}
                            </div>

                            <p class="{{ $textClass }} text-sm leading-relaxed whitespace-normal break-words js-comment-content">
                                {{ trim($comment->content) }}
                            </p>
                        </div>

                        @php
                        $user = auth()->user();
                        $isAdmin = $user && (($user->role ?? null) === 'admin' || ($user->is_admin ?? false));
                        $isComicOwner = $user && ((int)($comic->created_by ?? 0) === (int)$user->id);

                        $canDelete = $isAdmin || $isComicOwner;
                        @endphp

                        <div class="relative flex-shrink-0">
                            {{-- Nút ... --}}
                            <button type="button"
                                class="opacity-0 group-hover/comment:opacity-100 transition-opacity
                                text-gray-500 hover:text-gray-700
                                w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-200/70 flex-shrink-0"
                                title="Tùy chọn"
                                data-comment-menu-btn>
                                <i class="fas fa-ellipsis-h text-sm"></i>
                            </button>

                            {{-- ========================================================== --}}
                            {{-- MENU DROPDOWN (Phiên bản siêu gọn - ôm sát nội dung) --}}
                            {{-- ========================================================== --}}
                            <div class="hidden absolute top-8 left-1/2 -translate-x-1/2 z-50 w-max filter drop-shadow-[0_2px_8px_rgba(0,0,0,0.12)]"
                                data-comment-menu>

                                {{-- 1. Mũi tên (Căn giữa) --}}
                                <div class="absolute -top-1 left-1/2 -translate-x-1/2 w-3 h-3 bg-white transform rotate-45 z-0"></div>

                                {{-- 2. Nội dung menu --}}
                                {{-- Bỏ w-full để menu co giãn tự động, không bị kéo dài --}}
                                <div class="relative z-10 bg-white rounded-lg overflow-hidden py-1 shadow-sm flex flex-col">

                                    {{-- Item 1: Báo cáo bình luận --}}
                                    <form method="POST" action="{{ route('comments.report', $comment->id) }}" class="w-full">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-left px-3 py-1.5 text-[13px] font-medium text-gray-900 hover:bg-gray-100 transition-colors whitespace-nowrap">
                                            Báo cáo bình luận
                                        </button>
                                    </form>

                                    {{-- Item 2: Xóa (Chủ sở hữu) HOẶC Báo cáo bình luận (Khách) --}}
                                    @if($canDelete)
                                    <form method="POST" action="{{ route('comments.destroy', $comment->id) }}"
                                        onsubmit="return confirm('Bạn có chắc muốn xoá bình luận này không?');" class="w-full">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-full text-left px-3 py-1.5 text-[13px] font-medium text-red-600 hover:bg-gray-100 transition-colors whitespace-nowrap">
                                            Xoá bình luận
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                            {{-- END MENU DROPDOWN --}}

                        </div>

                    </div>

                    {{-- Hàng action: Like / Trả lời / Thời gian --}}
                    <div class="flex items-center gap-4 mt-1.5 ml-1">
                        @auth
                        @php
                        $currentUser = auth()->user();
                        $isLiked = $comment->isLikedBy($currentUser);
                        @endphp

                        {{-- Like --}}
                        <div class="flex items-center gap-1">
                            <form action="{{ route('comments.reaction', ['comment' => $comment->id, 'type' => 'like']) }}"
                                method="POST" class="inline">
                                @csrf
                                <button type="button"
                                    data-comment-reaction="like"
                                    data-comment-id="{{ $comment->id }}"
                                    data-action="{{ route('comments.reaction', ['comment' => $comment->id, 'type' => 'like']) }}"
                                    class="js-comment-like-btn text-xs font-medium transition-colors {{ $isLiked ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}"
                                    title="Thích">
                                    <i class="fas fa-thumbs-up mr-1"></i>Thích
                                </button>
                            </form>
                            <span class="js-comment-like-count text-xs {{ $subTextClass }} {{ ($comment->likes_count ?? 0) == 0 ? 'hidden' : '' }}">
                                {{ $comment->likes_count ?? 0 }}
                            </span>
                        </div>

                        {{-- Trả lời --}}
                        <button type="button"
                            onclick="window.showReplyForm('{{ $comment->id }}')"
                            class="text-xs font-medium {{ $subTextClass }} hover:text-blue-600 transition-colors flex items-center gap-1">
                            <i class="far fa-comment-dots"></i>Trả lời
                        </button>

                        {{-- Thời gian --}}
                        <span class="text-xs {{ $subTextClass }} ml-auto">
                            {{ $comment->created_at->diffForHumans() }}
                        </span>
                        @endauth
                    </div>

                    {{-- Nút hiện/ẩn phản hồi --}}
                    @if($comment->replies->count() > 0)
                    <button
                        class="text-xs text-blue-500 hover:text-blue-600 font-medium mt-2 reply-toggle flex items-center gap-1.5 transition-colors"
                        data-comment-id="{{ $comment->id }}"
                        data-reply-count="{{ $comment->replies->count() }}"
                        data-toggle-replies="{{ $comment->id }}">
                        <i class="fas fa-reply text-xs"></i>
                        <span class="reply-count-text">{{ $comment->replies->count() }} phản hồi</span>
                    </button>
                    @endif

                    {{-- Phần reply form --}}

                    {{-- Form trả lời --}}
                    @auth
                    <div id="reply-{{ $comment->id }}" class="hidden mt-3">
                        <div class="flex gap-3">
                            <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=random' }}"
                                class="w-8 h-8 rounded-full object-cover flex-shrink-0"
                                alt="{{ auth()->user()->name }}">
                            <form method="POST"
                                action="{{ route('comments.store', $comic) }}"
                                class="flex-1 js-comment-form"
                                data-comment-form="reply">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                                {{-- THÊM ĐOẠN NÀY --}}
                                @if(isset($chapter))
                                <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
                                @endif
                                {{-- HẾT --}}

                                {{-- KHUNG NHẬP REPLY --}}
                                <div class="relative flex items-center bg-gray-100 rounded-[18px] px-2 py-1 shadow-sm focus-within:bg-white focus-within:shadow-md focus-within:ring-2 focus-within:ring-blue-100 transition-all">

                                    {{-- Textarea --}}
                                    <textarea name="content"
                                        rows="1"
                                        style="min-height: 32px; max-height: 80px;"
                                        class="flex-1 bg-transparent border-none p-0 px-2 text-[13px] text-gray-800 placeholder-gray-500 focus:outline-none focus:ring-0 transition-all resize-none js-comment-textarea overflow-hidden leading-relaxed py-1.5"
                                        placeholder="Viết phản hồi..."
                                        oninput="this.style.height = 'auto'; this.style.height = (this.scrollHeight) + 'px';"></textarea>

                                    {{-- Button --}}
                                    <button type="submit"
                                        class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-full text-blue-600 hover:bg-gray-200 transition-colors opacity-80 hover:opacity-100 focus:outline-none ml-1"
                                        title="Gửi phản hồi">
                                        <i class="fas fa-paper-plane text-xs transform rotate-12 translate-x-[-1px] translate-y-[1px]"></i>
                                    </button>
                                </div>

                                <div class="js-comment-error hidden text-xs text-red-600 mt-1 ml-2 flex items-center gap-1.5 bg-red-50 border border-red-200 rounded-lg px-3 py-2 max-w-md relative"></div>

                            </form>
                            <button type="button"
                                onclick="document.getElementById('reply-{{ $comment->id }}').classList.add('hidden')"
                                class="px-3 py-1 text-xs text-gray-500 hover:text-gray-700 flex-shrink-0">
                                Hủy
                            </button>
                        </div>
                    </div>
                    @endauth

                    {{-- Replies --}}
                    <div
                        class="replies mt-3 ml-6 space-y-3 hidden transition-all duration-200"
                        id="replies-{{ $comment->id }}"
                        data-replies-container="{{ $comment->id }}"
                        data-reply-container="true">
                        <div class="mt-4 space-y-4 js-replies-container" data-parent-id="{{ $comment->id }}">
                            @foreach($comment->replies as $reply)
                            <div class="flex gap-3 js-comment-item group"
                                id="comment-{{ $reply->id }}"
                                data-comment-id="{{ $reply->id }}"
                                data-is-reply="1"
                                data-parent-id="{{ $reply->parent_id }}">
                                <img src="{{ $reply->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($reply->user->name).'&background=random' }}"
                                    class="w-10 h-10 rounded-full object-cover flex-shrink-0"
                                    alt="{{ $reply->user->name }}">

                                <div class="flex-1 min-w-0">
                                    {{-- Bubble reply + dấu ... --}}
                                    <div class="flex items-center gap-2 group/comment">

                                        {{-- Bubble reply --}}
                                        <div class="{{ $bubbleBgClass }} rounded-2xl px-4 py-2.5 inline-block max-w-md js-bubble">

                                            {{-- Bình luận bình thường --}}
                                            <div class="font-semibold {{ $nameClass }} text-sm mb-0.5">
                                                {{ $reply->user->name }}
                                            </div>

                                            <p class="{{ $textClass }} text-sm leading-relaxed whitespace-normal break-words js-comment-content">
                                                {{ trim($reply->content) }}
                                            </p>
                                        </div>

                                        {{-- Dấu ... bên phải bubble --}}

                                        @php
                                        $user = auth()->user();
                                        $isAdmin = $user && (($user->role ?? null) === 'admin' || ($user->is_admin ?? false));
                                        $isComicOwner = $user && ((int)($comic->created_by ?? 0) === (int)$user->id);
                                        $canDelete = $isAdmin || $isComicOwner;
                                        @endphp

                                        <div class="relative flex-shrink-0">
                                            <button type="button"
                                                class="opacity-0 group-hover/comment:opacity-100 transition-opacity
                                                text-gray-500 hover:text-gray-700
                                                w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-200/70 flex-shrink-0"
                                                title="Tùy chọn"
                                                data-comment-menu-btn>
                                                <i class="fas fa-ellipsis-h text-sm"></i>
                                            </button>

                                            {{-- ========================================================== --}}
                                            {{-- MENU DROPDOWN (Phiên bản siêu gọn - ôm sát nội dung) --}}
                                            {{-- ========================================================== --}}
                                            <div class="hidden absolute top-8 left-1/2 -translate-x-1/2 z-50 w-max filter drop-shadow-[0_2px_8px_rgba(0,0,0,0.12)]"
                                                data-comment-menu>

                                                {{-- 1. Mũi tên (Căn giữa) --}}
                                                <div class="absolute -top-1 left-1/2 -translate-x-1/2 w-3 h-3 bg-white transform rotate-45 z-0"></div>

                                                {{-- 2. Nội dung menu --}}
                                                {{-- Bỏ w-full để menu co giãn tự động, không bị kéo dài --}}
                                                <div class="relative z-10 bg-white rounded-lg overflow-hidden py-1 shadow-sm flex flex-col">

                                                    {{-- Item 1: Báo cáo bình luận --}}
                                                    <form method="POST" action="{{ route('comments.report', $reply->id) }}" class="w-full">
                                                        @csrf
                                                        <button type="submit"
                                                            class="w-full text-left px-3 py-1.5 text-[13px] font-medium text-gray-900 hover:bg-gray-100 transition-colors whitespace-nowrap">
                                                            Báo cáo bình luận
                                                        </button>
                                                    </form>

                                                    {{-- Item 2: Xóa (Chủ sở hữu) HOẶC Báo cáo bình luận (Khách) --}}
                                                    @if($canDelete)
                                                    <form method="POST" action="{{ route('comments.destroy', $reply->id) }}"
                                                        onsubmit="return confirm('Bạn có chắc muốn xoá bình luận này không?');" class="w-full">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="w-full text-left px-3 py-1.5 text-[13px] font-medium text-red-600 hover:bg-gray-100 transition-colors whitespace-nowrap">
                                                            Xoá bình luận
                                                        </button>
                                                    </form>
                                                    @endif
                                                </div>
                                            </div>
                                            {{-- END MENU DROPDOWN --}}

                                        </div>
                                    </div>

                                    {{-- Action buttons --}}
                                    <div class="flex items-center gap-4 mt-1.5 ml-1">
                                        @auth
                                        @php
                                        $currentUser = auth()->user();
                                        $isReplyLiked = $reply->isLikedBy($currentUser);
                                        @endphp
                                        <div class="flex items-center gap-1">
                                            <form action="{{ route('comments.reaction', ['comment' => $reply->id, 'type' => 'like']) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <button type="button"
                                                    data-comment-reaction="like"
                                                    data-comment-id="{{ $reply->id }}"
                                                    data-action="{{ route('comments.reaction', ['comment' => $reply->id, 'type' => 'like']) }}"
                                                    class="js-comment-like-btn text-xs font-medium transition-colors {{ $isReplyLiked ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}"
                                                    title="Thích">
                                                    <i class="fas fa-thumbs-up mr-1"></i>Thích
                                                </button>
                                            </form>
                                            <span class="js-comment-like-count text-xs {{ $subTextClass }} {{ ($reply->likes_count ?? 0) == 0 ? 'hidden' : '' }}">
                                                {{ $reply->likes_count ?? 0 }}
                                            </span>
                                        </div>

                                        <button type="button"
                                            onclick="scrollToReplyForm('{{ $comment->id }}', '{{ $reply->user->name }}')"
                                            class="text-xs font-medium text-gray-500 hover:text-blue-600 transition-colors flex items-center gap-1">
                                            <i class="far fa-comment-dots"></i>Trả lời
                                        </button>

                                        <span class="text-xs {{ $subTextClass }} ml-auto">
                                            {{ $reply->created_at->diffForHumans() }}
                                        </span>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $comments->appends(['filter' => $commentFilter ?? request('filter')])->links() }}
        </div>
        @else
        <div class="text-center py-10 {{ $subTextClass }} text-sm">
            Chưa có bình luận nào. Hãy là người đầu tiên bình luận!
        </div>
        @endif
        @else
        <div class="text-center py-10">
            <i class="fas fa-lock {{ $subTextClass }} text-3xl mb-3 block"></i>
            <p class="text-gray-600 mb-3">Vui lòng đăng nhập để xem bình luận</p>
            <a href="{{ route('login.form') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors inline-block">
                Đăng nhập ngay
            </a>
        </div>
        @endauth
    </div>
</div>

<script>
    window.commentTheme = {
        bubbleBg: "{{ $bubbleBgClass }}",
        nameText: "{{ $nameClass }}",
        contentText: "{{ $textClass }}"
    };
</script>

@include('user.comics.partials.comments.scripts')