@extends('layouts.admin')

@section('title', 'Xử lý vi phạm')

@section('content')
<div class="space-y-4">
    <h1 class="text-xl font-bold">Xử lý vi phạm</h1>

    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="divide-y">
            @forelse($reports as $r)
            @php
            $commentId = $r->comment?->id;
            $comicSlug = $r->comic?->slug;
            $jumpUrl = $comicSlug && $commentId
            ? route('user.comics.show', $comicSlug) . '#comment-' . $commentId
            : '#';
            @endphp

            <div class="p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="text-sm text-gray-600">
                            Báo cáo bởi: <b>{{ $r->reporter?->name ?? 'N/A' }}</b>
                            • {{ $r->created_at->format('d/m/Y H:i') }}
                            • Trạng thái: <b>{{ $r->status }}</b>
                        </div>

                        <div class="mt-2">
                            <div class="text-sm font-semibold">
                                Truyện: {{ $r->comic?->title ?? 'N/A' }}
                            </div>

                            <div class="mt-2 p-3 bg-gray-50 rounded-lg text-sm">
                                <div class="text-gray-600 mb-1">
                                    Bình luận của: <b>{{ $r->comment?->user?->name ?? 'N/A' }}</b>
                                    @if($r->comment?->parent_id) <span class="text-xs">(reply)</span> @endif
                                </div>
                                <div class="text-gray-800">
                                    {{ $r->comment?->content ?? '[Không tìm thấy nội dung]' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex-shrink-0">
                        <a href="{{ $jumpUrl }}"
                            class="inline-flex items-center px-3 py-2 text-sm font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                            Xem truyện & tới bình luận
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-6 text-gray-500">Chưa có báo cáo nào.</div>
            @endforelse
        </div>
    </div>

    <div>
        {{ $reports->links() }}
    </div>
</div>
@endsection