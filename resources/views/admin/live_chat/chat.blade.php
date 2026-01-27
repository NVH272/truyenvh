@extends('layouts.admin')

@section('content')
<style>
    /* Facebook Messenger Style */
    .messenger-container {
        display: flex;
        height: calc(100vh - 200px);
        max-height: 800px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .back-button {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        transition: background 0.2s;
    }

    .back-button:hover {
        background: rgba(255,255,255,0.3);
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
        object-fit: cover;
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

    .section-title {
        padding: 12px 16px;
        font-size: 12px;
        font-weight: 600;
        color: #65676b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>


<div class="container-fluid px-4 py-4">
    <div class="messenger-container">
        <!-- Sidebar -->
        <div class="messenger-sidebar">
            <div class="messenger-header">
                <a href="{{ route('admin.messages.index') }}" class="back-button" title="Quay lại">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <span>💬 Tin nhắn</span>
            </div>
            <div class="conversation-list">
                @if(isset($users) && isset($admins) && ($users->count() > 0 || $admins->count() > 0))
                    @if($users->count() > 0)
                    <div class="section-title">Khách hàng</div>
                    @foreach($users as $u)
                    <a href="{{ route('admin.messages.chat', $u->id) }}" class="conversation-item text-decoration-none {{ $u->id == $user->id ? 'active' : '' }}" style="color: inherit;">
                        <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="conversation-avatar">
                        <div class="conversation-info">
                            <div class="conversation-name">{{ $u->name }}</div>
                            <div class="conversation-preview">{{ $u->email }}</div>
                        </div>
                    </a>
                    @endforeach
                    @endif

                    @if($admins->count() > 0)
                    <div class="section-title">Admin khác</div>
                    @foreach($admins as $a)
                    <a href="{{ route('admin.messages.chat', $a->id) }}" class="conversation-item text-decoration-none {{ $a->id == $user->id ? 'active' : '' }}" style="color: inherit;">
                        <img src="{{ $a->avatar_url }}" alt="{{ $a->name }}" class="conversation-avatar">
                        <div class="conversation-info">
                            <div class="conversation-name">{{ $a->name }}</div>
                            <div class="conversation-preview">{{ $a->email }}</div>
                        </div>
                    </a>
                    @endforeach
                    @endif
                @else
                <div class="empty-chat">
                    <p>Chưa có cuộc trò chuyện nào</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Chat Area -->
        <div class="chat-area">
            <div class="chat-header">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="chat-header-avatar">
                <div class="chat-header-name">{{ $user->name }}</div>
            </div>
            <div class="chat-messages" id="chat-box">
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
            <form id="admin-chat-form" action="{{ route('admin.messages.send') }}" method="POST" class="chat-input-area">
                @csrf
                <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                <input type="text" name="message" class="chat-input" placeholder="Nhập tin nhắn..." required autocomplete="off" id="admin-message-input">
                <button type="submit" class="send-button">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chatBox = document.getElementById("chat-box");
        const chatForm = document.getElementById("admin-chat-form");
        const messageInput = document.getElementById("admin-message-input");

        // Auto scroll to bottom
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function loadMessages() {
            fetch("{{ route('admin.messages.chat', $user->id) }}", {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(res => res.text())
                .then(html => {
                    if (chatBox) {
                        const oldScroll = chatBox.scrollTop;
                        const oldHeight = chatBox.scrollHeight;
                        chatBox.innerHTML = html;
                        const newHeight = chatBox.scrollHeight;
                        // Only auto-scroll if user was at bottom
                        if (oldScroll + oldHeight >= oldHeight - 50) {
                            chatBox.scrollTop = chatBox.scrollHeight;
                        }
                    }
                })
                .catch(err => console.error("Lỗi tải tin nhắn:", err));
        }

        // Gửi tin nhắn AJAX
        if (chatForm) {
            chatForm.addEventListener("submit", function(e) {
                e.preventDefault();

                const formData = new FormData(chatForm);

                fetch("{{ route('admin.messages.send') }}", {
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
                            loadMessages();
                            if (chatBox) {
                                setTimeout(() => {
                                    chatBox.scrollTop = chatBox.scrollHeight;
                                }, 100);
                            }
                        } else if (data.error) {
                            alert(data.error);
                        }
                    })
                    .catch(err => {
                        console.error("Lỗi gửi tin:", err);
                        alert("Có lỗi xảy ra khi gửi tin nhắn");
                    });
            });
        }

        // Load lại tin nhắn mỗi 3 giây
        setInterval(loadMessages, 3000);
    });
</script>
@endsection
