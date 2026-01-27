<style>
    /* CSS cho 2 màn hình: List & Conversation */
    .chat-view {
        display: none;
        height: 100%;
        flex-direction: column;
    }

    .chat-view.active {
        display: flex;
        /* Chỉ hiện view nào có class active */
    }

    /* Nút Back (chỉ hiện khi vào đoạn chat) */
    .chatbox-back {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        font-size: 16px;
        margin-right: 10px;
        display: none;
    }

    /* Danh sách Admin */
    .admin-list-container {
        flex: 1;
        overflow-y: auto;
        padding: 10px;
    }

    .admin-item {
        display: flex;
        align-items: center;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 8px;
        cursor: pointer;
        transition: background 0.2s;
        position: relative;
    }

    .admin-item:hover {
        background: #e9ecef;
    }

    .admin-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 10px;
        border: 1px solid #ddd;
    }

    .admin-info {
        flex: 1;
    }

    .admin-name {
        font-weight: bold;
        font-size: 14px;
        color: #333;
    }

    .admin-role {
        font-size: 12px;
        color: #666;
    }

    /* Badge tin nhắn chưa đọc trong list */
    .admin-unread {
        background: #ff3b30;
        color: white;
        font-size: 10px;
        font-weight: bold;
        padding: 2px 6px;
        border-radius: 10px;
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }
</style>

@auth
@if(Auth::user()->hasVerifiedEmail())
<div id="chat-toggle" class="chat-toggle-btn">
    <i class="fas fa-comment-dots"></i>
</div>
@endif
@endauth

<div id="chat-box-container" class="chatbox">
    <div class="chatbox-header">
        <div style="display: flex; align-items: center;">
            <button id="chat-back" class="chatbox-back"><i class="fas fa-arrow-left"></i></button>
            <span id="chat-title">Hỗ trợ trực tuyến</span>
        </div>
        <button id="chat-close" class="chatbox-close">×</button>
    </div>

    <div id="chat-view-list" class="chat-view active">
        <div class="admin-list-container custom-scroll" id="admin-list-content">
            <div style="text-align: center; margin-top: 20px; color: #666;">
                <i class="fas fa-spinner fa-spin"></i> Đang tải danh sách hỗ trợ...
            </div>
        </div>
    </div>

    <div id="chat-view-conversation" class="chat-view">
        <div class="chatbox-messages custom-scroll" id="chat-messages">
            {{-- Tin nhắn sẽ load vào đây --}}
        </div>

        <div class="chatbox-input-area">
            <form id="chat-form" action="" method="POST" style="display: flex; width: 100%; align-items: center; gap: 8px;">
                @csrf
                <input type="hidden" id="chat-receiver" name="receiver_id" value="">
                <input type="text" name="message" class="chatbox-input" placeholder="Nhập tin nhắn..." required autocomplete="off">
                <button type="submit" class="chatbox-send-btn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</div>

