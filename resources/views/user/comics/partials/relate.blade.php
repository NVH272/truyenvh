<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-100">
        <h3 class="font-bold text-gray-800 text-base border-l-4 border-blue-500 pl-3 uppercase">
            Truyện liên quan
        </h3>
    </div>
    <div class="p-4 space-y-4">
        <div class="p-4 space-y-4">
            @forelse(($relatedComics ?? collect()) as $related)
            <a href="{{ route('user.comics.show', $related->slug) }}" class="flex gap-3 group cursor-pointer">
                <div class="w-16 h-24 flex-shrink-0 rounded overflow-hidden relative">
                    <img src="{{ $related->cover_url }}"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                        alt="{{ $related->title }}">
                </div>

                <div class="flex-1 min-w-0 py-1">
                    <h4 class="text-sm font-bold text-gray-800 group-hover:text-blue-600 truncate transition-colors">
                        {{ $related->title }}
                    </h4>

                    @php $avg = (float)($related->ratings_avg_rating ?? 0); @endphp
                    <div class="flex items-center text-xs text-yellow-500 my-1">
                        <i class="fas fa-star"></i>
                        <span class="text-gray-400 ml-1">
                            {{ number_format($avg, 1) }}
                            @if(!empty($related->ratings_count))
                            ({{ $related->ratings_count }})
                            @endif
                        </span>
                    </div>

                    <div class="flex flex-wrap gap-1">
                        @foreach($related->categories->take(2) as $cat)
                        <span class="text-[10px] px-1.5 py-0.5 bg-gray-100 text-gray-500 rounded">
                            {{ $cat->name }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </a>
            @empty
            <div class="text-sm text-gray-400 italic text-center">
                Chưa có truyện liên quan
            </div>
            @endforelse
        </div>
    </div>
</div>