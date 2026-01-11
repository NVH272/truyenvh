@extends('layouts.admin')

@section('title', 'Xử lý vi phạm')
@section('header', 'Quản lý Vi Phạm')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-12">

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Reports -->
        <div class="bg-slate-800 rounded-2xl p-5 border border-slate-700/50 shadow-lg relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-blue-500/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Tổng báo cáo</p>
                    <h3 class="text-3xl font-black text-white brand-font tracking-wide">{{ $reports->total() }}</h3>
                </div>
                <div class="p-3 bg-slate-700/50 rounded-xl text-blue-400">
                    <i class="fas fa-flag text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending (Placeholder logic) -->
        <div class="bg-slate-800 rounded-2xl p-5 border border-slate-700/50 shadow-lg relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-yellow-500/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Chờ xử lý</p>
                    <h3 class="text-3xl font-black text-white brand-font tracking-wide">--</h3>
                </div>
                <div class="p-3 bg-slate-700/50 rounded-xl text-yellow-400">
                    <i class="fas fa-hourglass-half text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Resolved (Placeholder logic) -->
        <div class="bg-slate-800 rounded-2xl p-5 border border-slate-700/50 shadow-lg relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-emerald-500/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Đã xử lý</p>
                    <h3 class="text-3xl font-black text-white brand-font tracking-wide">--</h3>
                </div>
                <div class="p-3 bg-slate-700/50 rounded-xl text-emerald-400">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Banned Words (Placeholder logic) -->
        <a href="{{ route('admin.banned_words.index') }}" class="block">
            <div class="bg-slate-800 rounded-2xl p-5 border border-slate-700/50 shadow-lg relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-24 h-24 bg-red-500/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>

                <div class="relative z-10 flex justify-between items-start">
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Danh sách từ cấm</p>
                        {{-- Thay thế '--' bằng biến số lượng từ cấm thực tế nếu có, ví dụ: $bannedWords->count() --}}
                        <h3 class="text-3xl font-black text-white brand-font tracking-wide">{{ $bannedWordsCount }}</h3>
                    </div>
                    <div class="p-3 bg-slate-700/50 rounded-xl text-red-400">
                        <i class="fas fa-ban text-xl"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Reports List -->
    <div class="space-y-6">
        @forelse($reports as $r)
        @php
        $commentId = $r->comment?->id;
        $comicSlug = $r->comic?->slug;
        $jumpUrl = $comicSlug && $commentId
        ? route('user.comics.show', $comicSlug) . '#comment-' . $commentId
        : '#';
        $isPending = $r->status === 'pending';
        @endphp

        <div class="bg-slate-800 rounded-2xl border border-slate-700/60 shadow-xl overflow-hidden group hover:border-slate-600 transition-all duration-300">

            <!-- Header: Status Strip & Info -->
            <div class="flex items-stretch min-h-[50px] border-b border-slate-700/50">
                <!-- Status Indicator Strip -->
                <div class="w-1.5 {{ $isPending ? 'bg-yellow-500' : 'bg-emerald-500' }}"></div>

                <div class="flex-1 flex flex-col sm:flex-row justify-between items-center px-5 py-3 bg-slate-900/40 gap-3">
                    <!-- Reporter Info -->
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user-shield text-slate-400 text-xs"></i>
                        </div>
                        <div class="text-xs">
                            <span class="text-slate-400 block">Người báo cáo</span>
                            <span class="font-bold text-slate-200">{{ $r->reporter?->name ?? 'Ẩn danh' }}</span>
                        </div>
                    </div>

                    <!-- Meta Info -->
                    <div class="flex items-center gap-4 text-xs text-slate-500 font-mono-tech">
                        <span class="flex items-center gap-1.5" title="Thời gian báo cáo">
                            <i class="far fa-clock"></i> {{ $r->created_at->format('H:i d/m/Y') }}
                        </span>
                        <span class="px-2.5 py-1 rounded-md font-bold uppercase tracking-wider text-[10px]
                                {{ $isPending ? 'bg-yellow-500/10 text-yellow-500 ring-1 ring-yellow-500/20' : 'bg-emerald-500/10 text-emerald-500 ring-1 ring-emerald-500/20' }}">
                            {{ ucfirst($r->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 lg:grid-cols-12 gap-8">

                <!-- Main Content -->
                <div class="lg:col-span-8 space-y-5">

                    <!-- Context Info -->
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-slate-500 text-xs font-bold uppercase bg-slate-700/30 px-2 py-1 rounded">Tại truyện</span>
                        <a href="{{ $jumpUrl }}" target="_blank" class="font-bold text-blue-400 hover:text-blue-300 hover:underline truncate transition-colors flex items-center gap-1">
                            {{ $r->comic?->title ?? 'Truyện không tồn tại' }}
                            <i class="fas fa-external-link-alt text-[10px] opacity-70"></i>
                        </a>
                    </div>

                    <!-- Evidence / Comment Block -->
                    <div class="relative">
                        <!-- Label -->
                        <div class="absolute -top-3 left-4">
                            <span class="bg-slate-800 text-rose-400 text-[10px] font-bold px-2 py-0.5 border border-rose-500/30 rounded uppercase tracking-wide">
                                Nội dung vi phạm
                            </span>
                        </div>

                        <div class="bg-slate-900/60 rounded-xl border border-slate-700/70 p-5 mt-2 flex gap-4 items-start hover:bg-slate-900/80 transition-colors">
                            <!-- User Avatar -->
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($r->comment?->user?->name ?? 'Unknown') }}&background=random&color=fff"
                                class="w-10 h-10 rounded-full border-2 border-slate-700 shadow-sm flex-shrink-0 mt-1 opacity-80">

                            <div class="flex-1 min-w-0">
                                <!-- User Name Header -->
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-200 text-sm">
                                            {{ $r->comment?->user?->name ?? 'Người dùng đã xóa' }}
                                        </span>
                                        @if($r->comment?->parent_id)
                                        <span class="text-[10px] bg-slate-700 text-slate-400 px-1.5 py-0.5 rounded">Trả lời</span>
                                        @endif
                                    </div>
                                    @if(!$r->comment)
                                    <span class="text-[10px] text-red-500 italic flex items-center gap-1">
                                        <i class="fas fa-ban"></i> Đã bị xóa
                                    </span>
                                    @endif
                                </div>

                                <!-- Comment Text -->
                                <div class="text-slate-300 text-sm leading-relaxed whitespace-pre-wrap font-serif italic border-l-2 border-slate-700 pl-3">
                                    "{{ $r->comment?->content ?? 'Nội dung này không còn tồn tại trên hệ thống.' }}"
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Panel -->
                <div class="lg:col-span-4 flex flex-col justify-center space-y-3 border-t lg:border-t-0 lg:border-l border-slate-700 pt-5 lg:pt-0 lg:pl-8">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1 text-center lg:text-left">Xử lý</p>

                    <!-- Delete Button -->
                    @if($r->comment)
                    <form method="POST" action="{{ route('comments.destroy', $r->comment->id) }}"
                        onsubmit="return confirm('Xác nhận xoá bình luận vi phạm này?');" class="w-full">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="group relative w-full flex items-center justify-between px-4 py-3 rounded-lg bg-slate-800 hover:bg-rose-900/30 text-slate-400 hover:text-rose-400 border border-slate-600 hover:border-rose-500/50 transition-all duration-200 overflow-hidden">
                            <div class="flex flex-col items-start relative z-10">
                                <span class="text-xs font-bold uppercase tracking-wide">Xoá bình luận</span>
                                <span class="text-[10px] opacity-60 font-normal">Gỡ bỏ nội dung</span>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-slate-700/50 group-hover:bg-rose-500/20 flex items-center justify-center transition-colors relative z-10">
                                <i class="fas fa-trash-alt text-sm group-hover:scale-110 transition-transform"></i>
                            </div>
                        </button>
                    </form>
                    @else
                    <div class="w-full px-4 py-3 rounded-lg bg-slate-800/50 border border-slate-700 border-dashed text-slate-500 text-xs text-center italic cursor-not-allowed">
                        <i class="fas fa-check mr-1"></i> Nội dung đã bị gỡ bỏ
                    </div>
                    @endif

                    <!-- Ban User Button -->
                    @if($r->comment?->user)
                    <form method="POST" action="{{ route('admin.users.toggle-active', $r->comment->user->id) }}"
                        onsubmit="return confirm('Xác nhận khoá tài khoản người dùng này?');" class="w-full">
                        @csrf
                        <button type="submit" class="group w-full flex items-center justify-between px-4 py-3 rounded-lg bg-slate-800 hover:bg-amber-900/30 text-slate-400 hover:text-amber-400 border border-slate-600 hover:border-amber-500/50 transition-all duration-200">
                            <div class="flex flex-col items-start">
                                <span class="text-xs font-bold uppercase tracking-wide">Khoá tài khoản</span>
                                <span class="text-[10px] opacity-60 font-normal">Cấm người đăng</span>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-slate-700/50 group-hover:bg-amber-500/20 flex items-center justify-center transition-colors">
                                <i class="fas fa-user-slash text-sm group-hover:scale-110 transition-transform"></i>
                            </div>
                        </button>
                    </form>
                    @endif
                </div>

            </div>
        </div>
        @empty
        <div class="py-20 flex flex-col items-center justify-center bg-slate-800/50 rounded-3xl border border-slate-700 border-dashed">
            <div class="relative mb-6">
                <div class="absolute inset-0 bg-emerald-500/20 rounded-full blur-xl"></div>
                <div class="relative w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center border border-slate-600 shadow-xl">
                    <i class="fas fa-shield-alt text-4xl text-emerald-500"></i>
                </div>
            </div>
            <h3 class="text-xl font-bold text-white mb-2 brand-font tracking-wide">Hệ thống an toàn</h3>
            <p class="text-slate-400 text-sm max-w-xs text-center leading-relaxed">
                Hiện tại không có báo cáo vi phạm nào cần xử lý. Hãy quay lại sau!
            </p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($reports->hasPages())
    <div class="mt-8 bg-slate-800 px-6 py-4 border border-slate-700 rounded-2xl shadow-lg flex justify-center">
        {{ $reports->links() }}
    </div>
    @endif
</div>
@endsection