{{-- 2. BÌNH LUẬN --}}
<div id="comments-section" class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-lg font-bold text-blue-600 flex items-center gap-2">
            <i class="fas fa-comments"></i>
            <span>Bình luận</span>
            <span class="text-sm font-normal text-gray-500">({{ $comments->total() }})</span>
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

            <form method="POST"
                action="{{ route('comments.store', $comic) }}"
                class="flex-1 js-comment-form"
                data-comment-form="main">
                @csrf

                {{-- PHẦN 1: KHUNG NHẬP COMMENT --}}
                {{-- Thay đổi: 'items-end' -> 'items-center' để nút luôn nằm giữa --}}
                <div class="relative flex items-center bg-gray-100 rounded-[20px] px-3 py-2 shadow-sm focus-within:bg-white focus-within:shadow-md focus-within:ring-2 focus-within:ring-blue-100 transition-all">

                    {{-- Textarea --}}
                    <textarea name="content"
                        rows="1"
                        style="min-height: 36px; max-height: 120px;"
                        class="flex-1 bg-transparent border-none p-0 px-2 text-sm text-gray-800 placeholder-gray-500 focus:outline-none focus:ring-0 transition-all resize-none js-comment-textarea overflow-hidden leading-relaxed py-1"
                        placeholder="Viết bình luận..."
                        oninput="this.style.height = 'auto'; this.style.height = (this.scrollHeight) + 'px';">{{ old('content') }}</textarea>

                    {{-- Button --}}
                    {{-- Thay đổi: Bỏ 'mb-0.5' vì đã dùng items-center --}}
                    <button type="submit"
                        class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full text-blue-600 hover:bg-gray-200 transition-colors opacity-80 hover:opacity-100 focus:outline-none ml-1"
                        title="Gửi bình luận">
                        <i class="fas fa-paper-plane text-sm transform rotate-12 translate-x-[-2px] translate-y-[1px]"></i>
                    </button>
                </div>

                {{-- PHẦN 2: THÔNG BÁO LỖI (Bên ngoài) --}}
                @error('content')
                <div class="text-xs text-red-500 mt-2 ml-4 text-left">
                    {{ $message }}
                </div>
                @enderror

            </form>

            {{-- Script giữ nguyên --}}
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
            <div class="flex gap-3 js-comment-item"
                id="comment-{{ $comment->id }}"
                data-comment-id="{{ $comment->id }}"
                data-timestamp="{{ $comment->created_at->timestamp }}"
                data-likes="{{ $comment->likes_count ?? 0 }}">
                {{-- Avatar --}}
                <img src="{{ $comment->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($comment->user->name).'&background=random' }}"
                    class="w-10 h-10 rounded-full object-cover flex-shrink-0"
                    alt="{{ $comment->user->name }}">

                <div class="flex-1 min-w-0">
                    {{-- Bubble comment --}}
                    <div class="bg-gray-100 rounded-2xl px-4 py-2.5 inline-block max-w-md">
                        <div class="font-semibold text-gray-800 text-sm mb-0.5">{{ $comment->user->name }}</div>
                        <p class="text-gray-700 text-sm leading-relaxed whitespace-normal break-words js-comment-content">
                            {{ trim($comment->content) }}
                        </p>
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
                            <span class="js-comment-like-count text-xs text-gray-500 {{ ($comment->likes_count ?? 0) == 0 ? 'hidden' : '' }}">
                                {{ $comment->likes_count ?? 0 }}
                            </span>
                        </div>

                        {{-- Trả lời --}}
                        <button type="button"
                            onclick="window.showReplyForm('{{ $comment->id }}')"
                            class="text-xs font-medium text-gray-500 hover:text-blue-600 transition-colors flex items-center gap-1">
                            <i class="far fa-comment-dots"></i>Trả lời
                        </button>

                        {{-- Thời gian --}}
                        <span class="text-xs text-gray-500 ml-auto">
                            {{ $comment->created_at->diffForHumans() }}
                        </span>
                        @endauth
                    </div>

                    {{-- Nút hiện/ẩn phản hồi --}}
                    @if($comment->replies->count() > 0)
                    <button
                        class="text-xs text-blue-500 hover:text-blue-600 font-medium mt-2 reply-toggle flex items-center gap-1.5 transition-colors"
                        data-comment-id="{{ $comment->id }}"
                        data-reply-count="{{ $comment->replies->count() }}">
                        <i class="fas fa-reply text-xs"></i>
                        <span class="reply-count-text">{{ $comment->replies->count() }} phản hồi</span>
                    </button>
                    @endif

                    {{-- Form trả lời cấp 1 --}}
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

                                {{-- PHẦN 1: KHUNG NHẬP REPLY (Bong bóng xám nhỏ gọn) --}}
                                <div class="relative flex items-center bg-gray-100 rounded-[18px] px-2 py-1 shadow-sm focus-within:bg-white focus-within:shadow-md focus-within:ring-2 focus-within:ring-blue-100 transition-all">

                                    {{-- Textarea: Font chữ nhỏ (13px), tự giãn dòng --}}
                                    <textarea name="content"
                                        rows="1"
                                        style="min-height: 32px; max-height: 80px;"
                                        class="flex-1 bg-transparent border-none p-0 px-2 text-[13px] text-gray-800 placeholder-gray-500 focus:outline-none focus:ring-0 transition-all resize-none js-comment-textarea overflow-hidden leading-relaxed py-1.5"
                                        placeholder="Viết phản hồi..."
                                        oninput="this.style.height = 'auto'; this.style.height = (this.scrollHeight) + 'px';"></textarea>

                                    {{-- Button: Nút nhỏ (w-7 h-7), luôn nằm giữa theo chiều dọc --}}
                                    <button type="submit"
                                        class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-full text-blue-600 hover:bg-gray-200 transition-colors opacity-80 hover:opacity-100 focus:outline-none ml-1"
                                        title="Gửi phản hồi">
                                        <i class="fas fa-paper-plane text-xs transform rotate-12 translate-x-[-1px] translate-y-[1px]"></i>
                                    </button>
                                </div>
                                {{-- Đóng thẻ div khung xám --}}

                                {{-- PHẦN 2: THÔNG BÁO LỖI (Nằm ngoài) --}}
                                @error('content')
                                <div class="text-[11px] text-red-500 mt-1 ml-3 text-left">
                                    {{ $message }}
                                </div>
                                @enderror

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
                        data-reply-container="true">
                        <div class="mt-4 space-y-4 js-replies-container" data-parent-id="{{ $comment->id }}">
                            @foreach($comment->replies as $reply)
                            <div class="flex gap-3 js-comment-item" id="comment-{{ $reply->id }}" data-comment-id="{{ $reply->id }}">
                                <img src="{{ $reply->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($reply->user->name).'&background=random' }}"
                                    class="w-10 h-10 rounded-full object-cover flex-shrink-0"
                                    alt="{{ $reply->user->name }}">
                                <div class="flex-1 min-w-0">
                                    <div class="bg-gray-100 rounded-2xl px-4 py-2.5 inline-block max-w-md">
                                        <div class="font-semibold text-gray-800 text-sm mb-0.5">{{ $reply->user->name }}</div>
                                        <p class="text-gray-700 text-sm leading-relaxed whitespace-normal break-words js-comment-content">
                                            {{ trim($reply->content) }}
                                        </p>
                                    </div>

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
                                            <span class="js-comment-like-count text-xs text-gray-500 {{ ($reply->likes_count ?? 0) == 0 ? 'hidden' : '' }}">
                                                {{ $reply->likes_count ?? 0 }}
                                            </span>
                                        </div>

                                        <button type="button"
                                            onclick="scrollToReplyForm('{{ $comment->id }}', '{{ $reply->user->name }}')"
                                            class="text-xs font-medium text-gray-500 hover:text-blue-600 transition-colors flex items-center gap-1">
                                            <i class="far fa-comment-dots"></i>Trả lời
                                        </button>

                                        <span class="text-xs text-gray-500 ml-auto">
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
        <div class="text-center py-10 text-gray-500 text-sm">
            Chưa có bình luận nào. Hãy là người đầu tiên bình luận!
        </div>
        @endif
        @else
        <div class="text-center py-10">
            <i class="fas fa-lock text-gray-400 text-3xl mb-3 block"></i>
            <p class="text-gray-600 mb-3">Vui lòng đăng nhập để xem bình luận</p>
            <a href="{{ route('login.form') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors inline-block">
                Đăng nhập ngay
            </a>
        </div>
        @endauth
    </div>
