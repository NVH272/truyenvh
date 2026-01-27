@extends('layouts.app')

@section('content')
<style>
    /* Facebook Messenger Style */
    .messenger-container {
        display: flex;
        height: calc(100vh - 200px);
        max-height: 800px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .messenger-sidebar {
        width: 350px;
        background: #f0f2f5;
        border-right: 1px solid #e4e6eb;
        display: flex;
        flex-direction: column;
    }

    .messenger-header {
        background: #0084ff;
        color: white;
        padding: 16px;
        font-weight: 600;
        font-size: 20px;
    }

    .conversation-list {
        flex: 1;
        overflow-y: auto;
        padding: 8px;
    }

    .conversation-item {
        display: flex;
        align-items: center;
        padding: 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.2s;
        margin-bottom: 4px;
    }

    .conversation-item:hover {
        background: #e4e6eb;
    }

    .conversation-item.active {
        background: #e7f3ff;
    }

    .conversation-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 12px;
        object-fit: cover;
    }

    .conversation-info {
        flex: 1;
        min-width: 0;
    }

    .conversation-name {
        font-weight: 600;
        font-size: 15px;
        color: #050505;
        margin-bottom: 4px;
    }

    .conversation-preview {
        font-size: 13px;
        color: #65676b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .unread-badge {
        background: #0084ff;
        color: white;
        border-radius: 10px;
        padding: 2px 8px;
        font-size: 12px;
        font-weight: 600;
        margin-left: auto;
    }

    .chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #f0f2f5;
    }

    .chat-header {
        background: #fff;
        padding: 12px 16px;
        border-bottom: 1px solid #e4e6eb;
        display: flex;
        align-items: center;
    }

    .chat-header-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 12px;
    }

    .chat-header-name {
        font-weight: 600;
        font-size: 15px;
        color: #050505;
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        background: #f0f2f5;
    }

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

    .chat-input-area {
        background: #fff;
        padding: 12px 16px;
        border-top: 1px solid #e4e6eb;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .chat-input {
        flex: 1;
        border: none;
        outline: none;
        padding: 10px 16px;
        background: #f0f2f5;
        border-radius: 20px;
        font-size: 15px;
        color: #050505;
    }

    .chat-input::placeholder {
        color: #8a8d91;
    }

    .send-button {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #0084ff;
        color: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s;
    }

    .send-button:hover {
        background: #0066cc;
    }

    .send-button:disabled {
        background: #c4c4c4;
        cursor: not-allowed;
    }

    .empty-chat {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #65676b;
    }
</style>

<div class="container-fluid px-4 py-4">
    <div class="messenger-container">
        <!-- Sidebar -->
        <div class="messenger-sidebar">
            <div class="messenger-header">
                💬 Tin nhắn
            </div>
            <div class="conversation-list">
                @php
                $admin = \App\Models\User::where('role', 'admin')->first();
                @endphp
                @if($admin)
                <div class="conversation-item active" onclick="window.location.href='{{ route('user.live_chat.chat') }}'">
                    <img src="{{ $admin->avatar_url }}" alt="{{ $admin->name }}" class="conversation-avatar">
                    <div class="conversation-info">
                        <div class="conversation-name">{{ $admin->name }}</div>
                        <div class="conversation-preview">Admin hỗ trợ</div>
                    </div>
                </div>
                @else
                <div class="empty-chat">
                    <p>Chưa có admin hỗ trợ</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Chat Area -->
        <div class="chat-area">
            @if($admin)
            <div class="chat-header">
                <img src="{{ $admin->avatar_url }}" alt="{{ $admin->name }}" class="chat-header-avatar">
                <div class="chat-header-name">{{ $admin->name }}</div>
            </div>
            <div class="chat-messages" id="chat-messages">
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
                    <p>Chưa có tin nhắn nào. Bắt đầu cuộc trò chuyện!</p>
                </div>
                @endforelse
            </div>
            <form action="{{ route('chat.send') }}" method="POST" class="chat-input-area" id="chat-form">
                @csrf
                <input type="hidden" name="receiver_id" value="{{ $admin->id }}">
                <input type="text" name="message" class="chat-input" placeholder="Nhập tin nhắn..." required autocomplete="off">
                <button type="submit" class="send-button">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
            @else
            <div class="empty-chat">
                <p>Chưa có admin hỗ trợ</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chatMessages = document.getElementById("chat-messages");
        const chatForm = document.getElementById("chat-form");

        // Auto scroll to bottom
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Auto refresh messages every 3 seconds
        setInterval(function() {
            fetch("{{ route('chat.messages') }}?receiver_id={{ $admin->id ?? '' }}", {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(res => res.text())
                .then(html => {
                    if (chatMessages) {
                        const oldScroll = chatMessages.scrollTop;
                        const oldHeight = chatMessages.scrollHeight;
                        chatMessages.innerHTML = html;
                        const newHeight = chatMessages.scrollHeight;
                        // Only auto-scroll if user was at bottom
                        if (oldScroll + oldHeight >= oldHeight - 50) {
                            chatMessages.scrollTop = chatMessages.scrollHeight;
                        }
                    }
                })
                .catch(err => console.error("Error loading messages:", err));
        }, 3000);

        // AJAX form submission
        if (chatForm) {
            chatForm.addEventListener("submit", function(e) {
                e.preventDefault();

                const formData = new FormData(chatForm);
                const messageInput = chatForm.querySelector('input[name="message"]');

                fetch("{{ route('chat.send') }}", {
                        method: "POST",
                        body: formData,
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            messageInput.value = '';
                            // Reload messages
                            location.reload();
                        } else if (data.error) {
                            alert(data.error);
                        }
                    })
                    .catch(err => {
                        console.error("Error sending message:", err);
                        alert("Có lỗi xảy ra khi gửi tin nhắn");
                    });
            });
        }
    });
</script>
@endsection