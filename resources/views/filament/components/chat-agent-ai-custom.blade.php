<style>
    :root {
        /* ========= Colores ========= */
        --chat--color-background: #fff;
        --chat--color-text: #888888;
        --chat--color--primary: #3eca8c;
        --chat--color--primary-shade-50: #36b87d;
        --chat--color--primary--shade-100: #2f9f6c;

        --chat-color-header: #fff;
        --chat-color-msg-bot: var(--chat--color-background);
    }

    .dark {
        --chat-color-header-text: #101627;
    }


    /* Burbuja flotante (botón para abrir/cerrar) */
    #chat-toggle-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: var(--chat--color--primary);
        color: #fff;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        z-index: 9999;
        transition: transform .15s ease;
    }

    #chat-toggle-btn:hover {
        transform: scale(1.05);
        background: var(--chat--color--primary-shade-50)
    }

    #chat-toggle-btn:active {
        background: var(--chat--color--primary--shade-100)
    }

    /* Ventana del chat */
    #custom-chat {
        position: fixed;
        bottom: 90px;
        right: 20px;
        width: 380px;
        max-width: 90vw;
        height: 560px;
        max-height: 75vh;
        background: var(--chat--color-background);
        border-radius: 14px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        display: flex;
        flex-direction: column;
        font-family: system-ui, sans-serif;
        z-index: 9999;
        overflow: hidden;
        opacity: 0;
        transform: translateY(16px) scale(.97);
        pointer-events: none;
        transition: opacity .18s ease, transform .18s ease;
    }

    #custom-chat.open {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    #chat-header {
        background: var(--chat--color--primary);
        color: var(--chat-color-header);
        padding: 14px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    #chat-header .title {
        font-weight: 600;
        font-size: 15px;
    }

    #chat-header .subtitle {
        font-size: 12px;
        opacity: .85;
    }

    #chat-close {
        background: none;
        border: none;
        color: #fff;
        font-size: 18px;
        cursor: pointer;
        opacity: .8;
    }

    #chat-close:hover {
        opacity: 1;
    }

    #chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 14px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        background: var(--chat--color-background);
        color: #000;
    }

    .msg {
        max-width: 80%;
        padding: 8px 12px;
        border-radius: 14px;
        font-size: 14px;
        line-height: 1.4;
        white-space: pre-wrap;
    }

    .msg.bot {
        align-self: flex-start;
        background: var(--chat-color-msg-bot);
        border: 1px solid #e5e7eb;
        border-bottom-left-radius: 4px;
    }

    .msg.user {
        align-self: flex-end;
        background: var(--chat--color--primary-shade-50);
        color: #fff;
        border-bottom-right-radius: 4px;
    }

    .options-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 4px;
        align-self: flex-start;
        max-width: 90%;
    }

    .option-btn {
        background: #fff;
        border: 1px solid var(--chat--color--primary);
        color: var(--chat--color--primary);
        padding: 6px 12px;
        border-radius: 16px;
        font-size: 13px;
        cursor: pointer;
        transition: background .15s, color .15s;
    }

    .option-btn:hover {
        background: var(--chat--color--primary);
        color: #fff;
    }

    .option-btn:disabled {
        opacity: .5;
        cursor: default;
    }

    #chat-typing {
        align-self: flex-start;
        font-size: 13px;
        color: #888;
        padding: 0 4px;
    }

    #chat-input-row {
        display: flex;
        gap: 8px;
        padding: 10px;
        border-top: 1px solid #eee;
        background: #fff;
    }

    #chat-input {
        flex: 1;
        border: 1px solid #ddd;
        border-radius: 20px;
        padding: 8px 14px;
        font-size: 14px;
        outline: none;
        resize: none;
        max-height: 80px;
        color: #000;
    }

    #chat-send {
        background: var(--chat--color--primary);
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 38px;
        height: 38px;
        cursor: pointer;
        flex-shrink: 0;
    }

    #chat-send:disabled {
        opacity: .5;
        cursor: default;
    }
</style>

