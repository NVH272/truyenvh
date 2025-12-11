@extends('layouts.app')

@section('title', 'Truyện của bạn')
@section('header', 'Danh sách truyện của bạn')

@section('content')

<div class="max-w-[1920px] mx-auto space-y-8 pb-10">

    {{-- 1. Header & Actions (Thiết kế mới: Sạch sẽ, Hiện đại) --}}
    <div class="flex flex-col md:flex-row justify-between items-end gap-4 border-b border-slate-200 pb-5">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Thư viện truyện</h2>
            <div class="flex items-center gap-2 mt-2 text-sm text-slate-500 font-medium">
                <span class="flex items-center gap-1.5">
                    <i class="fas fa-book-open text-blue-500"></i>
                    Tổng số: <span class="text-slate-800 font-bold">{{ $comics->total() }}</span> bộ
                </span>
                <span class="text-slate-300">|</span>
                <span>Quản lý danh sách tác phẩm của bạn</span>
            </div>
        </div>

        {{-- Nút đăng truyện (Style nổi bật) --}}
        <a href="{{ route('user.comics.create') }}"
            class="group relative inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold text-white transition-all duration-200 bg-slate-900 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 hover:bg-blue-600 shadow-lg hover:shadow-blue-500/30 hover:-translate-y-0.5">
            <i class="fas fa-plus mr-2 transition-transform duration-300 group-hover:rotate-90"></i>
            <span>Đăng truyện mới</span>
        </a>
    </div>

    @if($comics->isEmpty())
    {{-- Empty State (Thiết kế mới: Trân trọng, mời gọi) --}}
    <div class="flex flex-col items-center justify-center py-20 px-4 bg-white border-2 border-dashed border-slate-200 rounded-3xl text-center">
        <div class="w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mb-6 shadow-sm">
            <i class="fas fa-feather-alt text-4xl text-blue-400 transform -rotate-12"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">Chưa có tác phẩm nào</h3>
        <p class="text-slate-500 text-sm mb-8 max-w-sm mx-auto leading-relaxed">
            Thư viện của bạn đang trống. Hãy bắt đầu hành trình sáng tạo và chia sẻ câu chuyện đầu tiên của bạn ngay hôm nay!
        </p>
        <a href="{{ route('user.comics.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5">
            <i class="fas fa-magic mr-2"></i> Đăng truyện ngay
        </a>
    </div>
    @else

    {{-- Grid Container (GIỮ NGUYÊN CODE CŨ 100% về giao diện, chỉ thêm Stretched Link) --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-8 gap-x-4 gap-y-6">
        @foreach($comics as $comic)
        <div class="group relative bg-transparent rounded-md overflow-hidden transition-all duration-300 hover:-translate-y-1">

            {{-- *** LINK PHỦ TOÀN BỘ THẺ (Stretched Link) *** --}}
            {{-- Thay route('comics.show', $comic->slug) bằng route thực tế trang đọc truyện của bạn --}}
            {{-- Ví dụ: route('comics.show') hoặc link chi tiết --}}
            <a href="{{ route('admin.comics.edit', $comic->id) }}" class="absolute inset-0 z-10" aria-hidden="true">

                {{-- Cover Image Wrapper --}}
                <div class="relative aspect-[2/3] rounded-md overflow-hidden shadow-md border border-slate-700/50 group-hover:shadow-lg group-hover:border-blue-500/50 transition-all">

                    {{-- Ảnh bìa --}}
                    <div class="block w-full h-full">
                        <img src="{{ $comic->cover_url }}" alt="{{ $comic->title }}"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    </div>

                    {{-- Badge: Chapter (Top Left) --}}
                    <div class="absolute top-1.5 left-1.5 pointer-events-none z-20"> {{-- z-20 để nổi lên trên link phủ --}}
                        <span class="px-1.5 py-0.5 text-[9px] font-bold bg-black/70 text-white rounded backdrop-blur-sm shadow-sm">
                            {{ $comic->chapters_count ?? 0 }} chương
                        </span>
                    </div>

                    {{-- Badge: Status (Top Right) --}}
                    <div class="absolute top-1.5 right-1.5 pointer-events-none z-20">
                        @if($comic->status === 'ongoing')
                        <span class="px-1.5 py-0.5 text-[9px] font-bold bg-blue-600/90 text-white rounded shadow-sm">Đang tiến hành</span>
                        @elseif($comic->status === 'completed')
                        <span class="px-1.5 py-0.5 text-[9px] font-bold bg-green-600/90 text-white rounded shadow-sm">Hoàn thành</span>
                        @else
                        <span class="px-1.5 py-0.5 text-[9px] font-bold bg-yellow-600/90 text-white rounded shadow-sm">Tạm dừng</span>
                        @endif
                    </div>

                    {{-- Overlay Stats (Bottom on Image) --}}
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent pt-6 pb-1.5 px-2 flex justify-between items-end text-[10px] text-white/90 pointer-events-none z-20">
                        <span class="flex items-center gap-1">
                            <i class="fas fa-eye text-[9px]"></i>
                            {{ number_format($comic->views ?? 0) }}
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="fas fa-heart text-red-400 text-[9px]"></i>
                            {{ number_format($comic->follows ?? 0) }}
                        </span>
                    </div>

                    {{-- Action Overlay (Center Hover) --}}
                    {{-- QUAN TRỌNG: z-30 để cao hơn Stretched Link (z-10) -> Click vào đây sẽ không kích hoạt link phủ --}}
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center gap-3 backdrop-blur-[1px] z-30 pointer-events-none group-hover:pointer-events-auto">
                        <a href="{{ route('user.comics.edit', $comic->id) }}" class="w-8 h-8 rounded-full bg-white text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition shadow-lg relative z-40" title="Sửa">
                            <i class="fas fa-pen text-xs"></i>
                        </a>
                        <form action="{{ route('user.comics.destroy', $comic->id) }}" method="POST"
                            onsubmit="return confirm('Xóa truyện này?');" class="relative z-40">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-full bg-white text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition shadow-lg" title="Xóa">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Content Below Image --}}
                <div class="mt-2 space-y-1 relative z-20"> {{-- z-20 để text nổi lên trên (nếu cần select text) --}}
                    {{-- Title --}}
                    <h3 class="text-[13px] font-bold text-slate-800 dark:text-slate-200 line-clamp-2 leading-snug group-hover:text-blue-600 transition-colors" title="{{ $comic->title }}">
                        {{-- Link ở đây không cần thiết nữa vì đã có stretched link phủ toàn bộ thẻ, nhưng giữ lại để chuẩn semantic --}}
                        <span class="block">
                            {{ $comic->title }}
                        </span>
                    </h3>

                    {{-- Pending status --}}
                    <div class="flex items-center gap-1 text-[10px] mt-0.5">
                        @if($comic->approval_status === 'pending')
                        <span class="px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 border border-yellow-300">
                            Đang chờ duyệt
                        </span>
                        @elseif($comic->approval_status === 'rejected')
                        <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 border border-red-300"
                            title="{{ $comic->rejection_reason }}">
                            Bị từ chối
                        </span>
                        @else
                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-300">
                            Đã duyệt
                        </span>
                        @endif
                    </div>

                    {{-- Rating Stars --}}
                    <div class="flex items-center gap-0.5 text-yellow-500 text-[10px]">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <=round($comic->rating ?? 0))
                            <i class="fas fa-star"></i>
                            @else
                            <i class="far fa-star text-slate-400"></i>
                            @endif
                            @endfor
                            <span class="text-slate-500 ml-1">({{ number_format($comic->rating ?? 0, 1) }})</span>
                    </div>

                    {{-- Author --}}
                    <div class="text-[11px] text-slate-500 truncate" title="Tác giả">
                        <i class="fas fa-user-edit text-[9px] mr-1"></i> {{ $comic->author ?? 'Đang cập nhật' }}
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    {{-- Pagination (Thiết kế mới: Căn giữa, padding rộng) --}}
    <div class="mt-12 flex justify-center border-t border-slate-200 pt-8">
        {{ $comics->links() }}
    </div>

    @endif

</div>

@endsection