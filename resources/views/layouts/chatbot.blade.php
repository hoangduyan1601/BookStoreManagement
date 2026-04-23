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
    #chatbot-window.active { display: flex !important; animation: popIn 0.3s ease; }
    @keyframes popIn {
        from { opacity: 0; transform: scale(0.8) translateY(20px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('chatbot-toggle');
    const window = document.getElementById('chatbot-window');
    const close = document.getElementById('close-chat');
    const form = document.getElementById('chat-form');
    const input = document.getElementById('chat-input');
    const msgBox = document.getElementById('chat-messages');

    toggle.addEventListener('click', () => {
        window.classList.toggle('active');
        document.getElementById('bot-notif').style.display = 'none';
        if(window.classList.contains('active')) input.focus();
    });

    close.addEventListener('click', () => window.classList.remove('active'));

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const text = input.value.trim();
        if(!text) return;

        // Add User Message
        appendMessage(text, 'user');
        input.value = '';

        // Add Typing Loader
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
            
            document.getElementById(loaderId).remove();
            appendMessage(data.reply, 'bot');
        } catch (error) {
            console.error(error);
            document.getElementById(loaderId).remove();
            appendMessage('Xin lỗi, tôi đang gặp trục trặc kỹ thuật. Vui lòng thử lại sau!', 'bot');
        }
    });

    function appendMessage(text, side) {
        const msg = document.createElement('div');
        msg.className = side === 'user' ? 'user-msg msg-bubble' : 'bot-msg msg-bubble';
        if(side === 'bot') {
            msg.style = 'align-self: flex-start; background: #fff; padding: 12px 16px; border-radius: 1rem 1rem 1rem 0; box-shadow: 0 2px 4px rgba(0,0,0,0.05); max-width: 85%; font-size: 0.9rem; border: 1px solid #e2e8f0;';
        }
        msg.textContent = text;
        msgBox.appendChild(msg);
        msgBox.scrollTop = msgBox.scrollHeight;
    }
});
</script>
