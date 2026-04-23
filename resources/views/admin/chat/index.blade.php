@extends('layouts.admin')

@section('title', 'Trung tâm Hỗ trợ Trực tuyến')

@section('content')
<style>
    .chat-container { height: calc(100vh - 250px); min-height: 500px; display: flex; background: white; border-radius: 1.5rem; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
    .chat-sidebar { width: 350px; border-right: 1px solid #f1f5f9; display: flex; flex-direction: column; }
    .chat-main { flex: 1; display: flex; flex-direction: column; background: #f8fafc; }
    .chat-list { overflow-y: auto; flex: 1; }
    .chat-item { padding: 1.25rem; border-bottom: 1px solid #f8fafc; cursor: pointer; transition: all 0.2s; border-left: 4px solid transparent; }
    .chat-item:hover { background: #f1f5f9; }
    .chat-item.active { background: #eff6ff; border-left-color: #3b82f6; }
    .msg-area { flex: 1; overflow-y: auto; padding: 2rem; display: flex; flex-direction: column; gap: 1rem; }
    .msg-bubble { max-width: 75%; padding: 0.75rem 1.25rem; border-radius: 1rem; font-size: 0.9rem; position: relative; }
    .msg-user { align-self: flex-start; background: white; border: 1px solid #e2e8f0; border-radius: 1rem 1rem 1rem 0; }
    .msg-admin { align-self: flex-end; background: #1e293b; color: white; border-radius: 1rem 1rem 0 1rem; }
    .msg-ai { align-self: flex-start; background: #f1f5f9; border-radius: 1rem 1rem 1rem 0; font-style: italic; opacity: 0.8; }
</style>

<div class="container-fluid p-0">
    <div class="dashboard-header d-flex align-items-center justify-content-between mb-4" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); padding: 2rem; border-radius: 1.5rem; color: white;">
        <div>
            <h2 class="fw-bold mb-1">Trung Tâm Hỗ Trợ</h2>
            <p class="mb-0 text-white-50">Tư vấn và giải đáp thắc mắc khách hàng thời gian thực</p>
        </div>
    </div>

    <div class="chat-container">
        <!-- Sidebar -->
        <div class="chat-sidebar">
            <div class="p-3 border-bottom">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 rounded-start-pill"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control bg-light border-0 rounded-end-pill" placeholder="Tìm hội thoại...">
                </div>
            </div>
            <div class="chat-list custom-scrollbar">
                @foreach($chats as $chat)
                    @php $identifier = $chat->MaKH ?? $chat->session_id; @endphp
                    <div class="chat-item" onclick="loadChat('{{ $identifier }}', this)">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; font-weight: bold;">
                                {{ strtoupper(substr($chat->customer->HoTen ?? 'Guest', 0, 1)) }}
                            </div>
                            <div class="flex-1 overflow-hidden">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 fw-bold text-dark text-truncate">{{ $chat->customer->HoTen ?? 'Khách vãng lai' }}</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">{{ $chat->lastMessage->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-0 text-muted small text-truncate">
                                    @if($chat->lastMessage->sender == 'admin') <i class="fas fa-reply me-1"></i> @endif
                                    {{ $chat->lastMessage->message }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="chat-main" id="chatMain" style="display: none;">
            <div class="p-3 border-bottom bg-white d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div id="activeChatAvatar" class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">?</div>
                    <div>
                        <h6 class="mb-0 fw-bold" id="activeChatName">Đang tải...</h6>
                        <small class="text-success"><i class="fas fa-circle me-1" style="font-size: 8px;"></i>Trực tuyến</small>
                    </div>
                </div>
            </div>

            <div class="msg-area custom-scrollbar" id="msgArea">
                <!-- Messages load here -->
            </div>

            <div class="p-3 bg-white border-top">
                <form id="replyForm" class="d-flex gap-2">
                    <input type="text" id="replyInput" class="form-control rounded-pill border-light px-3" placeholder="Nhập câu trả lời của bạn..." autocomplete="off">
                    <button type="submit" class="btn btn-dark rounded-pill px-4 shadow-sm">
                        <i class="fas fa-paper-plane me-2"></i>Gửi
                    </button>
                </form>
            </div>
        </div>

        <!-- Empty State -->
        <div class="chat-main justify-content-center align-items-center text-center" id="emptyState">
            <div class="p-5">
                <i class="fas fa-comments fs-1 text-muted mb-4 opacity-25"></i>
                <h5 class="fw-bold text-dark">Chọn một cuộc hội thoại</h5>
                <p class="text-muted">Chọn khách hàng từ danh sách bên trái để bắt đầu hỗ trợ.</p>
            </div>
        </div>
    </div>
</div>

<script>
let currentIdentifier = null;

async function loadChat(identifier, element) {
    currentIdentifier = identifier;
    
    // UI Update
    document.querySelectorAll('.chat-item').forEach(i => i.classList.remove('active'));
    element.classList.add('active');
    document.getElementById('chatMain').style.display = 'flex';
    document.getElementById('emptyState').style.display = 'none';

    // Set Header
    document.getElementById('activeChatName').innerText = element.querySelector('h6').innerText;
    document.getElementById('activeChatAvatar').innerText = element.querySelector('.bg-primary').innerText;

    const response = await fetch(`/admin/chat/${identifier}`);
    const messages = await response.json();
    
    const msgArea = document.getElementById('msgArea');
    msgArea.innerHTML = '';
    
    messages.forEach(m => {
        const div = document.createElement('div');
        div.className = `msg-bubble msg-${m.sender}`;
        div.innerHTML = `<div>${m.message}</div><small style="font-size: 0.65rem; opacity: 0.6;">${new Date(m.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</small>`;
        msgArea.appendChild(div);
    });
    
    msgArea.scrollTop = msgArea.scrollHeight;
}

document.getElementById('replyForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const input = document.getElementById('replyInput');
    const text = input.value.trim();
    if(!text || !currentIdentifier) return;

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
        const m = await response.json();
        const div = document.createElement('div');
        div.className = 'msg-bubble msg-admin';
        div.innerHTML = `<div>${m.message}</div><small style="font-size: 0.65rem; opacity: 0.6;">Vừa xong</small>`;
        document.getElementById('msgArea').appendChild(div);
        document.getElementById('msgArea').scrollTop = msgArea.scrollHeight;
    }
});

// Auto refresh every 5 seconds if chat is active
setInterval(() => {
    if(currentIdentifier) {
        // Có thể load lại chat ở đây
    }
}, 5000);
</script>
@endsection
