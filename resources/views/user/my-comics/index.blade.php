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
        <a href="{{ route('user.my-comics.create') }}"
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
        <a href="{{ route('user.my-comics.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5">
            <i class="fas fa-magic mr-2"></i> Đăng truyện ngay
        </a>
    </div>
    @else

    {{-- Grid Container --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-8 gap-x-4 gap-y-6">
        @foreach($comics as $comic)
        <div class="group relative bg-transparent rounded-md overflow-hidden transition-all duration-300 hover:-translate-y-1">

            {{-- *** LINK PHỦ TOÀN BỘ THẺ (Stretched Link) *** --}}
            {{-- Thay route('comics.show', $comic->slug) bằng route thực tế trang đọc truyện của bạn --}}
            {{-- Ví dụ: route('comics.show') hoặc link chi tiết --}}
            <a href="{{ route('user.comics.show', $comic->slug) }}" class="absolute inset-0 z-10" aria-hidden="true">

                {{-- Cover Image Wrapper --}}
                <div class="relative aspect-[2/3] rounded-md overflow-hidden shadow-md border border-slate-700/50 group-hover:shadow-lg group-hover:border-blue-500/50 transition-all">

                    {{-- Ảnh bìa --}}
                    <div class="block w-full h-full">
                        <img src="{{ $comic->cover_url }}" alt="{{ $comic->title }}"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    </div>

                    {{-- Badge: Status (Top Left) --}}
                    <div class="absolute top-1.5 left-1.5 pointer-events-none z-20">
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
                        <a href="{{ route('user.my-comics.edit', $comic->id) }}" class="w-8 h-8 rounded-full bg-white text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition shadow-lg relative z-40" title="Sửa">
                            <i class="fas fa-pen text-xs"></i>
                        </a>

                        <button type="button" onclick="openTransferModal({{ $comic->id }}, '{{ addslashes($comic->title) }}')" class="w-8 h-8 rounded-full bg-white text-emerald-600 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition shadow-lg relative z-40" title="Chuyển quyền quản lý">
                            <i class="fas fa-exchange-alt text-xs"></i>
                        </button>

                        <form action="{{ route('user.my-comics.destroy', $comic->id) }}" method="POST"
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
                <div class="mt-2 space-y-0.5 relative z-20"> {{-- z-20 để text nổi lên trên (nếu cần select text) --}}
                    {{-- Title --}}
                    <h3 class="text-[13px] font-bold text-slate-800 dark:text-slate-200 line-clamp-2 leading-snug group-hover:text-blue-600 transition-colors" title="{{ $comic->title }}">
                        {{-- Link ở đây không cần thiết nữa vì đã có stretched link phủ toàn bộ thẻ, nhưng giữ lại để chuẩn semantic --}}
                        <span class="block">
                            {{ $comic->title }}
                        </span>
                    </h3>

                    {{-- Chapter count (dưới tên truyện) --}}
                    <div class="text-[11px] text-slate-500 -mt-0.5">
                        {{ $comic->chapter_count ?? 0 }} chương
                    </div>

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
                    <div class="flex items-center gap-0.5 text-[10px]">
                        <x-rating-stars :rating="$comic->rating ?? 0" />
                        <span class="text-slate-500 ml-1">({{ number_format($comic->rating ?? 0, 1) }})</span>
                    </div>

                    {{-- Author --}}
                    <div class="text-[11px] text-slate-500 truncate" title="Tác giả">
                        <i class="fas fa-user-edit text-[9px] mr-1"></i> {{ $comic->authors_list ?? 'Đang cập nhật' }}
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    {{-- Pagination (Thiết kế mới: Căn giữa, padding rộng) --}}
    <div class="mt-12 flex justify-center border-t border-slate-200 pt-8 pb-8">
        {{-- Truyền tên file custom vào hàm links() --}}
        {{ $comics->links('vendor.pagination.custom') }}
    </div>

    @endif
</div>
@push('modals')
{{-- ============================================= --}}
{{-- MODAL CHUYỂN NHƯỢNG QUYỀN QUẢN LÝ (ĐẶT Ở ĐÂY) --}}
{{-- ============================================= --}}
<div id="transferModal" class="fixed top-0 left-0 w-full h-full z-[9999] hidden items-center justify-center bg-slate-900/40 backdrop-blur-[3px] opacity-0 transition-opacity duration-300 pointer-events-none">

    {{-- pointer-events-auto để phần nội dung bên trong vẫn click được --}}
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform scale-95 transition-transform duration-300 relative overflow-y-auto max-h-[90vh] pointer-events-auto" id="transferModalContent">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-lg font-extrabold text-slate-800 flex items-center gap-2">
                <i class="fas fa-exchange-alt text-emerald-500"></i>
                Chuyển Nhượng Truyện
            </h3>
            <button type="button" onclick="closeTransferModal()" class="text-slate-400 hover:text-rose-500 transition-colors focus:outline-none">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        {{-- Form --}}
        <form id="transferForm" method="POST" action="">
            @csrf
            @method('PATCH')
            <div class="p-6 space-y-4">
                <div class="p-3 bg-blue-50 text-blue-800 text-sm rounded-lg border border-blue-100">
                    Bạn đang chuyển nhượng tác phẩm: <br>
                    <strong id="transferComicTitle" class="text-blue-900 text-base mt-1 block"></strong>
                </div>

                <div class="space-y-2">
                    <label for="new_owner_id" class="block text-sm font-bold text-slate-700">
                        Chọn người đăng mới <span class="text-rose-500">*</span>
                    </label>
                    <select name="new_owner_id" id="new_owner_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm outline-none transition-all bg-slate-50 focus:bg-white cursor-pointer">
                        <option value="" hidden>-- Chọn Admin / Poster khác --</option>
                        @foreach($eligibleUsers as $u)
                        <option value="{{ $u->id }}">
                            {{ $u->role === 'admin' ? 'Admin' : 'Poster' }}: {{ $u->name }} - {{ $u->email }}
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 italic mt-1">
                        <i class="fas fa-info-circle mr-1"></i> Sau khi chuyển, bạn sẽ mất quyền đăng bộ truyện này.
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="closeTransferModal()" class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 transition-colors">
                    Hủy bỏ
                </button>
                <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn mất quyền quản lý bộ truyện này không?')" class="px-5 py-2 rounded-xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-500/30 transition-all hover:-translate-y-0.5">
                    Xác nhận chuyển
                </button>
            </div>
        </form>
    </div>
</div>
@endpush

{{-- Javascript điều khiển Modal --}}
@push('scripts')
<script>
    function openTransferModal(comicId, comicTitle) {
        const modal = document.getElementById('transferModal');
        const modalContent = document.getElementById('transferModalContent');
        const form = document.getElementById('transferForm');
        const titleSpan = document.getElementById('transferComicTitle');

        // Set tên truyện vào modal
        titleSpan.innerText = comicTitle;

        // Set URL action cho form
        const baseUrl = "{{ route('user.my-comics.transfer', ':id') }}";
        form.action = baseUrl.replace(':id', comicId);

        // Bật pointer-events để modal chặn click xuống dưới khi mở
        modal.classList.remove('pointer-events-none');

        // Hiệu ứng mở
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }, 10);
    }

    function closeTransferModal() {
        const modal = document.getElementById('transferModal');
        const modalContent = document.getElementById('transferModalContent');

        // Tắt pointer-events
        modal.classList.add('pointer-events-none');

        // Hiệu ứng đóng
        modal.classList.add('opacity-0');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('transferForm').reset(); // reset form
        }, 300);
    }
</script>
@endpush

@endsection