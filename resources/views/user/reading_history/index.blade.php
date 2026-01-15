@extends('layouts.app')

@section('title', 'Lịch sử đọc truyện')

@section('content')
{{-- HEADER: COMPACT & CLEAN --}}
<div class="flex items-end justify-between mb-6 pb-2 border-b border-slate-100">
    <div>
        <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-100 text-rose-600">
                <i class="fas fa-history text-sm"></i>
            </span>
            Lịch sử đọc
        </h2>
        <p class="text-xs text-slate-500 font-medium mt-1 ml-1">
            Theo dõi tiến trình đọc truyện của bạn
        </p>
    </div>
</div>

@if($histories->isEmpty())
{{-- EMPTY STATE: SLIM, COMPACT & POLISHED --}}
<div class="group relative flex flex-col items-center justify-center py-10 px-6 rounded-[2rem] border-2 border-dashed border-slate-200 hover:border-rose-300/50 bg-gradient-to-b from-white to-slate-50/80 transition-all duration-300 overflow-hidden">

    {{-- Hiệu ứng ánh sáng nền nhẹ khi hover (Tạo cảm giác cao cấp) --}}
    <div class="absolute inset-0 bg-gradient-to-tr from-rose-50/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>

    {{-- Icon nhỏ gọn được làm đẹp --}}
    <div class="relative z-10 mb-4">
        {{-- Lớp nền tỏa sáng nhẹ phía sau icon --}}
        <div class="absolute inset-0 bg-rose-100 rounded-2xl blur-lg opacity-40 group-hover:opacity-60 transition-opacity"></div>
        {{-- Container chứa icon chính --}}
        <div class="relative w-14 h-14 bg-gradient-to-tr from-white to-rose-50 rounded-2xl shadow-[0_4px_15px_-3px_rgba(244,63,94,0.2)] border border-white flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
            {{-- Dùng icon có màu sắc --}}
            <i class="fas fa-book-open text-xl text-rose-500/90"></i>
        </div>
    </div>

    {{-- Text chính có thêm dòng phụ nhỏ --}}
    <div class="relative z-10 text-center mb-6">
        <p class="text-slate-700 font-semibold mb-1">
            Bạn chưa đọc bộ truyện nào gần đây
        </p>
        <p class="text-xs text-slate-400 font-medium">
            Hãy bắt đầu một hành trình mới ngay hôm nay!
        </p>
    </div>

    {{-- Nút bấm nhỏ dạng Soft Button (Nền mềm) --}}
    <a href="{{ url('/') }}" class="relative z-10 inline-flex items-center gap-2 px-5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 text-xs font-bold rounded-full shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
        <i class="far fa-compass"></i>
        <span>Khám phá thư viện</span>
    </a>
</div>
@else
<div class="relative border-l-2 border-slate-300 ml-6">
    @foreach($histories as $comicHistories)
    @php
    $comic = $comicHistories->first()->comic;
    $lastTime = $comicHistories->max('last_read_at');
    @endphp

    <div class="relative pl-10 mb-8 pb-8 border-b border-slate-100 last:border-b-0">
        {{-- Timeline Node --}}
        <span class="absolute -left-[9px] top-1 w-4 h-4 bg-white border-2 border-slate-400 rounded-full z-10"></span>

        {{-- Time Badge --}}
        @php
        $lastTime = $comicHistories
        ->sortByDesc('last_read_at')
        ->first()
        ->last_read_at;
        @endphp
        <div class="mb-4">
            <span class="inline-block px-2 py-1 text-xs font-medium text-slate-600 bg-slate-100 rounded">
                {{ $lastTime->diffForHumans() }}
            </span>
        </div>

        <div class="flex gap-4">
            {{-- Comic Cover --}}
            <a href="{{ route('user.comics.show', $comic->slug) }}" class="flex-shrink-0">
                <img src="{{ $comic->cover_url }}"
                    alt="{{ $comic->title }}"
                    class="w-20 h-28 object-cover rounded-lg shadow-md hover:shadow-lg transition-shadow">
            </a>

            {{-- Comic Info & Chapters --}}
            <div class="flex-1 min-w-0">
                <a href="{{ route('user.comics.show', $comic->slug) }}" class="block mb-3 group">
                    <h3 class="font-bold text-slate-800 text-lg group-hover:text-blue-600 transition-colors line-clamp-2">
                        {{ $comic->title }}
                    </h3>
                </a>

                {{-- Chapters List --}}
                <div class="space-y-3">
                    @foreach($comicHistories as $history)
                    <div class="bg-slate-50 rounded-lg p-3 hover:bg-slate-100 transition-colors">
                        {{-- Chapter Info --}}
                        <div class="flex items-center justify-between mb-2">
                            <a href="{{ route('user.comics.chapters.read', ['comic' => $comic->id, 'chapter_number' => $history->chapter->chapter_number]) }}"
                                class="text-sm font-semibold text-slate-700 hover:text-blue-600 transition-colors">
                                Chương {{ $history->chapter->chapter_number }}: {{ $history->chapter->title }}
                            </a>
                            <span class="text-xs font-medium text-slate-500 ml-2">
                                {{ $history->progress }}%
                            </span>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-300
                                {{ $history->progress == 100 ? 'bg-green-500' : 'bg-blue-500' }}"
                                style="width: {{ $history->progress }}%">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
@endsection