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
        background: #dc3545;
        color: white;
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
        width: 340px;
        max-height: 400px;
        overflow-y: auto;
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        z-index: 1050;
        margin-top: 8px;
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
        padding: 12px 16px;
        gap: 10px;
        border-bottom: 1px solid #f1f3f5;
        text-decoration: none;
        color: inherit;
        transition: background 0.15s;
        cursor: pointer;
    }
    .notif-item:hover { background: #f8f9fa; }
    .notif-item-avatar {
        width: 36px; height: 36px;
        border-radius: 50%;
        background: #e6edff;
        color: #4361ee;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .notif-item-body { flex: 1; overflow: hidden; }
    .notif-item-title {
        font-weight: 600;
        font-size: 0.85rem;
        color: #333;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .notif-item-msg {
        font-size: 0.8rem;
        color: #6c757d;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-top: 2px;
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
        background: #dc3545;
        color: white;
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



    // Inject badge into sidebar Laporan link
    const sidebarLinks = document.querySelectorAll('.sidebar-menu li a');
    sidebarLinks.forEach(link => {
        if (link.textContent.trim().includes('Laporan')) {
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

    // Polling for unread chats
    const NOTIF_POLL_INTERVAL = 10000; // 10 seconds
    let notifPoller = null;

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

    function updateNotifUI(unreadList, totalUnread) {
        // Bell badge
        const bellBadge = document.getElementById('notifBellBadge');
        if (bellBadge) {
            if (totalUnread > 0) {
                bellBadge.textContent = totalUnread > 99 ? '99+' : totalUnread;
                bellBadge.classList.remove('d-none');
            } else {
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
                <div class="notif-empty">
                    <i class="bi bi-chat-dots"></i>
                    Tidak ada pesan baru
                </div>
            `;
            return;
        }

        notifList.innerHTML = unreadList.map(item => `
            <a class="notif-item" href="/admin/laporan/${item.reportId}/chat">
                <div class="notif-item-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div class="notif-item-body">
                    <div class="notif-item-title">${escapeNotifHtml(item.userName)} — ${escapeNotifHtml(item.reportTitle)}</div>
                    <div class="notif-item-msg">${escapeNotifHtml(item.lastMessage || 'Pesan baru')}</div>
                </div>
                <span class="notif-item-badge">${item.unreadCount}</span>
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
