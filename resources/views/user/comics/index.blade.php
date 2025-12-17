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
                                            Chapter
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 text-center text-xs">
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 text-right text-xs">
                                    </td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- BÌNH LUẬN (UI giống mẫu, dữ liệu thật) --}}
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-blue-600 flex items-center gap-2">
                            <i class="fas fa-comments"></i>
                            <span>Bình luận</span>
                            <span class="text-sm font-normal text-gray-500">({{ $comments->total() }})</span>
                        </h2>
                    </div>

                    <div class="p-6">
                        {{-- Input Comment --}}
                        @auth
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
                        @else
                        <div class="mb-6 p-3 bg-gray-50 border border-gray-200 rounded-lg flex items-center gap-2 text-sm text-gray-700">
                            <i class="fas fa-info-circle"></i>
                            <a href="{{ route('login.form') }}" class="font-medium text-blue-600 underline hover:text-blue-800">
                                Đăng nhập để bình luận
                            </a>
                        </div>
                        @endauth

                        {{-- Comment List --}}
                        @if($comments->count() > 0)
                        <div class="space-y-6">
                            @foreach($comments as $comment)
                            <div class="flex gap-4" id="comment-{{ $comment->id }}">
                                {{-- Avatar --}}
                                <img src="{{ $comment->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($comment->user->name).'&background=random' }}"
                                    class="w-10 h-10 rounded-full object-cover"
                                    alt="{{ $comment->user->name }}">

                                <div class="flex-1">
                                    {{-- Header: tên + thời gian --}}
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="font-bold text-gray-800 text-sm">{{ $comment->user->name }}</span>
                                        {{-- Badge cấp độ (placeholder) --}}
                                        <span class="px-1.5 py-0.5 bg-blue-100 text-blue-600 text-[10px] rounded font-bold">
                                            Thành viên
                                        </span>
                                        <span class="text-xs text-gray-400 ml-auto">
                                            <i class="far fa-clock"></i>
                                            {{ $comment->created_at->diffForHumans() }}
                                        </span>
                                    </div>

                                    {{-- Nội dung bình luận --}}
                                    <p class="text-gray-600 text-sm leading-relaxed bg-gray-50 p-2 rounded-br-lg rounded-bl-lg rounded-tr-lg whitespace-normal break-words">
                                        {{ trim($comment->content) }}
                                    </p>

                                    {{-- Hàng action: Thích / Không thích / Trả lời --}}
                                    <div class="flex items-center gap-3 mt-1 text-xs text-gray-500 font-medium">
                                        @auth
                                        @php
                                        $currentUser = auth()->user();
                                        $isLiked = $comment->isLikedBy($currentUser);
                                        $isDisliked = $comment->isDislikedBy($currentUser);
                                        @endphp

                                        {{-- Thích --}}
                                        <form action="{{ route('comments.reaction', ['comment' => $comment->id, 'type' => 'like']) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <button type="button"
                                                data-comment-reaction="like"
                                                data-comment-id="{{ $comment->id }}"
                                                data-action="{{ route('comments.reaction', ['comment' => $comment->id, 'type' => 'like']) }}"
                                                class="js-comment-like-btn flex items-center gap-1 transition-colors {{ $isLiked ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
                                                <i class="far fa-thumbs-up"></i>
                                                <span>Thích</span>
                                            </button>
                                        </form>

                                        {{-- Không thích --}}
                                        <form action="{{ route('comments.reaction', ['comment' => $comment->id, 'type' => 'dislike']) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <button type="button"
                                                data-comment-reaction="dislike"
                                                data-comment-id="{{ $comment->id }}"
                                                data-action="{{ route('comments.reaction', ['comment' => $comment->id, 'type' => 'dislike']) }}"
                                                class="js-comment-dislike-btn flex items-center gap-1 transition-colors {{ $isDisliked ? 'text-red-600' : 'text-gray-500 hover:text-blue-600' }}">
                                                <i class="far fa-thumbs-down"></i>
                                                <span>Không thích</span>
                                            </button>
                                        </form>

                                        {{-- Trả lời --}}
                                        <button type="button"
                                            onclick="document.getElementById('reply-{{ $comment->id }}').classList.toggle('hidden')"
                                            class="hover:text-blue-600 flex items-center gap-1">
                                            <i class="far fa-comment-dots"></i>
                                            <span>Trả lời</span>
                                        </button>
                                        @endauth
                                    </div>

                                    {{-- Thống kê like/dislike nhỏ --}}
                                    <div class="mt-1 text-[11px] text-gray-500 flex gap-3">
                                        @if(($comment->likes_count ?? 0) > 0)
                                        <span>{{ $comment->likes_count }} lượt thích</span>
                                        @endif
                                        @if(($comment->dislikes_count ?? 0) > 0)
                                        <span>{{ $comment->dislikes_count }} không thích</span>
                                        @endif
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
                                                class="w-8 h-8 rounded-full object-cover"
                                                alt="{{ $reply->user->name }}">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-0.5">
                                                    <span class="font-bold text-gray-800 text-xs">{{ $reply->user->name }}</span>
                                                    <span class="text-[10px] text-gray-400 ml-auto">
                                                        <i class="far fa-clock"></i>
                                                        {{ $reply->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <p class="text-gray-600 text-sm leading-relaxed bg-gray-50 px-2 py-1.5 rounded-br-lg rounded-bl-lg rounded-tr-lg whitespace-normal break-words">
                                                    {{ trim($reply->content) }}
                                                </p>

                                                <div class="flex items-center gap-3 mt-0.5 text-[11px] text-gray-500 font-medium">
                                                    @auth
                                                    @php
                                                    $currentUser = auth()->user();
                                                    $isReplyLiked = $reply->isLikedBy($currentUser);
                                                    $isReplyDisliked = $reply->isDislikedBy($currentUser);
                                                    @endphp
                                                    <form action="{{ route('comments.reaction', ['comment' => $reply->id, 'type' => 'like']) }}"
                                                        method="POST" class="inline">
                                                        @csrf
                                                        <button type="button"
                                                            data-comment-reaction="like"
                                                            data-comment-id="{{ $reply->id }}"
                                                            data-action="{{ route('comments.reaction', ['comment' => $reply->id, 'type' => 'like']) }}"
                                                            class="js-comment-like-btn flex items-center gap-1 transition-colors {{ $isReplyLiked ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
                                                            <i class="far fa-thumbs-up"></i>
                                                            <span>Thích</span>
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('comments.reaction', ['comment' => $reply->id, 'type' => 'dislike']) }}"
                                                        method="POST" class="inline">
                                                        @csrf
                                                        <button type="button"
                                                            data-comment-reaction="dislike"
                                                            data-comment-id="{{ $reply->id }}"
                                                            data-action="{{ route('comments.reaction', ['comment' => $reply->id, 'type' => 'dislike']) }}"
                                                            class="js-comment-dislike-btn flex items-center gap-1 transition-colors {{ $isReplyDisliked ? 'text-red-600' : 'text-gray-500 hover:text-blue-600' }}">
                                                            <i class="far fa-thumbs-down"></i>
                                                            <span>Không thích</span>
                                                        </button>
                                                    </form>

                                                    <button type="button"
                                                        onclick="document.getElementById('reply-{{ $reply->id }}').classList.toggle('hidden')"
                                                        class="hover:text-blue-600 flex items-center gap-1">
                                                        <i class="far fa-comment-dots"></i>
                                                        <span>Trả lời</span>
                                                    </button>
                                                    @endauth
                                                </div>

                                                <div class="mt-1 text-[10px] text-gray-500 flex gap-3">
                                                    @if(($reply->likes_count ?? 0) > 0)
                                                    <span>{{ $reply->likes_count }} lượt thích</span>
                                                    @endif
                                                    @if(($reply->dislikes_count ?? 0) > 0)
                                                    <span>{{ $reply->dislikes_count }} không thích</span>
                                                    @endif
                                                </div>

                                                {{-- Form trả lời cấp 2 --}}
                                                @auth
                                                <div id="reply-{{ $reply->id }}" class="hidden mt-3">
                                                    <div class="flex gap-2">
                                                        <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=random' }}"
                                                            class="w-7 h-7 rounded-full object-cover"
                                                            alt="{{ auth()->user()->name }}">
                                                        <form method="POST" action="{{ route('comments.store', $comic) }}" class="flex-1">
                                                            @csrf
                                                            <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                                                            <textarea name="content"
                                                                rows="2"
                                                                class="w-full border border-gray-300 rounded-lg p-2 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all resize-none"
                                                                placeholder="Viết phản hồi..."></textarea>
                                                            <div class="flex justify-end mt-2 gap-2">
                                                                <button type="button"
                                                                    onclick="document.getElementById('reply-{{ $reply->id }}').classList.add('hidden')"
                                                                    class="px-3 py-1 text-[11px] text-gray-500 hover:text-gray-700">
                                                                    Hủy
                                                                </button>
                                                                <button type="submit"
                                                                    class="px-3 py-1 bg-blue-600 text-white text-[11px] font-semibold rounded hover:bg-blue-700 transition-colors">
                                                                    Gửi
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
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
                        <div class="mt-6">
                            {{ $comments->links() }}
                        </div>
                        @else
                        <div class="text-center py-10 text-gray-500 text-sm">
                            Chưa có bình luận nào. Hãy là người đầu tiên bình luận!
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

        // === Comment like / dislike AJAX (giữ nguyên chức năng) ===
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : null;

        function updateCommentUI(commentId, data) {
            const root = document.getElementById('comment-' + commentId);
            if (!root) return;

            const likeBtn = root.querySelector('.js-comment-like-btn');
            const dislikeBtn = root.querySelector('.js-comment-dislike-btn');

            const likes = data.likes_count ?? 0;
            const dislikes = data.dislikes_count ?? 0;
            const userReaction = data.user_reaction; // 'like' | 'dislike' | null

            if (likeBtn) {
                likeBtn.classList.remove('text-blue-600');
                likeBtn.classList.add('text-gray-500');
                if (userReaction === 'like') {
                    likeBtn.classList.remove('text-gray-500');
                    likeBtn.classList.add('text-blue-600');
                }
            }

            if (dislikeBtn) {
                dislikeBtn.classList.remove('text-red-600');
                dislikeBtn.classList.add('text-gray-500');
                if (userReaction === 'dislike') {
                    dislikeBtn.classList.remove('text-gray-500');
                    dislikeBtn.classList.add('text-red-600');
                }
            }

            const likeCountEl = root.querySelector('.js-comment-like-count');
            const dislikeCountEl = root.querySelector('.js-comment-dislike-count');

            if (likeCountEl) likeCountEl.textContent = likes > 0 ? likes : '';
            if (dislikeCountEl) dislikeCountEl.textContent = dislikes > 0 ? dislikes : '';
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
    });
</script>

@endsection