{{-- resources/views/user/live_chat/chat_content.blade.php --}}

@if(isset($receiver))
<div data-receiver-id="{{ $receiver->id }}" style="display: none;"></div>
@endif

@forelse($messages as $msg)
<div class="message-wrapper {{ $msg->sender_id == auth()->id() ? 'sent' : 'received' }}">
    @if($msg->sender_id != auth()->id())
    <img src="{{ $msg->sender->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($msg->sender->name) }}"
        alt="{{ $msg->sender->name }}"
        class="message-avatar">
    @endif
    <div class="message-bubble {{ $msg->sender_id == auth()->id() ? 'sent' : 'received' }}">
        {{ $msg->message }}
    </div>
</div>
@empty
<div style="text-align: center; padding: 20px; color: #888; font-size: 13px;">
    <p>Bắt đầu cuộc trò chuyện!</p>
</div>
@endforelse