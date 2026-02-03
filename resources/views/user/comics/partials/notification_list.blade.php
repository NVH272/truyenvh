@forelse($notifications as $notify)
<a href="{{ route('notifications.read', $notify->id) }}"
    class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition duration-150 {{ $notify->read_at ? 'opacity-60' : 'bg-blue-50/50' }}">
    <div class="flex items-start">
        <img class="h-10 w-10 rounded-full object-cover mr-3 border" src="{{ $notify->data['image'] }}" alt="Icon">
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-gray-900 truncate">
                {{ $notify->data['title'] }}
            </p>
            {{-- Dùng {!! !!} để render thẻ in đậm trong message --}}
            <p class="text-sm text-gray-600 line-clamp-2">
                {!! \Illuminate\Support\Str::markdown($notify->data['message']) !!}
            </p>
            <p class="text-xs text-gray-400 mt-1">
                {{ $notify->created_at->diffForHumans() }}
            </p>
        </div>
        @if(!$notify->read_at)
        <span class="inline-block w-2 h-2 bg-blue-600 rounded-full mt-2"></span>
        @endif
    </div>
</a>
@empty
<div class="px-4 py-6 text-center text-gray-500">
    <i class="fas fa-bell-slash text-2xl mb-2 opacity-50"></i>
    <p class="text-sm">Không có thông báo nào.</p>
</div>
@endforelse