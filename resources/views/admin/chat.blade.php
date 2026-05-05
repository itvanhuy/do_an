@extends('layouts.admin')
@section('title', 'Live Chat')
@section('content')
<div style="display:flex; gap:0; height:calc(100vh - 120px); background:white; border-radius:10px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.1);">

    {{-- Danh sách user --}}
    <div id="user-list" style="width:280px; border-right:1px solid #eee; overflow-y:auto; background:#f9f9f9;">
        <div style="padding:16px; font-weight:700; font-size:15px; border-bottom:1px solid #eee; background:white;">
            💬 Conversations
            <span id="total-unread" style="background:#ff3b3b; color:white; border-radius:10px; padding:2px 8px; font-size:12px; margin-left:8px; display:none;"></span>
        </div>
        <div id="users-container" style="padding:8px;">
            <p style="color:#aaa; text-align:center; padding:20px; font-size:13px;">Loading...</p>
        </div>
    </div>

    {{-- Khung chat --}}
    <div style="flex:1; display:flex; flex-direction:column;">
        <div id="chat-header-admin" style="padding:16px; border-bottom:1px solid #eee; font-weight:600; font-size:15px; background:white;">
            Select a conversation
        </div>
        <div id="admin-messages" style="flex:1; overflow-y:auto; padding:16px; display:flex; flex-direction:column; gap:8px; background:#f9f9f9;">
            <p style="color:#aaa; text-align:center; margin-top:40px; font-size:13px;">Select a user to view messages</p>
        </div>
        <div id="admin-input-area" style="display:none; padding:12px; border-top:1px solid #eee; gap:8px; background:white; align-items:center;">
            <input type="text" id="admin-input" placeholder="Type a reply..." maxlength="500"
                style="flex:1; border:1px solid #ddd; border-radius:20px; padding:10px 16px; font-size:14px; outline:none;">
            <button id="admin-send"
                style="background:#9147ff; color:white; border:none; border-radius:50%; width:40px; height:40px; cursor:pointer; font-size:15px; display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<style>
    .user-item { padding:12px; border-radius:8px; cursor:pointer; transition:background 0.2s; margin-bottom:4px; }
    .user-item:hover { background:#ede9f7; }
    .user-item.active { background:#9147ff; color:white; }
    .user-item .name { font-weight:600; font-size:14px; }
    .user-item .preview { font-size:12px; opacity:0.7; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:2px; }
    .user-item .badge { background:#ff3b3b; color:white; border-radius:10px; padding:1px 7px; font-size:11px; float:right; }
    .chat-msg { max-width:75%; padding:9px 13px; border-radius:12px; font-size:13px; line-height:1.5; word-break:break-word; }
    .chat-msg.user { background:white; color:#333; align-self:flex-start; border-bottom-left-radius:3px; box-shadow:0 1px 3px rgba(0,0,0,0.1); }
    .chat-msg.admin { background:#9147ff; color:white; align-self:flex-end; border-bottom-right-radius:3px; }
    .chat-msg .time { font-size:10px; opacity:0.65; margin-top:3px; }
    #admin-input:focus { border-color:#9147ff; }
</style>

<script>
    let currentUserId = null;
    let adminLastId = 0;

    function formatTime(dateStr) {
        const d = new Date(dateStr);
        return d.getHours().toString().padStart(2,'0') + ':' + d.getMinutes().toString().padStart(2,'0');
    }

    function appendAdminMessages(msgs) {
        if (msgs.length === 0) return;
        const container = document.getElementById('admin-messages');
        if (container.querySelector('p')) container.innerHTML = '';
        msgs.forEach(m => {
            const div = document.createElement('div');
            div.className = 'chat-msg ' + m.sender;
            div.innerHTML = `${m.message}<div class="time">${formatTime(m.created_at)}</div>`;
            container.appendChild(div);
            adminLastId = Math.max(adminLastId, m.id);
        });
        container.scrollTop = container.scrollHeight;
    }

    function loadUsers() {
        fetch('{{ route("admin.chat.users") }}')
            .then(r => r.json())
            .then(data => {
                const container = document.getElementById('users-container');
                const totalUnread = document.getElementById('total-unread');
                if (!data.users || data.users.length === 0) {
                    container.innerHTML = '<p style="color:#aaa; text-align:center; padding:20px; font-size:13px;">No conversations yet</p>';
                    return;
                }
                let unreadTotal = 0;
                container.innerHTML = data.users.map(u => {
                    unreadTotal += parseInt(u.unread_count) || 0;
                    return `<div class="user-item ${currentUserId == u.id ? 'active' : ''}" onclick="selectUser(${u.id}, '${u.full_name || u.username}')">
                        ${u.unread_count > 0 ? `<span class="badge">${u.unread_count}</span>` : ''}
                        <div class="name">${u.full_name || u.username}</div>
                        <div class="preview">${u.last_message || ''}</div>
                    </div>`;
                }).join('');
                if (unreadTotal > 0) {
                    totalUnread.style.display = 'inline';
                    totalUnread.textContent = unreadTotal;
                } else {
                    totalUnread.style.display = 'none';
                }
            });
    }

    function selectUser(userId, name) {
        currentUserId = userId;
        adminLastId = 0;
        document.getElementById('chat-header-admin').textContent = '💬 ' + name;
        document.getElementById('admin-input-area').style.display = 'flex';
        document.getElementById('admin-messages').innerHTML = '';
        loadMessages();
        loadUsers();
    }

    function loadMessages() {
        if (!currentUserId) return;
        fetch(`/admin/chat/messages/${currentUserId}?last_id=${adminLastId}`)
            .then(r => r.json())
            .then(data => {
                if (data.success) appendAdminMessages(data.messages);
            });
    }

    function sendReply() {
        const msg = document.getElementById('admin-input').value.trim();
        if (!msg || !currentUserId) return;
        document.getElementById('admin-input').value = '';
        fetch(`/admin/chat/send/${currentUserId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ message: msg })
        }).then(r => r.json()).then(data => {
            if (data.success) loadMessages();
        });
    }

    document.getElementById('admin-send').addEventListener('click', sendReply);
    document.getElementById('admin-input').addEventListener('keypress', e => { if (e.key === 'Enter') sendReply(); });

    // Load users ngay và polling
    loadUsers();
    setInterval(() => {
        loadUsers();
        if (currentUserId) loadMessages();
    }, 4000);
</script>
@endsection
