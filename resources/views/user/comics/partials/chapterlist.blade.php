<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-lg font-bold text-blue-600 flex items-center gap-2">
            <i class="fas fa-list-ul"></i> Danh sách chương
        </h2>
        <span class="text-xs text-gray-500">
            @if($comic->chapters->isNotEmpty())
            Cập nhật lúc: {{ $comic->chapters->first()->updated_at->diffForHumans() }}
            @else
            Chưa có chapter
            @endif
        </span>

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
                @forelse($comic->chapters as $chapter)
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="px-6 py-3">
                        <a href="{{ route('user.comics.chapters.read', [
                    'comic' => $comic->id,
                    'chapter_number' => $chapter->chapter_number
                ]) }}"
                            class="font-medium text-gray-700 group-hover:text-blue-600 transition-colors">
                            Chapter {{ $chapter->chapter_number }}
                        </a>
                    </td>

                    <td class="px-4 py-3 text-gray-500 text-center text-xs">
                        {{ optional($chapter->updated_at)->format('d/m/Y') }}
                    </td>

                    <td class="px-4 py-3 text-gray-500 text-right text-xs">
                        {{ number_format($chapter->views ?? 0) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-6 text-center text-gray-400 text-sm">
                        Truyện chưa có chapter nào
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>