<button id="chat-toggle-btn">💬</button>

<div id="custom-chat">
    <div id="chat-header">
        <div>
            <div class="title">🤖 Asistente de Seguros</div>
            <div class="subtitle">Respondo tus consultas en línea.</div>
        </div>
        <button id="chat-close">✕</button>
    </div>
    <div id="chat-messages"></div>
    <div id="chat-input-row">
        <textarea id="chat-input" rows="1" placeholder="Escribe tu consulta..."></textarea>
        <button id="chat-send">➤</button>
    </div>
</div>

<script>
    (function() {
        const webhookUrl = '{{ $chatUrl }}';
        const token = '{{ $token }}';
        const sessionId = localStorage.getItem('chat_session_id') || crypto.randomUUID();
        localStorage.setItem('chat_session_id', sessionId);

        const toggleBtn = document.getElementById('chat-toggle-btn');
        const chatWindow = document.getElementById('custom-chat');
        const closeBtn = document.getElementById('chat-close');
        const messagesEl = document.getElementById('chat-messages');
        const inputEl = document.getElementById('chat-input');
        const sendBtn = document.getElementById('chat-send');

        let initialized = false;

        function openChat() {
            chatWindow.classList.add('open');
            toggleBtn.textContent = '✕';
            if (!initialized) {
                initialized = true;
                addMessage('¡Hola! 👋', 'bot');
                addMessage('¿En qué puedo ayudarte hoy?', 'bot');
            }
            inputEl.focus();
        }

        function closeChat() {
            chatWindow.classList.remove('open');
            toggleBtn.textContent = '💬';
        }

        toggleBtn.addEventListener('click', () => {
            chatWindow.classList.contains('open') ? closeChat() : openChat();
        });
        closeBtn.addEventListener('click', closeChat);

        function addMessage(text, from) {
            const div = document.createElement('div');
            div.className = 'msg ' + from;
            div.textContent = text;
            messagesEl.appendChild(div);
            scrollDown();
            return div;
        }

        function addOptions(options) {
            console.log('es', options);

            const wrap = document.createElement('div');
            wrap.className = 'options-wrap';
            options.forEach(opt => {
                const btn = document.createElement('button');
                btn.className = 'option-btn';
                btn.textContent = opt.label;
                btn.onclick = () => {
                    wrap.querySelectorAll('button').forEach(b => b.disabled = true);
                    sendMessage(opt.label);
                };
                wrap.appendChild(btn);
            });
            messagesEl.appendChild(wrap);
            scrollDown();
        }

        function showTyping() {
            const div = document.createElement('div');
            div.id = 'chat-typing';
            div.textContent = 'Escribiendo...';
            messagesEl.appendChild(div);
            scrollDown();
        }

        function hideTyping() {
            document.getElementById('chat-typing')?.remove();
        }

        function scrollDown() {
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        async function sendMessage(text) {
            if (!text.trim()) return;

            addMessage(text, 'user');
            inputEl.value = '';
            setLoading(true);
            showTyping();

            try {
                const res = await fetch(webhookUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        sessionId,
                        chatInput: text,
                        metadata: {
                            token
                        },
                    }),
                });

                //console.log(res.body);

                const data = await res.json();
                hideTyping();



                const {
                    output,
                    actions
                } = JSON.parse(data.output)

                console.log(actions);


                const textInput = output ||
                    'No entendí tu consulta, ¿podés reformularla?';
                addMessage(textInput, 'bot');

                if (Array.isArray(actions) && actions.length) {
                    addOptions(actions);
                }
            } catch (err) {
                hideTyping();
                addMessage('Ocurrió un error al conectar con el asistente. Probá de nuevo.', 'bot');
                console.error('chat error:', err);
            } finally {
                setLoading(false);
            }
        }

        function setLoading(state) {
            sendBtn.disabled = state;
            inputEl.disabled = state;
        }

        sendBtn.addEventListener('click', () => sendMessage(inputEl.value));
        inputEl.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage(inputEl.value);
            }
        });
    })();
</script>
