{{-- Notification CSS --}}
<style>
    /* Notification Bell */
    .notif-bell-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
        margin-right: 12px;
    }
    .notif-bell-btn {
        background: none;
        border: none;
        font-size: 1.3rem;
        color: #6c757d;
        cursor: pointer;
        padding: 6px 8px;
        border-radius: 8px;
        transition: all 0.2s;
        position: relative;
    }
    .notif-bell-btn:hover {
        background: #f1f3f9;
        color: #4361ee;
    }
    .notif-badge {
        position: absolute;
        top: 2px; right: 2px;
        background: #FFCB05;
        color: #333;
        font-size: 0.65rem;
        font-weight: 700;
        min-width: 18px;
        height: 18px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
        line-height: 1;
        border: 2px solid white;
    }
    .notif-badge:empty, .notif-badge.d-none { display: none !important; }

    /* Dropdown */
    .notif-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        width: 380px;
        max-height: 480px;
        overflow-y: auto;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        z-index: 1050;
        margin-top: 12px;
        animation: slideInNotif 0.3s ease;
    }
    @keyframes slideInNotif {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .notif-dropdown.show { display: block; }
    .notif-dropdown-header {
        padding: 14px 16px;
        font-weight: 700;
        font-size: 0.95rem;
        color: #333;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .notif-dropdown-header i { color: #4361ee; }
    .notif-item {
        display: flex;
        padding: 16px;
        gap: 12px;
        border-bottom: 1px solid rgba(0,0,0,0.03);
        text-decoration: none;
        color: inherit;
        transition: all 0.2s;
        cursor: pointer;
    }
    .notif-item:hover { background: rgba(67, 97, 238, 0.04); }
    .notif-item-avatar {
        width: 42px; height: 42px;
        border-radius: 12px;
        background: linear-gradient(135deg, #e6edff 0%, #dbeafe 100%);
        color: #4361ee;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .notif-item-body { flex: 1; overflow: hidden; }
    .notif-item-title {
        font-weight: 700;
        font-size: 0.85rem;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .notif-item-msg {
        font-size: 0.8rem;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-top: 4px;
    }
    .notif-item-badge {
        background: #dc3545;
        color: white;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 10px;
        align-self: center;
        flex-shrink: 0;
    }
    .notif-empty {
        padding: 24px 16px;
        text-align: center;
        color: #adb5bd;
        font-size: 0.9rem;
    }
    .notif-empty i { font-size: 1.5rem; display: block; margin-bottom: 8px; }

    /* Sidebar Badge */
    .sidebar-notif-badge {
        background: #FFCB05;
        color: #333;
        font-size: 0.65rem;
        font-weight: 700;
        min-width: 18px;
        height: 18px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 5px;
        margin-left: auto;
    }
    .sidebar-notif-badge:empty, .sidebar-notif-badge.d-none { display: none !important; }
</style>

{{-- Notification Bell HTML (inject into navbar) --}}
<script>
(function() {



    // Inject badge into sidebar Pengaduan link
    const sidebarLinks = document.querySelectorAll('.sidebar-menu li a');
    sidebarLinks.forEach(link => {
        if (link.textContent.trim().includes('Pengaduan')) {
            // Avoid duplicating
            if (!link.querySelector('.sidebar-notif-badge')) {
                link.style.display = 'flex';
                link.style.alignItems = 'center';
                link.insertAdjacentHTML('beforeend', '<span class="sidebar-notif-badge d-none" id="sidebarNotifBadge"></span>');
            }
        }
    });

    // Toggle dropdown
    const bellBtn = document.getElementById('notifBellBtn');
    const dropdown = document.getElementById('notifDropdown');
    if (bellBtn && dropdown) {
        bellBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        });
        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && e.target !== bellBtn) {
                dropdown.classList.remove('show');
            }
        });
    }

    // Polling untuk unread chats (5 detik) 
    const NOTIF_POLL_INTERVAL = 5000; 
    let notifPoller = null;
    // Fetch unread chats 
    function fetchUnreadChats() {
        fetch('/admin/notifications/unread-chats', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                updateNotifUI(data.unread || [], data.totalUnread || 0);
            }
        })
        .catch(err => console.error('Notif polling error:', err));
    }
    // Update UI 
    function updateNotifUI(unreadList, totalUnread) {
        // Bell badge
        const bellBadge = document.getElementById('notifBellBadge');
        if (bellBadge) {
            if (totalUnread > 0) {
                bellBadge.textContent = totalUnread > 99 ? '99+' : totalUnread;
                bellBadge.style.display = 'block';
                bellBadge.classList.remove('d-none');
            } else {
                bellBadge.style.display = 'none';
                bellBadge.classList.add('d-none');
            }
        }

        // Sidebar badge
        const sidebarBadge = document.getElementById('sidebarNotifBadge');
        if (sidebarBadge) {
            if (totalUnread > 0) {
                sidebarBadge.textContent = totalUnread > 99 ? '99+' : totalUnread;
                sidebarBadge.classList.remove('d-none');
            } else {
                sidebarBadge.classList.add('d-none');
            }
        }

        // Dropdown list
        const notifList = document.getElementById('notifList');
        if (!notifList) return;

        if (unreadList.length === 0) {
            notifList.innerHTML = `
                <div class="notif-empty p-4 text-center">
                    <i class="bi bi-chat-left-dots fs-1 text-muted opacity-25 d-block mb-2"></i>
                    <span class="text-muted small">Semua pesan sudah dibaca</span>
                </div>
            `;
            return;
        }
        
        notifList.innerHTML = unreadList.map(item => `
            <a class="notif-item" href="/admin/laporan/${item.reportId}/chat">
                <div class="notif-item-avatar" style="background: #FFFAE6; color: #FFCB05;">
                    <i class="bi bi-chat-fill"></i>
                </div>
                <div class="notif-item-body">
                    <div class="notif-item-title fw-bold" style="color: #333;">${escapeNotifHtml(item.userName)}</div>
                    <div class="notif-item-msg text-muted small">${escapeNotifHtml(item.reportTitle)}: ${escapeNotifHtml(item.lastMessage || 'Pesan baru')}</div>
                </div>
                <span class="notif-item-badge" style="background: #FFCB05; color: #333; border: none;">${item.unreadCount}</span>
            </a>
        `).join('');
    }

    function escapeNotifHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function startNotifPolling() {
        fetchUnreadChats(); // Initial fetch
        notifPoller = setInterval(fetchUnreadChats, NOTIF_POLL_INTERVAL);
    }

    function stopNotifPolling() {
        if (notifPoller) clearInterval(notifPoller);
        notifPoller = null;
    }

    // Start polling when page is visible
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) stopNotifPolling();
        else startNotifPolling();
    });

    // Auto-start
    startNotifPolling();
})();
</script>
