{{-- 2. BÌNH LUẬN --}}
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-lg font-bold text-blue-600 flex items-center gap-2">
            <i class="fas fa-comments"></i>
            <span>Bình luận</span>
            <span class="text-sm font-normal text-gray-500">({{ $comments->total() }})</span>
        </h2>

        {{-- Filter Dropdown --}}
        <div class="relative">
            <select id="comment-filter" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm bg-white cursor-pointer hover:border-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                <option value="latest">Mới nhất</option>
                <option value="oldest">Cũ nhất</option>
                <option value="popular">Nổi bật</option>
            </select>
        </div>
    </div>

    <div class="p-6">
        @auth
        {{-- Input Comment --}}
        <div class="flex gap-4 mb-8">
            <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=random' }}"
                class="w-10 h-10 rounded-full object-cover"
                alt="{{ auth()->user()->name }}">

            <form method="POST" action="{{ route('comments.store', $comic) }}" class="flex-1">
                @csrf
                <textarea name="content"
                    rows="3"
                    class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all resize-none"
                    placeholder="Để lại bình luận của bạn...">{{ old('content') }}</textarea>
                @error('content')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
                <div class="flex justify-end mt-2">
                    <button type="submit"
                        class="px-4 py-1.5 bg-blue-600 text-white text-sm font-semibold rounded hover:bg-blue-700 transition-colors">
                        Gửi bình luận
                    </button>
                </div>
            </form>
        </div>

        {{-- Comment List --}}
        @if($comments->count() > 0)
        <div class="space-y-6" id="comments-container">
            @foreach($comments as $comment)
            <div class="flex gap-3" id="comment-{{ $comment->id }}" data-timestamp="{{ $comment->created_at->timestamp }}" data-likes="{{ $comment->likes_count ?? 0 }}">
                {{-- Avatar --}}
                <img src="{{ $comment->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($comment->user->name).'&background=random' }}"
                    class="w-10 h-10 rounded-full object-cover flex-shrink-0"
                    alt="{{ $comment->user->name }}">

                <div class="flex-1 min-w-0">
                    {{-- Bubble comment --}}
                    <div class="bg-gray-100 rounded-2xl px-4 py-2.5 inline-block max-w-md">
                        <div class="font-semibold text-gray-800 text-sm mb-0.5">{{ $comment->user->name }}</div>
                        <p class="text-gray-700 text-sm leading-relaxed whitespace-normal break-words">
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
                            onclick="document.getElementById('reply-{{ $comment->id }}').classList.toggle('hidden')"
                            class="text-xs font-medium text-gray-500 hover:text-blue-600 transition-colors flex items-center gap-1">
                            <i class="far fa-comment-dots"></i>Trả lời
                        </button>

                        {{-- Thời gian --}}
                        <span class="text-xs text-gray-500 ml-auto">
                            {{ $comment->created_at->diffForHumans() }}
                        </span>
                        @endauth
                    </div>

                    {{-- Form trả lời cấp 1 --}}
                    @auth
                    <div id="reply-{{ $comment->id }}" class="hidden mt-3">
                        <div class="flex gap-3">
                            <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=random' }}"
                                class="w-8 h-8 rounded-full object-cover"
                                alt="{{ auth()->user()->name }}">
                            <form method="POST" action="{{ route('comments.store', $comic) }}" class="flex-1">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                <textarea name="content"
                                    rows="2"
                                    class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all resize-none"
                                    placeholder="Viết phản hồi..."></textarea>
                                <div class="flex justify-end mt-2 gap-2">
                                    <button type="button"
                                        onclick="document.getElementById('reply-{{ $comment->id }}').classList.add('hidden')"
                                        class="px-3 py-1 text-xs text-gray-500 hover:text-gray-700">
                                        Hủy
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-1 bg-blue-600 text-white text-xs font-semibold rounded hover:bg-blue-700 transition-colors">
                                        Gửi phản hồi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endauth

                    {{-- Replies --}}
                    @if($comment->replies->count() > 0)
                    <div class="mt-4 space-y-4">
                        @foreach($comment->replies as $reply)
                        <div class="flex gap-3" id="comment-{{ $reply->id }}">
                            <img src="{{ $reply->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($reply->user->name).'&background=random' }}"
                                class="w-10 h-10 rounded-full object-cover flex-shrink-0"
                                alt="{{ $reply->user->name }}">
                            <div class="flex-1 min-w-0">
                                <div class="bg-gray-100 rounded-2xl px-4 py-2.5 inline-block max-w-md">
                                    <div class="font-semibold text-gray-800 text-sm mb-0.5">{{ $reply->user->name }}</div>
                                    <p class="text-gray-700 text-sm leading-relaxed whitespace-normal break-words">
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
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $comments->links() }}
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