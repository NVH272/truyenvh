{{-- resources/views/user/live_chat/chat_box.blade.php --}}

{{-- 1. CSS - GIAO DIỆN FACEBOOK MESSENGER --}}
<style>
    /* Font chuẩn hệ thống giống FB */
    .chatbox-wrapper {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }

    /* Container chính */
    .chatbox {
        position: fixed;
        bottom: -600px;
        right: 20px;
        width: 330px;
        height: 480px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 12px 28px 0 rgba(0, 0, 0, 0.2), 0 2px 4px 0 rgba(0, 0, 0, 0.1);
        transition: bottom 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .chatbox.show {
        bottom: 20px;
    }

    /* Header */
    .chatbox-header {
        background: #fff;
        color: #050505;
        padding: 12px 16px;
        font-weight: 700;
        font-size: 17px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #f0f0f0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        z-index: 10;
        flex-shrink: 0;
    }

    .chatbox-title-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Nút đóng/back */
    .chatbox-close,
    .chatbox-back {
        background: #f5f5f5;
        border: none;
        color: #0084ff;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s;
        font-size: 18px;
    }

    .chatbox-close:hover,
    .chatbox-back:hover {
        background: #e4e6eb;
    }

    .chatbox-back {
        display: none;
        margin-right: 5px;
    }

    /* Views */
    .chat-view {
        display: none;
        flex: 1;
        flex-direction: column;
        overflow: hidden;
        min-height: 0;
        background: #fff;
    }

    .chat-view.active {
        display: flex;
    }

    /* Danh sách Admin */
    .admin-list-container {
        flex: 1;
        overflow-y: auto;
        padding: 8px;
    }

    .admin-item {
        display: flex;
        align-items: center;
        padding: 10px;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.2s;
        position: relative;
        margin-bottom: 2px;
    }

    .admin-item:hover {
        background: #f2f2f2;
    }

    .admin-avatar-wrapper {
        position: relative;
        margin-right: 12px;
    }

    .admin-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .admin-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .admin-name {
        font-weight: 600;
        font-size: 15px;
        color: #050505;
        margin-bottom: 2px;
    }

    .admin-role {
        font-size: 13px;
        color: #65676b;
    }

    .admin-unread {
        background: #0084ff;
        color: white;
        font-size: 11px;
        font-weight: bold;
        padding: 4px 8px;
        border-radius: 20px;
    }

    /* Vùng chat messages */
    .chatbox-messages {
        flex: 1;
        overflow-y: auto;
        padding: 12px;
        background: #fff;
        display: flex;
        flex-direction: column;
        /* scroll-behavior: smooth; */
    }

    /* Vùng nhập liệu */
    .chatbox-input-area {
        padding: 12px;
        background: #fff;
        border-top: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
        z-index: 20;
    }

    .chatbox-input {
        flex: 1;
        background: #f0f2f5;
        border: none;
        border-radius: 20px;
        padding: 9px 12px;
        font-size: 15px;
        outline: none;
        color: #050505;
        transition: background 0.2s;
    }

    .chatbox-input:focus {
        background: #e4e6eb;
    }

    .chatbox-send-btn {
        background: none;
        border: none;
        color: #0084ff;
        font-size: 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 6px;
        border-radius: 50%;
        transition: background 0.2s;
    }

    .chatbox-send-btn:hover {
        background: #f0f2f5;
    }

    /* Toggle Button (Nút nổi) */
    .chat-toggle-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        background: #0084ff;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0, 132, 255, 0.4);
        z-index: 9998;
        font-size: 28px;
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .chat-toggle-btn:hover {
        transform: scale(1.05);
    }

    /* Scrollbar đẹp hơn */
    .custom-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scroll::-webkit-scrollbar-thumb {
        background: #bcc0c4;
        border-radius: 10px;
    }

    .custom-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    /* ... CSS bong bóng chat ... */
    .message-wrapper {
        display: flex;
        margin-bottom: 4px;
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
        margin-right: 8px;
        object-fit: cover;
        margin-bottom: 2px;
    }

    .message-bubble {
        max-width: 70%;
        padding: 8px 12px;
        border-radius: 18px;
        font-size: 14px;
        line-height: 1.4;
        word-wrap: break-word;
        position: relative;
    }

    .message-bubble.sent {
        background: #0084ff;
        color: white;
    }

    .message-bubble.received {
        background: #e4e6eb;
        color: #050505;
    }

    /* Nút cuộn xuống đáy */
    .scroll-bottom-btn {
        position: absolute;
        bottom: 70px;
        /* Nằm trên ô nhập liệu một chút */
        left: 50%;
        transform: translateX(-50%) scale(0);
        /* Mặc định ẩn bằng scale */
        width: 35px;
        height: 35px;
        background: #fff;
        border-radius: 50%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        border: 1px solid #eee;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #0084ff;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: 50;
    }

    .scroll-bottom-btn.show {
        transform: translateX(-50%) scale(1);
        /* Hiện lên */
    }

    .scroll-bottom-btn:hover {
        background: #f5f5f5;
    }

    /* Badge thông báo tin mới trên nút cuộn */
    .new-msg-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #ff3b30;
        color: white;
        font-size: 10px;
        font-weight: bold;
        padding: 2px 5px;
        border-radius: 10px;
        display: none;
        /* Mặc định ẩn */
    }

    .scroll-bottom-btn.has-new .new-msg-badge {
        display: block;
    }
