@extends('layouts.app')

@section('title', 'Quản lý báo cáo lỗi')

@section('content')
<div class="max-w-6xl mx-auto p-4 sm:p-6 lg:py-8 space-y-6">

    {{-- HEADER TRANG --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-red-500 shrink-0">
                    <i class="fas fa-flag text-lg"></i>
                </span>
                Quản lý báo cáo lỗi
            </h1>
            <p class="text-gray-500 text-sm mt-2 sm:ml-13">Theo dõi và khắc phục các vấn đề chapter do độc giả phản hồi.</p>
        </div>

        {{-- Thống kê nhanh --}}
        @php
        $unreadCount = $errorNotifications->whereNull('read_at')->count();
        @endphp
        @if($unreadCount > 0)
        <div class="px-4 py-2 bg-red-50 rounded-lg border border-red-100 flex items-center gap-2">
            <span class="relative flex h-2.5 w-2.5">
                <span class="absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
            </span>
            <span class="text-sm font-semibold text-red-700">{{ $unreadCount }} lỗi cần xử lý</span>
        </div>
        @endif
    </div>

    {{-- KHUNG DANH SÁCH --}}
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 text-gray-500 uppercase text-[11px] font-bold tracking-widest border-b border-gray-200/80">
                    <tr>
                        <th class="px-6 py-4">Chi tiết báo cáo</th>
                        <th class="px-6 py-4 w-32 text-center">Trạng thái</th>
                        <th class="px-6 py-4 w-32 text-center">Thời gian</th>
                        <th class="px-6 py-4 w-40 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($errorNotifications as $notification)
                    <tr class="{{ empty($notification->read_at) ? 'bg-red-50/20' : 'bg-white' }} hover:bg-gray-50/80 transition-colors duration-200">

                        {{-- Cột Nội dung (Đưa lên đầu) --}}
                        <td class="px-6 py-4 whitespace-normal min-w-[300px]">
                            <div class="font-bold text-gray-900 mb-1">
                                <a href="{{ $notification->data['url'] ?? '#' }}" class="hover:text-blue-600 transition-colors underline-offset-4 hover:underline">
                                    {{ $notification->data['title'] ?? 'Báo lỗi hệ thống' }}
                                </a>
                            </div>
                            <div class="text-gray-600 text-sm line-clamp-2 leading-snug">
                                {{ $notification->data['message'] ?? 'Không có nội dung mô tả.' }}
                            </div>
                        </td>

                        {{-- Cột Tình trạng (Chuyển ra giữa và căn giữa) --}}
                        <td class="px-6 py-4 text-center">
                            @if(empty($notification->read_at))
                            <span class="inline-flex items-center justify-center w-full max-w-[90px] px-2.5 py-1 rounded-md text-xs font-semibold bg-red-100 text-red-700">
                                Chưa xử lý
                            </span>
                            @else
                            <span class="inline-flex items-center justify-center w-full max-w-[90px] px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-600">
                                Đã xử lý
                            </span>
                            @endif
                        </td>

                        {{-- Cột Thời gian --}}
                        <td class="px-6 py-4 text-center text-gray-500 text-xs font-medium">
                            {{ $notification->created_at->diffForHumans() }}
                        </td>

                        {{-- Cột Hành động --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">

                                {{-- Nút Xem --}}
                                @php
                                // Xử lý tạo link Đọc truyện an toàn
                                $readUrl = $notification->data['url'] ?? '#';
                                if (isset($notification->data['comic_id']) && isset($notification->data['chapter_id'])) {
                                $errorChapter = \App\Models\Chapter::find($notification->data['chapter_id']);
                                if ($errorChapter) {
                                $readUrl = route('user.comics.chapters.read', [
                                'comic' => $notification->data['comic_id'],
                                'chapter_number' => $errorChapter->chapter_number
                                ]);
                                }
                                }
                                @endphp

                                <a href="{{ $readUrl }}" target="_blank"
                                    class="w-8 h-8 flex items-center justify-center rounded-md text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 transition-colors"
                                    title="Đến trang đọc chapter">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>

                                <a href="{{ isset($notification->data['comic_id']) && isset($notification->data['chapter_id']) ? route('user.comics.chapters.edit', ['comic' => $notification->data['comic_id'], 'chapter' => $notification->data['chapter_id']]) : ($notification->data['url'] ?? '#') }}"
                                    class="w-8 h-8 flex items-center justify-center rounded-md text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                                    title="Đến trang sửa chapter">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                {{-- Nút Đánh dấu đã xử lý --}}
                                @if(empty($notification->read_at))
                                <form action="{{ route('poster.errors.read', $notification->id) }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit"
                                        class="w-8 h-8 flex items-center justify-center rounded-md text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 transition-colors"
                                        title="Đánh dấu đã xử lý">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>
                                @endif

                                {{-- Nút Xóa --}}
                                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="m-0"
                                    onsubmit="return confirm('Xóa báo cáo này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 flex items-center justify-center rounded-md text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                        title="Xóa báo cáo">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    {{-- Trạng thái trống --}}
                    <tr>
                        <td colspan="4" class="px-6 py-20 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-50 mb-4 text-gray-400">
                                <i class="fas fa-check-circle text-3xl text-green-400"></i>
                            </div>
                            <h3 class="text-gray-900 font-bold text-lg mb-1">Mọi thứ đều hoàn hảo!</h3>
                            <p class="text-gray-500 text-sm">Không có báo cáo lỗi nào cần bạn xử lý lúc này.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Phân trang --}}
        @if($errorNotifications->hasPages())
        <div class="px-6 py-4 border-t border-gray-200/80 bg-gray-50/50">
            {{ $errorNotifications->links() }}
        </div>
        @endif
    </div>
</div>
@endsection