@extends('layouts.admin')

@section('title', 'D-Mail Communication System')
@section('header', 'D-Mail System')

@section('content')
<script>
    const currentUserId = {{ isset($user) ? $user->id : 'null' }};
</script>
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

    /* Hiệu ứng Scanline & Vignette */
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

    /* DANH SÁCH CHAT */
    .conversation-list {
        flex: 1;
        overflow-y: auto;
    }

    .conversation-list::-webkit-scrollbar {
        width: 4px;
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
        display: flex;
        /* Dùng flex để căn chỉnh badge */
        align-items: center;
        gap: 6px;
        /* Khoảng cách giữa tên và badge */
    }

    /* Style cho Badge Role */
    .role-badge {
        font-size: 0.6rem;
        padding: 1px 4px;
        border-radius: 2px;
        text-transform: uppercase;
        font-family: 'Share Tech Mono', monospace;
        letter-spacing: 0.5px;
        border: 1px solid transparent;
        line-height: 1;
    }

    /* Màu sắc cho từng Role */
    .role-admin {
        color: #ef4444;
        /* Màu đỏ */
        border-color: #ef4444;
        background: rgba(239, 68, 68, 0.1);
        box-shadow: 0 0 5px rgba(239, 68, 68, 0.2);
    }

    .role-poster {
        color: #10b981;
        /* Màu xanh lá */
        border-color: #10b981;
        background: rgba(16, 185, 129, 0.1);
        box-shadow: 0 0 5px rgba(16, 185, 129, 0.2);
    }

    .role-user {
        color: #94a3b8;
        /* Màu xám */
        border-color: #475569;
        background: rgba(71, 85, 105, 0.2);
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

    /* RIGHT SIDE (CHAT AREA & EMPTY STATE) */
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

    .message-bubble.sent {
        background: rgba(234, 88, 12, 0.2);
        border: 1px solid var(--sg-accent);
        color: #fff;
        border-bottom-right-radius: 0;
        box-shadow: 0 0 10px rgba(234, 88, 12, 0.1);
    }

    .message-bubble.received {
        background: rgba(51, 65, 85, 0.4);
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

    /* EMPTY STATE (DEFAULT) */
    .empty-state-container {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background-color: transparent;
        position: relative;
        z-index: 10;
        background-image: linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
        background-size: 40px 40px;
    }

    .empty-content-box {
        border: 1px dashed var(--sg-border);
        background: rgba(15, 23, 42, 0.4);
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

    .divergence-number {
        color: var(--sg-accent);
        font-size: 1.8rem;
        letter-spacing: 4px;
        margin-bottom: 1rem;
        font-weight: bold;
        text-shadow: 0 0 15px var(--sg-accent);
    }

    /* Search Bar */
    .messenger-search-box {
        height: 72px;
        min-height: 72px;
        display: flex;
        align-items: center;
        padding: 0 1rem;
        border-bottom: 1px solid var(--sg-border);
        background: rgba(0, 0, 0, 0.2);
        box-sizing: border-box;
    }

    .search-input-wrapper {
        width: 100%;
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-input-wrapper i {
        position: absolute;
        left: 10px;
        color: var(--sg-text-dim);
        font-size: 0.8rem;
    }

    .messenger-search-input {
        width: 100%;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--sg-border);
        padding: 8px 10px 8px 30px;
        border-radius: 4px;
        color: var(--sg-text-main);
        font-family: 'Share Tech Mono', monospace;
        outline: none;
        transition: all 0.2s;
        font-size: 0.9rem;
    }

    .messenger-search-input:focus {
        border-color: var(--sg-accent);
        box-shadow: 0 0 8px rgba(234, 88, 12, 0.2);
        background: rgba(0, 0, 0, 0.3);
    }

    /* Group Header (Accordion) */
    .group-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1rem;
        background: rgba(255, 255, 255, 0.02);
        border-bottom: 1px solid rgba(255, 255, 255, 0.03);
        cursor: pointer;
        user-select: none;
        transition: background 0.2s;
    }

    .group-header:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    .group-title {
        font-size: 0.75rem;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .group-count {
        font-size: 0.7rem;
        opacity: 0.7;
        margin-left: 5px;
    }

    .group-arrow {
        font-size: 0.8rem;
        color: var(--sg-text-dim);
        transition: transform 0.3s ease;
    }

    /* Xoay mũi tên khi đóng */
    .group-header.collapsed .group-arrow {
        transform: rotate(-90deg);
    }

    .group-list {
        display: block;
        /* transition: max-height 0.3s ease-out; */
    }

    .group-list.hidden {
        display: none;
    }

    /* Màu sắc tiêu đề nhóm */
    .text-admin {
        color: #ef4444;
        text-shadow: 0 0 5px rgba(239, 68, 68, 0.3);
    }

    .text-poster {
        color: #10b981;
        text-shadow: 0 0 5px rgba(16, 185, 129, 0.3);
    }

    .text-user {
        color: #94a3b8;
    }

    /* Chấm đỏ báo hiệu tin mới của nhóm */
    .group-unread-dot {
        width: 8px;
        height: 8px;
        background-color: #ef4444;
        border-radius: 50%;
        margin-right: 10px;
        box-shadow: 0 0 5px #ef4444;
        animation: pulse-dot 2s infinite;
    }

    @keyframes pulse-dot {
        0% { transform: scale(0.95); opacity: 0.8; }
        50% { transform: scale(1.1); opacity: 1; }
        100% { transform: scale(0.95); opacity: 0.8; }
    }

    /* Helper để căn chỉnh mũi tên và chấm đỏ */
    .group-actions {
        display: flex;
        align-items: center;
    }
</style>

<div class="messenger-container font-tech">
    <div class="messenger-sidebar">
        {{-- 1. THANH TÌM KIẾM --}}
        <div class="messenger-search-box">
            <div class="search-input-wrapper">
                <i class="fas fa-search"></i>
                <input type="text"
                    id="user-search-input"
                    class="messenger-search-input"
                    placeholder="Search ID/Name..."
                    onkeyup="handleSearch(this.value)">
            </div>
        </div>

        <div class="conversation-list custom-scroll" id="sidebar-content">

            {{-- SECTION 1: ADMINS --}}
            <div class="role-group-wrapper">
                {{-- Tính toán xem có tin chưa đọc không --}}
                @php $hasUnreadAdmin = $admins->sum('unread_count') > 0; @endphp

                <div class="group-header collapsed" onclick="toggleGroup('list-admin', this)">
                    <div class="group-title text-admin">
                        ADMINS <span class="group-count" id="count-admin">({{ $admins->count() }})</span>
                    </div>
                    
                    <div class="group-actions">
                        {{-- Hiển thị chấm đỏ nếu có tin chưa đọc --}}
                        @if($hasUnreadAdmin)
                            <span class="group-unread-dot" id="dot-admin"></span>
                        @endif
                        <i class="fas fa-chevron-down group-arrow"></i>
                    </div>
                </div>
                
                <div id="list-admin" class="group-list hidden">
                    @foreach($admins as $u)
                        @include('admin.live_chat.partials.user_item', ['u' => $u, 'role' => 'admin'])
                    @endforeach
                </div>
            </div>

            {{-- SECTION 2: POSTERS --}}
            <div class="role-group-wrapper">
                @php $hasUnreadPoster = $posters->sum('unread_count') > 0; @endphp

                <div class="group-header collapsed" onclick="toggleGroup('list-poster', this)">
                    <div class="group-title text-poster">
                        POSTERS <span class="group-count" id="count-poster">({{ $posters->count() }})</span>
                    </div>

                    <div class="group-actions">
                        @if($hasUnreadPoster)
                            <span class="group-unread-dot" id="dot-poster"></span>
                        @endif
                        <i class="fas fa-chevron-down group-arrow"></i>
                    </div>
                </div>
                
                <div id="list-poster" class="group-list hidden">
                    @foreach($posters as $u)
                        @include('admin.live_chat.partials.user_item', ['u' => $u, 'role' => 'poster'])
                    @endforeach
                </div>
            </div>

            {{-- SECTION 3: USERS --}}
            <div class="role-group-wrapper">
                @php $hasUnreadUser = $users->sum('unread_count') > 0; @endphp

                <div class="group-header collapsed" onclick="toggleGroup('list-user', this)">
                    <div class="group-title text-user">
                        USERS <span class="group-count" id="count-user">({{ $users->count() }})</span>
                    </div>

                    <div class="group-actions">
                        @if($hasUnreadUser)
                            <span class="group-unread-dot" id="dot-user"></span>
                        @endif
                        <i class="fas fa-chevron-down group-arrow"></i>
                    </div>
                </div>
                
                <div id="list-user" class="group-list hidden">
                    @foreach($users as $u)
                        @include('admin.live_chat.partials.user_item', ['u' => $u, 'role' => 'user'])
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <div class="chat-area" id="right-side-content">
        @if(isset($user))
        {{-- Nếu trang được load trực tiếp qua URL, include partial chat vào đây --}}
        @include('admin.live_chat.chat_content')
        @else
        {{-- Mặc định hiển thị Empty State --}}
        <div class="empty-state-container">
            <div class="empty-content-box">
                <div class="divergence-number">1.048596</div>
                <div class="gear-wrapper">
                    <i class="fas fa-cog gear-icon gear-1"></i>
                    <i class="fas fa-cog gear-icon gear-2"></i>
                </div>
                <p class="empty-text" style="font-size: 1.2rem; color: var(--sg-text-main); letter-spacing: 3px; margin-bottom: 0.5rem; text-shadow: 0 0 10px rgba(255,255,255,0.1);">EL PSY KONGROO</p>
                <p class="text-xs text-slate-500 mt-2 font-term">Select a timeline to establish connection...</p>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    // 1. Hàm đóng mở danh sách
    function toggleGroup(listId, headerElement) {
        const list = document.getElementById(listId);
        list.classList.toggle('hidden');
        headerElement.classList.toggle('collapsed');
    }

    // 2. Hàm xử lý tìm kiếm (Debounce để tránh spam request)
    let searchTimeout;

    function handleSearch(query) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 300); // Đợi 300ms sau khi ngừng gõ mới tìm
    }

    function performSearch(query) {
        const url = "{{ route('admin.messages.search') }}";
        if (!query.trim()) {
            // Nếu query rỗng, load lại danh sách ban đầu
            loadChatList();
            return;
        }
        fetch(`${url}?query=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                // 1. Cập nhật dữ liệu cho 3 nhóm
                updateGroup('list-admin', 'count-admin', data.admins, 'admin');
                updateGroup('list-poster', 'count-poster', data.posters, 'poster');
                updateGroup('list-user', 'count-user', data.users, 'user');
                
                // 2. [MỚI] TỰ ĐỘNG MỞ CÁC NHÓM KHI CÓ KẾT QUẢ
                // Tìm tất cả danh sách đang bị ẩn và hiện nó lên
                document.querySelectorAll('.group-list').forEach(list => {
                    list.classList.remove('hidden');
                });

                // Tìm tất cả header đang đóng (mũi tên ngang) và xoay nó xuống
                document.querySelectorAll('.group-header').forEach(header => {
                    header.classList.remove('collapsed');
                });
            })
            .catch(err => console.error("Lỗi tìm kiếm:", err));
    }

    // Hàm render HTML cho từng user (Tái tạo lại HTML của Blade bằng JS)
    function updateGroup(listId, countId, users, roleType) {
        const listContainer = document.getElementById(listId);
        const countSpan = document.getElementById(countId);

        // Cập nhật số lượng
        countSpan.innerText = `(${users.length})`;

        // Xóa danh sách cũ
        listContainer.innerHTML = '';

        if (users.length === 0) {
            // listContainer.innerHTML = '<div class="p-2 text-xs text-center text-slate-600">No results</div>';
            return;
        }

        // Render danh sách mới
        users.forEach(u => {
            const isActive = (typeof currentUserId !== 'undefined' && currentUserId == u.id) ? 'active' : '';
            const badge = u.unread_count > 0 ? `<span class="unread-badge">${u.unread_count}</span>` : '';

            // Avatar fallback
            const avatarUrl = u.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(u.name)}&background=random`;

            let roleBadge = '';
            if (roleType === 'admin') roleBadge = '<span class="role-badge role-admin">AD</span>';
            if (roleType === 'poster') roleBadge = '<span class="role-badge role-poster">POSTER</span>';

            const html = `
            <div class="conversation-item ${isActive}" onclick="loadChat('${u.id}', this)">
                <img src="${avatarUrl}" class="conversation-avatar">
                <div class="conversation-info">
                    <div class="conversation-name">
                        <span class="truncate max-w-[120px]">${u.name}</span>
                        ${roleBadge}
                    </div>
                    <div class="conversation-preview font-term">${u.email}</div>
                </div>
                ${badge}
            </div>
            `;
            listContainer.insertAdjacentHTML('beforeend', html);
        });
    }
    // --- KHAI BÁO BIẾN TOÀN CỤC ---
    let refreshInterval;

    // Hàm escape HTML
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

    function loadChatList() {
        // THÊM: ?scope=admin_dashboard
        fetch("{{ route('chat.list') }}?scope=admin_dashboard")
            .then(res => res.json())
            .then(data => {
                // Dữ liệu trả về sẽ là: { mode: 'admin_dashboard', admins: [...], users: [...] }

                // 1. Render phần Round Table (data.admins)
                renderAdminSection(data.admins);

                // 2. Render phần Customers (data.users)
                renderCustomerSection(data.users);
            });
    }

    // Hàm cuộn xuống đáy
    function scrollToBottom() {
        const chatBox = document.getElementById('chat-box');
        if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
    }

    // --- 1. HÀM BẮT ĐẦU TỰ ĐỘNG LOAD (ĐÃ TỐI ƯU) ---
    function startAutoRefresh(userId) {
        stopAutoRefresh();

        // Tăng thời gian lên 6000ms (6 giây) để giảm tải server
        refreshInterval = setInterval(() => {
            const container = document.getElementById('right-side-content');
            if (!userId || !container) return;

            // Gọi AJAX lấy dữ liệu mới
            fetch(`/admin/messages/${userId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(html => {
                    // --- KỸ THUẬT DOM PARSER (CHỐNG NHẤP NHÁY) ---

                    // 1. Biến chuỗi HTML trả về thành một tài liệu DOM ảo
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    // 2. Lấy phần tin nhắn từ HTML mới
                    const newChatBox = doc.getElementById('chat-box');
                    // 3. Lấy phần tin nhắn hiện tại trên màn hình
                    const currentChatBox = document.getElementById('chat-box');

                    if (newChatBox && currentChatBox) {
                        // Kiểm tra xem Admin có đang ở dưới cùng không TRƯỚC KHI cập nhật
                        const isAtBottom = (currentChatBox.scrollHeight - currentChatBox.scrollTop - currentChatBox.clientHeight) < 100;

                        // CHỈ CẬP NHẬT NỘI DUNG TIN NHẮN (Không đụng vào Form nhập liệu)
                        // Nếu nội dung thay đổi thì mới cập nhật để đỡ tốn tài nguyên render
                        if (currentChatBox.innerHTML !== newChatBox.innerHTML) {
                            currentChatBox.innerHTML = newChatBox.innerHTML;

                            // Nếu đang ở dưới đáy thì tự cuộn xuống tin mới
                            if (isAtBottom) {
                                scrollToBottom();
                            }
                        }
                    } else {
                        // Fallback: Nếu cấu trúc HTML thay đổi quá nhiều hoặc load lần đầu
                        // Thì mới replace toàn bộ container (chấp nhận nháy 1 lần này)
                        container.innerHTML = html;
                        scrollToBottom();
                        attachFormListener();
                    }
                })
                .catch(err => console.error("Auto refresh error:", err));

        }, 3000); // <--- Đã sửa thành 3 giây
    }

    // --- 2. HÀM DỪNG TỰ ĐỘNG LOAD ---
    function stopAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
            refreshInterval = null;
        }
    }

    // --- 3. SỬA LẠI HÀM loadChat ---
    function loadChat(userId, element) {
        // UI Active Class
        document.querySelectorAll('.conversation-item').forEach(el => el.classList.remove('active'));
        if (element) element.classList.add('active');

        const container = document.getElementById('right-side-content');

        // Hiệu ứng mờ nhẹ khi chuyển người khác
        container.style.opacity = '0.6';
        stopAutoRefresh();

        fetch(`/admin/messages/${userId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.text())
            .then(html => {
                // Khi chuyển người thì thay thế toàn bộ (Full Replace)
                container.innerHTML = html;
                container.style.opacity = '1';

                scrollToBottom();
                attachFormListener(); // Gắn sự kiện submit form mới

                // Cập nhật URL
                window.history.pushState({
                    path: `/admin/messages/${userId}`
                }, '', `/admin/messages/${userId}`);

                // Bắt đầu auto refresh
                startAutoRefresh(userId);
            })
            .catch(err => {
                console.error(err);
                container.style.opacity = '1';
                alert('Không thể tải tin nhắn.');
            });
            // === [LOGIC MỚI] XỬ LÝ ẨN BADGE VÀ DOT ===
        
            // A. Ẩn/Xóa số badge của user hiện tại
            const badge = element.querySelector('.unread-badge');
            if (badge) {
                badge.remove(); // Xóa hẳn khỏi DOM
            }

            // B. Kiểm tra lại nhóm (Admin/Poster/User) xem còn ai chưa đọc không
            // Tìm thẻ cha chứa danh sách (ví dụ: id="list-admin")
            const parentList = element.closest('.group-list');
            
            if (parentList) {
                // Đếm xem trong danh sách này còn bao nhiêu badge chưa bị xóa
                const remainingBadges = parentList.querySelectorAll('.unread-badge');
                
                // Nếu không còn badge nào (length = 0) -> Ẩn chấm đỏ của nhóm
                if (remainingBadges.length === 0) {
                    // Lấy ID của nhóm (vd: 'list-admin' -> lấy chữ 'admin')
                    const listId = parentList.id; 
                    const roleName = listId.replace('list-', ''); // ra 'admin', 'poster', hoặc 'user'
                    
                    // Tìm chấm đỏ tương ứng và ẩn đi
                    const groupDot = document.getElementById(`dot-${roleName}`);
                    if (groupDot) {
                        groupDot.style.display = 'none';
                    }
                }
            }
            // === [KẾT THÚC LOGIC MỚI] ===
    }

    // --- 4. SỰ KIỆN GỬI TIN NHẮN ---
    function attachFormListener() {
        const form = document.getElementById('admin-chat-form');
        if (!form) return;

        // Xóa clone cũ để tránh gán sự kiện nhiều lần
        const newForm = form.cloneNode(true);
        form.parentNode.replaceChild(newForm, form);

        newForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const messageInput = document.getElementById('admin-message-input');
            const messageText = messageInput.value.trim();
            const receiverId = document.querySelector('input[name="receiver_id"]').value;
            const csrfToken = document.querySelector('input[name="_token"]').value;

            if (!messageText) return;

            // UX: Disable nút gửi nhưng KHÔNG disable input để tránh mất focus
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
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        messageInput.value = ''; // Xóa input

                        // Append tin nhắn mới vào ngay lập tức (UI Optimistic Update)
                        const chatBox = document.getElementById('chat-box');
                        const emptyChat = chatBox.querySelector('.empty-chat');
                        if (emptyChat) emptyChat.remove();

                        const messageWrapper = document.createElement('div');
                        messageWrapper.className = 'message-wrapper sent';
                        messageWrapper.innerHTML = `
                            <div class="message-bubble sent">
                                ${escapeHtml(messageText)}
                            </div>
                        `;
                        chatBox.appendChild(messageWrapper);
                        scrollToBottom();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gửi tin nhắn thất bại.');
                })
                .finally(() => {
                    sendButton.disabled = false;
                    sendButton.style.opacity = '1';
                    messageInput.focus(); // Giữ focus vào ô nhập liệu
                });
        });
    }

    // --- KHỞI TẠO ---
    document.addEventListener("DOMContentLoaded", function() {
        scrollToBottom();
        attachFormListener();

        const receiverInput = document.querySelector('input[name="receiver_id"]');
        if (receiverInput && receiverInput.value) {
            startAutoRefresh(receiverInput.value);
        }
    });
</script>
@endsection