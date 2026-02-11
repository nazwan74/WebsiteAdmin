<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat Laporan #{{ $laporan['id'] }}</title>
    
    <!-- CSS Eksternal -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- JavaScript Eksternal -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f4f6f9;
        }
        
        /* Gaya Sidebar */
        .sidebar {
            width: 180px;
            height: 100vh;
            background-color: white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            padding: 15px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            transition: transform 0.3s ease;
        }

        /* Sidebar Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }

        .sidebar-overlay.active { display: block; }
        
        /* Hamburger Button */
        .hamburger-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #333;
            cursor: pointer;
            padding: 0.5rem;
            margin-right: 1rem;
        }
        .hamburger-btn:hover { color: #4361ee; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .navbar { margin-left: 0 !important; }
            .main-content { margin-left: 0 !important; }
            .hamburger-btn { display: block; }
        }
        
        .sidebar-logo {
            display: flex; justify-content: center; align-items: center; margin-bottom: 20px;
        }
        .sidebar-logo img {
            max-width: 100px; max-height: 50px; object-fit: contain;
        }
        
        .sidebar-menu { list-style: none; padding: 0; }
        .sidebar-menu li { margin-bottom: 10px; }
        .sidebar-menu li a {
            text-decoration: none; color: #6c757d; display: flex; align-items: center; padding: 8px;
            border-radius: 8px; transition: all 0.3s ease; font-size: 0.9rem;
        }
        .sidebar-menu li a:hover { background-color: #f1f3f9; color: #4361ee; }
        .sidebar-menu li a.active { background-color: #e6edff; color: #4361ee; font-weight: 600; }
        .sidebar-menu li a i { margin-right: 10px; color: #6c757d; font-size: 1rem; }
        .sidebar-menu li a.active i { color: #4361ee; }

        /* Navbar */
        .navbar {
            margin-left: 180px; background-color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: margin-left 0.3s ease;
        }
        .navbar-dashboard-title { font-weight: 600; color: #333; }
        .navbar-dashboard-subtitle { font-size: 0.875rem; color: #6c757d; }

        /* Main Content */
        .main-content {
            margin-left: 180px; margin-top: 70px; padding: 20px;
            transition: margin-left 0.3s ease; position: relative; z-index: 1;
        }

        /* --- CHAT SPECIFIC CSS --- */
        .chat-container {
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            height: calc(100vh - 120px); /* Fill remaining height */
            overflow: hidden;
        }
        
        .chat-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
        }
        
        .chat-box {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            background: #f8f9fa;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .msg { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 16px; }
        .msg .avatar { 
            width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; 
            background: #e9ecef; color: #495057; flex-shrink: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .msg .content { display: flex; flex-direction: column; max-width: 75%; min-width: 0; }
        
        .msg .header { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
        .msg .name { font-size: 0.85rem; font-weight: 600; color: #343a40; }
        .msg .time { font-size: 0.75rem; color: #adb5bd; }

        .msg .bubble { 
            padding: 10px 14px; border-radius: 12px; font-size: 0.95rem; line-height: 1.5;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05); 
            word-wrap: break-word; 
            overflow-wrap: break-word; 
            word-break: break-word;
            white-space: pre-wrap;
            max-width: 100%;
        }
        
        /* User Message (Left) */
        .msg.user .bubble { 
            background: #fff; color: #212529; border-top-left-radius: 2px;
            border: 1px solid #e9ecef;
        }
        
        /* Admin Message (Right) */
        .msg.admin { flex-direction: row-reverse; }
        .msg.admin .header { flex-direction: row-reverse; }
        .msg.admin .content { align-items: flex-end; }
        .msg.admin .bubble { 
            background: #4361ee; color: #fff; border-top-right-radius: 2px;
            box-shadow: 0 2px 4px rgba(67, 97, 238, 0.2);
        }
        .msg.admin .status { font-size: 0.7rem; color: #adb5bd; margin-top: 2px; text-align: right; }

        .date-separator { text-align: center; margin: 20px 0; position: relative; }
        .date-separator::before {
            content: ''; position: absolute; left: 0; top: 50%; width: 100%; height: 1px; background: #e9ecef; z-index: 1;
        }
        .date-separator span { 
            position: relative; z-index: 2; background: #f8f9fa; color: #adb5bd; 
            padding: 0 12px; font-size: 0.8rem; font-weight: 500;
        }

        .chat-footer {
            padding: 15px 20px;
            background: #fff;
            border-top: 1px solid #e9ecef;
        }

        .composer { display: flex; gap: 10px; align-items: flex-end; }
        .composer textarea { 
            resize: none; border-radius: 20px; padding: 10px 16px; min-height: 44px; max-height: 120px;
            border: 1px solid #ced4da; background-color: #f8f9fa;
        }
        .composer textarea:focus { background-color: #fff; box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15); border-color: #4361ee; }
        
        .btn-send {
            width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            background: #4361ee; color: white; border: none; transition: all 0.2s; box-shadow: 0 2px 6px rgba(67, 97, 238, 0.3);
        }
        .btn-send:hover { background: #304ffe; transform: scale(1.05); }
        .btn-send:disabled { background: #e9ecef; color: #adb5bd; cursor: not-allowed; transform: none; box-shadow: none; }
        /* Chat Actions */
        .msg .actions {
            opacity: 0; transition: opacity 0.2s; display: flex; align-items: center; margin: 0 8px;
            position: relative;
        }
        .msg:hover .actions { opacity: 1; }
        
        .action-btn {
            background: none; border: none; color: #adb5bd; cursor: pointer; padding: 4px;
            display: flex; align-items: center; justify-content: center;
        }
        .action-btn:hover { color: #495057; }
        
        .msg-menu {
            position: absolute; top: 100%; right: 0; background: #fff; border: 1px solid #e9ecef;
            border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 10;
            display: none; min-width: 100px; overflow: hidden;
        }
        .msg-menu.show { display: block; }
        
        .msg-menu-item {
            display: block; width: 100%; padding: 8px 12px; text-align: left; background: none; border: none;
            font-size: 0.85rem; color: #343a40; cursor: pointer;
        }
        .msg-menu-item:hover { background-color: #f8f9fa; }
        .msg-menu-item.text-danger:hover { background-color: #fff5f5; }
        
        /* Inline Edit Form */
        .edit-form { width: 100%; }
        .edit-textarea {
            width: 100%; resize: none; border: 1px solid #ced4da; border-radius: 8px; padding: 8px;
            font-size: 0.95rem; font-family: inherit; margin-bottom: 6px;
        }
        .edit-buttons { display: flex; gap: 6px; justify-content: flex-end; }
    </style>
</head>

<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <img src="{{ URL::to('Images/Gesa_Logo.png')}}" alt="Logo GESA" style="height: 80px;">
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="/admin/dashboard">
                    <i class="bi bi-grid"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="/admin/articel">
                    <i class="bi bi-journal-text"></i> Artikel
                </a>
            </li>
            <li>
                <a href="/admin/laporan" class="active">
                    <i class="bi bi-file-earmark-text"></i> Laporan
                </a>
            </li>
            
            @if(Session::get('admin.role') === 'super_admin')
            <li>
                <a href="/admin/pengaturan">
                    <i class="bi bi-gear"></i> Pengaturan
                </a>
            </li>
            @endif
            <li>
                <a href="/admin/profile">
                    <i class="bi bi-person-circle"></i> Profile
                </a>
            </li>
        </ul>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container-fluid">
            <button class="hamburger-btn" id="hamburgerBtn" type="button">
                <i class="bi bi-list"></i>
            </button>
            <div class="d-flex align-items-center">
                <div class="ms-3">
                    <div class="navbar-dashboard-title">Chat Laporan</div>
                    <div class="navbar-dashboard-subtitle">ID: #{{ $laporan['id'] }}</div>
                </div>
            </div>
            <div class="ms-auto me-3">
                <form method="POST" action="{{ route('admin.logout') }}" id="logoutForm">
                    @csrf
                    <button type="button" class="btn btn-outline-danger" onclick="confirmLogout()">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="chat-container">
            <!-- Header Chat -->
            <div class="chat-header">
                <div>
                    <h5 class="mb-0 fw-bold">{{ $laporan['judul'] ?? 'Laporan' }}</h5>
                    <div class="text-muted small">Pelapor: {{ $laporan['user_name'] ?? ($laporan['nama'] ?? 'User') }}</div>
                </div>
                <a href="{{ route('admin.laporan', $laporan['id']) }}" class="btn btn-sm btn-light border">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <!-- Chat Box (Messages) -->
            <div id="chatBox" class="chat-box"></div>

            <!-- Chat Footer (Input) -->
            <div class="chat-footer">
                <form id="sendForm" class="composer" action="javascript:void(0)" method="post" onsubmit="return false;">
                    <textarea id="messageInput" class="form-control" placeholder="Tulis pesan..." rows="1" required></textarea>
                    <button type="submit" class="btn-send">
                        <i class="bi bi-send-fill" style="margin-left: 2px;"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // --- SIDEBAR & NAVBAR SCRIPTS ---
        document.addEventListener('DOMContentLoaded', function() {
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            function toggleSidebar() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            }
            function closeSidebar() {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }

            if (hamburgerBtn) hamburgerBtn.addEventListener('click', toggleSidebar);
            if (overlay) overlay.addEventListener('click', closeSidebar);
            
            // Close sidebar when clicking menu links on mobile
            document.querySelectorAll('.sidebar-menu a').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) closeSidebar();
                });
            });
        });

        function confirmLogout() {
            Swal.fire({
                title: 'Konfirmasi Logout',
                text: 'Apakah Anda yakin ingin keluar?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) document.getElementById('logoutForm').submit();
            });
        }

        // --- CHAT LOGIC SCRIPTS ---
        const laporanId = @json($laporan['id']);
        const messagesUrl = @json(route('admin.laporan.chat.messages', $laporan['id']));
        const sendUrl = @json(route('admin.laporan.chat.send', $laporan['id']));
        // Base URL for update/delete: /admin/laporan/{id}/chat/{messageId}
        const baseUrl = @json(url('/admin/laporan/' . $laporan['id'] . '/chat'));
        
        let lastMessageTime = 0; 
        let isFetching = false;
        let poller = null;

        // Close any open menus when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.actions')) {
                document.querySelectorAll('.msg-menu.show').forEach(el => el.classList.remove('show'));
            }
        });

        function escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/\"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function getDateObject(val) {
            if (!val) return null;
            if (typeof val === 'number') return new Date(val); // millis
            const d = new Date(val);
            if (!isNaN(d.getTime())) return d;
            return null;
        }

        function dateKey(val) {
            const d = getDateObject(val);
            if (!d) return '';
            return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
        }

        function formatDateSeparator(val) {
            const d = getDateObject(val);
            if (!d) return '';
            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            return d.toLocaleDateString('id-ID', options);
        }

        function formatTimeOnly(val) {
            const d = getDateObject(val);
            if (!d) return '';
            return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }

        function toggleMenu(btn) {
            // Close others
            document.querySelectorAll('.msg-menu.show').forEach(el => {
                if (el !== btn.nextElementSibling) el.classList.remove('show');
            });
            const menu = btn.nextElementSibling;
            menu.classList.toggle('show');
        }

        function editMessage(msgId, text) {
            const bubble = document.getElementById(`bubble-${msgId}`);
            if (!bubble) return;
            
            // Save original html to restore if canceled
            if (!bubble.hasAttribute('data-original')) {
                bubble.setAttribute('data-original', bubble.innerHTML);
            }
            
            const currentText = text || bubble.innerText.trim();
            const w = bubble.offsetWidth;
            const h = bubble.offsetHeight;
            
            // Heuristic for height based on content if small, or use current height + padding
            // We'll trust offsetHeight but add a bit for editing comfort
            const style = `width: ${Math.max(w, 150)}px; height: ${Math.max(h + 20, 60)}px;`;

            const formHtml = `
                <div class="edit-form">
                    <textarea class="edit-textarea" style="${style}">${escapeHtml(currentText)}</textarea>
                    <div class="edit-buttons">
                        <button type="button" class="btn btn-sm btn-light border" onclick="cancelEdit('${msgId}')">Batal</button>
                        <button type="button" class="btn btn-sm btn-primary" onclick="saveEdit('${msgId}')">Simpan</button>
                    </div>
                </div>
            `;
            bubble.innerHTML = formHtml;
            const ta = bubble.querySelector('textarea');
            ta.focus();
            // Move cursor to end
            const len = ta.value.length;
            ta.setSelectionRange(len, len);
        }

        function cancelEdit(msgId) {
            const bubble = document.getElementById(`bubble-${msgId}`);
            if (bubble && bubble.hasAttribute('data-original')) {
                bubble.innerHTML = bubble.getAttribute('data-original');
                bubble.removeAttribute('data-original'); // cleanup
            }
        }

        function saveEdit(msgId) {
            const bubble = document.getElementById(`bubble-${msgId}`);
            const textarea = bubble.querySelector('textarea');
            const newText = textarea.value.trim();
            
            if (!newText) return; // Prevent empty
            
            const btn = bubble.querySelector('.btn-primary');
            const originalBtnContent = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            fetch(`${baseUrl}/${msgId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ textMessage: newText })
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update UI
                    bubble.innerText = newText;
                    bubble.removeAttribute('data-original');
                    
                    // Show "edited" status if not already
                    const parent = bubble.parentElement;
                    let stat = parent.querySelector('.status');
                    if (stat) {
                         if (!stat.innerText.includes('diedit')) stat.innerText += ' (diedit)';
                    } else {
                         // Create status if missing (e.g. user message side, though usually admin edits own)
                         // For now assume admin side has status
                    }
                    
                    // Also update global/local data store if we had one, but we rely on DOM here
                } else {
                    Swal.fire('Error', data.message || 'Gagal mengupdate pesan', 'error');
                    cancelEdit(msgId);
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                cancelEdit(msgId);
            });
        }

        function deleteMessage(msgId) {
            Swal.fire({
                title: 'Hapus Pesan?',
                text: "Pesan yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`${baseUrl}/${msgId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'success') {
                            const el = document.getElementById(`msg-${msgId}`);
                            if (el) el.remove();
                        } else {
                            Swal.fire('Error', data.message || 'Gagal menghapus pesan', 'error');
                        }
                    })
                    .catch(e => {
                        console.error(e);
                        Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                    });
                }
            });
        }

        function createMessageElement(m) {
            const isAdmin = (m.chatType === 'ADMIN' || (m.sender||'').toLowerCase() === 'admin');
            const role = isAdmin ? 'admin' : 'user';
            const text = m.textMessage || m.message || m.text || '';
            
            // Prefer createdAt (numeric) over created_at (string)
            const timeVal = m.createdAt || m.created_at || new Date();
            const timeOnly = formatTimeOnly(timeVal);
            
            // ID is essential for edit/delete
            const msgId = m.chatId || m.id; 
            
            const item = document.createElement('div');
            item.className = `msg ${role}`;
            if (msgId) item.id = `msg-${msgId}`;
            
            const avatarHtml = `<div class="avatar"><i class="bi bi-person-fill"></i></div>`;
            const name = role === 'admin' ? 'Admin' : (m.sender_name || 'User');
            
            // Actions Menu (Only for Admin messages usually, but implemented for Admin role here)
            let actionsHtml = '';
            if (role === 'admin' && msgId) { 
                actionsHtml = `
                    <div class="actions">
                        <button class="action-btn" onclick="toggleMenu(this)">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <div class="msg-menu">
                            <button class="msg-menu-item" onclick="editMessage('${msgId}', '${escapeHtml(text).replace(/'/g, "\\'")}')">
                                <i class="bi bi-pencil me-2"></i> Edit
                            </button>
                            <button class="msg-menu-item text-danger" onclick="deleteMessage('${msgId}')">
                                <i class="bi bi-trash me-2"></i> Hapus
                            </button>
                        </div>
                    </div>
                `;
            }
            
            item.innerHTML = `
                ${avatarHtml}
                <div class="content">
                    <div class="header">
                        <span class="name">${escapeHtml(name)}</span>
                        <span class="time">${escapeHtml(timeOnly)}</span>
                    </div>
                    <div class="bubble" id="bubble-${msgId}">${escapeHtml(String(text))}</div>
                    ${role === 'admin' ? `<div class="status">Terkirim</div>` : ''}
                </div>
                ${actionsHtml}
            `;
            return item;
        }

        function renderMessages(messages) {
            const box = document.getElementById('chatBox');
            box.innerHTML = '';
            let lastDate = '';
            
            messages.forEach(m => {
                const timeVal = m.createdAt || m.created_at;
                const dKey = dateKey(timeVal);
                
                if (dKey && dKey !== lastDate) {
                    const sep = document.createElement('div');
                    sep.className = 'date-separator';
                    sep.innerHTML = `<span>${formatDateSeparator(timeVal)}</span>`;
                    box.appendChild(sep);
                    lastDate = dKey;
                }
                box.appendChild(createMessageElement(m));
            });
            
            box.scrollTop = box.scrollHeight;
        }

        function appendMessages(newMessages) {
            if (!newMessages || !newMessages.length) return;
            const box = document.getElementById('chatBox');
            const wasAtBottom = box.scrollTop + box.clientHeight >= box.scrollHeight - 100;
            
            let lastDate = lastMessageTime ? dateKey(lastMessageTime) : '';
            if (box.lastElementChild) {
                // Try to guess last date from DOM or simpler just rely on lastMessageTime check
            }

            newMessages.forEach(m => {
                const timeVal = m.createdAt || m.created_at;
                const dKey = dateKey(timeVal);
                
                if (dKey && dKey !== lastDate) {
                    const sep = document.createElement('div');
                    sep.className = 'date-separator';
                    sep.innerHTML = `<span>${formatDateSeparator(timeVal)}</span>`;
                    box.appendChild(sep);
                    lastDate = dKey;
                }
                
                // Avoid duplicates if poller fetches overlap
                if (m.chatId && document.getElementById(`msg-${m.chatId}`)) return;
                if (m.id && document.getElementById(`msg-${m.id}`)) return;
                
                box.appendChild(createMessageElement(m));
            });
            
            if (wasAtBottom) {
                box.scrollTop = box.scrollHeight;
            }
        }

        function fetchMessages() {
            if (isFetching) return;
            isFetching = true;
            
            const url = lastMessageTime ? `${messagesUrl}?since=${lastMessageTime}` : messagesUrl;
            
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (data && data.status === 'success') {
                    const msgs = data.messages || [];
                    if (msgs.length === 0) return;

                     const lastMsg = msgs[msgs.length - 1];
                     const thisLastTime = lastMsg.createdAt || 0;

                    if (!lastMessageTime) {
                        renderMessages(msgs);
                        lastMessageTime = thisLastTime;
                    } else {
                        const newMsgs = msgs.filter(m => {
                            const t = m.createdAt || 0;
                            // Also check ID to be sure
                            if (m.chatId && document.getElementById(`msg-${m.chatId}`)) return false;
                            return t > lastMessageTime;
                        });
                        
                        if (newMsgs.length > 0) {
                            appendMessages(newMsgs);
                            // Only update lastMessageTime if new messages actually have newer time
                            const lastNew = newMsgs[newMsgs.length - 1];
                            const tLast = lastNew.createdAt || 0;
                            if (tLast > lastMessageTime) lastMessageTime = tLast;
                        }
                    }
                }
            })
            .catch(console.error)
            .finally(() => { isFetching = false; });
        }

        // Auto-Resize Textarea
        const messageInput = document.getElementById('messageInput');
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // Send Message
        document.getElementById('sendForm').addEventListener('submit', function(e) {
            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            if (!message) return;
            
            const btn = this.querySelector('button[type="submit"]');
            const originalBtnContent = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ textMessage: message })
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    input.value = '';
                    input.style.height = 'auto';
                    fetchMessages(); // Refresh
                }
            })
            .catch(console.error)
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalBtnContent;
                input.focus();
            });
        });

        // Enter to send
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('sendForm').dispatchEvent(new Event('submit'));
            }
        });

        // Polling
        function startPolling() {
            if (poller) return;
            poller = setInterval(fetchMessages, 3000);
        }
        function stopPolling() {
            if (poller) clearInterval(poller);
            poller = null;
        }

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) stopPolling();
            else { fetchMessages(); startPolling(); }
        });

        fetchMessages();
        startPolling();
    </script>
</body>
</html>
