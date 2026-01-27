@extends('layouts.admin')

@section('title', 'D-Mail System - Connection Established')
@section('header', 'D-Mail System')

@section('content')
<style>
    /* === STEINS;GATE THEME (TRANSPARENT VERSION) === */
    :root {
        --sg-bg-dark: transparent;
        --sg-bg-panel: rgba(0, 0, 0, 0.2);
        --sg-border: #334155;
        --sg-text-main: #cbd5e1;
        --sg-text-dim: #64748b;
        --sg-accent: #ea580c;
        --sg-accent-glow: #fb923c;
    }

    .font-tech {
        font-family: 'Share Tech Mono', monospace;
    }

    .font-term {
        font-family: 'Courier New', Courier, monospace;
    }

    /* CONTAINER CHÍNH */
    .messenger-container {
        display: flex;
        height: calc(100vh - 180px);
        min-height: 600px;
        background: var(--sg-bg-dark);
        border: none;
        box-shadow: none;
        overflow: hidden;
        position: relative;
    }

    /* Hiệu ứng Scanline mờ */
    .messenger-container::after {
        content: " ";
        display: block;
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background: linear-gradient(rgba(15, 23, 42, 0) 50%, rgba(0, 0, 0, 0.2) 50%);
        background-size: 100% 4px;
        z-index: 50;
        pointer-events: none;
        opacity: 0.3;
    }

    /* Hiệu ứng Vignette */
    .messenger-container::before {
        content: " ";
        display: block;
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background: radial-gradient(circle, rgba(0, 0, 0, 0) 60%, rgba(0, 0, 0, 0.6) 100%);
        z-index: 50;
        pointer-events: none;
    }

    /* SIDEBAR */
    .messenger-sidebar {
        width: 300px;
        background: var(--sg-bg-panel);
        border-right: 1px solid var(--sg-border);
        display: flex;
        flex-direction: column;
        z-index: 60;
        backdrop-filter: blur(5px);
    }

    .messenger-header {
        background: transparent;
        color: var(--sg-accent);
        padding: 1rem;
        font-size: 1.1rem;
        border-bottom: 1px solid var(--sg-border);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Nút Back Style Steins;Gate */
    .back-button {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--sg-border);
        color: var(--sg-text-main);
        width: 32px;
        height: 32px;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        transition: all 0.2s;
    }

    .back-button:hover {
        background: var(--sg-accent);
        border-color: var(--sg-accent);
        color: #fff;
        box-shadow: 0 0 10px var(--sg-accent);
    }

    /* DANH SÁCH CHAT */
    .conversation-list {
        flex: 1;
        overflow-y: auto;
    }

    .conversation-list::-webkit-scrollbar {
        width: 4px;
    }

    .conversation-list::-webkit-scrollbar-track {
        background: transparent;
    }

    .conversation-list::-webkit-scrollbar-thumb {
        background: #475569;
        border-radius: 2px;
    }

    .section-title {
        padding: 0.75rem 1rem;
        font-size: 0.7rem;
        font-weight: bold;
        color: var(--sg-text-dim);
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.03);
    }

    .conversation-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.03);
        cursor: pointer;
        transition: all 0.2s ease;
        color: var(--sg-text-main);
        position: relative;
    }

    .conversation-item:hover {
        background: rgba(234, 88, 12, 0.08);
        padding-left: 1.25rem;
    }

    .conversation-item:hover::before {
        content: ">";
        position: absolute;
        left: 0.25rem;
        color: var(--sg-accent);
        font-weight: bold;
        font-family: 'Share Tech Mono', monospace;
    }

    .conversation-item.active {
        background: rgba(234, 88, 12, 0.15);
        border-left: 2px solid var(--sg-accent);
    }

    .conversation-avatar {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 4px;
        margin-right: 0.75rem;
        object-fit: cover;
        border: 1px solid var(--sg-border);
        filter: grayscale(30%) contrast(1.1);
    }

    .conversation-name {
        font-size: 0.9rem;
        font-weight: 600;
        color: #e2e8f0;
    }

    .conversation-preview {
        font-size: 0.75rem;
        color: var(--sg-text-dim);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* CHAT AREA */
    .chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: transparent;
        z-index: 60;
        position: relative;
    }

    .chat-header {
        background: rgba(0, 0, 0, 0.2);
        /* Tối hơn chút để tách biệt */
        padding: 0 1rem;
        height: 60px;
        border-bottom: 1px solid var(--sg-border);
        display: flex;
        align-items: center;
        backdrop-filter: blur(5px);
    }

    .chat-header-avatar {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 4px;
        margin-right: 0.75rem;
        object-fit: cover;
        border: 1px solid var(--sg-border);
    }

    .chat-header-name {
        font-weight: 600;
        font-size: 1.1rem;
        color: var(--sg-accent-glow);
        text-shadow: 0 0 5px rgba(234, 88, 12, 0.3);
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
        background: transparent;
    }

    .chat-messages::-webkit-scrollbar {
        width: 4px;
    }

    .chat-messages::-webkit-scrollbar-track {
        background: transparent;
    }

    .chat-messages::-webkit-scrollbar-thumb {
        background: #475569;
        border-radius: 2px;
    }

    .message-wrapper {
        display: flex;
        margin-bottom: 1rem;
        align-items: flex-end;
    }

    .message-wrapper.sent {
        justify-content: flex-end;
    }

    .message-wrapper.received {
        justify-content: flex-start;
    }

    .message-avatar {
        width: 2rem;
        height: 2rem;
        border-radius: 4px;
        margin: 0 0.5rem;
        object-fit: cover;
        border: 1px solid var(--sg-border);
    }

    .message-bubble {
        max-width: 70%;
        padding: 0.75rem 1rem;
        border-radius: 4px;
        font-size: 0.9rem;
        line-height: 1.4;
        word-wrap: break-word;
        position: relative;
        font-family: 'Courier New', Courier, monospace;
    }

    /* Bong bóng gửi đi: Màu cam đậm đặc trưng */
    .message-bubble.sent {
        background: rgba(234, 88, 12, 0.2);
        border: 1px solid var(--sg-accent);
        color: #fff;
        border-bottom-right-radius: 0;
        box-shadow: 0 0 10px rgba(234, 88, 12, 0.1);
    }

    /* Bong bóng nhận: Màu xám trong suốt */
    .message-bubble.received {
        background: rgba(51, 65, 85, 0.4);
        /* Slate-700 opacity */
        border: 1px solid var(--sg-border);
        color: #e2e8f0;
        border-bottom-left-radius: 0;
    }

    .chat-input-area {
        background: rgba(0, 0, 0, 0.2);
        padding: 1rem;
        border-top: 1px solid var(--sg-border);
        display: flex;
        align-items: center;
        gap: 1rem;
        backdrop-filter: blur(5px);
    }

    .chat-input {
        flex: 1;
        border: 1px solid var(--sg-border);
        outline: none;
        padding: 0.75rem 1rem;
        background: rgba(15, 23, 42, 0.6);
        /* Nền nhập liệu tối */
        border-radius: 4px;
        font-size: 0.95rem;
        color: #fff;
        font-family: 'Courier New', Courier, monospace;
        transition: all 0.2s;
    }

    .chat-input:focus {
        border-color: var(--sg-accent);
        box-shadow: 0 0 10px rgba(234, 88, 12, 0.2);
    }

    .chat-input::placeholder {
        color: var(--sg-text-dim);
    }

    .send-button {
        width: 40px;
        height: 40px;
        background: rgba(234, 88, 12, 0.1);
        color: var(--sg-accent);
        border: 1px solid var(--sg-accent);
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .send-button:hover {
        background: var(--sg-accent);
        color: #fff;
        box-shadow: 0 0 15px var(--sg-accent);
    }

    .empty-chat {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: var(--sg-text-dim);
        font-family: 'Share Tech Mono', monospace;
    }
</style>

<div class="messenger-container font-tech">
    <div class="messenger-sidebar">
        <div class="messenger-header">
            <a href="{{ route('admin.messages.index') }}" class="back-button" title="Return to Index">
                <i class="fas fa-arrow-left"></i>
            </a>
            <span style="text-shadow: 0 0 5px rgba(234, 88, 12, 0.5);">Timeline Log</span>
        </div>

        <div class="conversation-list custom-scroll">
            @if(isset($users) && isset($admins) && ($users->count() > 0 || $admins->count() > 0))
            @if($users->count() > 0)
            <div class="section-title">Lab Members</div>
            @foreach($users as $u)
            <a href="{{ route('admin.messages.chat', $u->id) }}" class="conversation-item text-decoration-none {{ $u->id == $user->id ? 'active' : '' }}" style="color: inherit;">
                <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="conversation-avatar">
                <div class="conversation-info">
                    <div class="conversation-name">{{ $u->name }}</div>
                    <div class="conversation-preview font-term">{{ $u->email }}</div>
                </div>
            </a>
            @endforeach
            @endif

            @if($admins->count() > 0)
            <div class="section-title">Round Table</div>
            @foreach($admins as $a)
            <a href="{{ route('admin.messages.chat', $a->id) }}" class="conversation-item text-decoration-none {{ $a->id == $user->id ? 'active' : '' }}" style="color: inherit;">
                <img src="{{ $a->avatar_url }}" alt="{{ $a->name }}" class="conversation-avatar">
                <div class="conversation-info">
                    <div class="conversation-name">{{ $a->name }}</div>
                    <div class="conversation-preview font-term">{{ $a->email }}</div>
                </div>
            </a>
            @endforeach
            @endif
            @else
            <div class="p-8 text-center text-slate-500">
                <p>No other signals found.</p>
            </div>
            @endif
        </div>
    </div>

    <div class="chat-area">
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
    </div>
</div>

<script>
    document.getElementById('admin-chat-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const csrfToken = document.querySelector('input[name="_token"]').value;
        const messageInput = document.getElementById('admin-message-input');
        const messageText = messageInput.value.trim();
        const receiverId = document.querySelector('input[name="receiver_id"]').value;

        if (!messageText) return;

        const sendButton = this.querySelector('.send-button');
        sendButton.disabled = true;
        sendButton.style.opacity = '0.5';

        fetch('{{ route("admin.messages.send") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    receiver_id: receiverId,
                    message: messageText
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.error || 'Lỗi khi gửi tin nhắn');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    messageInput.value = '';

                    const chatBox = document.getElementById('chat-box');
                    const emptyChat = chatBox.querySelector('.empty-chat');
                    if (emptyChat) {
                        emptyChat.remove();
                    }

                    const messageWrapper = document.createElement('div');
                    messageWrapper.className = 'message-wrapper sent';
                    messageWrapper.innerHTML = `
                <div class="message-bubble sent">
                    ${escapeHtml(messageText)}
                </div>
            `;
                    chatBox.appendChild(messageWrapper);
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Lỗi khi gửi tin nhắn. Vui lòng thử lại.');
            })
            .finally(() => {
                sendButton.disabled = false;
                sendButton.style.opacity = '1';
                messageInput.focus();
            });
    });

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // Auto scroll to bottom on load
    const chatBox = document.getElementById('chat-box');
    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
</script>
@endsection