</style>

{{-- 2. HTML --}}
@auth
@if(Auth::user()->hasVerifiedEmail())

<div class="chatbox-wrapper">
    <div id="chat-toggle" class="chat-toggle-btn">
        <i class="fab fa-facebook-messenger"></i> {{-- Icon Messenger --}}
    </div>

    <div id="chat-box-container" class="chatbox">
        <div class="chatbox-header">
            <div class="chatbox-title-group">
                <button id="chat-back" class="chatbox-back"><i class="fas fa-arrow-left"></i></button>
                <span id="chat-title" style="color: #0084ff;">Chat</span>
            </div>
            <button id="chat-close" class="chatbox-close">
                <i class="fas fa-minus" style="font-size: 14px;"></i>
            </button>
        </div>

        <div id="chat-view-list" class="chat-view active">
            <div class="admin-list-container custom-scroll" id="admin-list-content">
                <div style="text-align: center; margin-top: 40px; color: #65676b;">
                    <i class="fas fa-circle-notch fa-spin" style="font-size: 24px; color: #0084ff;"></i>
                </div>
            </div>
        </div>

        <div id="chat-view-conversation" class="chat-view">
            <div class="chatbox-messages custom-scroll" id="chat-messages">
            </div>
            <div id="scroll-bottom-btn" class="scroll-bottom-btn">
                <i class="fas fa-arrow-down"></i>
                <span class="new-msg-badge">New</span>
            </div>
            <div class="chatbox-input-area">
                <form id="chat-form" style="display: flex; width: 100%; align-items: center; gap: 8px;">
                    @csrf
                    <input type="hidden" id="chat-receiver" name="receiver_id" value="">
                    <input type="text" name="message" class="chatbox-input" placeholder="Aa" required autocomplete="off">
                    <button type="submit" class="chatbox-send-btn"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- 3. JAVASCRIPT --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const toggle = document.getElementById("chat-toggle");
        const box = document.getElementById("chat-box-container");
        const closeBtn = document.getElementById("chat-close");
        const backBtn = document.getElementById("chat-back");
        const title = document.getElementById("chat-title");
        const viewList = document.getElementById("chat-view-list");
        const viewChat = document.getElementById("chat-view-conversation");
        const adminList = document.getElementById("admin-list-content");
        const messages = document.getElementById("chat-messages");
        const scrollBtn = document.getElementById("scroll-bottom-btn");
        const form = document.getElementById("chat-form");
        const receiverInput = document.getElementById("chat-receiver");

        let isUserScrolling = false;

        // --- SỰ KIỆN SCROLL ĐỂ ẨN/HIỆN NÚT ---
        messages.addEventListener("scroll", () => {
            // Khoảng cách từ vị trí hiện tại đến đáy
            const distanceToBottom = messages.scrollHeight - messages.scrollTop - messages.clientHeight;

            if (distanceToBottom > 100) {
                // Nếu đang ở xa đáy (>100px) -> Hiện nút
                scrollBtn.classList.add("show");
                isUserScrolling = true;
            } else {
                // Nếu đang ở gần đáy -> Ẩn nút và xóa badge
                scrollBtn.classList.remove("show");
                scrollBtn.classList.remove("has-new");
                isUserScrolling = false;
            }
        });

        // --- SỰ KIỆN CLICK NÚT CUỘN ---
        scrollBtn.addEventListener("click", () => {
            messages.scrollTo({
                top: messages.scrollHeight,
                behavior: "smooth"
            });
        });

        let currentReceiverId = null;
        let pollInterval = null;

        // --- MỞ CHATBOX ---
        toggle.addEventListener("click", () => {
            box.classList.add("show");
            toggle.style.opacity = "0";
            toggle.style.pointerEvents = "none"; // Ẩn nút nhẹ nhàng
            showListView();
        });

        // --- ĐÓNG CHATBOX ---
        closeBtn.addEventListener("click", () => {
            box.classList.remove("show");
            toggle.style.opacity = "1";
            toggle.style.pointerEvents = "auto";
            if (pollInterval) clearInterval(pollInterval);
        });

        // --- QUAY LẠI LIST ---
        backBtn.addEventListener("click", showListView);

        function showListView() {
            viewList.classList.add("active");
            viewChat.classList.remove("active");
            backBtn.style.display = "none";
            title.innerText = "Đoạn chat";
            title.style.color = "#050505"; // Màu đen cho title danh sách
            if (pollInterval) clearInterval(pollInterval);
            loadAdmins();
        }

        // --- LOAD ADMIN LIST (HTML Template đã sửa lại cho đẹp) ---
        function loadAdmins() {
            fetch("{{ route('chat.list') }}")
                .then(res => res.json())
                .then(data => {
                    if (data.users.length === 0) {
                        adminList.innerHTML = "<div style='text-align:center; padding:20px; color:#65676b;'>Hiện không có nhân viên hỗ trợ online.</div>";
                        return;
                    }
                    let html = "";
                    data.users.forEach(u => {
                        let badge = u.unread_count > 0 ? `<span class="admin-unread">${u.unread_count}</span>` : "";
                        // Fallback avatar nếu null
                        let avatar = u.avatar ?
                            "{{ asset('storage') }}/" + u.avatar :
                            `https://ui-avatars.com/api/?name=${encodeURIComponent(u.name)}&background=random`;

                        html += `
                            <div class="admin-item" onclick="openChat(${u.id}, '${u.name}')">
                                <div class="admin-avatar-wrapper">
                                    <img src="${avatar}" class="admin-avatar">
                                </div>
                                <div class="admin-info">
                                    <div class="admin-name">${u.name}</div>
                                    <div class="admin-role">${u.email}</div>
                                </div>
                                ${badge}
                            </div>
                        `;
                    });
                    adminList.innerHTML = html;
                });
        }

        // --- MỞ ĐOẠN CHAT (Global function) ---
        window.openChat = function(id, name) {
            viewList.classList.remove("active");
            viewChat.classList.add("active");
            backBtn.style.display = "flex";
            title.innerText = name;
            title.style.color = "#0084ff";
            currentReceiverId = id;
            receiverInput.value = id;

            // Gọi loadMessages với tham số scroll là 'instant' (tức thì)
            loadMessages(id, 'instant');

            if (pollInterval) clearInterval(pollInterval);
            pollInterval = setInterval(() => loadMessages(id, false), 3000);
        };

        // --- KIỂM TRA NGƯỜI DÙNG Ở GẦN CUỐI KHÔNG ---
        function isUserNearBottom() {
            const threshold = 100; // khoảng cách cho phép (px)
            return messages.scrollHeight - messages.scrollTop - messages.clientHeight < threshold;
        }

        // --- LOAD TIN NHẮN (AJAX) ---
        function loadMessages(id, scrollBehavior = false) {
            const wasNearBottom = !isUserScrolling; // Lưu trạng thái trước khi load

            fetch("{{ route('chat.messages') }}?receiver_id=" + id, {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                .then(res => res.text())
                .then(html => {
                    // Mẹo: Kiểm tra độ dài nội dung để biết có tin nhắn mới không
                    const oldHtmlLength = messages.innerHTML.length;
                    messages.innerHTML = html;
                    const newHtmlLength = messages.innerHTML.length;

                    // 1. Trường hợp 'instant' (Mở chat): Cuộn ngay lập tức
                    if (scrollBehavior === 'instant') {
                        messages.scrollTop = messages.scrollHeight;
                        return;
                    }

                    // 2. Trường hợp 'smooth' (Mình gửi tin): Cuộn mượt
                    if (scrollBehavior === 'smooth') {
                        messages.scrollTo({
                            top: messages.scrollHeight,
                            behavior: 'smooth'
                        });
                        return;
                    }

                    // 3. Trường hợp Auto-refresh (Người khác gửi tin)
                    if (scrollBehavior === false) {
                        if (wasNearBottom) {
                            // Nếu đang ở đáy -> Tự cuộn xuống tiếp
                            messages.scrollTop = messages.scrollHeight;
                        } else if (newHtmlLength > oldHtmlLength) {
                            // Nếu đang ở trên CAO và CÓ tin mới -> Hiện badge đỏ
                            scrollBtn.classList.add("has-new");
                        }
                    }
                });
        }

        // --- GỬI TIN NHẮN ---
        form.addEventListener("submit", (e) => {
            e.preventDefault();
            const input = form.querySelector('input[name="message"]');
            if (!input.value.trim()) return;

            let formData = new FormData(form);
            fetch("{{ route('chat.send') }}", {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    form.reset();
                    receiverInput.value = currentReceiverId;
                    // Khi gửi tin xong, muốn cuộn mượt xuống dưới thì dùng 'smooth'
                    loadMessages(currentReceiverId, 'smooth');
                }
            });
        });

        // --- CSS DYNAMIC CHO BUBBLES (Style giống Messenger) ---
        const style = document.createElement('style');
        style.textContent = `
            .chatbox-messages .message-wrapper { 
                display: flex; 
                margin-bottom: 4px; /* Khoảng cách giữa các tin nhắn nhỏ lại */
                align-items: flex-end; 
            }
            .chatbox-messages .message-wrapper.sent { justify-content: flex-end; }
            .chatbox-messages .message-wrapper.received { justify-content: flex-start; }
            
            .chatbox-messages .message-avatar { 
                width: 28px; height: 28px; 
                border-radius: 50%; margin-right: 8px; 
                object-fit: cover; 
                margin-bottom: 2px;
            }
            
            .chatbox-messages .message-bubble { 
                max-width: 70%; 
                padding: 8px 12px; 
                border-radius: 18px; /* Bo tròn đều */
                font-size: 14px; 
                line-height: 1.4; 
                word-wrap: break-word; 
                position: relative;
            }
            
            /* Style tin nhắn Gửi đi (Xanh) */
            .chatbox-messages .message-bubble.sent { 
                background: #0084ff; 
                color: white; 
            }
            
            /* Style tin nhắn Nhận (Xám) */
            .chatbox-messages .message-bubble.received { 
                background: #e4e6eb; 
                color: #050505; 
            }
        `;
        document.head.appendChild(style);
    });
</script>
@endif
@endauth