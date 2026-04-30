@extends('layouts.admin')

@section('title', 'Luxury Concierge Center')

@section('content')
<style>
    :root {
        --luxury-gold: #af9245;
        --luxury-dark: #1e293b;
        --luxury-bg: #f8fafc;
        --sidebar-width: 380px;
        --message-bg-received: #ffffff;
        --message-bg-sent: #1e293b;
        --message-bg-ai: #fffbeb;
    }

    /* Layout override to make it full screen */
    .admin-content-wrapper { padding: 0 !important; }
    
    .chat-app {
        display: flex;
        height: calc(100vh - 65px); /* Trừ đi chiều cao của navbar admin */
        background: #fff;
        overflow: hidden;
    }

    /* Sidebar Styles */
    .chat-sidebar {
        width: var(--sidebar-width);
        border-right: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        background: #fff;
        z-index: 20;
    }

    .sidebar-header {
        padding: 1.5rem;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
    }

    .search-container {
        position: relative;
    }

    .search-container i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .search-input {
        padding-left: 2.5rem;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        font-size: 0.9rem;
        transition: all 0.3s;
        height: 45px;
    }

    .search-input:focus {
        border-color: var(--luxury-gold);
        box-shadow: 0 0 0 3px rgba(175, 146, 69, 0.1);
        background: #fff;
        outline: none;
    }

    .chat-list {
        flex: 1;
        overflow-y: auto;
    }

    .chat-item {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f8fafc;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        gap: 1rem;
        position: relative;
    }

    .chat-item:hover {
        background: #f8fafc;
    }

    .chat-item.active {
        background: #f0f7ff;
        border-left: 4px solid var(--luxury-gold);
    }

    .avatar-wrapper {
        position: relative;
        flex-shrink: 0;
    }

    .avatar {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: var(--luxury-dark);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.2rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        text-transform: uppercase;
    }

    .status-indicator {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #10b981;
        border: 2px solid #fff;
    }

    .chat-info {
        flex: 1;
        min-width: 0;
    }

    .chat-info h6 {
        margin-bottom: 0.25rem;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 1rem;
    }

    .chat-info .time {
        font-size: 0.75rem;
        font-weight: 400;
        color: #94a3b8;
    }

    .chat-info .last-msg {
        font-size: 0.85rem;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.2;
    }

    .unread-badge {
        background: #ef4444;
        color: #fff;
        font-size: 0.65rem;
        padding: 2px 8px;
        border-radius: 20px;
        font-weight: 800;
        letter-spacing: 0.5px;
    }

    /* Main Content Styles */
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: var(--luxury-bg);
        position: relative;
    }

    .chat-header {
        padding: 1.25rem 2rem;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        z-index: 10;
        border-bottom: 1px solid #e2e8f0;
    }

    .chat-header .user-meta h5 {
        margin: 0;
        font-weight: 800;
        color: var(--luxury-dark);
        font-family: 'Playfair Display', serif;
    }

    .msg-area {
        flex: 1;
        overflow-y: auto;
        padding: 2.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.75rem;
        scroll-behavior: smooth;
    }

    .msg-group {
        display: flex;
        flex-direction: column;
        max-width: 70%;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .msg-group.received { align-self: flex-start; }
    .msg-group.sent { align-self: flex-end; align-items: flex-end; }

    .msg-bubble {
        padding: 1.15rem 1.4rem;
        border-radius: 22px;
        font-size: 0.95rem;
        line-height: 1.6;
        position: relative;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02), 0 1px 2px rgba(0,0,0,0.03);
    }

    .received .msg-bubble {
        background: var(--message-bg-received);
        color: #334155;
        border-bottom-left-radius: 4px;
        border: 1px solid #e2e8f0;
    }

    .sent .msg-bubble {
        background: var(--message-bg-sent);
        color: #fff;
        border-bottom-right-radius: 4px;
    }

    .ai .msg-bubble {
        background: var(--message-bg-ai);
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .msg-meta {
        margin-top: 0.65rem;
        font-size: 0.725rem;
        color: #94a3b8;
        display: flex;
        gap: 0.6rem;
        align-items: center;
        padding: 0 0.5rem;
    }

    .sender-tag {
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-size: 0.65rem;
    }

    .received .sender-tag { color: var(--luxury-dark); }
    .sent .sender-tag { color: var(--luxury-gold); }
    .ai .sender-tag { color: #d97706; }

    /* Input Area */
    .chat-input-container {
        padding: 1.75rem 2.5rem;
        background: #fff;
        border-top: 1px solid #e2e8f0;
    }

    .input-wrapper {
        background: #f8fafc;
        border-radius: 20px;
        padding: 0.6rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        border: 1px solid #e2e8f0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
    }

    .input-wrapper:focus-within {
        border-color: var(--luxury-gold);
        background: #fff;
        box-shadow: 0 10px 25px -5px rgba(175, 146, 69, 0.15), 0 8px 10px -6px rgba(175, 146, 69, 0.1);
        transform: translateY(-2px);
    }

    .chat-input {
        flex: 1;
        border: none;
        background: transparent;
        padding: 0.8rem 1.2rem;
        font-size: 1rem;
        outline: none;
        color: #1e293b;
    }

    .chat-input::placeholder { color: #94a3b8; }

    .send-btn {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: var(--luxury-dark);
        color: #fff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        font-size: 1.1rem;
    }

    .send-btn:hover {
        background: #000;
        transform: scale(1.05) rotate(-5deg);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    /* Empty State */
    .empty-state {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        background: #fff;
    }

    .empty-state .icon-box {
        width: 120px;
        height: 120px;
        background: #f8fafc;
        border-radius: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2rem;
        color: var(--luxury-gold);
        font-size: 3.5rem;
        transform: rotate(-10deg);
        box-shadow: 20px 20px 60px #d9d9d9, -20px -20px 60px #ffffff;
    }

    .empty-state h3 {
        font-family: 'Playfair Display', serif;
        font-weight: 800;
        color: var(--luxury-dark);
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* Markdown styling in bubbles */
    .msg-bubble p { margin-bottom: 0.75rem; }
    .msg-bubble p:last-child { margin-bottom: 0; }
    .msg-bubble ul, .msg-bubble ol { padding-left: 1.5rem; margin-bottom: 0.75rem; }
    .msg-bubble strong { font-weight: 800; }
</style>

<div class="chat-app">
    <!-- Sidebar -->
    <aside class="chat-sidebar">
        <div class="sidebar-header">
            <h4 class="fw-bold mb-4" style="font-family: 'Playfair Display', serif; color: var(--luxury-dark); letter-spacing: -0.5px;">Concierge Chat</h4>
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" id="searchChat" class="form-control search-input" placeholder="Tìm kiếm khách hàng...">
            </div>
        </div>
        
        <div class="chat-list custom-scrollbar">
            @foreach($chats as $chat)
                @php 
                    $identifier = $chat->MaKH ?? $chat->session_id; 
                    $name = $chat->customer->HoTen ?? 'Guest Visitor';
                    $initial = strtoupper(substr($name, 0, 1));
                    $isUnread = !$chat->lastMessage->is_read && $chat->lastMessage->sender != 'admin';
                @endphp
                <div class="chat-item {{ $isUnread ? 'bg-light-gold' : '' }}" onclick="loadChat('{{ $identifier }}', this)">
                    <div class="avatar-wrapper">
                        <div class="avatar">{{ $initial }}</div>
                        <div class="status-indicator"></div>
                    </div>
                    <div class="chat-info">
                        <h6>
                            <span class="text-truncate" style="max-width: 180px;">{{ $name }}</span>
                            <span class="time">{{ $chat->lastMessage->created_at->diffForHumans(null, true) }}</span>
                        </h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="last-msg mb-0">
                                @if($chat->lastMessage->sender == 'admin') <span style="color: var(--luxury-gold); font-weight: 700;">Bạn:</span> 
                                @elseif($chat->lastMessage->sender == 'ai') <span style="color: #d97706; font-weight: 700;">AI:</span> @endif
                                {{ $chat->lastMessage->message }}
                            </p>
                            @if($isUnread)
                                <span class="unread-badge">MỚI</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </aside>

    <!-- Main Chat -->
    <main class="chat-main" id="chatMain" style="display: none;">
        <header class="chat-header">
            <div class="d-flex align-items-center gap-3">
                <div id="activeChatAvatar" class="avatar" style="width: 48px; height: 48px; font-size: 1.1rem; border: 2px solid var(--luxury-gold);">?</div>
                <div class="user-meta">
                    <h5 id="activeChatName">Loading...</h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="status-indicator" style="position: static; display: inline-block;"></span>
                        <small class="text-success fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Trực tuyến</small>
                    </div>
                </div>
            </div>
            <div class="actions">
                <button class="btn btn-white border-0 shadow-sm rounded-pill px-4 py-2 fw-bold text-dark transition-all hover-translate-y-1" onclick="refreshMessages()" style="font-size: 0.85rem;">
                    <i class="fas fa-sync-alt me-2 text-gold-primary"></i> LÀM MỚI
                </button>
            </div>
        </header>

        <div class="msg-area custom-scrollbar" id="msgArea">
            <!-- Messages load here -->
        </div>

        <footer class="chat-input-container">
            <form id="replyForm">
                <div class="input-wrapper">
                    <input type="text" id="replyInput" class="chat-input" placeholder="Nhập tin nhắn phản hồi của bạn..." autocomplete="off">
                    <button type="submit" class="send-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
            <div class="d-flex justify-content-center align-items-center gap-3 mt-3">
                <div style="height: 1px; flex: 1; background: linear-gradient(to right, transparent, #e2e8f0);"></div>
                <small class="text-muted" style="font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase; font-weight: 700;">
                    <i class="fas fa-shield-alt me-1 text-gold-primary"></i> LUXURY AI CO-PILOT ACTIVE
                </small>
                <div style="height: 1px; flex: 1; background: linear-gradient(to left, transparent, #e2e8f0);"></div>
            </div>
        </footer>
    </main>

    <!-- Empty State -->
    <div class="empty-state" id="emptyState">
        <div class="icon-box">
            <i class="fas fa-paper-plane"></i>
        </div>
        <h3>Concierge Center</h3>
        <p class="text-muted mx-auto" style="max-width: 400px; font-size: 1.05rem; line-height: 1.6;">Chọn một cuộc trò chuyện từ danh sách bên trái để bắt đầu cung cấp dịch vụ hỗ trợ đẳng cấp cho khách hàng của chúng tôi.</p>
        <div class="mt-5 d-flex gap-4">
            <div class="text-center">
                <div class="h4 mb-0 fw-bold" style="color: var(--luxury-dark);">{{ count($chats) }}</div>
                <small class="text-muted text-uppercase ls-1" style="font-size: 0.7rem;">Hội thoại</small>
            </div>
            <div style="width: 1px; background: #e2e8f0;"></div>
            <div class="text-center">
                <div class="h4 mb-0 fw-bold text-success">24/7</div>
                <small class="text-muted text-uppercase ls-1" style="font-size: 0.7rem;">AI Trực tuyến</small>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
let currentIdentifier = null;

async function loadChat(identifier, element) {
    currentIdentifier = identifier;
    
    // UI Update
    document.querySelectorAll('.chat-item').forEach(i => i.classList.remove('active'));
    element.classList.add('active');
    element.classList.remove('bg-light-gold');
    const badge = element.querySelector('.unread-badge');
    if(badge) badge.remove();

    document.getElementById('chatMain').style.display = 'flex';
    document.getElementById('emptyState').style.display = 'none';

    // Set Header
    document.getElementById('activeChatName').innerText = element.querySelector('h6 span').innerText;
    document.getElementById('activeChatAvatar').innerText = element.querySelector('.avatar').innerText;

    await refreshMessages();
}

async function refreshMessages() {
    if(!currentIdentifier) return;

    try {
        const response = await fetch(`/admin/chat/${currentIdentifier}`);
        const messages = await response.json();
        
        const msgArea = document.getElementById('msgArea');
        const isAtBottom = msgArea.scrollHeight - msgArea.scrollTop <= msgArea.clientHeight + 150;

        msgArea.innerHTML = '';
        
        messages.forEach(m => {
            const group = document.createElement('div');
            group.className = `msg-group ${m.sender === 'admin' ? 'sent' : 'received'} ${m.sender === 'ai' ? 'ai' : ''}`;
            
            let senderName = 'Khách hàng';
            if(m.sender === 'ai') senderName = 'Luxury AI';
            if(m.sender === 'admin') senderName = 'Quản trị viên';

            const time = new Date(m.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            const content = (m.sender === 'ai' || m.sender === 'admin') ? marked.parse(m.message) : m.message;
            
            group.innerHTML = `
                <div class="msg-bubble">${content}</div>
                <div class="msg-meta">
                    <span class="sender-tag">${senderName}</span>
                    <span>•</span>
                    <span>${time}</span>
                </div>
            `;
            msgArea.appendChild(group);
        });
        
        if(isAtBottom) {
            msgArea.scrollTop = msgArea.scrollHeight;
        }
    } catch (error) {
        console.error('Error refreshing messages:', error);
    }
}

document.getElementById('replyForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const input = document.getElementById('replyInput');
    const text = input.value.trim();
    if(!text || !currentIdentifier) return;

    try {
        const response = await fetch('/admin/chat/reply', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                message: text,
                identifier: currentIdentifier
            })
        });

        if(response.ok) {
            input.value = '';
            await refreshMessages();
            const msgArea = document.getElementById('msgArea');
            msgArea.scrollTop = msgArea.scrollHeight;
        }
    } catch (error) {
        console.error('Error sending reply:', error);
    }
});

// Auto refresh every 7 seconds
setInterval(() => {
    if(currentIdentifier) {
        refreshMessages();
    }
}, 7000);

// Search functionality
document.getElementById('searchChat').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    document.querySelectorAll('.chat-item').forEach(item => {
        const name = item.querySelector('h6 span').innerText.toLowerCase();
        item.style.display = name.includes(term) ? 'flex' : 'none';
    });
});
</script>
@endsection