</div>

@push('scripts')
<script>
    (function() {
        if (window.__comicCommentsInitialized) return;
        window.__comicCommentsInitialized = true;

        function getCsrfToken() {
            var meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        }

        function appendCommentToDom(comment, form) {
            if (!comment) return;
            var isReply = !!comment.parent_id;

            if (!isReply) {
                var container = document.getElementById('comments-container');
                if (!container) return;

                var wrapper = document.createElement('div');
                wrapper.className = 'flex gap-3 js-comment-item';
                wrapper.id = 'comment-' + comment.id;
                wrapper.setAttribute('data-comment-id', comment.id);
                wrapper.setAttribute('data-timestamp', comment.timestamp || '');
                wrapper.setAttribute('data-likes', comment.likes_count || 0);

                // URL cho reaction & reply
                var likeUrl = '/comments/' + comment.id + '/reaction/like';
                var storeUrl = form.action;

                wrapper.innerHTML = '' +
                    '<img src="' + (comment.user.avatar_url || '') + '" ' +
                    'class="w-10 h-10 rounded-full object-cover flex-shrink-0" ' +
                    'alt="' + (comment.user.name || '') + '">' +
                    '<div class="flex-1 min-w-0">' +
                    '<div class="bg-gray-100 rounded-2xl px-4 py-2.5 inline-block max-w-md">' +
                    '<div class="font-semibold text-gray-800 text-sm mb-0.5">' + (comment.user.name || '') + '</div>' +
                    '<p class="text-gray-700 text-sm leading-relaxed whitespace-normal break-words js-comment-content"></p>' +
                    '</div>' +
                    '<div class="flex items-center gap-4 mt-1.5 ml-1">' +
                    '<div class="flex items-center gap-1">' +
                    '<form method="POST" action="' + likeUrl + '" class="inline">' +
                    '<button type="button" ' +
                    'data-comment-reaction="like" ' +
                    'data-comment-id="' + comment.id + '" ' +
                    'data-action="' + likeUrl + '" ' +
                    'class="js-comment-like-btn text-xs font-medium transition-colors text-gray-500 hover:text-blue-600" ' +
                    'title="Thích">' +
                    '<i class="fas fa-thumbs-up mr-1"></i>Thích' +
                    '</button>' +
                    '</form>' +
                    '<span class="js-comment-like-count text-xs text-gray-500 hidden">0</span>' +
                    '</div>' +
                    '<button type="button" ' +
                    'onclick="window.showReplyForm(\'' + comment.id + '\')" ' +
                    'class="text-xs font-medium text-gray-500 hover:text-blue-600 transition-colors flex items-center gap-1">' +
                    '<i class="far fa-comment-dots"></i>Trả lời' +
                    '</button>' +
                    '<span class="text-xs text-gray-500 ml-auto">' +
                    (comment.created_human || 'Vừa xong') +
                    '</span>' +
                    '</div>' +
                    '<div id="reply-' + comment.id + '" class="hidden mt-3">' +
                    '<div class="flex gap-3">' +
                    '<img src="' + (comment.user.avatar_url || '') + '" ' +
                    'class="w-8 h-8 rounded-full object-cover" ' +
                    'alt="' + (comment.user.name || '') + '">' +
                    '<form method="POST" action="' + storeUrl + '" class="flex-1 js-comment-form" data-comment-form="reply">' +
                    '<input type="hidden" name="parent_id" value="' + comment.id + '">' +
                    '<textarea name="content" rows="2" ' +
                    'class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all resize-none js-comment-textarea" ' +
                    'placeholder="Viết phản hồi..."></textarea>' +
                    '<div class="flex justify-end mt-2 gap-2">' +
                    '<button type="button" ' +
                    'onclick="document.getElementById(\'reply-' + comment.id + '\').classList.add(\'hidden\')" ' +
                    'class="px-3 py-1 text-xs text-gray-500 hover:text-gray-700">' +
                    'Hủy' +
                    '</button>' +
                    '<button type="submit" ' +
                    'class="px-4 py-1 bg-blue-600 text-white text-xs font-semibold rounded hover:bg-blue-700 transition-colors">' +
                    'Gửi phản hồi' +
                    '</button>' +
                    '</div>' +
                    '</form>' +
                    '</div>' +
                    '</div>' +
                    '<div class="mt-4 space-y-4 js-replies-container" data-parent-id="' + comment.id + '"></div>' +
                    '</div>';

                var contentEl = wrapper.querySelector('.js-comment-content');
                if (contentEl) {
                    contentEl.textContent = comment.content || '';
                }

                // Thêm mới lên đầu (mới nhất)
                if (container.firstChild) {
                    container.insertBefore(wrapper, container.firstChild);
                } else {
                    container.appendChild(wrapper);
                }

                return;
            }

            // Reply
            var repliesContainer = document.querySelector('.js-replies-container[data-parent-id="' + comment.parent_id + '"]');
            if (!repliesContainer) {
                var parentWrapper = document.getElementById('comment-' + comment.parent_id);
                if (!parentWrapper) return;
                repliesContainer = document.createElement('div');
                repliesContainer.className = 'mt-4 space-y-4 js-replies-container';
                repliesContainer.setAttribute('data-parent-id', comment.parent_id);
                parentWrapper.querySelector('.flex-1.min-w-0').appendChild(repliesContainer);
            }

            var replyWrapper = document.createElement('div');
            replyWrapper.className = 'flex gap-3 js-comment-item';
            replyWrapper.id = 'comment-' + comment.id;
            replyWrapper.setAttribute('data-comment-id', comment.id);

            var likeUrlReply = '/comments/' + comment.id + '/reaction/like';

            replyWrapper.innerHTML = '' +
                '<img src="' + (comment.user.avatar_url || '') + '" ' +
                'class="w-10 h-10 rounded-full object-cover flex-shrink-0" ' +
                'alt="' + (comment.user.name || '') + '">' +
                '<div class="flex-1 min-w-0">' +
                '<div class="bg-gray-100 rounded-2xl px-4 py-2.5 inline-block max-w-md">' +
                '<div class="font-semibold text-gray-800 text-sm mb-0.5">' + (comment.user.name || '') + '</div>' +
                '<p class="text-gray-700 text-sm leading-relaxed whitespace-normal break-words js-comment-content"></p>' +
                '</div>' +
                '<div class="flex items-center gap-4 mt-1.5 ml-1">' +
                '<div class="flex items-center gap-1">' +
                '<form method="POST" action="' + likeUrlReply + '" class="inline">' +
                '<button type="button" ' +
                'data-comment-reaction="like" ' +
                'data-comment-id="' + comment.id + '" ' +
                'data-action="' + likeUrlReply + '" ' +
                'class="js-comment-like-btn text-xs font-medium transition-colors text-gray-500 hover:text-blue-600" ' +
                'title="Thích">' +
                '<i class="fas fa-thumbs-up mr-1"></i>Thích' +
                '</button>' +
                '</form>' +
                '<span class="js-comment-like-count text-xs text-gray-500 hidden">0</span>' +
                '</div>' +
                '<button type="button" ' +
                'onclick="scrollToReplyForm(\'' + comment.parent_id + '\', \'' + (comment.user.name || '') + '\')" ' +
                'class="text-xs font-medium text-gray-500 hover:text-blue-600 transition-colors flex items-center gap-1">' +
                '<i class="far fa-comment-dots"></i>Trả lời' +
                '</button>' +
                '<span class="text-xs text-gray-500 ml-auto">' +
                (comment.created_human || 'Vừa xong') +
                '</span>' +
                '</div>' +
                '</div>';

            var replyContentEl = replyWrapper.querySelector('.js-comment-content');
            if (replyContentEl) {
                replyContentEl.textContent = comment.content || '';
            }

            repliesContainer.appendChild(replyWrapper);

            // Sau khi thêm node mới, bind lại các form/comment events
            setupCommentForms();
        }

        async function submitCommentForm(form) {
            if (!form) return;

            var submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
            }

            var textarea = form.querySelector('.js-comment-textarea');
            var errorEl = form.querySelector('.js-comment-error');
            if (errorEl) {
                errorEl.textContent = '';
                errorEl.classList.add('hidden');
            }

            try {
                var formData = new FormData(form);
                var response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) {
                    if (response.status === 422) {
                        var errorData = await response.json().catch(function() {
                            return null;
                        });
                        var msg = errorData && errorData.errors && errorData.errors.content ?
                            errorData.errors.content.join(' ') :
                            'Nội dung bình luận không hợp lệ.';
                        if (!errorEl && textarea) {
                            errorEl = document.createElement('p');
                            errorEl.className = 'js-comment-error text-xs text-red-500 mt-1';
                            textarea.parentNode.insertBefore(errorEl, textarea.nextSibling);
                        }
                        if (errorEl) {
                            errorEl.textContent = msg;
                            errorEl.classList.remove('hidden');
                        }
                    }
                    return;
                }

                var data = await response.json();
                if (data && data.status === 'success') {
                    appendCommentToDom(data.comment, form);
                    if (textarea) {
                        textarea.value = '';
                    }

                    // Nếu là reply form thì ẩn lại block reply
                    var parentIdInput = form.querySelector('input[name="parent_id"]');
                    if (parentIdInput) {
                        var replyBlock = document.getElementById('reply-' + parentIdInput.value);
                        if (replyBlock) {
                            replyBlock.classList.add('hidden');
                        }
                    }
                }
            } catch (e) {
                console.error('Error submitting comment form', e);
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
            }
        }

        function setupCommentForms() {
            var forms = document.querySelectorAll('.js-comment-form');
            forms.forEach(function(form) {
                if (form.__commentFormBound) return;
                form.__commentFormBound = true;

                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    submitCommentForm(form);
                });

                var textarea = form.querySelector('.js-comment-textarea');
                if (textarea && !textarea.__commentKeydownBound) {
                    textarea.__commentKeydownBound = true;
                    textarea.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' || e.keyCode === 13) {
                            if (e.shiftKey) {
                                // Shift + Enter: xuống dòng
                                return;
                            }
                            // Enter: submit form
                            e.preventDefault();
                            submitCommentForm(form);
                        }
                    });
                }
            });
        }

        window.showReplyForm = function(commentId) {
            var replyBlock = document.getElementById('reply-' + commentId);
            if (!replyBlock) return;
            replyBlock.classList.remove('hidden');
            var textarea = replyBlock.querySelector('.js-comment-textarea');
            if (textarea) {
                textarea.focus();
                // Đưa con trỏ xuống cuối nội dung (nếu có)
                var len = textarea.value.length;
                textarea.setSelectionRange(len, len);
            }
        };

        function setupFilter() {
            var filterSelect = document.getElementById('comment-filter');
            if (!filterSelect || filterSelect.__commentFilterBound) return;
            filterSelect.__commentFilterBound = true;
            filterSelect.addEventListener('change', function() {
                var params = new URLSearchParams(window.location.search);
                params.set('filter', filterSelect.value || 'latest');
                params.delete('page'); // reset về trang 1 khi đổi filter
                var newUrl = window.location.pathname + '?' + params.toString();
                loadComments(newUrl);
            });
        }

        function setupPaginationAjax() {
            var section = document.getElementById('comments-section');
            if (!section || section.__commentPaginationBound) return;
            section.__commentPaginationBound = true;

            section.addEventListener('click', function(e) {
                var link = e.target.closest('.pagination a');
                if (!link) return;
                e.preventDefault();
                loadComments(link.href);
            });
        }

        async function loadComments(url) {
            try {
                var response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!response.ok) return;
                var html = await response.text();
                var tmp = document.createElement('div');
                tmp.innerHTML = html;
                var newSection = tmp.querySelector('#comments-section');
                var oldSection = document.getElementById('comments-section');
                if (!newSection || !oldSection || !oldSection.parentNode) return;
                oldSection.parentNode.replaceChild(newSection, oldSection);

                // Re-init bindings on new DOM
                if (window.initComicComments) {
                    window.initComicComments();
                }
            } catch (e) {
                console.error('Error loading comments section', e);
            }
        }

        window.initComicComments = function() {
            setupCommentForms();
            setupFilter();
            setupPaginationAjax();
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', window.initComicComments);
        } else {
            window.initComicComments();
        }
    })();

    // Nút hiện/ẩn phản hồi - cải tiến
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.reply-toggle');
        if (!btn) return;
        e.preventDefault();

        const commentId = btn.dataset.commentId;
        const replies = document.getElementById(`replies-${commentId}`);
        if (!replies) return;

        const repliesContainer = replies.querySelector('.js-replies-container');
        const isHidden = replies.classList.contains('hidden');
        const replyCount = btn.dataset.replyCount || (repliesContainer ? repliesContainer.children.length : 0);
        const countText = btn.querySelector('.reply-count-text');
        const icon = btn.querySelector('i');

        if (isHidden) {
            // Mở phản hồi
            replies.classList.remove('hidden');
            if (countText) {
                countText.textContent = `Ẩn ${replyCount} phản hồi`;
            }
            if (icon) {
                icon.style.transform = 'rotate(180deg)';
                icon.style.transition = 'transform 0.3s ease';
            }
        } else {
            // Đóng phản hồi
            replies.classList.add('hidden');
            if (countText) {
                countText.textContent = `${replyCount} phản hồi`;
            }
            if (icon) {
                icon.style.transform = 'rotate(0deg)';
                icon.style.transition = 'transform 0.3s ease';
            }
        }
    });
</script>
@endpush