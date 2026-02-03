<div class="divide-y divide-gray-100">
    @forelse($notifications as $notify)

    {{-- 1. ĐỔI THẺ A THÀNH DIV (CONTAINER CHÍNH) --}}
    <div class="group relative block px-5 py-4 hover:bg-gray-50 transition-all duration-200 
                {{ $notify->read_at ? 'bg-white' : 'bg-blue-50/40' }}">

        {{-- 2. TẠO LỚP LINK NỀN (Z-INDEX THẤP NHẤT: 0) --}}
        {{-- Thẻ này phủ kín container, chịu trách nhiệm chuyển trang --}}
        <a href="{{ route('notifications.read', $notify->id) }}" class="absolute inset-0 z-0"></a>

        {{-- 3. NỘI DUNG (Z-INDEX: 10) --}}
        {{-- Thêm pointer-events-none để click xuyên qua nội dung xuống thẻ a nền --}}
        <div class="flex gap-4 relative z-10 pointer-events-none">

            {{-- AVATAR --}}
            <div class="flex-shrink-0 relative">
                <img class="h-11 w-11 rounded-full object-cover border border-gray-200 shadow-sm"
                    src="{{ $notify->data['image'] }}" alt="Icon">

                @if(!$notify->read_at)
                <span class="absolute -top-1 -right-1 flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-50"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500 border-2 border-white"></span>
                </span>
                @endif
            </div>

            {{-- TEXT CONTENT --}}
            <div class="flex-1 min-w-0 pr-6">
                <div class="mb-0.5">
                    <span class="text-sm font-semibold text-gray-900">
                        {{ $notify->data['title'] }}
                    </span>
                    <span class="text-xs text-gray-400 mx-1">•</span>
                    <span class="text-xs text-gray-500">
                        {{ $notify->created_at->diffForHumans() }}
                    </span>
                </div>

                <div class="text-sm line-clamp-2 {{ $notify->read_at ? 'text-gray-500 font-normal' : 'text-gray-800 font-medium' }}">
                    {!! \Illuminate\Support\Str::markdown($notify->data['message']) !!}
                </div>
            </div>
        </div>

        {{-- 4. MENU & NÚT BẤM (Z-INDEX: 20 - CAO NHẤT) --}}
        {{-- Thêm pointer-events-auto để chặn click xuyên qua, giúp nút bấm hoạt động --}}
        <div class="absolute right-4 top-4 notif-actions z-20 pointer-events-auto">

            {{-- Nút ba chấm --}}
            <button type="button"
                class="h-8 w-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-200/50 transition-all 
                       opacity-0 group-hover:opacity-100 focus:opacity-100 md:opacity-0 opacity-100"
                onclick="
                    // Logic JS giữ nguyên
                    document.querySelectorAll('[id^=notif-menu-]').forEach(el => { 
                        if(el.id !== 'notif-menu-{{ $notify->id }}') el.classList.add('hidden') 
                    });
                    const m = document.getElementById('notif-menu-{{ $notify->id }}'); 
                    if (m) { m.classList.toggle('hidden'); }
                ">
                <i class="fas fa-ellipsis-h text-sm"></i>
            </button>

            {{-- Menu Dropdown (Z-Index 50 để nổi lên trên cùng) --}}
            <div id="notif-menu-{{ $notify->id }}"
                class="hidden absolute right-0 top-9 w-56 bg-white rounded-xl shadow-2xl ring-1 ring-black ring-opacity-5 z-50 transform origin-top-right transition-all">

                <div class="py-2">
                    @if(!$notify->read_at)
                    <form method="POST" action="{{ route('notifications.markOne', $notify->id) }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition-colors flex items-center gap-3">
                            <span class="w-5 flex justify-center"><i class="fas fa-check text-xs"></i></span>
                            <span class="font-medium">Đánh dấu đã đọc</span>
                        </button>
                    </form>
                    @endif

                    <form method="POST" action="{{ route('notifications.destroy', $notify->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors flex items-center gap-3">
                            <span class="w-5 flex justify-center"><i class="fas fa-trash-alt text-xs"></i></span>
                            <span class="font-medium">Gỡ thông báo này</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
    @empty
    <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
        <div class="bg-gray-50 rounded-full p-4 mb-3">
            <i class="fas fa-bell-slash text-2xl text-gray-400"></i>
        </div>
        <p class="text-gray-900 font-medium">Không có thông báo mới</p>
        <p class="text-gray-500 text-sm mt-1">Chúng tôi sẽ báo cho bạn khi có cập nhật.</p>
    </div>
    @endforelse
</div>