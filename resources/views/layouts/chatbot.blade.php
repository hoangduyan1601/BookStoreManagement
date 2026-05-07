<!-- Chatbot Floating UI -->
<div id="chatbot-wrapper" style="position: fixed; bottom: 30px; right: 30px; z-index: 9999; font-family: 'Inter', sans-serif;">
    <!-- Chatbot Toggle Button -->
    <button id="chatbot-toggle" class="btn shadow-lg d-flex align-items-center justify-content-center" 
            style="width: 65px; height: 65px; border-radius: 50%; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border: 3px solid #fff; transition: all 0.3s ease;">
        <i class="fas fa-robot text-white fs-2"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display:none;" id="bot-notif">1</span>
    </button>

    <!-- Chat Window -->
    <div id="chatbot-window" class="shadow-2xl" 
         style="display: none; position: absolute; bottom: 85px; right: 0; width: 380px; height: 550px; background: #fff; border-radius: 1.5rem; overflow: hidden; flex-direction: column; border: 1px solid #e2e8f0; transform-origin: bottom right; transition: all 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);">
        
        <!-- Header -->
        <div class="p-3 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); color: white;">
            <div class="d-flex align-items-center">
                <div class="bg-white rounded-circle p-2 me-2" style="width: 40px; height: 40px;">
                    <i class="fas fa-robot text-dark"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold">Luxury AI Assistant</h6>
                    <small class="opacity-75"><i class="fas fa-circle text-success me-1" style="font-size: 8px;"></i>Đang trực tuyến</small>
                </div>
            </div>
            <button id="close-chat" class="btn btn-sm text-white opacity-75 hover-opacity-100"><i class="fas fa-times"></i></button>
        </div>

        <!-- Messages Body -->
        <div id="chat-messages" class="p-4" style="flex: 1; overflow-y: auto; background: #f8fafc; display: flex; flex-direction: column; gap: 15px;">
            <div class="bot-msg msg-bubble" style="align-self: flex-start; background: #fff; padding: 12px 16px; border-radius: 1rem 1rem 1rem 0; box-shadow: 0 2px 4px rgba(0,0,0,0.05); max-width: 85%; font-size: 0.9rem; border: 1px solid #e2e8f0;">
                Chào mừng bạn đến với Luxury BookStore! 👋 Tôi là trợ lý AI thông minh. Tôi có thể giúp gì cho bạn?
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-3 border-top bg-white">
            <!-- Quick Replies -->
            <div class="d-flex gap-2 mb-2 overflow-auto custom-scrollbar quick-replies" style="white-space: nowrap; padding-bottom: 4px;">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 text-nowrap quick-reply-btn" style="font-size: 0.8rem;">🔥 Khuyến mãi</button>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 text-nowrap quick-reply-btn" style="font-size: 0.8rem;">✨ Sách mới</button>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 text-nowrap quick-reply-btn" style="font-size: 0.8rem;">📦 Tra cứu đơn hàng</button>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 text-nowrap quick-reply-btn" style="font-size: 0.8rem;">🏆 Bán chạy nhất</button>
            </div>
            
            <form id="chat-form" class="d-flex gap-2 align-items-center">
                <input type="text" id="chat-input" class="form-control border-0 bg-light rounded-pill px-3" placeholder="Nhập tin nhắn..." autocomplete="off" style="font-size: 0.9rem;">
                <button type="submit" class="btn btn-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
            <div class="text-center mt-2">
                <small class="text-muted" style="font-size: 10px;">Powered by Luxury AI Engine</small>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Scrollbar for Quick Replies */
    .quick-replies::-webkit-scrollbar { height: 4px; }
    .quick-replies::-webkit-scrollbar-track { background: transparent; }
    .quick-replies::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .quick-replies::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    .quick-reply-btn:hover { background-color: #f8fafc; color: #1e293b; border-color: #cbd5e1; }

    #chatbot-window.active { display: flex !important; animation: popIn 0.3s ease; }
    @keyframes popIn {
        from { opacity: 0; transform: scale(0.8) translateY(20px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    @keyframes shake {
        0% { transform: scale(1); }
        10%, 20% { transform: scale(1.1) rotate(-3deg); }
        30%, 50%, 70%, 90% { transform: scale(1.1) rotate(3deg); }
        40%, 60%, 80% { transform: scale(1.1) rotate(-3deg); }
        100% { transform: scale(1) rotate(0); }
    }
    .shake-animation { animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both; }

    .user-msg {
        align-self: flex-end !important;
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%) !important;
        color: white !important;
        border-radius: 1rem 1rem 0 1rem !important;
        padding: 12px 16px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        max-width: 85%;
        font-size: 0.9rem;
    }
    .user-msg p, .bot-msg p { margin-bottom: 0.5rem; }
    .user-msg p:last-child, .bot-msg p:last-child { margin-bottom: 0; }
    
    .typing-loader {
        width: 40px;
        height: 20px;
        display: flex;
        gap: 4px;
        align-items: center;
        justify-content: center;
    }
    .typing-loader div { width: 6px; height: 6px; background: #94a3b8; border-radius: 50%; animation: bounce 0.6s infinite alternate; }
    .typing-loader div:nth-child(2) { animation-delay: 0.2s; }
    .typing-loader div:nth-child(3) { animation-delay: 0.4s; }
    @keyframes bounce { from { transform: translateY(0); } to { transform: translateY(-5px); } }
</style>
<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
// Function cho AI Card để thêm vào giỏ hàng
async function addToCartAI(maSP) {
    try {
        const response = await fetch("{{ route('cart.add') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ MaSP: maSP, SoLuong: 1 })
        });
        const data = await response.json();
        if(data.status === 'success') {
            alert('Đã thêm sản phẩm vào giỏ hàng!');
            if(window.updateCartBadge) window.updateCartBadge();
        } else {
            alert(data.message || 'Có lỗi xảy ra.');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        alert('Vui lòng đăng nhập để thực hiện tính năng này.');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Cấu hình marked
    marked.setOptions({
        sanitize: false,
        headerIds: false,
        mangle: false
    });

    const toggle = document.getElementById('chatbot-toggle');
    const chatWindow = document.getElementById('chatbot-window');
    const close = document.getElementById('close-chat');
    const form = document.getElementById('chat-form');
    const input = document.getElementById('chat-input');
    const msgBox = document.getElementById('chat-messages');

    toggle.addEventListener('click', async () => {
        const isActive = chatWindow.classList.toggle('active');
        const notif = document.getElementById('bot-notif');
        
        if(isActive) {
            notif.style.display = 'none';
            notif.textContent = '0';
            input.focus();
            await loadHistory();
            fetch("{{ route('chatbot.mark-read') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
        }
    });

    close.addEventListener('click', () => chatWindow.classList.remove('active'));

    async function checkUnread() {
        if(chatWindow.classList.contains('active')) return;
        
        try {
            const response = await fetch("{{ route('chatbot.unread') }}");
            const data = await response.json();
            if(data.unread_count > 0) {
                const notif = document.getElementById('bot-notif');
                notif.textContent = data.unread_count;
                notif.style.display = 'block';
                toggle.classList.add('shake-animation');
                setTimeout(() => toggle.classList.remove('shake-animation'), 500);
            }
        } catch (error) {
            console.error('Error checking unread:', error);
        }
    }

    checkUnread();
    setInterval(checkUnread, 10000);

    setInterval(async () => {
        if(chatWindow.classList.contains('active')) {
            await loadHistory(true);
        }
    }, 5000);

    async function loadHistory(force = false) {
        if(msgBox.dataset.loaded === 'true' && !force) return;
        
        try {
            const response = await fetch("{{ route('chatbot.history') }}");
            const messages = await response.json();
            
            if(messages.length > 0) {
                const currentCount = msgBox.querySelectorAll('.msg-bubble').length;
                if(msgBox.dataset.loaded !== 'true' || messages.length > currentCount) {
                    msgBox.innerHTML = ''; 
                    messages.forEach(m => {
                        appendMessage(m.message, m.sender === 'user' ? 'user' : 'bot', true);
                    });
                    msgBox.scrollTop = msgBox.scrollHeight;
                }
            }
            msgBox.dataset.loaded = 'true';
        } catch (error) {
            console.error('Error loading history:', error);
        }
    }

    document.querySelectorAll('.quick-reply-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const text = this.textContent.replace(/[🔥✨📦🏆]/g, '').trim();
            input.value = text;
            form.dispatchEvent(new Event('submit'));
        });
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const text = input.value.trim();
        if(!text) return;

        appendMessage(text, 'user');
        input.value = '';

        const loaderId = 'loader-' + Date.now();
        const loader = document.createElement('div');
        loader.id = loaderId;
        loader.className = 'bot-msg msg-bubble';
        loader.style = 'align-self: flex-start; background: #fff; padding: 12px 16px; border-radius: 1rem 1rem 1rem 0; border: 1px solid #e2e8f0;';
        loader.innerHTML = '<div class="typing-loader"><div></div><div></div><div></div></div>';
        msgBox.appendChild(loader);
        msgBox.scrollTop = msgBox.scrollHeight;

        try {
            const response = await fetch("{{ route('chatbot.chat') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: text })
            });
            const data = await response.json();
            
            const l = document.getElementById(loaderId);
            if(l) l.remove();
            appendMessage(data.reply, 'bot');
        } catch (error) {
            console.error(error);
            const l = document.getElementById(loaderId);
            if(l) l.remove();
            appendMessage('Xin lỗi, tôi đang gặp trục trặc kỹ thuật. Vui lòng thử lại sau!', 'bot');
        }
    });

    function appendMessage(text, side, skipNotif = false) {
        const msg = document.createElement('div');
        msg.className = side === 'user' ? 'user-msg msg-bubble' : 'bot-msg msg-bubble';
        if(side === 'bot') {
            msg.style = 'align-self: flex-start; background: #fff; padding: 12px 16px; border-radius: 1rem 1rem 1rem 0; box-shadow: 0 2px 4px rgba(0,0,0,0.05); max-width: 85%; font-size: 0.9rem; border: 1px solid #e2e8f0;';
            msg.innerHTML = marked.parse(text);

            if(!chatWindow.classList.contains('active') && !skipNotif) {
                const notif = document.getElementById('bot-notif');
                let count = parseInt(notif.textContent) || 0;
                count++;
                notif.textContent = count;
                notif.style.display = 'block';
                
                toggle.classList.add('shake-animation');
                setTimeout(() => toggle.classList.remove('shake-animation'), 500);
            }
        } else {
            msg.textContent = text;
        }
        msgBox.appendChild(msg);
        if(!skipNotif) msgBox.scrollTop = msgBox.scrollHeight;
    }
});
</script>
