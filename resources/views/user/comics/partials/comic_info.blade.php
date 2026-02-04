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
                    <!-- <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">HOT</span> -->
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
                        <x-rating-stars :rating="$avgRating" sizeClass="text-sm" />
                        <span>( {{ number_format($avgRating, 1) }}/5 - {{ $ratingCount }} đánh giá )</span>
                        <a href="{{ route('verification.notice') }}" class="text-blue-600 text-xs underline ml-1">
                            Xác thực email để đánh giá
                        </a>
                    </div>
                    @endif
                    @else
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <x-rating-stars :rating="$avgRating" sizeClass="text-sm" />
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
                        <span class="text-gray-500 w-24 flex-shrink-0">
                            <i class="fas fa-user mr-1.5"></i> Tác giả:
                        </span>
                        <div class="flex flex-wrap items-center gap-x-1 gap-y-1">
                            @php
                                $authorRelations = $comic->relationLoaded('authors') ? $comic->authors : $comic->authors;
                            @endphp

                            @forelse($authorRelations as $author)
                                <a href="{{ route('user.comics.author.show', $author->name) }}"
                                    class="font-medium text-blue-600 hover:underline hover:text-blue-400 transition">
                                    {{ $author->name }}
                                </a>
                                @if(!$loop->last)
                                    <span class="text-gray-400">,</span>
                                @endif
                            @empty
                                <span class="text-gray-400">Đang cập nhật</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-gray-500 w-24 flex-shrink-0"><i class="fas fa-eye mr-1.5"></i> Lượt xem:</span>
                        <span class="font-medium text-gray-800">{{ number_format($comic->views ?? 0) }}</span>
                    </div>

                    <div class="flex items-center gap-2 col-span-2">
                        <span class="text-gray-500 w-24 flex-shrink-0"><i class="fas fa-tags mr-1.5"></i> Thể loại:</span>
                        <div class="flex flex-wrap gap-1">
                            @forelse ($comic->categories as $category)
                            <a href="{{ route('user.comics.filter', ['categories[0]' => $category->slug]) }}"
                                class="text-blue-500 hover:underline">
                                {{ $category->name }}
                            </a>
                            @if (!$loop->last)
                            <span class="text-black-400">, </span>
                            @endif
                            @empty
                            <span class="text-gray-400">Đang cập nhật</span>
                            @endforelse
                        </div>
                    </div>                    
                </div>

                {{-- Mô tả ngắn --}}
                <div class="mb-6">
                    <h3 class="font-bold text-gray-800 border-b-2 border-blue-500 inline-block mb-2 pb-1">
                        Sơ lược
                    </h3>
                    <p class="text-gray-600 text-sm leading-relaxed whitespace-pre-line">
                        {{ $comic->description ?? 
                            'Chào mừng các độc giả thân mến của TruyenVH, hãy cùng thưởng thức bộ truyện tranh ' 
                            . $comic->title . 
                            ' đầy cuốn hút trên website của chúng tôi. Để có trải nghiệm đọc truyện tốt nhất, bạn nên đăng ký tài khoản tại TruyenVH. Khi đăng ký, bạn có thể theo dõi những bộ truyện yêu thích,
                            bình luận và giao lưu cùng cộng đồng độc giả sôi nổi. TruyenVH tự hào mang đến kho truyện tranh đa dạng và phong phú với nhiều thể loại hấp dẫn. Hãy đăng ký ngay hôm nay để không bỏ lỡ những chương mới nhất của ' . $comic->title . '!'
                        }}
                    </p>

                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-wrap gap-3 justify-center md:justify-start">
                    {{-- ĐỌC TỪ ĐẦU --}}
                    @if(isset($firstChapter) && $firstChapter)
                    <a href="{{ route('user.comics.chapters.read', [
                        'comic' => $comic->id,
                        'chapter_number' => $firstChapter->chapter_number
                    ]) }}"
                        class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-lg border border-gray-300 shadow-lg hover:bg-blue-700 hover:-translate-y-0.5 transition-all flex items-center gap-2">
                        <i class="fas fa-book-open"></i> Đọc từ đầu
                    </a>
                    @else
                    <button disabled
                        class="px-6 py-2.5 bg-gray-300 text-gray-500 font-bold rounded-lg border border-gray-200 shadow cursor-not-allowed flex items-center gap-2 opacity-70">
                        <i class="fas fa-book-open"></i> Đọc từ đầu
                    </button>
                    @endif

                    {{-- Follow Button --}}
                    @auth
                    @if(auth()->user()->hasVerifiedEmail())
                    <form action="{{ route('comics.follow', $comic) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="px-6 py-2.5 {{ $isFollowing ? 'bg-gray-200 text-gray-800' : 'bg-red-500 text-white' }} font-bold rounded-lg border border-gray-300 shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2 hover:bg-red-600">
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

                    {{-- ĐỌC MỚI NHẤT --}}
                    @if(isset($latestChapter) && $latestChapter)
                    <a href="{{ route('user.comics.chapters.read', [
                        'comic' => $comic->id,
                        'chapter_number' => $latestChapter->chapter_number
                    ]) }}"
                        class="px-6 py-2.5 bg-yellow-300 text-gray-700 font-bold rounded-lg border border-gray-300 shadow-lg hover:bg-yellow-400 hover:-translate-y-0.5 transition-all flex items-center gap-2">
                        <i class="fas fa-book-open-reader"></i> Đọc mới nhất
                    </a>
                    @else
                    <button disabled
                        class="px-6 py-2.5 bg-gray-200 text-gray-500 font-bold rounded-lg border border-gray-200 shadow cursor-not-allowed flex items-center gap-2 opacity-70">
                        <i class="fas fa-book-open-reader"></i> Đọc mới nhất
                    </button>
                    @endif

                    @php
                    $isComicOwner = auth()->check() && ((int)$comic->created_by === (int)auth()->id());
                    @endphp
                    @if($isComicOwner)
                    <a href="{{ route('user.comics.chapters.create', ['comic' => $comic->id, 'redirect_to' => url()->full()]) }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-lg border border-gray-300 shadow-lg hover:bg-gray-200 hover:-translate-y-0.5 transition-all flex items-center gap-2">
                        <i class="fas fa-list"></i> Thêm chapter mới
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>