@push('styles')
<style>
    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        10%,
        30%,
        50%,
        70%,
        90% {
            transform: translateX(-5px);
        }

        20%,
        40%,
        60%,
        80% {
            transform: translateX(5px);
        }
    }

    .shake-animation {
        animation: shake 0.5s ease-in-out;
    }

    .js-comment-textarea.border-red-500 {
        border: 2px solid #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
        background-color: #fef2f2 !important;
    }

    /* Đảm bảo form có border khi shake */
    .js-comment-form.shake-animation {
        position: relative;
    }

    .js-comment-form.shake-animation .js-comment-textarea {
        border-color: #ef4444 !important;
    }

    /* Style cho nút đóng thông báo */
    .js-close-error-btn {
        padding: 2px 4px;
        border-radius: 4px;
        cursor: pointer;
    }

    .js-close-error-btn:hover {
        background-color: rgba(239, 68, 68, 0.1);
    }

    .js-close-error-btn:focus {
        outline: 2px solid rgba(239, 68, 68, 0.3);
        outline-offset: 2px;
    }
</style>
@endpush

@push('scripts')
<script>
    (function() {
        if (window.__comicCommentsInitialized) return;
        window.__comicCommentsInitialized = true;

        const theme = window.commentTheme || {
            bubbleBg: 'bg-gray-100',
            nameText: 'text-gray-800',
            contentText: 'text-gray-700'
        };

        function getCsrfToken() {
            var meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        }

        function appendCommentToDom(comment, form) {
            if (!comment) return;
            var isReply = !!comment.parent_id;

            // --- XỬ LÝ COMMENT CHA ---
            if (!isReply) {
                var container = document.getElementById('comments-container');
                if (!container) return;

                var wrapper = document.createElement('div');
                wrapper.className = 'flex gap-3 js-comment-item';
                wrapper.id = 'comment-' + comment.id;
                wrapper.setAttribute('data-comment-id', comment.id);
                wrapper.setAttribute('data-timestamp', comment.timestamp || '');
                wrapper.setAttribute('data-likes', comment.likes_count || 0);

                var likeUrl = '/comments/' + comment.id + '/reaction/like';
                var storeUrl = form.action;

                // SỬA: Thay class cứng bằng biến theme
                wrapper.innerHTML = '' +
                    '<img src="' + (comment.user.avatar_url || '') + '" ' +
                    'class="w-10 h-10 rounded-full object-cover flex-shrink-0" ' +
                    'alt="' + (comment.user.name || '') + '">' +
                    '<div class="flex-1 min-w-0">' +
                    // Bubble Background
                    '<div class="' + theme.bubbleBg + ' rounded-2xl px-4 py-2.5 inline-block max-w-md js-bubble">' +
                    // Name Color
                    '<div class="font-semibold ' + theme.nameText + ' text-sm mb-0.5">' + (comment.user.name || '') + '</div>' +
                    // Content Color
                    '<p class="' + theme.contentText + ' text-sm leading-relaxed whitespace-normal break-words js-comment-content"></p>' +
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
                    // Input hidden chapter_id (nếu có trong form gốc thì copy sang)
                    (form.querySelector('input[name="chapter_id"]') ? '<input type="hidden" name="chapter_id" value="' + form.querySelector('input[name="chapter_id"]').value + '">' : '') +
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

                if (container.firstChild) {
                    container.insertBefore(wrapper, container.firstChild);
                } else {
                    container.appendChild(wrapper);
                }

                return;
            }

            // --- XỬ LÝ COMMENT CON (REPLY) ---
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
            replyWrapper.setAttribute('data-is-reply', '1');
            replyWrapper.setAttribute('data-parent-id', comment.parent_id || '');

            var likeUrlReply = '/comments/' + comment.id + '/reaction/like';

            // SỬA: Thay class cứng bằng biến theme
            replyWrapper.innerHTML = '' +
                '<img src="' + (comment.user.avatar_url || '') + '" ' +
                'class="w-10 h-10 rounded-full object-cover flex-shrink-0" ' +
                'alt="' + (comment.user.name || '') + '">' +
                '<div class="flex-1 min-w-0">' +
                // Bubble Background
                '<div class="' + theme.bubbleBg + ' rounded-2xl px-4 py-2.5 inline-block max-w-md js-bubble">' +
                // Name Color
                '<div class="font-semibold ' + theme.nameText + ' text-sm mb-0.5">' + (comment.user.name || '') + '</div>' +
                // Content Color
                '<p class="' + theme.contentText + ' text-sm leading-relaxed whitespace-normal break-words js-comment-content"></p>' +
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

            setupCommentForms();
        }

        async function submitCommentForm(form) {
            if (!form) return false;

            var submitBtn = form.querySelector('button[type="submit"]');
            var textarea = form.querySelector('.js-comment-textarea');
            var content = textarea ? textarea.value.trim() : '';

            // Kiểm tra từ cấm trước khi submit
            var hits = findBannedHits(content);

            // Debug log
            if (hits.length > 0) {
                console.log('Phát hiện từ cấm:', hits);
            }

            if (hits.length) {
                // Hiển thị thông báo vi phạm rõ ràng
                showFormError(form, 'Bình luận chứa từ ngữ bị cấm: ' + hits.join(', '));

                // Thêm hiệu ứng shake cho form
                if (form) {
                    form.classList.add('shake-animation');
                    setTimeout(function() {
                        form.classList.remove('shake-animation');
                    }, 500);
                }

                // Focus vào textarea để user có thể sửa
                if (textarea) {
                    textarea.focus();
                    // Highlight textarea với border đỏ
                    textarea.classList.add('border-red-500');
                    setTimeout(function() {
                        textarea.classList.remove('border-red-500');
                    }, 2000);
                }

                return false; // Chặn hoàn toàn việc submit
            }

            // Nếu không có từ cấm, tiếp tục submit
            if (submitBtn) {
                submitBtn.disabled = true;
            }
            // Xóa error message nếu có (tìm cả trong và ngoài form)
            var errorEl = form.querySelector('.js-comment-error');
            if (!errorEl) {
                const parentContainer = form.closest('.flex.gap-3');
                if (parentContainer) {
                    errorEl = parentContainer.querySelector('.js-comment-error');
                }
            }
            if (errorEl) {
                hideFormError(errorEl);
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
                    let err = null;
                    try {
                        err = await response.json();
                    } catch (e) {}

                    const msg =
                        (err && err.errors && err.errors.content && err.errors.content[0]) ||
                        (err && err.message) ||
                        'Không thể gửi bình luận.';

                    showFormError(form, msg);

                    // Nếu là lỗi về từ cấm, thêm hiệu ứng tương tự
                    if (msg.includes('từ ngữ bị cấm') || msg.includes('từ cấm')) {
                        if (form) {
                            form.classList.add('shake-animation');
                            setTimeout(function() {
                                form.classList.remove('shake-animation');
                            }, 500);
                        }
                        if (textarea) {
                            textarea.focus();
                            textarea.classList.add('border-red-500');
                            setTimeout(function() {
                                textarea.classList.remove('border-red-500');
                            }, 2000);
                        }
                    }

                    return false;
                }

                var data = await response.json();
                if (data && data.status === 'success') {
                    // Xóa error message nếu có (tìm cả trong và ngoài form)
                    var errorEl = form.querySelector('.js-comment-error');
                    if (!errorEl) {
                        const parentContainer = form.closest('.flex.gap-3');
                        if (parentContainer) {
                            errorEl = parentContainer.querySelector('.js-comment-error');
                        }
                    }
                    if (errorEl) {
                        hideFormError(errorEl);
                    }

                    appendCommentToDom(data.comment, form);
                    if (textarea) {
                        textarea.value = '';
                        textarea.style.height = 'auto';
                    }

                    // Nếu là reply form thì ẩn lại block reply
                    var parentIdInput = form.querySelector('input[name="parent_id"]');
                    if (parentIdInput) {
                        var replyBlock = document.getElementById('reply-' + parentIdInput.value);
                        if (replyBlock) {
                            replyBlock.classList.add('hidden');
                        }
                    }

                    return true;
                }
            } catch (e) {
                console.error('Error submitting comment form', e);
                showFormError(form, 'Đã xảy ra lỗi khi gửi bình luận. Vui lòng thử lại.');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
            }

            return true;
        }

        function setupCommentForms() {
            var forms = document.querySelectorAll('.js-comment-form');
            forms.forEach(function(form) {
                if (form.__commentFormBound) return;
                form.__commentFormBound = true;

                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var result = submitCommentForm(form);
                    // Nếu có từ cấm, không submit
                    if (result === false) {
                        return false;
                    }
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
                            var result = submitCommentForm(form);
                            // Nếu có từ cấm, không submit
                            if (result === false) {
                                return false;
                            }
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

    function getBannedWords() {
        const el = document.getElementById('banned-words-data');
        if (!el) return [];
        try {
            return JSON.parse(el.dataset.banned || '[]');
        } catch (e) {
            return [];
        }
    }

    function normalizeText(s) {
        return (s || '').toLowerCase().trim().replace(/\s+/g, ' ');
    }

    function findBannedHits(text) {
        const banned = getBannedWords();
        const t = normalizeText(text);

        if (!t) return [];

        // Debug: kiểm tra banned words có được load không
        if (banned.length === 0) {
            console.warn('Không có từ cấm nào được load');
        }

        const hits = [];
        for (const w0 of banned) {
            const w = normalizeText(w0);
            if (!w) continue;

            // match theo "từ" để đỡ match nhầm
            const re = new RegExp(`(^|[^a-z0-9])${w.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}([^a-z0-9]|$)`, 'u');
            if (re.test(t)) hits.push(w0);
        }
        return [...new Set(hits)];
    }

    function showFormError(form, msg) {
        if (!form) {
            console.error('showFormError: form không tồn tại');
            return;
        }

        // Tìm error element: trong form trước (cho reply form)
        let errorEl = form.querySelector('.js-comment-error');

        // Nếu không tìm thấy trong form, tìm trong parent container (cho form chính)
        if (!errorEl) {
            // Tìm trong parent container - form chính có error element trong cùng container
            const parentContainer = form.closest('.flex.gap-3');
            if (parentContainer) {
                errorEl = parentContainer.querySelector('.js-comment-error');
            }
        }

        // Nếu vẫn không có, tìm trong parent của form
        if (!errorEl && form.parentElement) {
            errorEl = form.parentElement.querySelector('.js-comment-error');
        }

        if (!errorEl) {
            console.error('Không tìm thấy error element cho form');
            console.log('Form:', form);
            console.log('Form parent:', form.parentElement);
            console.log('Parent container:', form.closest('.flex.gap-3'));
            // Thử tìm tất cả error elements để debug
            const allErrors = document.querySelectorAll('.js-comment-error');
            console.log('Tất cả error elements:', allErrors);
            return;
        }

        console.log('Tìm thấy error element:', errorEl);

        // Tạo HTML với icon cảnh báo và nút đóng
        errorEl.innerHTML = '<i class="fas fa-exclamation-triangle text-red-500 flex-shrink-0"></i><span class="flex-1">' + msg + '</span><button type="button" class="js-close-error-btn flex-shrink-0 ml-2 text-red-400 hover:text-red-600 transition-colors focus:outline-none" aria-label="Đóng thông báo"><i class="fas fa-times text-xs"></i></button>';
        errorEl.classList.remove('hidden');

        // Thêm animation fade-in
        errorEl.style.opacity = '0';
        errorEl.style.transform = 'translateY(-5px)';
        setTimeout(function() {
            errorEl.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            errorEl.style.opacity = '1';
            errorEl.style.transform = 'translateY(0)';
        }, 10);

        // Thêm event listener cho nút đóng
        const closeBtn = errorEl.querySelector('.js-close-error-btn');
        if (closeBtn) {
            // Xóa các event listener cũ nếu có
            const newCloseBtn = closeBtn.cloneNode(true);
            closeBtn.parentNode.replaceChild(newCloseBtn, closeBtn);

            newCloseBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                hideFormError(errorEl);
            });
        }

        // Scroll đến error nếu cần
        errorEl.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest'
        });
    }

    function hideFormError(errorEl) {
        if (!errorEl) return;

        // Animation fade-out
        errorEl.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        errorEl.style.opacity = '0';
        errorEl.style.transform = 'translateY(-5px)';

        setTimeout(function() {
            errorEl.classList.add('hidden');
            errorEl.innerHTML = '';
            errorEl.textContent = '';
            // Reset style
            errorEl.style.opacity = '';
            errorEl.style.transform = '';
        }, 300);
    }

    // ============================================================
    // BỔ SUNG: XỬ LÝ SỰ KIỆN CLICK CHO DROPDOWN VÀ LIKE
    // ============================================================

    document.addEventListener('click', function(e) {

        // --- 1. XỬ LÝ DROPDOWN MENU (...) ---
        const menuBtn = e.target.closest('[data-comment-menu-btn]');
        if (menuBtn) {
            e.preventDefault();
            e.stopPropagation();

            const wrapper = menuBtn.closest('.relative');
            const menu = wrapper.querySelector('[data-comment-menu]');

            if (menu) {
                // Đóng tất cả menu khác đang mở để tránh rối mắt
                document.querySelectorAll('[data-comment-menu]').forEach(m => {
                    if (m !== menu) m.classList.add('hidden');
                });
                // Toggle menu hiện tại
                menu.classList.toggle('hidden');
            }
            return;
        }

        // Click ra ngoài thì đóng tất cả menu
        if (!e.target.closest('[data-comment-menu]')) {
            document.querySelectorAll('[data-comment-menu]').forEach(menu => {
                menu.classList.add('hidden');
            });
        }

        // --- 2. XỬ LÝ LIKE COMMENT ---
        const likeBtn = e.target.closest('[data-comment-reaction="like"]');
        if (likeBtn) {
            e.preventDefault();
            // Chặn click liên tục
            if (likeBtn.dataset.processing === 'true') return;
            likeBtn.dataset.processing = 'true';

            const actionUrl = likeBtn.dataset.action;
            const icon = likeBtn.querySelector('i');
            // Tìm span đếm số like trong cùng container
            const container = likeBtn.closest('.flex.items-center');
            const countSpan = container ? container.querySelector('.js-comment-like-count') : null;
            const isLiked = likeBtn.classList.contains('text-blue-600');

            // --- Hiệu ứng UI tức thì (Optimistic UI) ---
            if (isLiked) {
                // Đang like -> Bỏ like
                likeBtn.classList.remove('text-blue-600');
                likeBtn.classList.add('text-gray-500', 'hover:text-blue-600');
                if (countSpan) {
                    let count = parseInt(countSpan.textContent.trim()) || 0;
                    count = Math.max(0, count - 1);
                    countSpan.textContent = count;
                    if (count === 0) countSpan.classList.add('hidden');
                }
            } else {
                // Chưa like -> Like
                likeBtn.classList.remove('text-gray-500', 'hover:text-blue-600');
                likeBtn.classList.add('text-blue-600');
                // Animation nảy icon
                if (icon) {
                    icon.style.transform = 'scale(1.2)';
                    setTimeout(() => icon.style.transform = 'scale(1)', 200);
                }
                if (countSpan) {
                    let count = parseInt(countSpan.textContent.trim()) || 0;
                    count++;
                    countSpan.textContent = count;
                    countSpan.classList.remove('hidden');
                }
            }

            // --- Gửi AJAX ---
            fetch(actionUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(res => {
                    if (!res.ok) throw new Error('Network error');
                    return res.json();
                })
                .then(data => {
                    // Cập nhật lại số liệu chính xác từ server
                    if (data && typeof data.likes_count !== 'undefined' && countSpan) {
                        countSpan.textContent = data.likes_count;
                        if (data.likes_count > 0) countSpan.classList.remove('hidden');
                        else countSpan.classList.add('hidden');
                    }
                })
                .catch(err => {
                    console.error('Lỗi like:', err);
                    // Có thể hoàn tác UI tại đây nếu muốn
                })
                .finally(() => delete likeBtn.dataset.processing);
        }
    });
</script>
@endpush