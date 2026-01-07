@extends('layouts.app')

@section('title', $comic->title ?? 'Chi tiết truyện')

@section('content')
{{-- Container chính --}}
<div class="bg-[#f0f2f5] min-h-screen pb-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- === PHẦN 1: THÔNG TIN TRUYỆN (TOP - FULL WIDTH) === --}}
        @include('user.comics.partials.comic_info', ['comic' => $comic])

        {{-- === PHẦN 2: MAIN CONTENT LAYOUT (Tỉ lệ 5:3) === --}}
        {{-- Sử dụng grid-cols-8 để chia chính xác 5 phần và 3 phần --}}
        <div class="grid grid-cols-1 lg:grid-cols-8 gap-6">

            {{-- CỘT TRÁI (LEFT): Chiếm 5/8 --}}
            <div class="lg:col-span-5 space-y-6">

                {{-- 1. DANH SÁCH CHƯƠNG --}}
                @include('user.comics.partials.chapterlist', ['comic' => $comic])

                {{-- 2. BÌNH LUẬN --}}
                @include('user.comics.partials.comments', ['comic' => $comic])

            </div>

            {{-- CỘT PHẢI (RIGHT): Chiếm 3/8 --}}
            <div class="lg:col-span-3 space-y-6">

                {{-- 1. TRUYỆN LIÊN QUAN --}}
                @include('user.comics.partials.relate')


                {{-- 2. TOP LƯỢT XEM --}}
                @include('user.comics.partials.topview')

            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Scrollbar cho danh sách chương */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Facebook-style comment bubble */
    .js-comment-like-btn,
    .js-comment-dislike-btn {
        display: inline;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .js-comment-like-btn:hover,
    .js-comment-dislike-btn:hover {
        opacity: 0.7;
    }

    .comment-bubble,
    .reply-bubble {
        display: inline-block;
    }
</style>

<script>
    // Hàm scroll và focus form trả lời cấp 1 khi click trả lời từ reply
    function scrollToReplyForm(commentId, replyAuthorName) {
        const formContainer = document.getElementById('reply-' + commentId);
        if (formContainer) {
            formContainer.classList.remove('hidden');
            formContainer.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            const textarea = formContainer.querySelector('textarea');
            if (textarea) {
                textarea.focus();
                // Thêm mention tên tác giả (optional)
                textarea.value = '@' + replyAuthorName + ' ';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.rating-star');
        const input = document.getElementById('rating-input');
        const form = document.getElementById('rating-form');

        if (stars && input && form) {
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const value = this.dataset.value;
                    input.value = value;
                    form.submit(); // click lại star để đổi rating bất cứ lúc nào
                });
            });
        }

        // === Comment like / dislike AJAX (giữ nguyên chức năng) ===
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : null;

        function updateCommentUI(commentId, data) {
            const root = document.getElementById('comment-' + commentId);
            if (!root) return;

            const likeBtn = root.querySelector('.js-comment-like-btn');

            const likes = data.likes_count ?? 0;
            const userReaction = data.user_reaction; // 'like' | null

            if (likeBtn) {
                likeBtn.classList.remove('text-blue-600');
                likeBtn.classList.add('text-gray-500');
                if (userReaction === 'like') {
                    likeBtn.classList.remove('text-gray-500');
                    likeBtn.classList.add('text-blue-600');
                }
            }

            const likeCountEl = root.querySelector('.js-comment-like-count');

            if (likeCountEl) {
                if (likes > 0) {
                    likeCountEl.textContent = likes;
                    likeCountEl.classList.remove('hidden');
                } else {
                    likeCountEl.textContent = '';
                    likeCountEl.classList.add('hidden');
                }
            }
        }

        if (csrfToken) {
            document.querySelectorAll('[data-comment-reaction]').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();

                    const commentId = btn.getAttribute('data-comment-id');
                    const action = btn.getAttribute('data-action');

                    if (!action || !commentId) return;

                    fetch(action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        })
                        .then(function(res) {
                            if (!res.ok) throw new Error('Network response was not ok');
                            return res.json();
                        })
                        .then(function(data) {
                            updateCommentUI(commentId, data);
                        })
                        .catch(function(err) {
                            console.error('Comment reaction error:', err);
                        });
                });
            });
        }

        // === Comment Filtering ===
        const filterSelect = document.getElementById('comment-filter');
        const commentsContainer = document.getElementById('comments-container');

        if (filterSelect && commentsContainer) {
            filterSelect.addEventListener('change', function() {
                sortComments(this.value);
            });
        }

        function sortComments(filterType) {
            if (!commentsContainer) return;

            // Lấy tất cả comment elements
            const commentElements = Array.from(commentsContainer.querySelectorAll(':scope > div[id^="comment-"]'));

            // Sắp xếp comments dựa trên filter type
            commentElements.sort(function(a, b) {
                if (filterType === 'latest') {
                    // Mới nhất: so sánh data-timestamp (newer first)
                    const timestampA = parseInt(a.dataset.timestamp) || 0;
                    const timestampB = parseInt(b.dataset.timestamp) || 0;
                    return timestampB - timestampA;
                } else if (filterType === 'oldest') {
                    // Cũ nhất: so sánh data-timestamp (older first)
                    const timestampA = parseInt(a.dataset.timestamp) || 0;
                    const timestampB = parseInt(b.dataset.timestamp) || 0;
                    return timestampA - timestampB;
                } else if (filterType === 'popular') {
                    // Nổi bật: so sánh likes (more likes first)
                    const likesA = parseInt(a.dataset.likes) || 0;
                    const likesB = parseInt(b.dataset.likes) || 0;
                    return likesB - likesA;
                }
            });

            // Xóa tất cả comments từ container
            commentsContainer.innerHTML = '';

            // Thêm lại comments theo thứ tự mới
            commentElements.forEach(function(element) {
                commentsContainer.appendChild(element);
            });
        }
    });

    // === Comment menu toggle ===
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-comment-menu-btn]');
        const menus = document.querySelectorAll('[data-comment-menu]');

        // Click nút ...
        if (btn) {
            const wrap = btn.parentElement; // div.relative
            const menu = wrap.querySelector('[data-comment-menu]');

            // đóng tất cả menu khác
            menus.forEach(m => {
                if (m !== menu) m.classList.add('hidden');
            });

            // toggle menu hiện tại
            menu.classList.toggle('hidden');
            return;
        }

        // Click bên trong menu -> không đóng (để bấm item)
        if (e.target.closest('[data-comment-menu]')) return;

        // Click ngoài -> đóng hết
        menus.forEach(m => m.classList.add('hidden'));
    });
</script>


@endsection