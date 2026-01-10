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
        @php
        $isComicOwner = auth()->check() && (int)$comic->created_by === (int)auth()->id();
        @endphp
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 sticky top-0 z-10 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="px-6 py-3 font-medium">Số chương</th>
                    <th class="px-4 py-3 font-medium text-center">Cập nhật</th>
                    <th class="px-4 py-3 font-medium text-right">Lượt xem</th>
                    @if($isComicOwner)
                    <th class="px-4 py-3 font-medium text-center">Hành động</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($comic->chapters as $chapter)
                <tr class="hover:bg-gray-50 transition-colors group">
                    {{-- Số chapter --}}
                    <td class="px-6 py-3">
                        <a href="{{ route('user.comics.chapters.read', ['comic' => $comic->id,'chapter_number' => $chapter->chapter_number]) }}"
                            class="font-medium text-gray-700 group-hover:text-blue-600 transition-colors">
                            Chapter {{ $chapter->chapter_number }}
                        </a>
                    </td>

                    {{-- Cập nhật --}}
                    <td class="px-4 py-3 text-gray-500 text-center text-xs">
                        {{ optional($chapter->updated_at)->format('d/m/Y') }}
                    </td>

                    {{-- Lượt xem --}}
                    <td class="px-4 py-3 text-gray-500 text-right text-xs">
                        {{ number_format($chapter->views ?? 0) }}
                    </td>

                    {{-- Hành động (chỉ chủ truyện) --}}
                    @if($isComicOwner)
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-2">

                            {{-- SỬA --}}
                            <a href="{{ route('user.comics.chapters.edit', ['comic' => $comic->id, 'chapter' => $chapter->id]) }}"
                                class="inline-flex items-center justify-center w-8 h-8
                                rounded-lg border border-gray-300
                                text-gray-600 hover:text-blue-600
                                hover:border-blue-500 hover:bg-blue-50
                                transition-all"
                                title="Sửa chapter">
                                <i class="fa-solid fa-pen text-xs"></i>
                            </a>

                            {{-- XOÁ --}}
                            <form method="POST"
                                action="{{ route('user.comics.chapters.destroy', [
                                        'comic' => $comic->id,
                                        'chapter' => $chapter->id
                                ]) }}"
                                onsubmit="return confirm('Bạn có chắc chắn muốn xoá chapter này?');">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="inline-flex items-center justify-center w-8 h-8
                               rounded-lg border border-gray-300
                               text-gray-600 hover:text-red-600
                               hover:border-red-500 hover:bg-red-50
                               transition-all"
                                    title="Xoá chapter">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>

                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $isComicOwner ? 4 : 3 }}"
                        class="px-6 py-6 text-center text-gray-400 text-sm">
                        Truyện chưa có chapter nào
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>