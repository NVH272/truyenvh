@extends('layouts.app')

@section('title', $comic->title ?? 'Chi tiết truyện')

@section('content')
{{-- Container chính --}}
<div class="bg-[#f0f2f5] min-h-screen pb-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- === PHẦN 1: THÔNG TIN TRUYỆN (TOP - FULL WIDTH) === --}}
        @include('user.comics.partials.comic_info', ['comic' => $comic])

        {{-- === PHẦN 2: MAIN CONTENT LAYOUT (Tỉ lệ 5:3) === --}}
        {{-- Sử dụng grid-cols-8 để chia chính xác 5 phần và 3 phần --}}
        <div class="grid grid-cols-1 lg:grid-cols-8 gap-6">

            {{-- CỘT TRÁI (LEFT): Chiếm 5/8 --}}
            <div class="lg:col-span-5 space-y-6">

                {{-- 1. DANH SÁCH CHƯƠNG --}}
                @include('user.comics.partials.chapterlist', ['comic' => $comic])

                {{-- 2. BÌNH LUẬN --}}
                @include('user.comics.partials.comments.index', ['comic' => $comic])

            </div>

            {{-- CỘT PHẢI (RIGHT): Chiếm 3/8 --}}
            <div class="lg:col-span-3 space-y-6">

                {{-- 1. TRUYỆN LIÊN QUAN --}}
                @include('user.comics.partials.relate')


                {{-- 2. TOP LƯỢT XEM --}}
                @include('user.comics.partials.topview')

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

    /* Facebook-style comment bubble */
    .js-comment-like-btn,
    .js-comment-dislike-btn {
        display: inline;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .js-comment-like-btn:hover,
    .js-comment-dislike-btn:hover {
        opacity: 0.7;
    }

    .comment-bubble,
    .reply-bubble {
        display: inline-block;
    }
</style>

@endsection