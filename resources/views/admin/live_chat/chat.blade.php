@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>💬 Chat với {{ $user->name }}</h3>
        <a href="{{ route('admin.messages.index') }}" class="btn btn-secondary">
            ← Quay lại
        </a>
    </div>

    <div class="card">
        <div id="chat-box" class="card-body" style="height: 400px; overflow-y: auto; font-size: 16px;">
            @forelse($messages as $msg)
            <div class="mb-2 {{ $msg->sender_id == auth()->id() ? 'text-end' : 'text-start' }}">
                <span class="badge px-3 py-2 {{ $msg->sender_id == auth()->id() ? 'bg-primary' : 'bg-secondary' }}"
                    style="font-size: 16px;">
                    {{ $msg->message }}
                </span>
            </div>
            @empty
            <p class="text-muted text-center">Chưa có tin nhắn nào.</p>
            @endforelse
        </div>
    </div>

    <form action="{{ route('admin.messages.send') }}" method="POST" class="mt-3">
        @csrf
        <input type="hidden" name="receiver_id" value="{{ $user->id }}">
        <div class="input-group">
            <input type="text" name="message" class="form-control" placeholder="Nhập tin nhắn..." required
                style="font-size: 16px;">
            <button class="btn btn-primary">Gửi</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chatBox = document.getElementById("chat-box");

        function loadMessages() {
            fetch("{{ route('admin.messages.chat', $user->id) }}", {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                .then(res => res.text())
                .then(html => {
                    chatBox.innerHTML = html;
                    chatBox.scrollTop = chatBox.scrollHeight; // tự động cuộn xuống
                });
        }

        // Load lại tin nhắn mỗi 5 giây
        setInterval(loadMessages, 5000);
    });
</script>
@endsection