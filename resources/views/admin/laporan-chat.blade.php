<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        .msg .content { position: relative; }
        @media (min-width: 992px) { .msg .content { max-width: 520px; } }
        @media (max-width: 576px) { .msg .content { max-width: 88%; } }

        .msg .header { display: flex; align-items: center; gap: 8px; }
        .msg .name { font-size: .875rem; font-weight: 600; color: #111827; }
        .msg .time { font-size: .875rem; font-weight: 400; color: #6b7280; }

        .msg .bubble { margin-top: 6px; padding: 10px 12px; border-radius: 14px; box-shadow: 0 2px 6px rgba(0,0,0,.05); word-break: break-word; overflow-wrap: anywhere; white-space: pre-wrap; }
        
        /* User Message - Kiri, Warna Putih */
        .msg.user .bubble { background: #ffffff; color: #111827; border: 1px solid #e9ecef; border-top-left-radius: 4px; }
        .msg.user { justify-content: flex-start; }
        .msg.user .content { align-items: flex-start; }
        
        /* Admin Message - Kanan, Warna Biru */
        .msg.admin { justify-content: flex-end; }
        .msg.admin .content { align-items: flex-end; }
        .msg.admin .bubble { background: #1a73e8; color: #fff; border-top-right-radius: 4px; }

        .msg .status { margin-top: 4px; font-size: .8rem; color: #6b7280; }
        
        .msg .actions { margin-top: 4px; display: flex; gap: 4px; visibility: hidden; }
        .msg:hover .actions { visibility: visible; }
        .msg .actions button { padding: 2px 6px; font-size: .75rem; border: none; border-radius: 4px; cursor: pointer; background: #e9ecef; color: #495057; }
        .msg .actions button:hover { background: #dee2e6; }
        .msg.admin .actions button { background: #0d6efd; color: white; }
        .msg.admin .actions button:hover { background: #0b5ed7; }

        /* Inline Edit Mode */
        .msg.edit-mode .bubble { display: none; }
        .msg.edit-mode .actions { display: none; }
        .msg .edit-container { display: none; gap: 6px; margin-top: 6px; }
        .msg.edit-mode .edit-container { display: flex; flex-direction: column; }
        .msg .edit-container textarea { padding: 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.875rem; font-family: inherit; resize: vertical; min-height: 60px; }
        .msg .edit-container .edit-buttons { display: flex; gap: 6px; }
        .msg .edit-container button { padding: 6px 12px; font-size: 0.8rem; border: none; border-radius: 4px; cursor: pointer; font-weight: 500; }
        .msg .edit-container .btn-save { background: #10b981; color: white; }
        .msg .edit-container .btn-save:hover { background: #059669; }
        .msg .edit-container .btn-cancel { background: #ef4444; color: white; }
        .msg .edit-container .btn-cancel:hover { background: #dc2626; }

        .date-separator { text-align: center; margin: 12px 0; }
        .date-separator span { display: inline-block; background: #f1f3f5; color: #6c757d; padding: 4px 10px; border-radius: 999px; font-size: .8rem; box-shadow: 0 1px 2px rgba(0,0,0,.05); }

        .composer { position: sticky; bottom: 0; background: #f8f9fa; padding-top: 8px; }
        /* More (three-dots) toggle and popup menu */
        .more-toggle { background: transparent; border: none; cursor: pointer; padding: 4px; font-size: 1rem; color: #6b7280; display: inline-flex; align-items: center; }
        .msg .more-toggle { visibility: hidden; opacity: 0; transform: translateY(0); transition: opacity .15s ease; }
        .msg:hover .more-toggle { visibility: visible; opacity: 1; }
        .more-menu { position: absolute; top: calc(100% + 6px); right: 0; display: none; background: #fff; border: 1px solid #e9ecef; box-shadow: 0 6px 18px rgba(0,0,0,.08); border-radius: 8px; z-index: 50; min-width: 120px; }
        .more-menu.show { display: block; }
        .more-menu button { display: block; width: 100%; text-align: left; padding: 8px 10px; border: none; background: transparent; cursor: pointer; font-size: .9rem; }
        .more-menu button:hover { background: #f1f3f5; }
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
            <a href="{{ route('admin.laporan', $laporan['id']) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div id="chatBox" class="chat-box"></div>

        <form id="sendForm" class="mt-3 d-flex gap-2 composer" action="javascript:void(0)" method="post" onsubmit="return false;">
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
        let justSent = false;

        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/\"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function formatDateSeparator(timestamp) {
            try {
                const d = new Date(typeof timestamp === 'number' ? timestamp : new Date(timestamp).getTime());
                if (isNaN(d.getTime())) return '';
                return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            } catch (_) { return ''; }
        }

        function dateKey(timestamp) {
            try {
                const d = new Date(typeof timestamp === 'number' ? timestamp : new Date(timestamp).getTime());
                if (isNaN(d.getTime())) return '';
                return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
            } catch (_) { return ''; }
        }

        function formatTimeOnly(timestamp) {
            try {
                const d = new Date(typeof timestamp === 'number' ? timestamp : new Date(timestamp).getTime());
                if (isNaN(d.getTime())) return '';
                return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            } catch (_) { return ''; }
        }

        function displayNameFor(chatType) {
            return chatType === 'admin' ? 'Admin' : 'User';
        }

        function deleteMessage(chatId, chatType) {
            // Validasi: hanya ADMIN yang bisa delete
            if (!chatType || chatType.toLowerCase() !== 'admin') {
                alert('Hanya pesan ADMIN yang bisa dihapus');
                return;
            }
            
            if (!confirm('Hapus pesan ini?')) return;
            
            const deleteUrl = @json(route('admin.laporan.chat.delete', [$laporan['id'], ':messageId']));
            const url = deleteUrl.replace(':messageId', chatId);
            
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data && data.status === 'success') {
                    // Reset state dan fetch messages baru
                    lastMessageTime = null;
                    lastMessageId = null;
                    justSent = false;
                    fetchMessages();
                } else {
                    alert('Gagal menghapus pesan');
                }
            })
            .catch(() => alert('Gagal menghapus pesan'));
        }

        function editMessage(chatId) {
            const msgElement = document.querySelector(`[data-message-id="${chatId}"]`);
            const chatType = msgElement.getAttribute('data-chat-type');
            
            // Hanya bisa edit jika chatType = 'admin'
            if (chatType !== 'admin') {
                alert('Hanya pesan ADMIN yang bisa diedit');
                return;
            }
            
            msgElement.classList.add('edit-mode');
            const textarea = msgElement.querySelector('[data-edit-area]');
            textarea.focus();
            textarea.select();
        }

        function cancelEdit(chatId) {
            const msgElement = document.querySelector(`[data-message-id="${chatId}"]`);
            msgElement.classList.remove('edit-mode');
        }

        function saveMessage(chatId) {
            const msgElement = document.querySelector(`[data-message-id="${chatId}"]`);
            const chatType = msgElement.getAttribute('data-chat-type');
            
            // Validasi chatType sebelum simpan
            if (chatType !== 'admin') {
                alert('Hanya pesan ADMIN yang bisa diedit');
                cancelEdit(chatId);
                return;
            }
            
            const textarea = msgElement.querySelector('[data-edit-area]');
            const newText = textarea.value.trim();
            
            if (!newText) {
                alert('Pesan tidak boleh kosong');
                return;
            }
            
            const updateUrl = @json(route('admin.laporan.chat.update', [$laporan['id'], ':messageId']));
            const url = updateUrl.replace(':messageId', chatId);
            
            fetch(url, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ textMessage: newText })
            })
            .then(r => r.json())
            .then(data => {
                if (data && data.status === 'success') {
                    // Update teks pesan di UI
                    const bubble = msgElement.querySelector('[data-bubble]');
                    bubble.textContent = newText;
                    // Keluar dari mode edit
                    msgElement.classList.remove('edit-mode');
                    // Reset state dan fetch messages baru untuk sync
                    lastMessageTime = null;
                    lastMessageId = null;
                    justSent = false;
                    fetchMessages();
                } else {
                    alert('Gagal mengubah pesan');
                }
            })
            .catch(() => alert('Gagal mengubah pesan'));
        }

        // Toggle menu helper
        function toggleMenu(ev, menuId) {
            ev = ev || window.event;
            ev.stopPropagation();
            const menu = document.getElementById(menuId);
            if (!menu) return;
            // Close other menus
            document.querySelectorAll('.more-menu.show').forEach(m => { if (m !== menu) m.classList.remove('show'); });
            menu.classList.toggle('show');
        }

        // Close menus on outside click
        document.addEventListener('click', function() {
            document.querySelectorAll('.more-menu.show').forEach(m => m.classList.remove('show'));
        });

        function createMessageElement(m) {
            const chatType = (m.chatType || '').toLowerCase();
            const isAdmin = chatType === 'admin';
            const text = m.textMessage || m.message || m.text || m.content || m.bubble || '';
            const time = m.createdAt || m.created_at || '';
            const chatId = m.chatId || '';
            const item = document.createElement('div');
            item.className = `msg ${chatType}`;
            item.setAttribute('data-message-id', chatId);
            item.setAttribute('data-chat-type', chatType);
            const timeOnly = formatTimeOnly(time);
            const name = displayNameFor(chatType);
            const leftAvatar = !isAdmin ? `<div class="avatar"><i class="bi bi-person-fill"></i></div>` : '';
            const rightAvatar = isAdmin ? `<div class="avatar"><i class="bi bi-person-fill"></i></div>` : '';

            // Tombol edit hanya untuk ADMIN chatType
            const editButton = isAdmin ? `<button type="button" class="btn-edit" onclick="editMessage('${chatId}')" title="Edit pesan"><i class="bi bi-pencil"></i></button>` : '';
            item.innerHTML = `
                ${leftAvatar}
                <div class="content">
                    <div class="header">
                        <span class="name">${escapeHtml(name)}</span>
                        ${timeOnly ? `<span class="time">${escapeHtml(timeOnly)}</span>` : ''}
                        <button type="button" class="more-toggle" onclick="toggleMenu(event, 'menu-${chatId}')" aria-haspopup="true" aria-expanded="false" title="Opsi">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                    </div>
                    <div class="bubble" data-bubble>${escapeHtml(String(text))}</div>
                    <div class="more-menu" id="menu-${chatId}">
                        ${isAdmin ? `<button type="button" onclick="editMessage('${chatId}')"><i class="bi bi-pencil"></i> Edit</button>
                        <button type="button" onclick="deleteMessage('${chatId}', '${chatType}')"><i class="bi bi-trash"></i> Hapus</button>` : `<button type="button" onclick="deleteMessage('${chatId}', '${chatType}')"><i class="bi bi-trash"></i> Hapus</button>`}
                    </div>
                    <div class="edit-container">
                        <textarea data-edit-area>${escapeHtml(String(text))}</textarea>
                        <div class="edit-buttons">
                            <button type="button" class="btn-save" onclick="saveMessage('${chatId}')">Simpan</button>
                            <button type="button" class="btn-cancel" onclick="cancelEdit('${chatId}')">Batal</button>
                        </div>
                    </div>
                    ${m.messageStatus ? `<div class="status">${escapeHtml(m.messageStatus)}</div>` : ''}
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
                const time = m.createdAt || '';
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
                const time = m.createdAt || m.created_at || '';
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
            // Jika baru kirim chat, ambil semua messages tanpa since untuk memastikan tidak ada yang terlewat
            const url = (justSent || !lastMessageTime) ? messagesUrl : `${messagesUrl}?since=${encodeURIComponent(lastMessageTime)}`;
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
                        if (justSent || !lastMessageTime) {
                            // Load all messages atau pertama kali
                            justSent = false;
                            lastMessageId = last ? (last.chatId || null) : null;
                            lastMessageTime = last ? (last.createdAt || null) : null;
                            lastRenderKey = `${msgs.length}|${lastMessageId || ''}|${lastMessageTime || ''}`;
                            renderMessages(msgs);
                        } else if (last) {
                            // Compare timestamps directly (both are milliseconds)
                            const lastTimestamp = typeof last.createdAt === 'number' ? last.createdAt : new Date(last.createdAt).getTime();
                            const currentTimestamp = typeof lastMessageTime === 'number' ? lastMessageTime : new Date(lastMessageTime).getTime();
                            if (lastTimestamp > currentTimestamp) {
                                const newMsgs = msgs.filter(m => {
                                    const mTimestamp = typeof m.createdAt === 'number' ? m.createdAt : new Date(m.createdAt).getTime();
                                    return mTimestamp > currentTimestamp;
                                });
                                appendMessages(newMsgs);
                                lastMessageId = last.chatId || lastMessageId;
                                lastMessageTime = last.createdAt || lastMessageTime;
                                lastRenderKey = `${msgs.length}|${lastMessageId || ''}|${lastMessageTime || ''}`;
                            }
                        }
                    }
                })
                .catch(() => {})
                .finally(() => { isFetching = false; });
        }

        // Polling ringan (pause saat tab tidak aktif)
        function startPolling() {
            if (poller) return;
            poller = setInterval(fetchMessages, 2000);
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
            e.stopPropagation();
            const input = document.getElementById('messageInput');
            const textMessage = input.value.trim();
            if (!textMessage) return false;
            const btn = this.querySelector('button[type="submit"]');
            const origHtml = btn ? btn.innerHTML : '';
            if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>'; }
            fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ textMessage })
            })
            .then(function(r) {
                if (r.status === 419) {
                    return r.text().then(function() { throw new Error('CSRF'); });
                }
                return r.json();
            })
            .then(function(data) {
                if (data && data.status === 'success') {
                    input.value = '';
                    input.style.height = 'auto';
                    // Set flag dan fetch messages langsung setelah send
                    justSent = true;
                    setTimeout(fetchMessages, 300);
                }
            })
            .catch(function() {})
            .finally(function() {
                if (btn) { btn.disabled = false; btn.innerHTML = origHtml; }
            });
            return false;
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


