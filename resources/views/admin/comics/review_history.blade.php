@extends('layouts.admin')

@section('title', 'Lịch sử duyệt truyện')
@section('header', 'Quản lý Truyện')

@section('content')
<div class="min-h-screen bg-slate-900 p-6">
    <div class="max-w-6xl mx-auto space-y-8">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Lịch sử duyệt truyện</h2>
                <p class="text-slate-400 text-sm mt-1">
                    Danh sách các truyện đã được <span class="text-emerald-400 font-semibold">phê duyệt</span> hoặc
                    <span class="text-red-400 font-semibold">từ chối</span>.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.comics.pending') }}"
                    class="px-4 py-2 text-sm font-medium text-slate-300 bg-slate-800 border border-slate-700 rounded-lg hover:bg-slate-700 hover:text-white transition-all shadow-sm flex items-center gap-2">
                    <i class="fas fa-clock-rotate-left"></i>
                    <span>Quay lại trang duyệt</span>
                </a>
                <a href="{{ route('admin.comics.index') }}"
                    class="px-4 py-2 text-sm font-medium text-slate-300 bg-slate-800 border border-slate-700 rounded-lg hover:bg-slate-700 hover:text-white transition-all shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Trang quản lý
                </a>
            </div>
        </div>

        {{-- Bảng lịch sử --}}
        @if($comics->isEmpty())
        <div class="bg-slate-800/50 border border-slate-700/50 border-dashed rounded-3xl p-10 text-center">
            <h3 class="text-white font-bold text-lg mb-2">Chưa có lịch sử duyệt nào</h3>
            <p class="text-slate-400 text-sm max-w-sm mx-auto">
                Khi bạn phê duyệt hoặc từ chối truyện, chúng sẽ xuất hiện tại đây.
            </p>
        </div>
        @else
        <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden shadow-xl">
            <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
                <h3 class="text-slate-100 font-semibold text-sm uppercase tracking-wide">
                    Tổng cộng: <span class="text-blue-400">{{ $comics->total() }}</span> truyện
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-slate-200">
                    <thead class="bg-slate-900/80 text-xs uppercase text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Truyện</th>
                            <th class="px-4 py-3 text-left">Người đăng</th>
                            <th class="px-4 py-3 text-left">Trạng thái</th>
                            <th class="px-4 py-3 text-left">Người duyệt</th>
                            <th class="px-4 py-3 text-left">Thời gian</th>
                            <th class="px-4 py-3 text-left w-72">Lý do từ chối</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/60">
                        @foreach($comics as $comic)
                        <tr class="hover:bg-slate-700/40 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-14 rounded bg-slate-900 overflow-hidden flex-shrink-0">
                                        <img src="{{ $comic->cover_url }}" class="w-full h-full object-cover" alt="{{ $comic->title }}">
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-100 line-clamp-1">
                                            {{ $comic->title }}
                                        </div>
                                        @if($comic->categories->isNotEmpty())
                                        <div class="flex flex-wrap gap-1 mt-1 text-[10px] text-slate-400">
                                            @foreach($comic->categories->take(3) as $cat)
                                            <span class="px-1.5 py-0.5 rounded bg-slate-900/80 border border-slate-600/60">
                                                {{ $cat->name }}
                                            </span>
                                            @endforeach
                                            @if($comic->categories->count() > 3)
                                            <span class="text-slate-500">+{{ $comic->categories->count() - 3 }}</span>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-slate-100">{{ $comic->creator->name ?? 'N/A' }}</div>
                                <div class="text-[11px] text-slate-500">ID: {{ $comic->creator->id ?? '—' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @if($comic->approval_status === 'approved')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-300 border border-emerald-500/40">
                                    <i class="fas fa-check-circle mr-1"></i> Đã duyệt
                                </span>
                                @elseif($comic->approval_status === 'rejected')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-semibold bg-red-500/10 text-red-300 border border-red-500/40">
                                    <i class="fas fa-times-circle mr-1"></i> Đã từ chối
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-semibold bg-slate-600/40 text-slate-200">
                                    Khác
                                </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-slate-100">{{ $comic->approver->name ?? 'N/A' }}</div>
                                <div class="text-[11px] text-slate-500">
                                    {{ $comic->approver ? 'ID: '.$comic->approver->id : '' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-300">
                                @if($comic->approved_at)
                                <div>{{ $comic->approved_at->format('H:i d/m/Y') }}</div>
                                <div class="text-[11px] text-slate-500">
                                    {{ $comic->approved_at->diffForHumans() }}
                                </div>
                                @else
                                <span class="text-slate-500 text-xs">Chưa có</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-300">
                                @if($comic->approval_status === 'rejected')
                                <span class="text-xs text-slate-300">
                                    {{ $comic->rejection_reason ?: 'Không có lý do' }}
                                </span>
                                @else
                                <span class="text-xs text-slate-500">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-slate-700">
                {{ $comics->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection