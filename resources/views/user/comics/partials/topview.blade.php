<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-gray-800 text-base border-l-4 border-red-500 pl-3 uppercase">
            Top Lượt Xem
        </h3>
        <div class="flex gap-1">
            <button class="text-[10px] px-2 py-0.5 bg-red-500 text-white rounded-full">Ngày</button>
            <button class="text-[10px] px-2 py-0.5 bg-gray-200 text-gray-500 rounded-full hover:bg-gray-300">Tuần</button>
        </div>
    </div>
    <div class="divide-y divide-gray-100">
        @foreach($topViewedComics as $index => $comic)
        <div class="flex items-center gap-3 p-3 hover:bg-gray-50 transition-colors cursor-pointer group">
            {{-- Thứ hạng --}}
            <span
                class="w-6 h-6 flex-shrink-0 flex items-center justify-center rounded-full
        {{ $index < 3 ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-600' }}
        text-xs font-bold">
                {{ $index + 1 }}
            </span>

            {{-- Ảnh bìa --}}
            <div class="w-16 h-24 flex-shrink-0 rounded overflow-hidden">
                <a href="{{ route('user.comics.show', $comic->slug) }}">
                    <img src="{{ $comic->cover_url }}"
                        class="w-full h-full object-cover"
                        alt="{{ $comic->title }}">
                </a>
            </div>

            {{-- Thông tin --}}
            <div class="flex-1 min-w-0">
                <h4
                    class="text-sm font-medium text-gray-800 group-hover:text-red-500 truncate transition-colors">
                    <a href="{{ route('user.comics.show', $comic->slug) }}">
                        {{ $comic->title }}
                    </a>
                </h4>

                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-eye text-gray-400"></i>
                    {{ number_format($comic->views) }}

                    <span class="mx-1">•</span>

                    Ch. {{ $comic->chapters_max_chapter_number ?? '?' }}
                </p>
            </div>
        </div>
        @endforeach

    </div>
</div>