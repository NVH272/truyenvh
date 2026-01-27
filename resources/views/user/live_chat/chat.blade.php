@if(isset($receiver))
<div data-receiver-id="{{ $receiver->id }}" style="display: none;"></div>
@endif

@forelse($messages as $msg)
<div class="message-wrapper {{ $msg->sender_id == auth()->id() ? 'sent' : 'received' }}">
    @if($msg->sender_id != auth()->id())
    <img src="{{ $msg->sender->avatar_url }}" alt="{{ $msg->sender->name }}" class="message-avatar">
    @endif
    <div class="message-bubble {{ $msg->sender_id == auth()->id() ? 'sent' : 'received' }}">
        {{ $msg->message }}
    </div>
</div>
@empty
<div style="text-align: center; padding: 40px; color: #65676b;">
    <p>Chưa có tin nhắn nào. Bắt đầu cuộc trò chuyện!</p>
</div>
@endforelse

<style>
    .message-wrapper {
        display: flex;
        margin-bottom: 8px;
        align-items: flex-end;
    }

    .message-wrapper.sent {
        justify-content: flex-end;
    }

    .message-wrapper.received {
        justify-content: flex-start;
    }

    .message-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        margin: 0 8px;
        object-fit: cover;
    }

    .message-bubble {
        max-width: 65%;
        padding: 8px 12px;
        border-radius: 18px;
        font-size: 15px;
        line-height: 1.4;
        word-wrap: break-word;
    }

    .message-bubble.sent {
        background: #0084ff;
        color: white;
        border-bottom-right-radius: 4px;
    }

    .message-bubble.received {
        background: #e4e6eb;
        color: #050505;
        border-bottom-left-radius: 4px;
    }
</style>