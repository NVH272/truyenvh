{{-- resources/views/user/live_chat/chat_content.blade.php --}}

@if(isset($receiver))
<div data-receiver-id="{{ $receiver->id }}" style="display: none;"></div>
@endif

@php
$lastSenderId = null;
@endphp

@foreach($messages as $index => $msg)
@php
// Kiểm tra xem tin nhắn tiếp theo có phải của cùng người gửi không
$nextMsg = $messages[$index + 1] ?? null;
$isLastInSequence = !$nextMsg || $nextMsg->sender_id != $msg->sender_id;

$isMe = $msg->sender_id == auth()->id();
@endphp

<div class="message-wrapper {{ $isMe ? 'sent' : 'received' }}" style="{{ !$isLastInSequence ? 'margin-bottom: 2px;' : 'margin-bottom: 8px;' }}">

    {{-- Avatar chỉ hiện nếu: 
             1. Là tin nhắn nhận (của người khác)
             2. VÀ là tin cuối cùng trong chuỗi liên tiếp (isLastInSequence) --}}
    @if(!$isMe)
    <div style="width: 28px; margin-right: 8px;">
        @if($isLastInSequence)
        <img src="{{ $msg->sender->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($msg->sender->name) }}"
            alt="{{ $msg->sender->name }}"
            class="message-avatar">
        @endif
    </div>
    @endif

    <div class="message-bubble {{ $msg->sender_id == auth()->id() ? 'sent' : 'received' }}">
        {{ $msg->message }}
    </div>
</div>
@endforeach

@if($messages->count() == 0)
<div style="text-align: center; padding: 20px; color: #888; font-size: 13px;">
    <p>Bắt đầu cuộc trò chuyện!</p>
</div>
@endif