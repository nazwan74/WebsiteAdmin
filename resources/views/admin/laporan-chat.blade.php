<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Laporan #{{ $laporan['id'] }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; overflow-x: hidden; }
        .chat-container { max-width: 900px; margin: 30px auto; }
        .chat-box { height: 60vh; overflow-y: auto; overflow-x: hidden; background: #fff; border-radius: 12px; border: 1px solid #e9ecef; padding: 16px; }

        /* Clean message layout (Tailwind-like, but with custom CSS) */
        .msg { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 14px; }
        .msg .avatar { width: 36px; height: 36px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; background: #e9ecef; color: #495057; flex: 0 0 36px; overflow: hidden; }
        .msg .avatar img { width: 36px; height: 36px; object-fit: cover; display: block; }
        .msg .avatar i { font-size: 1rem; color: #495057; }

        .msg .content { display: flex; flex-direction: column; width: 100%; max-width: 320px; line-height: 1.5; }
        @media (min-width: 992px) { .msg .content { max-width: 520px; } }
        @media (max-width: 576px) { .msg .content { max-width: 88%; } }

        .msg .header { display: flex; align-items: center; gap: 8px; }
        .msg .name { font-size: .875rem; font-weight: 600; color: #111827; }
        .msg .time { font-size: .875rem; font-weight: 400; color: #6b7280; }

        .msg .bubble { margin-top: 6px; padding: 10px 12px; border-radius: 14px; box-shadow: 0 2px 6px rgba(0,0,0,.05); word-break: break-word; overflow-wrap: anywhere; white-space: pre-wrap; }
        .msg.user .bubble { background: #f3f4f6; color: #111827; border-top-left-radius: 4px; }
        .msg.admin { justify-content: flex-end; }
        .msg.admin .content { align-items: flex-end; }
        .msg.admin .bubble { background: #1a73e8; color: #fff; border-top-right-radius: 4px; }

        .msg .status { margin-top: 4px; font-size: .8rem; color: #6b7280; }

        .date-separator { text-align: center; margin: 12px 0; }
        .date-separator span { display: inline-block; background: #f1f3f5; color: #6c757d; padding: 4px 10px; border-radius: 999px; font-size: .8rem; box-shadow: 0 1px 2px rgba(0,0,0,.05); }

        .composer { position: sticky; bottom: 0; background: #f8f9fa; padding-top: 8px; }
        .composer textarea { resize: none; }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="chat-container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0">Chat Laporan</h4>
                <div class="text-muted small">#{{ $laporan['id'] }} — {{ $laporan['judul'] ?? 'Laporan' }}</div>
            </div>
            <a href="{{ route('admin.laporan.detail', $laporan['id']) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div id="chatBox" class="chat-box"></div>

        <form id="sendForm" class="mt-3 d-flex gap-2 composer">
            <textarea id="messageInput" class="form-control" placeholder="Tulis pesan... (Enter untuk kirim, Shift+Enter baris baru)" rows="1" required></textarea>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-send"></i>
            </button>
        </form>
    </div>

    <script>
        const laporanId = @json($laporan['id']);
        const messagesUrl = @json(route('admin.laporan.chat.messages', $laporan['id']));
        const sendUrl = @json(route('admin.laporan.chat.send', $laporan['id']));
        let lastRenderKey = '';
        let isFetching = false;
        let poller = null;
        let lastMessageId = null;
        let lastMessageTime = null;

        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/\"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function formatDateSeparator(dateString) {
            try {
                const d = new Date(dateString);
                if (isNaN(d.getTime())) return '';
                return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            } catch (_) { return ''; }
        }

        function dateKey(dateString) {
            try {
                const d = new Date(dateString);
                if (isNaN(d.getTime())) return '';
                return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
            } catch (_) { return ''; }
        }

        function formatTimeOnly(dateString) {
            try {
                const d = new Date(dateString);
                if (isNaN(d.getTime())) return '';
                return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            } catch (_) { return ''; }
        }

        function displayNameFor(role) {
            return role === 'admin' ? 'Admin' : 'User';
        }

        function createMessageElement(m) {
            const role = (m.sender || '').toLowerCase() === 'admin' ? 'admin' : 'user';
            const text = m.message || m.text || m.content || m.bubble || '';
            const time = m.created_at || '';
            const item = document.createElement('div');
            item.className = `msg ${role}`;
            const timeOnly = formatTimeOnly(time);
            const name = displayNameFor(role);
            const leftAvatar = role === 'user' ? `<div class="avatar"><i class="bi bi-person-fill"></i></div>` : '';
            const rightAvatar = role === 'admin' ? `<div class="avatar"><i class="bi bi-person-fill"></i></div>` : '';

            item.innerHTML = `
                ${leftAvatar}
                <div class="content">
                    <div class="header">
                        <span class="name">${escapeHtml(name)}</span>
                        ${timeOnly ? `<span class="time">${escapeHtml(timeOnly)}</span>` : ''}
                    </div>
                    <div class="bubble">${escapeHtml(String(text))}</div>
                    ${role === 'admin' ? `<div class="status">Terkirim</div>` : ''}
                </div>
                ${rightAvatar}
            `;
            return item;
        }

        function renderMessages(messages) {
            const box = document.getElementById('chatBox');
            const wasAtBottom = box.scrollTop + box.clientHeight >= box.scrollHeight - 40;
            box.innerHTML = '';
            let lastDate = '';
            messages.forEach(m => {
                const time = m.created_at || '';
                const dKey = dateKey(time);
                if (dKey && dKey !== lastDate) {
                    const sep = document.createElement('div');
                    sep.className = 'date-separator';
                    sep.innerHTML = `<span>${formatDateSeparator(time)}</span>`;
                    box.appendChild(sep);
                    lastDate = dKey;
                }
                box.appendChild(createMessageElement(m));
            });
            if (wasAtBottom) {
                box.scrollTop = box.scrollHeight;
            }
        }

        function appendMessages(newMessages) {
            if (!newMessages || !newMessages.length) return;
            const box = document.getElementById('chatBox');
            const wasAtBottom = box.scrollTop + box.clientHeight >= box.scrollHeight - 40;
            let lastDateKey = lastMessageTime ? dateKey(lastMessageTime) : '';
            newMessages.forEach(m => {
                const time = m.created_at || '';
                const dKey = dateKey(time);
                if (dKey && dKey !== lastDateKey) {
                    const sep = document.createElement('div');
                    sep.className = 'date-separator';
                    sep.innerHTML = `<span>${formatDateSeparator(time)}</span>`;
                    box.appendChild(sep);
                    lastDateKey = dKey;
                }
                box.appendChild(createMessageElement(m));
            });
            if (wasAtBottom) {
                box.scrollTop = box.scrollHeight;
            }
        }

        function fetchMessages() {
            if (isFetching) return;
            isFetching = true;
            const url = lastMessageTime ? `${messagesUrl}?since=${encodeURIComponent(lastMessageTime)}` : messagesUrl;
            fetch(url, {
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(r => {
                    if (!r.ok) throw new Error('Network');
                    return r.json();
                })
                .then(data => {
                    if (data && data.status === 'success') {
                        const msgs = data.messages || [];
                        const last = msgs.length ? msgs[msgs.length - 1] : null;
                        if (!lastMessageTime) {
                            lastMessageId = last ? (last.id || null) : null;
                            lastMessageTime = last ? (last.created_at || null) : null;
                            lastRenderKey = `${msgs.length}|${lastMessageId || ''}|${lastMessageTime || ''}`;
                            renderMessages(msgs);
                        } else if (last && (new Date(last.created_at)).getTime() > (new Date(lastMessageTime)).getTime()) {
                            const newMsgs = msgs.filter(m => (new Date(m.created_at)).getTime() > (new Date(lastMessageTime)).getTime());
                            appendMessages(newMsgs);
                            lastMessageId = last.id || lastMessageId;
                            lastMessageTime = last.created_at || lastMessageTime;
                            lastRenderKey = `${msgs.length}|${lastMessageId || ''}|${lastMessageTime || ''}`;
                        }
                    }
                })
                .catch(() => {})
                .finally(() => { isFetching = false; });
        }

        // Polling ringan (pause saat tab tidak aktif)
        function startPolling() {
            if (poller) return;
            poller = setInterval(fetchMessages, 5000);
        }
        function stopPolling() {
            if (!poller) return;
            clearInterval(poller);
            poller = null;
        }
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) stopPolling(); else { fetchMessages(); startPolling(); }
        });
        window.addEventListener('beforeunload', stopPolling);
        fetchMessages();
        startPolling();

        document.getElementById('sendForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            if (!message) return;
            fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ message })
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    input.value = '';
                    fetchMessages();
                }
            })
            .catch(() => {});
        });


        // Enter to send, Shift+Enter new line, and auto-resize
        (function setupComposer() {
            const input = document.getElementById('messageInput');
            function autoResize() {
                input.style.height = 'auto';
                input.style.height = Math.min(input.scrollHeight, 160) + 'px';
            }
            input.addEventListener('input', autoResize);
            input.addEventListener('keydown', function(ev) {
                if (ev.key === 'Enter' && !ev.shiftKey) {
                    ev.preventDefault();
                    document.getElementById('sendForm').dispatchEvent(new Event('submit', { cancelable: true }));
                }
            });
            autoResize();
        })();
    </script>
</body>
</html>


