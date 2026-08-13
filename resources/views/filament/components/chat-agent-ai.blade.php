<link href="https://cdn.jsdelivr.net/npm/@n8n/chat/dist/style.css" rel="stylesheet" />

<style>
    :root {
        /* ========= Colores ========= */
        --chat--color--primary: #3eca8c;
        --chat--color--primary-shade-50: #36b87d;
        --chat--color--primary--shade-100: #2f9f6c;

        --chat--color--secondary: #3eca8c;
        --chat--color-secondary-shade-50: #36b87d;

        --chat--color-white: #ffffff;

        --chat--color-light: #ffffff;
        --chat--color-light-shade-50: #f8faf9;
        --chat--color-light-shade-100: #eef3f1;

        --chat--color-medium: #d1d5db;
        --chat--color-dark: #1f2937;
        --chat--color-disabled: #9ca3af;
        --chat--color-typing: #6b7280;

        /* ========= Ventana ========= */
        --chat--window--width: 380px;
        --chat--window--height: 600px;
        --chat--window--border-radius: 14px;

        /* ========= Encabezado ========= */
        --chat--header--background: #3eca8c;
        --chat--header--color: #ffffff;
        --chat--header--padding: 16px;

        /* ========= Mensajes ========= */
        --chat--message--font-size: 0.875rem;
        --chat--message--line-height: 1.45;

        --chat--message--bot--background: #f8faf9;
        --chat--message--bot--color: #1f2937;
        --chat--message--bot--border: 1px solid #eef3f1;

        --chat--message--user--background: #3eca8c;
        /*  --chat--message--user--color: #ffffff; */

        /* ========= Input ========= */
        --chat--textarea--height: 48px;

        /* ========= Botón flotante ========= */
        --chat--toggle--size: 56px;
        --chat--toggle--background: #3eca8c;
        --chat--toggle--hover--background: #36b87d;
    }

    /* Fuente un poco más pequeña */
    .n8n-chat {
        font-size: 13px;
    }

    .n8n-chat *,
    .n8n-chat input,
    .n8n-chat textarea,
    .n8n-chat button {
        font-size: 13px;
    }

    /* Input */
    .n8n-chat textarea,
    .n8n-chat input {
        border-radius: 10px;
        color: #1f2937 !important
    }

    /* Mensajes */
    .n8n-chat .chat-message {
        border-radius: 12px;
    }

    /* Scroll más fino */
    .n8n-chat ::-webkit-scrollbar {
        width: 6px;
    }

    .n8n-chat ::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 6px;
    }
</style>

<script type="module">
    import {
        createChat
    } from 'https://cdn.jsdelivr.net/npm/@n8n/chat/dist/chat.bundle.es.js';


    const response = () => {
        console.log('ess');
    }

    createChat({

        webhookUrl: '{{ $chatUrl }}',

        metadata: {
            token: '{{ $token }}',
        },

        initialMessages: [
            '¡Hola! 👋',
            '¿En qué puedo ayudarte hoy?'
        ],

        i18n: {
            es: {
                title: '🤖 Asistente de Seguros',
                subtitle: 'Respondo tus consultas en línea.',
                footer: '',
                getStarted: 'Comenzar conversación',
                inputPlaceholder: 'Escribe tu consulta...',
            },
        },

        defaultLanguage: 'es',
    });
</script>
