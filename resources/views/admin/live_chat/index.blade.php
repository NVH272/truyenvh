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

    .section-title {
        padding: 12px 16px;
        font-size: 12px;
        font-weight: 600;
        color: #65676b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #65676b;
    }
</style>

<div class="container-fluid px-4 py-4">
    <div class="messenger-container">
        <!-- Sidebar -->
        <div class="messenger-sidebar">
            <div class="messenger-header">
                💬 Quản lý Chat
            </div>
            <div class="conversation-list">
                @if($users->count() > 0 || $admins->count() > 0)
                    @if($users->count() > 0)
                    <div class="section-title">Khách hàng</div>
                    @foreach($users as $user)
                    <a href="{{ route('admin.messages.chat', $user->id) }}" class="conversation-item text-decoration-none" style="color: inherit;">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="conversation-avatar">
                        <div class="conversation-info">
                            <div class="conversation-name">{{ $user->name }}</div>
                            <div class="conversation-preview">{{ $user->email }}</div>
                        </div>
                        @if($user->unread_count > 0)
                        <span class="unread-badge">{{ $user->unread_count }}</span>
                        @endif
                    </a>
                    @endforeach
                    @endif

                    @if($admins->count() > 0)
                    <div class="section-title">Admin khác</div>
                    @foreach($admins as $admin)
                    <a href="{{ route('admin.messages.chat', $admin->id) }}" class="conversation-item text-decoration-none" style="color: inherit;">
                        <img src="{{ $admin->avatar_url }}" alt="{{ $admin->name }}" class="conversation-avatar">
                        <div class="conversation-info">
                            <div class="conversation-name">{{ $admin->name }}</div>
                            <div class="conversation-preview">{{ $admin->email }}</div>
                        </div>
                        @if($admin->unread_count > 0)
                        <span class="unread-badge">{{ $admin->unread_count }}</span>
                        @endif
                    </a>
                    @endforeach
                    @endif
                @else
                <div class="empty-state">
                    <p>Chưa có cuộc trò chuyện nào</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Empty Chat Area -->
        <div style="flex: 1; display: flex; align-items: center; justify-content: center; background: #f0f2f5; color: #65676b;">
            <div style="text-align: center;">
                <i class="fas fa-comments" style="font-size: 64px; margin-bottom: 16px; opacity: 0.3;"></i>
                <p style="font-size: 18px; font-weight: 600;">Chọn một cuộc trò chuyện để bắt đầu</p>
            </div>
        </div>
    </div>
</div>
@endsection
