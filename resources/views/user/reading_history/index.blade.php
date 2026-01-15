@extends('layouts.app')

@section('title', 'Lịch sử đọc truyện')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-800 mb-2">Lịch sử đọc truyện</h1>
    <p class="text-sm text-slate-500">Theo dõi tiến trình đọc truyện của bạn</p>
</div>

@if($histories->isEmpty())
<div class="text-center py-12">
    <i class="fas fa-book-open text-4xl text-slate-300 mb-4"></i>
    <p class="text-slate-500">Bạn chưa có lịch sử đọc truyện nào</p>
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