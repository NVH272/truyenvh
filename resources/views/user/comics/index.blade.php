@extends('layouts.app')

@section('title', $comic->title ?? 'Chi tiết truyện')

@section('content')
{{-- Container chính --}}
<div class="bg-[#f0f2f5] min-h-screen pb-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- === PHẦN THÔNG TIN TRUYỆN (TOP) === --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            <div class="p-6">
                <div class="flex flex-col md:flex-row gap-8">
                    {{-- Cột Trái: Ảnh Bìa --}}
                    <div class="w-full md:w-60 flex-shrink-0">
                        <div class="relative group aspect-[2/3] rounded-lg overflow-hidden shadow-md border border-gray-200">
                            <img src="{{ $comic->cover_url ?? 'https://placehold.co/400x600?text=No+Image' }}"
                                alt="{{ $comic->title ?? 'Cover' }}"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            {{-- Badge Hot/New nếu cần --}}
                            <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">HOT</span>
                        </div>
                    </div>

                    {{-- Cột Phải: Thông tin chi tiết --}}
                    <div class="flex-1">
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2 text-center md:text-left">
                            {{ $comic->title ?? 'DANDADAN' }}
                        </h1>

                        {{-- Rating --}}
                        <div class="flex items-center justify-center md:justify-start gap-2 mb-4">

                            @php
                            $avgRating = $comic->rating ?? 0;
                            $ratingCount = $comic->rating_count ?? 0;
                            $currentStars = $userRating ?? 0; // từ controller
                            @endphp

                            @auth
                            @if(auth()->user()->hasVerifiedEmail())
                            <form id="rating-form"
                                action="{{ route('comics.rate', $comic) }}"
                                method="POST"
                                class="flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="rating" id="rating-input" value="{{ $currentStars }}">

                                <div class="flex text-sm">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button"
                                        class="rating-star mx-[1px] {{ $i <= $currentStars ? 'text-yellow-400' : 'text-gray-300' }}"
                                        data-value="{{ $i }}">
                                        <i class="fas fa-star"></i>
                                        </button>
                                        @endfor
                                </div>

                                <span class="text-gray-500 text-sm">
                                    ({{ number_format($avgRating, 1) }}/5 - {{ $ratingCount }} đánh giá)
                                </span>
                            </form>
                            @else
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <div class="flex text-yellow-400 text-sm">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= round($avgRating) ? '' : 'text-gray-300' }}"></i>
                                        @endfor
                                </div>
                                <span>( {{ number_format($avgRating, 1) }}/5 - {{ $ratingCount }} đánh giá )</span>
                                <a href="{{ route('verification.notice') }}" class="text-blue-600 text-xs underline ml-1">
                                    Xác thực email để đánh giá
                                </a>
                            </div>
                            @endif
                            @else
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <div class="flex text-yellow-400 text-sm">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= round($avgRating) ? '' : 'text-gray-300' }}"></i>
                                        @endfor
                                </div>
                                <span>( {{ number_format($avgRating, 1) }}/5 - {{ $ratingCount }} đánh giá )</span>
                                <a href="{{ route('login.form') }}" class="text-blue-600 text-xs underline ml-1">
                                    Đăng nhập để đánh giá
                                </a>
                            </div>
                            @endauth
                        </div>

                        {{-- Grid Thông tin --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3 text-sm mb-6">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 w-24 flex-shrink-0"><i class="fas fa-user mr-1.5"></i> Tác giả:</span>
                                <span class="font-medium text-gray-800">{{ $comic->author ?? 'Đang cập nhật' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 w-24 flex-shrink-0"><i class="fas fa-rss mr-1.5"></i> Tình trạng:</span>
                                <span class="font-medium text-blue-600">{{ $comic->status_text ?? 'Đang tiến hành' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 w-24 flex-shrink-0"><i class="fas fa-tags mr-1.5"></i> Thể loại:</span>
                                <div class="flex flex-wrap gap-1">
                                    <a href="#" class="text-blue-500 hover:underline">Hành động</a>,
                                    <a href="#" class="text-blue-500 hover:underline">Hài hước</a>,
                                    <a href="#" class="text-blue-500 hover:underline">Shounen</a>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 w-24 flex-shrink-0"><i class="fas fa-eye mr-1.5"></i> Lượt xem:</span>
                                <span class="font-medium text-gray-800">12,345,678</span>
                            </div>
                        </div>

                        {{-- Mô tả ngắn --}}
                        <div class="mb-6">
                            <h3 class="font-bold text-gray-800 border-b-2 border-blue-500 inline-block mb-2 pb-1">
                                Sơ lược
                            </h3>
                            <p class="text-gray-600 text-sm leading-relaxed whitespace-pre-line">
                                {{ $comic->description ?? 'Nội dung truyện kể về Momo Ayase - một nữ sinh trung học tin vào ma quỷ nhưng không tin người ngoài hành tinh, và Okarun - một nam sinh tin vào người ngoài hành tinh nhưng không tin ma quỷ...' }}
                            </p>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-wrap gap-3 justify-center md:justify-start">
                            <a href="#" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-lg shadow-lg hover:bg-blue-700 hover:-translate-y-0.5 transition-all flex items-center gap-2">
                                <i class="fas fa-book-open"></i> Đọc từ đầu
                            </a>

                            {{-- Follow Button --}}
                            @auth
                            @if(auth()->user()->hasVerifiedEmail())
                            <form action="{{ route('comics.follow', $comic) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="px-6 py-2.5 {{ $isFollowing ? 'bg-gray-200 text-gray-800' : 'bg-red-500 text-white' }} font-bold rounded-lg shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2 hover:bg-red-600">
                                    <i class="fas fa-heart {{ $isFollowing ? 'text-red-500' : '' }}"></i>
                                    {{ $isFollowing ? 'Đã theo dõi' : 'Theo dõi' }}
                                </button>
                            </form>
                            @else
                            <a href="{{ route('verification.notice') }}"
                                class="px-6 py-2.5 bg-gray-300 text-gray-700 font-bold rounded-lg shadow flex items-center gap-2">
                                <i class="fas fa-heart"></i> Xác thực email để theo dõi
                            </a>
                            @endif
                            @else
                            <a href="{{ route('login.form') }}"
                                class="px-6 py-2.5 bg-gray-300 text-gray-700 font-bold rounded-lg shadow flex items-center gap-2">
                                <i class="fas fa-heart"></i> Đăng nhập để theo dõi
                            </a>
                            @endauth

                            <a href="#" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-lg border border-gray-300 hover:bg-gray-200 transition-all flex items-center gap-2">
                                <i class="fas fa-list"></i> Đọc mới nhất
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- === MAIN CONTENT LAYOUT (2 Columns) === --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT COLUMN: CHAPTERS & COMMENTS (Chiếm 2/3) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- DANH SÁCH CHƯƠNG --}}
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-blue-600 flex items-center gap-2">
                            <i class="fas fa-list-ul"></i> Danh sách chương
                        </h2>
                        <span class="text-xs text-gray-500">Cập nhật lúc: 10 phút trước</span>
                    </div>

                    <div class="max-h-[500px] overflow-y-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50 sticky top-0 z-10 text-xs text-gray-500 uppercase">
                                <tr>
                                    <th class="px-6 py-3 font-medium">Số chương</th>
                                    <th class="px-4 py-3 font-medium text-center">Cập nhật</th>
                                    <th class="px-4 py-3 font-medium text-right">Lượt xem</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @for($i = 200; $i >= 1; $i--)
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td class="px-6 py-3">
                                        <a href="#" class="font-medium text-gray-700 group-hover:text-blue-600 transition-colors">
                                            Chapter {{ $i }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 text-center text-xs">
                                        {{ rand(1, 24) }} giờ trước
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 text-right text-xs">
                                        {{ rand(1000, 50000) }}
                                    </td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- BÌNH LUẬN (GIAO DIỆN FACEBOOK) --}}
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            Bình luận
                            <span class="text-sm font-normal text-gray-500">({{ $comments->total() }})</span>
                        </h2>
                    </div>

                    <div class="p-4">
                        {{-- Form bình luận chính (chỉ cần đăng nhập) --}}
                        @auth
                        <div class="flex gap-2 mb-6">
                            <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=random' }}"
                                alt="{{ auth()->user()->name }}"
                                class="w-8 h-8 md:w-10 md:h-10 rounded-full object-cover flex-shrink-0">

                            <form method="POST" action="{{ route('comments.store', $comic) }}" class="flex-1 w-full">
                                @csrf
                                <div class="relative w-full">
                                    <textarea name="content" required
                                        placeholder="Viết bình luận công khai..."
                                        class="w-full bg-[#f0f2f5] border-none rounded-[20px] px-4 py-2.5 text-[15px] focus:ring-0 focus:outline-none resize-none overflow-hidden"
                                        style="min-height: 40px;"
                                        rows="1"
                                        oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">{{ old('content') }}</textarea>

                                    <button type="submit" class="absolute right-3 bottom-2.5 text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                                @error('content')
                                <p class="text-red-500 text-xs mt-1 ml-3">{{ $message }}</p>
                                @enderror
                            </form>
                        </div>
                        @else
                        <div class="mb-6 p-3 bg-[#f0f2f5] border border-gray-200 rounded-lg flex items-center gap-2 text-sm text-gray-700">
                            <i class="fas fa-info-circle"></i>
                            <a href="{{ route('login.form') }}" class="font-medium text-blue-600 underline hover:text-blue-800">
                                Đăng nhập để bình luận
                            </a>
                        </div>
                        @endauth

                        {{-- Danh sách bình luận --}}
                        @if($comments->count() > 0)
                        <div class="space-y-4">
                            @foreach($comments as $comment)
                            <div class="flex gap-2 group" id="comment-{{ $comment->id }}">
                                {{-- Avatar User --}}
                                <div class="flex-shrink-0 cursor-pointer pt-1">
                                    <img src="{{ $comment->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($comment->user->name).'&background=random' }}"
                                        alt="{{ $comment->user->name }}"
                                        class="w-8 h-8 rounded-full object-cover">
                                </div>

                                <div class="flex-1 max-w-full">
                                    {{-- Bong bóng chat (giống Facebook) --}}
                                    <div class="inline-block bg-[#f0f2f5] rounded-[18px] px-3 py-2">
                                        <a href="#" class="font-bold text-[13px] text-[#050505] hover:underline block leading-tight text-left">
                                            {{ $comment->user->name }}
                                        </a>
                                        <p class="text-[15px] text-[#050505] leading-snug whitespace-pre-wrap break-words mt-0.5 text-left">
                                            {{ $comment->content }}
                                        </p>
                                    </div>

                                    {{-- Action Bar (Thời gian - Thích/Không thích/Trả lời + cụm reaction bên phải) --}}
                                    <div class="flex items-center justify-between mt-0.5 ml-3">
                                        {{-- Bên trái: thời gian + text nút --}}
                                        <div class="flex items-center gap-3 text-xs text-[#65676b]">
                                            <span class="font-normal">
                                                {{ $comment->created_at->diffForHumans() }}
                                            </span>

                                            @auth
                                            @php
                                            $currentUser = auth()->user();
                                            $isLiked = $comment->isLikedBy($currentUser);
                                            $isDisliked = $comment->isDislikedBy($currentUser);
                                            @endphp

                                            {{-- Nút Thích --}}
                                            <form action="{{ route('comments.reaction', ['comment' => $comment->id, 'type' => 'like']) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    data-comment-reaction="like"
                                                    data-comment-id="{{ $comment->id }}"
                                                    data-action="{{ route('comments.reaction', ['comment' => $comment->id, 'type' => 'like']) }}"
                                                    class="js-comment-like-btn text-xs font-bold hover:underline cursor-pointer flex items-center gap-1 {{ $isLiked ? 'text-blue-600' : 'text-[#65676b] hover:text-[#4b4c4f]' }}">
                                                    <i class="fas fa-thumbs-up text-[11px]"></i>
                                                </button>
                                            </form>

                                            {{-- Nút Không thích --}}
                                            <form action="{{ route('comments.reaction', ['comment' => $comment->id, 'type' => 'dislike']) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    data-comment-reaction="dislike"
                                                    data-comment-id="{{ $comment->id }}"
                                                    data-action="{{ route('comments.reaction', ['comment' => $comment->id, 'type' => 'dislike']) }}"
                                                    class="js-comment-dislike-btn text-xs font-bold hover:underline cursor-pointer flex items-center gap-1 {{ $isDisliked ? 'text-red-600' : 'text-[#65676b] hover:text-[#4b4c4f]' }}">
                                                    <i class="fas fa-thumbs-down text-[11px]"></i>
                                                </button>
                                            </form>

                                            {{-- Nút Phản hồi --}}
                                            <button type="button"
                                                onclick="document.getElementById('reply-{{ $comment->id }}').classList.toggle('hidden')"
                                                class="text-xs font-bold text-[#65676b] hover:underline hover:text-[#4b4c4f] cursor-pointer">
                                                Trả lời
                                            </button>
                                            @endauth
                                        </div>

                                        {{-- Bên phải: cụm reaction (icon tròn + số lượng) --}}
                                        <div class="flex items-center gap-1 bg-white rounded-full px-2 py-[2px] shadow-sm js-comment-reaction-pill {{ ($comment->likes_count ?? 0) == 0 && ($comment->dislikes_count ?? 0) == 0 ? 'hidden' : '' }}">
                                            <span class="js-comment-like-wrapper flex items-center gap-1 {{ ($comment->likes_count ?? 0) == 0 ? 'hidden' : '' }}">
                                                <span class="w-4 h-4 rounded-full bg-[#1877f2] flex items-center justify-center text-white text-[9px]">
                                                    <i class="fas fa-thumbs-up"></i>
                                                </span>
                                                <span class="text-[11px] text-[#65676b] font-medium js-comment-like-count">
                                                    {{ $comment->likes_count ?? 0 }}
                                                </span>
                                            </span>
                                            <span class="js-comment-dislike-wrapper flex items-center gap-1 ml-1 {{ ($comment->dislikes_count ?? 0) == 0 ? 'hidden' : '' }}">
                                                <span class="w-4 h-4 rounded-full bg-[#e41e3f] flex items-center justify-center text-white text-[9px]">
                                                    <i class="fas fa-thumbs-down"></i>
                                                </span>
                                                <span class="text-[11px] text-[#65676b] font-medium js-comment-dislike-count">
                                                    {{ $comment->dislikes_count ?? 0 }}
                                                </span>
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Form Reply (Ẩn mặc định) --}}
                                    @auth
                                    <div id="reply-{{ $comment->id }}" class="hidden mt-2 flex gap-2 w-full">
                                        <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=random' }}"
                                            class="w-6 h-6 rounded-full object-cover">

                                        <form method="POST" action="{{ route('comments.store', $comic) }}" class="flex-1">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                            <div class="relative w-full">
                                                <textarea name="content" required
                                                    placeholder="Viết phản hồi..."
                                                    class="w-full bg-[#f0f2f5] border-none rounded-[18px] px-3 py-2 text-[14px] focus:ring-0 focus:outline-none resize-none overflow-hidden"
                                                    style="min-height: 36px;"
                                                    rows="1"
                                                    oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>
                                                <button type="submit" class="absolute right-3 bottom-2 text-blue-600 hover:text-blue-800 text-xs font-bold uppercase">
                                                    Gửi
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    @endauth

                                    {{-- Danh sách Replies --}}
                                    @if($comment->replies->count() > 0)
                                    <div class="mt-2 space-y-3">
                                        @foreach($comment->replies as $reply)
                                        <div class="flex gap-2" id="comment-{{ $reply->id }}">
                                            {{-- Avatar Reply (Nhỏ hơn) --}}
                                            <div class="flex-shrink-0 cursor-pointer pt-1">
                                                <img src="{{ $reply->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($reply->user->name).'&background=random' }}"
                                                    alt="{{ $reply->user->name }}"
                                                    class="w-6 h-6 rounded-full object-cover">
                                            </div>

                                            <div class="flex-1">
                                                {{-- Bong bóng chat reply --}}
                                                <div class="inline-block bg-[#f0f2f5] rounded-[18px] px-3 py-2">
                                                    <a href="#" class="font-bold text-[12px] text-[#050505] hover:underline block leading-tight text-left">
                                                        {{ $reply->user->name }}
                                                    </a>
                                                    <p class="text-[14px] text-[#050505] leading-snug whitespace-pre-wrap break-words mt-0.5 text-left">
                                                        {{ $reply->content }}
                                                    </p>
                                                </div>

                                                {{-- Action Bar Reply (giống Facebook, thu nhỏ) --}}
                                                <div class="flex items-center justify-between mt-0.5 ml-3">
                                                    <div class="flex items-center gap-3 text-[11px] text-[#65676b]">
                                                        <span class="font-normal">
                                                            {{ $reply->created_at->diffForHumans() }}
                                                        </span>

                                                        @auth
                                                        @php
                                                        $currentUser = auth()->user();
                                                        $isReplyLiked = $reply->isLikedBy($currentUser);
                                                        $isReplyDisliked = $reply->isDislikedBy($currentUser);
                                                        @endphp

                                                        <form action="{{ route('comments.reaction', ['comment' => $reply->id, 'type' => 'like']) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                data-comment-reaction="like"
                                                                data-comment-id="{{ $reply->id }}"
                                                                data-action="{{ route('comments.reaction', ['comment' => $reply->id, 'type' => 'like']) }}"
                                                                class="js-comment-like-btn text-[11px] font-bold hover:underline flex items-center gap-1 {{ $isReplyLiked ? 'text-blue-600' : 'text-[#65676b] hover:text-[#4b4c4f]' }}">
                                                                <i class="fas fa-thumbs-up text-[10px]"></i>
                                                            </button>
                                                        </form>

                                                        <form action="{{ route('comments.reaction', ['comment' => $reply->id, 'type' => 'dislike']) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                data-comment-reaction="dislike"
                                                                data-comment-id="{{ $reply->id }}"
                                                                data-action="{{ route('comments.reaction', ['comment' => $reply->id, 'type' => 'dislike']) }}"
                                                                class="js-comment-dislike-btn text-[11px] font-bold hover:underline flex items-center gap-1 {{ $isReplyDisliked ? 'text-red-600' : 'text-[#65676b] hover:text-[#4b4c4f]' }}">
                                                                <i class="fas fa-thumbs-down text-[10px]"></i>
                                                            </button>
                                                        </form>

                                                        <button onclick="document.getElementById('reply-{{ $reply->id }}').classList.toggle('hidden')"
                                                            class="text-[11px] font-bold text-[#65676b] hover:underline hover:text-[#4b4c4f]">
                                                            Trả lời
                                                        </button>
                                                        @endauth
                                                    </div>

                                                    <div class="flex items-center gap-1 bg-white rounded-full px-2 py-[1px] shadow-sm js-comment-reaction-pill {{ ($reply->likes_count ?? 0) == 0 && ($reply->dislikes_count ?? 0) == 0 ? 'hidden' : '' }}">
                                                        <span class="js-comment-like-wrapper flex items-center gap-1 {{ ($reply->likes_count ?? 0) == 0 ? 'hidden' : '' }}">
                                                            <span class="w-3.5 h-3.5 rounded-full bg-[#1877f2] flex items-center justify-center text-white text-[8px]">
                                                                <i class="fas fa-thumbs-up"></i>
                                                            </span>
                                                            <span class="text-[10px] text-[#65676b] font-medium js-comment-like-count">
                                                                {{ $reply->likes_count ?? 0 }}
                                                            </span>
                                                        </span>
                                                        <span class="js-comment-dislike-wrapper flex items-center gap-1 ml-1 {{ ($reply->dislikes_count ?? 0) == 0 ? 'hidden' : '' }}">
                                                            <span class="w-3.5 h-3.5 rounded-full bg-[#e41e3f] flex items-center justify-center text-white text-[8px]">
                                                                <i class="fas fa-thumbs-down"></i>
                                                            </span>
                                                            <span class="text-[10px] text-[#65676b] font-medium js-comment-dislike-count">
                                                                {{ $reply->dislikes_count ?? 0 }}
                                                            </span>
                                                        </span>
                                                    </div>
                                                </div>

                                                {{-- Form Reply Cấp 2 --}}
                                                @auth
                                                <div id="reply-{{ $reply->id }}" class="hidden mt-2 flex gap-2 w-full">
                                                    <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=random' }}"
                                                        class="w-5 h-5 rounded-full object-cover">

                                                    <form method="POST" action="{{ route('comments.store', $comic) }}" class="flex-1">
                                                        @csrf
                                                        <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                                                        <div class="relative w-full">
                                                            <textarea name="content" required
                                                                placeholder="Viết phản hồi..."
                                                                class="w-full bg-[#f0f2f5] border-none rounded-[18px] px-3 py-1.5 text-[13px] focus:ring-0 focus:outline-none resize-none overflow-hidden"
                                                                style="min-height: 32px;"
                                                                rows="1"
                                                                oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>
                                                            <button type="submit" class="absolute right-3 bottom-1.5 text-blue-600 hover:text-blue-800 text-[10px] font-bold uppercase">
                                                                Gửi
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                                @endauth

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
                        <div class="mt-6 border-t border-gray-100 pt-4">
                            {{ $comments->links() }}
                        </div>
                        @else
                        <div class="text-center py-10">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                                <i class="fas fa-comment-dots text-2xl"></i>
                            </div>
                            <p class="text-gray-500 font-medium">Chưa có bình luận nào</p>
                            <p class="text-gray-400 text-sm">Hãy là người đầu tiên chia sẻ suy nghĩ của bạn!</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: SIDEBAR (Chiếm 1/3) --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- TRUYỆN LIÊN QUAN --}}
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100">
                        <h3 class="font-bold text-gray-800 text-base border-l-4 border-blue-500 pl-3 uppercase">
                            Truyện liên quan
                        </h3>
                    </div>
                    <div class="p-4 space-y-4">
                        @foreach(range(1, 5) as $item)
                        <div class="flex gap-3 group cursor-pointer">
                            <div class="w-16 h-24 flex-shrink-0 rounded overflow-hidden relative">
                                <img src="https://placehold.co/100x150?text=Manga" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" alt="Related">
                            </div>
                            <div class="flex-1 min-w-0 py-1">
                                <h4 class="text-sm font-bold text-gray-800 group-hover:text-blue-600 truncate transition-colors">
                                    Tên truyện liên quan số {{ $item }}
                                </h4>
                                <div class="flex items-center text-xs text-yellow-500 my-1">
                                    <i class="fas fa-star"></i>
                                    <span class="text-gray-400 ml-1">4.8</span>
                                </div>
                                <div class="flex flex-wrap gap-1">
                                    <span class="text-[10px] px-1.5 py-0.5 bg-gray-100 text-gray-500 rounded">Hành động</span>
                                    <span class="text-[10px] px-1.5 py-0.5 bg-gray-100 text-gray-500 rounded">Fantasy</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- TOP THEO DÕI --}}
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800 text-base border-l-4 border-red-500 pl-3 uppercase">
                            Top Theo Dõi
                        </h3>
                        <div class="flex gap-1">
                            <button class="text-[10px] px-2 py-0.5 bg-red-500 text-white rounded-full">Ngày</button>
                            <button class="text-[10px] px-2 py-0.5 bg-gray-200 text-gray-500 rounded-full hover:bg-gray-300">Tuần</button>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach(range(1, 5) as $index => $item)
                        <div class="flex items-center gap-3 p-3 hover:bg-gray-50 transition-colors cursor-pointer group">
                            <span class="w-6 h-6 flex-shrink-0 flex items-center justify-center rounded-full {{ $index < 3 ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-600' }} text-xs font-bold">
                                {{ $index + 1 }}
                            </span>
                            <div class="w-12 h-16 flex-shrink-0 rounded overflow-hidden">
                                <img src="https://placehold.co/80x120?text=Top" class="w-full h-full object-cover" alt="Top">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-800 group-hover:text-red-500 truncate transition-colors">
                                    Siêu Phẩm Top {{ $index + 1 }}
                                </h4>
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-eye text-gray-400"></i> 1.2M
                                    <span class="mx-1">•</span>
                                    Chapter 200
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

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
</style>

