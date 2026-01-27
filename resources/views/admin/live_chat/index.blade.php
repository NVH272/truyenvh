@extends('layouts.admin')

@section('title', 'D-Mail Communication System')
@section('header', 'D-Mail System')

@section('content')
<style>
    /* === STEINS;GATE THEME (TRANSPARENT VERSION) === */
    :root {
        /* Xóa màu nền cứng, dùng transparent hoặc màu bán trong suốt */
        --sg-bg-dark: transparent;
        --sg-bg-panel: rgba(0, 0, 0, 0.2);
        /* Sidebar tối hơn nền một chút */
        --sg-border: #334155;
        /* Khớp với border-slate-700 của layout admin */
        --sg-text-main: #cbd5e1;
        /* Khớp với text-slate-300 */
        --sg-text-dim: #64748b;
        /* Khớp với text-slate-500 */
        --sg-accent: #ea580c;
        /* Cam cháy (Orange-600) */
        --sg-accent-glow: #fb923c;
        /* Cam sáng */
    }

    .font-tech {
        font-family: 'Share Tech Mono', monospace;
    }

    .font-term {
        font-family: 'Courier New', Courier, monospace;
    }

    /* CONTAINER CHÍNH - HÒA VÀO NỀN */
    .messenger-container {
        display: flex;
        /* Chiều cao tính toán để vừa khung admin */
        height: calc(100vh - 180px);
        min-height: 600px;
        background: var(--sg-bg-dark);
        /* Transparent */
        /* Loại bỏ border và shadow bao quanh để không bị tách biệt */
        border: none;
        box-shadow: none;
        overflow: hidden;
        position: relative;
    }

    /* Hiệu ứng Scanline - Giữ lại nhưng làm mờ hơn để hợp với nền xanh */
    .messenger-container::after {
        content: " ";
        display: block;
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background: linear-gradient(rgba(15, 23, 42, 0) 50%, rgba(0, 0, 0, 0.2) 50%);
        /* Dùng màu xanh đen của nền */
        background-size: 100% 4px;
        z-index: 50;
        pointer-events: none;
        opacity: 0.3;
        /* Giảm opacity */
    }

    /* Hiệu ứng Vignette (Tối 4 góc) */
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

    /* SIDEBAR - Tối hơn nền chính một chút để phân biệt */
    .messenger-sidebar {
        width: 300px;
        background: var(--sg-bg-panel);
        border-right: 1px solid var(--sg-border);
        display: flex;
        flex-direction: column;
        z-index: 60;
        /* Nổi lên trên scanline */
        backdrop-filter: blur(5px);
        /* Hiệu ứng mờ nhẹ nếu có nội dung phía sau */
    }

    .messenger-header {
        background: transparent;
        color: var(--sg-accent);
        padding: 1rem;
        font-size: 1.1rem;
        border-bottom: 1px solid var(--sg-border);
        text-transform: uppercase;
        letter-spacing: 2px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        text-shadow: 0 0 10px rgba(234, 88, 12, 0.4);
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
        /* Cam rất nhạt khi hover */
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
        /* Slate-200 */
    }

    .conversation-preview {
        font-size: 0.75rem;
        color: var(--sg-text-dim);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .unread-badge {
        background: var(--sg-accent);
        color: #fff;
        border-radius: 2px;
        padding: 0px 6px;
        font-size: 0.7rem;
        font-weight: bold;
        margin-left: auto;
        box-shadow: 0 0 8px var(--sg-accent);
    }

    /* EMPTY STATE */
    .empty-state-container {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background-color: transparent;
        /* Trong suốt */
        /* Lưới tọa độ mờ */
        background-image:
            linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
        background-size: 40px 40px;
        color: var(--sg-text-dim);
        position: relative;
        z-index: 10;
    }

    .empty-content-box {
        /* Bỏ nền đen, chỉ giữ viền mờ hoặc bỏ luôn viền để trông như đang lơ lửng */
        border: 1px dashed var(--sg-border);
        background: rgba(15, 23, 42, 0.4);
        /* Nền xanh đen bán trong suốt */
        padding: 3rem;
        border-radius: 4px;
        text-align: center;
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.2);
    }

    .gear-wrapper {
        position: relative;
        width: 100px;
        height: 100px;
        margin: 0 auto 1.5rem auto;
    }

    .gear-icon {
        color: #475569;
        /* Slate-600 */
        position: absolute;
    }

    @keyframes spin-slow {
        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes spin-reverse {
        100% {
            transform: rotate(-360deg);
        }
    }

    .gear-1 {
        font-size: 4rem;
        top: 0;
        left: 10px;
        opacity: 0.5;
        animation: spin-slow 25s linear infinite;
    }

    .gear-2 {
        font-size: 2.5rem;
        bottom: 10px;
        right: 10px;
        opacity: 0.3;
        animation: spin-reverse 18s linear infinite;
    }

    .empty-text {
        font-size: 1.2rem;
        color: var(--sg-text-main);
        letter-spacing: 3px;
        margin-bottom: 0.5rem;
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
    }

    .divergence-number {
        color: var(--sg-accent);
        font-size: 1.8rem;
        letter-spacing: 4px;
        margin-bottom: 1rem;
        font-weight: bold;
        text-shadow: 0 0 15px var(--sg-accent);
    }
</style>

<div class="messenger-container font-tech">
    <div class="messenger-sidebar">
        <div class="messenger-header">
            <i class="fas fa-satellite-dish"></i>
            <span>Future Gadget Lab</span>
        </div>

        <div class="conversation-list custom-scroll">
            @if($users->count() > 0 || $admins->count() > 0)
            @if($users->count() > 0)
            <div class="section-title">Lab Members</div>
            @foreach($users as $user)
            <a href="{{ route('admin.messages.chat', $user->id) }}" class="conversation-item text-decoration-none">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="conversation-avatar">
                <div class="conversation-info">
                    <div class="conversation-name">{{ $user->name }}</div>
                    <div class="conversation-preview font-term">{{ $user->email }}</div>
                </div>
                @if($user->unread_count > 0)
                <span class="unread-badge">{{ $user->unread_count }}</span>
                @endif
            </a>
            @endforeach
            @endif

            @if($admins->count() > 0)
            <div class="section-title">Round Table</div>
            @foreach($admins as $admin)
            <a href="{{ route('admin.messages.chat', $admin->id) }}" class="conversation-item text-decoration-none">
                <img src="{{ $admin->avatar_url }}" alt="{{ $admin->name }}" class="conversation-avatar">
                <div class="conversation-info">
                    <div class="conversation-name">{{ $admin->name }}</div>
                    <div class="conversation-preview font-term">{{ $admin->email }}</div>
                </div>
                @if($admin->unread_count > 0)
                <span class="unread-badge">{{ $admin->unread_count }}</span>
                @endif
            </a>
            @endforeach
            @endif
            @else
            <div class="p-8 text-center text-slate-500">
                <i class="fas fa-ban mb-2 text-2xl opacity-50"></i>
                <p>No signals detected.</p>
            </div>
            @endif
        </div>
    </div>

    <div class="empty-state-container">
        <div class="empty-content-box">
            <div class="divergence-number">1.048596</div>

            <div class="gear-wrapper">
                <i class="fas fa-cog gear-icon gear-1"></i>
                <i class="fas fa-cog gear-icon gear-2"></i>
            </div>

            <p class="empty-text">EL PSY KONGROO</p>
            <p class="text-xs text-slate-500 mt-2 font-term">Select a timeline to establish connection...</p>
        </div>
    </div>
</div>
@endsection