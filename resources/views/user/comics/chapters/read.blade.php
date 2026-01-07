@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6 space-y-4">

    <div class="bg-white rounded-xl border p-4">
        <div class="font-bold text-lg">{{ $comic->title }}</div>
        <div class="text-sm text-gray-600">
            Chapter {{ $chapter->chapter_number }} — {{ $chapter->title }}
            <span class="mx-2">•</span>
            {{ $chapter->pages->count() }} trang
        </div>
    </div>

    {{-- Render tất cả trang --}}
    <div class="space-y-3">
        @foreach($chapter->pages as $p)
        <img
            src="{{ $p->image_url }}"
            alt="Trang {{ $p->page_index }}"
            class="w-full rounded-lg border bg-white"
            loading="lazy">
        @endforeach
    </div>

</div>
@endsection