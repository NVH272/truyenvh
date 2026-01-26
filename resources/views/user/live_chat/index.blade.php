@extends('layouts.app')

@section('content')
<style>
    .chat-bubble {
        display: inline-block;
        padding: 12px 16px;
        border-radius: 20px;
        max-width: 70%;
        word-wrap: break-word;
        font-size: 16px;
        /* chữ to hơn */
        line-height: 1.4;
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

    .chat-row {
        margin-bottom: 10px;
    }

    /* Bỏ highlight khi focus input */
    .chat-input:focus {
        outline: none !important;
        box-shadow: none !important;
        border-color: #ccc !important;
    }
</style>

<div class="container">
    <h3>💬 Chat với Admin</h3>

    <div class="card">
        <div id="chat-box" class="card-body" style="height: 400px; overflow-y: auto;">
            @forelse($messages as $msg)
            <div class="chat-row {{ $msg->sender_id == auth()->id() ? 'text-end' : 'text-start' }}">
                <span class="chat-bubble {{ $msg->sender_id == auth()->id() ? 'me' : 'them' }}">
                    {{ $msg->message }}
                </span>
            </div>
            @empty
            <p class="text-muted text-center">Chưa có tin nhắn nào.</p>
            @endforelse
        </div>
    </div>

    <form action="{{ route('chat.send') }}" method="POST" class="mt-3">
        @csrf
        <input type="hidden" name="receiver_id" value="{{ $admin->id }}">
        <div class="input-group">
            <input type="text" name="message" class="form-control chat-input" placeholder="Nhập tin nhắn..." required>
            <button class="btn btn-primary">Gửi</button>
        </div>
    </form>
</div>

{{-- Tự động scroll xuống cuối --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let chatBox = document.getElementById("chat-box");
        chatBox.scrollTop = chatBox.scrollHeight;
    });
</script>
@endsection