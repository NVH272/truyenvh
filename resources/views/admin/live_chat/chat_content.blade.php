{{-- resources/views/admin/live_chat/partials/chat_content.blade.php --}}
<style>
    .chat-header {
        height: 72px;
        min-height: 72px;
        display: flex;
        align-items: center;
        padding: 0 1rem;
        border-bottom: 1px solid var(--sg-border);
        background: rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(5px);
        box-sizing: border-box;
    }
</style>
<div class="chat-header">
    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="chat-header-avatar">
    <div class="chat-header-name">{{ $user->name }}</div>
    <span class="text-xs text-slate-500 ml-auto font-term tracking-widest opacity-50">DIVERGENCE: 1.048596%</span>
</div>

<div class="chat-messages custom-scroll" id="chat-box">
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
    <div class="empty-chat">
        <i class="fas fa-satellite-dish mb-2 text-3xl opacity-50"></i>
        <p>No data packets received yet.</p>
        <p class="text-xs mt-1">Send a D-Mail to start connection.</p>
    </div>
    @endforelse
</div>

<form id="admin-chat-form" action="{{ route('admin.messages.send') }}" method="POST" class="chat-input-area">
    @csrf
    <input type="hidden" name="receiver_id" value="{{ $user->id }}">
    <input type="text" name="message" class="chat-input" placeholder="Enter command..." required autocomplete="off" id="admin-message-input">
    <button type="submit" class="send-button">
        <i class="fas fa-paper-plane"></i>
    </button>
</form>