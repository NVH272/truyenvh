@forelse($messages as $msg)
<div class="d-flex mb-3 {{ $msg->sender_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">

    {{-- Nếu không phải mình thì hiện avatar --}}
    @if($msg->sender_id != auth()->id())
    <img src="https://ui-avatars.com/api/?name={{ urlencode($msg->sender->name) }}&background=E4E6EB&color=000"
        class="rounded-circle me-2" width="32" height="32" alt="avatar">
    @endif

    <div class="chat-bubble {{ $msg->sender_id == auth()->id() ? 'me' : 'them' }}">
        {{ $msg->message }}
    </div>
</div>
@empty
<p class="text-muted text-center">Chưa có tin nhắn nào.</p>
@endforelse
<style>
    .chat-bubble {
        padding: 10px 15px;
        border-radius: 20px;
        max-width: 70%;
        font-size: 15px;
        line-height: 1.4;
        display: inline-block;
    }

    .chat-bubble.me {
        background-color: #0084FF;
        /* xanh Messenger */
        color: white;
        border-bottom-right-radius: 5px;
    }

    .chat-bubble.them {
        background-color: #E4E6EB;
        /* xám Messenger */
        color: black;
        border-bottom-left-radius: 5px;
    }
</style>