@auth
@if(Auth::user()->hasVerifiedEmail())
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- KHAI BÁO BIẾN ---
        const chatToggle = document.getElementById("chat-toggle");
        const chatBox = document.getElementById("chat-box-container");
        const chatClose = document.getElementById("chat-close");
        const chatBack = document.getElementById("chat-back");
        const chatTitle = document.getElementById("chat-title");

        // 2 Views chính
        const viewList = document.getElementById("chat-view-list");
        const viewChat = document.getElementById("chat-view-conversation");
        const adminListContent = document.getElementById("admin-list-content");

        // Thành phần chat
        const chatMessages = document.getElementById("chat-messages");
        const chatForm = document.getElementById("chat-form");
        const chatReceiverInput = document.getElementById("chat-receiver");

        if (!chatToggle || !chatBox) return;

        let currentReceiverId = null;
        let pollingInterval = null;

        // ============================================
        // 1. LOGIC CHUYỂN ĐỔI GIAO DIỆN (List <-> Chat)
        // ============================================

        // Hiển thị danh sách Admin
        function showListView() {
            viewList.classList.add('active'); // Hiện List
            viewChat.classList.remove('active'); // Ẩn Chat

            chatBack.style.display = 'none'; // Ẩn nút Back
            chatTitle.innerText = "Chọn nhân viên hỗ trợ";

            currentReceiverId = null; // Reset người nhận
            chatReceiverInput.value = '';

            // Dừng tải tin nhắn tự động để tiết kiệm tài nguyên
            if (pollingInterval) clearInterval(pollingInterval);

            loadAdminList(); // Tải lại danh sách (để cập nhật số tin chưa đọc)
        }

        // Hiển thị khung chat với 1 Admin cụ thể
        function showChatView(adminId, adminName) {
            viewList.classList.remove('active'); // Ẩn List
            viewChat.classList.add('active'); // Hiện Chat

            chatBack.style.display = 'block'; // Hiện nút Back
            chatTitle.innerText = adminName; // Đổi tên title thành tên Admin

            currentReceiverId = adminId;
            chatReceiverInput.value = adminId; // Gán ID vào form gửi tin

            loadMessages(adminId); // Tải tin nhắn cũ

            // Bắt đầu tự động cập nhật tin nhắn mới mỗi 3s
            if (pollingInterval) clearInterval(pollingInterval);
            pollingInterval = setInterval(() => {
                if (chatBox.classList.contains("show") && currentReceiverId) {
                    loadMessages(currentReceiverId, false); // false = không tự cuộn nếu đang đọc tin cũ
                }
            }, 3000);
        }

        // ============================================
        // 2. CÁC HÀM GỌI API (AJAX)
        // ============================================

        // Tải danh sách Admin
        function loadAdminList() {
            fetch("{{ route('chat.list') }}")
                .then(res => res.json())
                .then(data => {
                    const admins = data.users; // Controller trả về danh sách admin trong biến 'users'

                    if (admins.length === 0) {
                        adminListContent.innerHTML = '<div style="text-align:center; padding:20px; color:#666;">Chưa có nhân viên trực tuyến.</div>';
                        return;
                    }

                    let html = '';
                    admins.forEach(admin => {
                        // Badge đếm tin nhắn chưa đọc
                        let unreadBadge = admin.unread_count > 0 ?
                            `<span class="admin-unread">${admin.unread_count}</span>` :
                            '';

                        // Avatar mặc định nếu null
                        let avatarUrl = admin.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(admin.name)}&background=random`;

                        // Render từng dòng Admin
                        html += `
                            <div class="admin-item" onclick="selectAdmin(${admin.id}, '${admin.name}')">
                                <img src="${avatarUrl}" class="admin-avatar" alt="${admin.name}">
                                <div class="admin-info">
                                    <div class="admin-name">${admin.name}</div>
                                    <div class="admin-role">Admin hỗ trợ</div>
                                </div>
                                ${unreadBadge}
                            </div>
                        `;
                    });
                    adminListContent.innerHTML = html;
                })
                .catch(err => {
                    console.error(err);
                    adminListContent.innerHTML = '<div style="text-align:center; padding:20px; color:red;">Lỗi tải danh sách.</div>';
                });
        }

        // Hàm trung gian để gọi từ HTML onclick
        window.selectAdmin = function(id, name) {
            showChatView(id, name);
        }

        // Tải tin nhắn của 1 cuộc hội thoại
        function loadMessages(receiverId, autoScroll = true) {
            const url = "{{ route('chat.messages') }}?receiver_id=" + receiverId;
            fetch(url, {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                .then(res => res.text())
                .then(html => {
                    const oldScroll = chatMessages.scrollTop;
                    const oldHeight = chatMessages.scrollHeight;

                    chatMessages.innerHTML = html;

                    if (autoScroll) {
                        scrollToBottom();
                    } else {
                        // Giữ vị trí scroll nếu người dùng đang đọc tin nhắn cũ
                        if (oldScroll + chatMessages.clientHeight >= oldHeight - 50) {
                            scrollToBottom();
                        }
                    }
                })
                .catch(err => console.error("Lỗi tải tin nhắn:", err));
        }

        function scrollToBottom() {
            chatMessages.scrollTo({
                top: chatMessages.scrollHeight,
                behavior: "smooth"
            });
        }

        // ============================================
        // 3. SỰ KIỆN (EVENTS)
        // ============================================

        // Mở Chatbox -> Hiện danh sách Admin
        chatToggle.addEventListener("click", function() {
            chatBox.classList.add("show");
            chatToggle.style.display = "none";
            showListView(); // Luôn bắt đầu từ màn hình danh sách
        });

        // Đóng Chatbox -> Reset về ban đầu
        chatClose.addEventListener("click", function() {
            chatBox.classList.remove("show");
            chatToggle.style.display = "flex";
            if (pollingInterval) clearInterval(pollingInterval);
        });

        // Nút Back: Từ khung chat quay về danh sách
        chatBack.addEventListener("click", function() {
            showListView();
        });

        // Gửi tin nhắn
        chatForm.addEventListener("submit", function(e) {
            e.preventDefault();
            if (!currentReceiverId) return;

            let formData = new FormData(chatForm);

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
                        chatForm.reset();
                        chatReceiverInput.value = currentReceiverId; // Reset form làm mất value hidden, gán lại
                        loadMessages(currentReceiverId, true); // Load lại và cuộn xuống cuối
                    }
                })
                .catch(err => alert("Lỗi gửi tin nhắn"));
        });

        // Thêm CSS động cho bong bóng chat (Message Bubbles)
        const style = document.createElement('style');
        style.textContent = `
            .chatbox-messages .message-wrapper { display: flex; margin-bottom: 8px; align-items: flex-end; }
            .chatbox-messages .message-wrapper.sent { justify-content: flex-end; }
            .chatbox-messages .message-wrapper.received { justify-content: flex-start; }
            .chatbox-messages .message-avatar { width: 28px; height: 28px; border-radius: 50%; margin: 0 8px; object-fit: cover; }
            .chatbox-messages .message-bubble { max-width: 75%; padding: 8px 12px; border-radius: 18px; font-size: 14px; line-height: 1.4; word-wrap: break-word; }
            .chatbox-messages .message-bubble.sent { background: #0084ff; color: white; border-bottom-right-radius: 4px; }
            .chatbox-messages .message-bubble.received { background: #e4e6eb; color: #050505; border-bottom-left-radius: 4px; }
        `;
        document.head.appendChild(style);
    });
</script>
@endif
@endauth