<script>
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

        // ==== Comment like / dislike AJAX ====
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : null;

        function updateCommentUI(commentId, data) {
            const root = document.getElementById('comment-' + commentId);
            if (!root) return;

            const likeBtn = root.querySelector('.js-comment-like-btn');
            const dislikeBtn = root.querySelector('.js-comment-dislike-btn');
            const pill = root.querySelector('.js-comment-reaction-pill');
            const likeWrapper = root.querySelector('.js-comment-like-wrapper');
            const dislikeWrapper = root.querySelector('.js-comment-dislike-wrapper');
            const likeCountEl = root.querySelector('.js-comment-like-count');
            const dislikeCountEl = root.querySelector('.js-comment-dislike-count');

            const likes = data.likes_count ?? 0;
            const dislikes = data.dislikes_count ?? 0;
            const userReaction = data.user_reaction; // 'like' | 'dislike' | null

            // Cập nhật class active cho nút like/dislike
            if (likeBtn) {
                likeBtn.classList.remove('text-blue-600');
                likeBtn.classList.remove('text-[#65676b]');
                likeBtn.classList.remove('hover:text-[#4b4c4f]');
                if (userReaction === 'like') {
                    likeBtn.classList.add('text-blue-600');
                } else {
                    likeBtn.classList.add('text-[#65676b]', 'hover:text-[#4b4c4f]');
                }
            }

            if (dislikeBtn) {
                dislikeBtn.classList.remove('text-red-600');
                dislikeBtn.classList.remove('text-[#65676b]');
                dislikeBtn.classList.remove('hover:text-[#4b4c4f]');
                if (userReaction === 'dislike') {
                    dislikeBtn.classList.add('text-red-600');
                } else {
                    dislikeBtn.classList.add('text-[#65676b]', 'hover:text-[#4b4c4f]');
                }
            }

            // Cập nhật cụm reaction pill
            if (pill) {
                if (likes === 0 && dislikes === 0) {
                    pill.classList.add('hidden');
                } else {
                    pill.classList.remove('hidden');
                }
            }

            if (likeWrapper) {
                if (likes === 0) likeWrapper.classList.add('hidden');
                else likeWrapper.classList.remove('hidden');
            }
            if (dislikeWrapper) {
                if (dislikes === 0) dislikeWrapper.classList.add('hidden');
                else dislikeWrapper.classList.remove('hidden');
            }

            if (likeCountEl) likeCountEl.textContent = likes;
            if (dislikeCountEl) dislikeCountEl.textContent = dislikes;
        }

        if (csrfToken) {
            document.querySelectorAll('[data-comment-reaction]').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();

                    const type = btn.getAttribute('data-comment-reaction'); // like | dislike
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
    });
</script>

@endsection