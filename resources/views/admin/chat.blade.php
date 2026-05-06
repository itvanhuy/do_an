@extends('layouts.admin')
@section('title', 'Live Chat')

@section('styles')
<style>
    .chat-layout { display: flex; height: calc(100vh - 160px); gap: 0; background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden; }

    /* User list sidebar */
    .chat-users { width: 280px; border-right: 1px solid #eee; display: flex; flex-direction: column; flex-shrink: 0; }
    .chat-users-header { padding: 16px 20px; font-weight: 700; font-size: 15px; border-bottom: 1px solid #eee; background: #f8f9fa; }
    .chat-users-list { overflow-y: auto; flex: 1; }
    .chat-user-item { padding: 14px 20px; cursor: pointer; border-bottom: 1px solid #f5f5f5; transition: background 0.2s; display: flex; align-items: center; gap: 12px; }
    .chat-user-item:hover { background: #f0f4ff; }
    .chat-user-item.active { background: #e8f0fe; border-left: 3px solid #3498db; }
    .chat-user-avatar { width: 40px; height: 40px; border-radius: 50%; background: #9147ff; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; flex-shrink: 0; }
    .chat-user-info { flex: 1; min-width: 0; }
    .chat-user-name { font-weight: 600; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .chat-user-preview { font-size: 12px; color: #888; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px; }
    .chat-user-badge { background: #ff3b3b; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 11px; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0; }
    .chat-empty-users { padding: 40px 20px; text-align: center; color: #aaa; font-size: 13px; }

    /* Chat area */
    .chat-area { flex: 1; display: flex; flex-direction: column; min-width: 0; }
    .chat-area-header { padding: 16px 20px; border-bottom: 1px solid #eee; background: #f8f9fa; display: flex; align-items: center; gap: 12px; }
    .chat-area-title { font-weight: 700; font-size: 15px; }
    .chat-area-subtitle { font-size: 12px; color: #888; }
    .chat-messages-area { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 10px; background: #f9f9f9; }
    .chat-msg { max-width: 70%; padding: 10px 14px; border-radius: 12px; font-size: 13px; line-height: 1.5; word-break: break-word; }
    .chat-msg.user { background: #e8f0fe; color: #333; align-self: flex-start; border-bottom-left-radius: 3px; }
    .chat-msg.admin { background: #3498db; color: white; align-self: flex-end; border-bottom-right-radius: 3px; }
    .chat-msg .msg-time { font-size: 10px; opacity: 0.65; margin-top: 4px; }
    .chat-msg .msg-sender { font-size: 10px; font-weight: 600; margin-bottom: 2px; opacity: 0.75; }
    .chat-input-area { padding: 14px 20px; border-top: 1px solid #eee; display: flex; gap: 10px; background: white; }
    .chat-input { flex: 1; border: 1px solid #ddd; border-radius: 24px; padding: 10px 18px; font-size: 14px; outline: none; transition: border-color 0.2s; }
    .chat-input:focus { border-color: #3498db; }
    .chat-send-btn { background: #3498db; color: white; border: none; border-radius: 50%; width: 42px; height: 42px; cursor: pointer; font-size: 15px; display: flex; align-items: center; justify-content: center; transition: background 0.2s; flex-shrink: 0; }
    .chat-send-btn:hover { background: #2980b9; }
    .chat-placeholder { flex: 1; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 12px; color: #aaa; }
    .chat-placeholder i { font-size: 3rem; }
</style>
@endsection

@section('content')
<div style="margin-bottom:24px;">
    <h1 style="margin:0; font-size:1.5rem;">💬 Live Chat</h1>
    <p style="color:#888; margin:4px 0 0; font-size:14px;">Quản lý tin nhắn từ khách hàng</p>
</div>

<div class="chat-layout">
    {{-- Danh sách user --}}
    <div class="chat-users">
        <div class="chat-users-header">
            <i class="fas fa-users" style="color:#3498db;"></i> Khách hàng
            <span id="total-users-badge" style="font-size:12px; color:#888; font-weight:400;"></span>
        </div>
        <div class="chat-users-list" id="users-list">
            <div class="chat-empty-users">
                <i class="fas fa-spinner fa-spin" style="font-size:1.5rem; color:#ccc;"></i>
                <p style="margin-top:8px;">Đang tải...</p>
            </div>
        </div>
    </div>

    {{-- Khu vực chat --}}
    <div class="chat-area" id="chat-area">
        <div class="chat-placeholder" id="chat-placeholder">
            <i class="fas fa-comment-dots"></i>
            <p style="font-size:14px;">Chọn một khách hàng để xem tin nhắn</p>
        </div>

        <div id="chat-conversation" style="display:none; flex-direction:column; flex:1; min-height:0;">
            <div class="chat-area-header">
                <div class="chat-user-avatar" id="conv-avatar" style="width:36px; height:36px; font-size:14px;"></div>
                <div>
                    <div class="chat-area-title" id="conv-name"></div>
                    <div class="chat-area-subtitle" id="conv-subtitle">Đang chat</div>
                </div>
            </div>
            <div class="chat-messages-area" id="conv-messages"></div>
            <div class="chat-input-area">
                <input type="text" class="chat-input" id="admin-chat-input" placeholder="Nhập tin nhắn..." maxlength="1000">
                <button class="chat-send-btn" id="admin-chat-send"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let selectedUserId = null;
    let selectedUserName = '';
    let lastMessageId = 0;
    let pollInterval = null;

    // ---- Load danh sách users ----
    function loadUsers() {
        fetch('{{ route("admin.chat.users") }}')
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                const list = document.getElementById('users-list');
                const users = data.users;

                if (users.length === 0) {
                    list.innerHTML = '<div class="chat-empty-users"><i class="fas fa-inbox" style="font-size:2rem; color:#ddd;"></i><p style="margin-top:8px;">Chưa có tin nhắn nào</p></div>';
                    return;
                }

                document.getElementById('total-users-badge').textContent = `(${users.length})`;

                list.innerHTML = users.map(u => {
                    const name = u.full_name || u.username || 'User #' + u.id;
                    const initial = name.charAt(0).toUpperCase();
                    const preview = u.last_message ? u.last_message.substring(0, 35) + (u.last_message.length > 35 ? '...' : '') : '';
                    const badge = u.unread_count > 0 ? `<div class="chat-user-badge">${u.unread_count}</div>` : '';
                    const activeClass = u.id == selectedUserId ? 'active' : '';
                    return `
                        <div class="chat-user-item ${activeClass}" onclick="selectUser(${u.id}, '${name.replace(/'/g, "\\'")}')">
                            <div class="chat-user-avatar">${initial}</div>
                            <div class="chat-user-info">
                                <div class="chat-user-name">${name}</div>
                                <div class="chat-user-preview">${preview}</div>
                            </div>
                            ${badge}
                        </div>`;
                }).join('');
            });
    }

    // ---- Chọn user để chat ----
    function selectUser(userId, userName) {
        selectedUserId = userId;
        selectedUserName = userName;
        lastMessageId = 0;

        // Update UI
        document.getElementById('chat-placeholder').style.display = 'none';
        const conv = document.getElementById('chat-conversation');
        conv.style.display = 'flex';

        const initial = userName.charAt(0).toUpperCase();
        document.getElementById('conv-avatar').textContent = initial;
        document.getElementById('conv-name').textContent = userName;
        document.getElementById('conv-messages').innerHTML = '<div style="text-align:center; color:#ccc; font-size:12px; padding:20px;"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>';

        // Highlight active user
        document.querySelectorAll('.chat-user-item').forEach(el => el.classList.remove('active'));
        event.currentTarget.classList.add('active');

        // Load messages
        loadMessages(true);

        // Reset poll
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(() => loadMessages(false), 3000);
    }

    // ---- Load tin nhắn ----
    function loadMessages(initial = false) {
        if (!selectedUserId) return;
        const url = `{{ url('admin/chat/messages') }}/${selectedUserId}?last_id=${lastMessageId}`;
        fetch(url)
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                const msgs = data.messages;
                const container = document.getElementById('conv-messages');

                if (initial) {
                    container.innerHTML = '';
                    if (msgs.length === 0) {
                        container.innerHTML = '<div style="text-align:center; color:#aaa; font-size:13px; margin-top:30px;">Chưa có tin nhắn nào.</div>';
                        return;
                    }
                }

                if (msgs.length === 0) return;

                // Remove empty placeholder if exists
                const placeholder = container.querySelector('[data-empty]');
                if (placeholder) placeholder.remove();

                msgs.forEach(m => {
                    const div = document.createElement('div');
                    div.className = 'chat-msg ' + m.sender;
                    const senderLabel = m.sender === 'admin' ? 'Bạn (Admin)' : selectedUserName;
                    div.innerHTML = `
                        <div class="msg-sender">${senderLabel}</div>
                        ${escapeHtml(m.message)}
                        <div class="msg-time">${formatTime(m.created_at)}</div>`;
                    container.appendChild(div);
                    lastMessageId = Math.max(lastMessageId, m.id);
                });

                container.scrollTop = container.scrollHeight;

                // Refresh user list to update unread badges
                loadUsers();
            });
    }

    // ---- Gửi tin nhắn ----
    function sendAdminMessage() {
        if (!selectedUserId) return;
        const input = document.getElementById('admin-chat-input');
        const msg = input.value.trim();
        if (!msg) return;
        input.value = '';

        fetch(`{{ url('admin/chat/send') }}/${selectedUserId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: msg })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) loadMessages(false);
        });
    }

    document.getElementById('admin-chat-send').addEventListener('click', sendAdminMessage);
    document.getElementById('admin-chat-input').addEventListener('keypress', e => {
        if (e.key === 'Enter') sendAdminMessage();
    });

    // ---- Helpers ----
    function formatTime(dateStr) {
        const d = new Date(dateStr);
        return d.getHours().toString().padStart(2,'0') + ':' + d.getMinutes().toString().padStart(2,'0');
    }

    function escapeHtml(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // ---- Init ----
    loadUsers();
    setInterval(loadUsers, 10000); // Refresh user list mỗi 10s
</script>
@endsection
