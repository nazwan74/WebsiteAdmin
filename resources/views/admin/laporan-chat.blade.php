@extends('admin.layouts.app', [
    'activePage' => 'laporan',
    'navbarTitle' => 'Chat Pengaduan',
    'navbarSubtitle' => 'ID: #' . $laporan['id']
])

@section('title', 'Chat Pengaduan #' . $laporan['id'])

@section('head-scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection

@section('styles')
    <style>
        /* --- CHAT SPECIFIC CSS --- */
        .chat-container {
            max-width: 1100px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
            height: calc(100vh - 140px);
            overflow: hidden;
        }
        
        .chat-header {
            padding: 20px 24px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.4);
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
            background: #fff; color: #1e293b; border-top-left-radius: 4px;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        
        /* Admin Message (Right) */
        .msg.admin { flex-direction: row-reverse; }
        .msg.admin .header { flex-direction: row-reverse; }
        .msg.admin .content { align-items: flex-end; }
        .msg.admin .bubble { 
            background: linear-gradient(135deg, #FFCB05 0%, #E6B800 100%);
            color: #333; border-top-right-radius: 4px;
            box-shadow: 0 10px 20px rgba(255, 203, 5, 0.15);
        }
        .msg .status { font-size: 0.7rem; color: #adb5bd; margin-top: 4px; display: flex; align-items: center; gap: 4px; }
        .msg.admin .status { justify-content: flex-end; }
        
        .badge-edited {
            font-size: 0.65rem; background: #f1f3f5; color: #868e96; padding: 1px 6px; border-radius: 10px; font-weight: 500;
        }
        .msg.admin .badge-edited { background: rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); }

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
        .composer textarea:focus { background-color: #fff; box-shadow: 0 0 0 3px rgba(255, 203, 5, 0.15); border-color: #FFCB05; }
        
        .btn-send {
            width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            background: #FFCB05; color: #333; border: none; transition: all 0.2s; box-shadow: 0 2px 6px rgba(255, 203, 5, 0.3);
        }
        .btn-send:hover { transform: scale(1.05); opacity: 0.9; }
        .btn-send:disabled { background: #e9ecef; color: #adb5bd; cursor: not-allowed; transform: none; box-shadow: none; }
        
        /* Edit Mode Bar */
        .edit-mode-bar {
            display: none; padding: 8px 20px; background: #fff9db; border-top: 1px solid #ffec99;
            align-items: center; gap: 10px; border-bottom: 1px solid #ffec99;
        }
        .edit-mode-bar.active { display: flex; }
        .edit-mode-bar .edit-info { font-size: 0.85rem; color: #856404; flex: 1; }
        .edit-mode-bar .btn-cancel-edit { background: none; border: none; color: #856404; cursor: pointer; font-size: 1.1rem; }
        
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
        .edit-form { 
            width: 100%; animation: fadeIn 0.2s ease-out; background: rgba(255,255,255,0.1); padding: 5px; border-radius: 8px;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        
        .edit-textarea {
            width: 100%; resize: none; border: 2px solid #FFCB05; border-radius: 10px; padding: 10px;
            font-size: 0.95rem; font-family: inherit; margin-bottom: 8px; outline: none;
            box-shadow: 0 4px 12px rgba(255, 203, 5, 0.1);
        }
        .msg.admin .edit-textarea { background: #fff; color: #212529; }
        .edit-buttons { display: flex; gap: 8px; justify-content: flex-end; }
        .btn-edit-save { background: #FFCB05; color: #333; border: none; padding: 4px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; }
        .btn-edit-cancel { background: #f1f3f5; color: #495057; border: none; padding: 4px 12px; border-radius: 6px; font-size: 0.85rem; }

        /* Chat Images */
        .bubble img.chat-img {
            max-width: 100%; border-radius: 8px; cursor: pointer;
            transition: opacity 0.2s; max-height: 300px; object-fit: cover;
        }
        .bubble img.chat-img:hover { opacity: 0.9; }

        /* Attachment Button */
        .btn-attach {
            width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            background: transparent; color: #6c757d; border: 1px solid #ced4da; transition: all 0.2s; cursor: pointer;
        }
        .btn-attach:hover { background: #f8f9fa; color: #FFCB05; border-color: #FFCB05; }

        /* Image Preview */
        .image-preview-bar {
            display: none; padding: 8px 20px; background: #f8f9fa; border-top: 1px solid #e9ecef;
            align-items: center; gap: 10px;
        }
        .image-preview-bar.active { display: flex; }
        .image-preview-bar img {
            width: 60px; height: 60px; object-fit: cover; border-radius: 8px;
            border: 2px solid #FFCB05;
        }
        .image-preview-bar .preview-name {
            font-size: 0.85rem; color: #495057; flex: 1; overflow: hidden;
            text-overflow: ellipsis; white-space: nowrap;
        }
        .image-preview-bar .btn-remove-preview {
            background: none; border: none; color: #dc3545; cursor: pointer;
            font-size: 1.2rem; padding: 4px; display: flex; align-items: center;
        }
        .image-preview-bar .btn-remove-preview:hover { color: #a71d2a; }

        /* Lightbox Modal */
        .lightbox-overlay {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.9); z-index: 9999; justify-content: center; align-items: center;
            cursor: zoom-out; transition: all 0.3s ease; backdrop-filter: blur(5px);
        }
        .lightbox-overlay.active { display: flex; animation: fadeIn 0.3s; }
        .lightbox-overlay img {
            max-width: 90%; max-height: 85%; border-radius: 16px;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.5); cursor: default;
            transform: scale(0.9); transition: transform 0.3s ease;
        }
        .lightbox-overlay.active img { transform: scale(1); }
        .lightbox-close {
            position: absolute; top: 30px; right: 40px; color: #FFCB05; font-size: 1.5rem;
            cursor: pointer; background: rgba(255,255,255,0.1); border: none;
            width: 54px; height: 54px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            transition: all 0.2s; z-index: 10000; padding: 0;
        }
        .lightbox-close:hover { background: #FFCB05; color: #333; transform: rotate(90deg); }

        /* User message status */
        .msg.user .status { font-size: 0.7rem; color: #adb5bd; margin-top: 2px; }

        /* Category Badges */
        .badge-category {
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1.5px solid transparent; /* Dipertebal */
        }
        .theme-pernikahan { background: #fff1f2; color: #e11d48; border-color: rgba(225, 29, 72, 0.2) !important; }
        .theme-kekerasan { background: #fef2f2; color: #dc2626; border-color: rgba(220, 38, 38, 0.2) !important; }
        .theme-bullying { background: #fffbeb; color: #d97706; border-color: rgba(217, 119, 6, 0.2) !important; }
        .theme-stunting { background: #f0fdf4; color: #16a34a; border-color: rgba(22, 163, 74, 0.2) !important; }
        .theme-default { background: #f8fafc; color: #64748b; border-color: rgba(100, 116, 139, 0.2) !important; }
    </style>
@endsection

@section('content')
    <div class="chat-container">
        <!-- Header Chat -->
        <div class="chat-header">
            <div class="d-flex align-items-center gap-3">
                @php
                    $kategori = strtolower($laporan['case_type'] ?? ($laporan['kategori'] ?? ''));
                    $categoryIcon = match($kategori) {
                        'pernikahan anak' => 'bi-heart-break-fill',
                        'kekerasan anak' => 'bi-exclamation-triangle-fill',
                        'bullying' => 'bi-megaphone-fill',
                        'stunting' => 'bi-hospital-fill',
                        default => 'bi-tag-fill',
                    };
                    $kategoriTheme = match($kategori) {
                        'pernikahan anak' => 'theme-pernikahan',
                        'kekerasan anak' => 'theme-kekerasan',
                        'bullying' => 'theme-bullying',
                        'stunting' => 'theme-stunting',
                        default => 'theme-default',
                    };
                @endphp
                <div class="badge-category {{ $kategoriTheme }} p-0 shadow-sm" style="width: 44px; height: 44px; border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="bi {{ $categoryIcon }}" style="font-size: 1.3rem;"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-dark">{{ $laporan['judul'] ?? 'Pengaduan' }}</h6>
                    <div class="text-muted small" style="font-size: 0.75rem;">
                        <span class="fw-semibold">{{ $laporan['user_name'] ?? ($laporan['nama'] ?? 'User') }}</span> 
                        <span class="mx-1">•</span> 
                        ID: #{{ $laporan['id'] }}
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($chatClosed ?? false)
                    @php
                        $chatBadgeClass = str_contains($chatClosedMessage ?? '', 'ditolak') || str_contains($chatClosedMessage ?? '', 'dibatalkan')
                            ? 'bg-danger-subtle text-danger border-danger-subtle'
                            : 'bg-success-subtle text-success border-success-subtle';
                    @endphp
                    <span class="badge {{ $chatBadgeClass }} border px-3 py-2" style="border-radius: 10px; font-size: 0.75rem;">
                        <i class="bi bi-lock-fill me-1"></i> Chat Ditutup
                    </span>
                @endif
                <a href="{{ route('admin.laporan') }}" id="backToLaporan" class="btn btn-light shadow-sm text-muted px-3" style="border-radius: 12px; font-size: 0.85rem; border: 1px solid rgba(0,0,0,0.05);">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Pengaduan
                </a>
            </div>
        </div>

        <!-- Chat Box (Messages) -->
        <div id="chatBox" class="chat-box"></div>

        @if(!($chatClosed ?? false))
        <!-- Edit Mode Bar -->
        <div class="edit-mode-bar" id="editModeBar">
            <div class="edit-info">
                <i class="bi bi-pencil-square me-1"></i> Mengedit pesan...
            </div>
            <button type="button" class="btn-cancel-edit" onclick="cancelEditMode()" title="Batal edit">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Image Preview Bar -->
        <div class="image-preview-bar" id="imagePreviewBar">
            <img id="previewImg" src="" alt="Preview">
            <span class="preview-name" id="previewName"></span>
            <button type="button" class="btn-remove-preview" onclick="clearImagePreview()" title="Hapus gambar">
                <i class="bi bi-x-circle-fill"></i>
            </button>
        </div>
        @endif

        <!-- Chat Footer (Input) -->
        <div class="chat-footer">
            @if(Session::get('admin.role') === 'super_admin')
                <div class="p-3 bg-light rounded-4 text-center border">
                    <span class="text-muted small fw-semibold"><i class="bi bi-eye-fill text-primary me-2"></i> Mode Pantau (Read-Only) - Super Admin tidak dapat mengirim pesan chat.</span>
                </div>
            @elseif($chatClosed ?? false)
                <div class="p-3 bg-light rounded-4 text-center border">
                    <span class="text-muted small fw-semibold">
                        <i class="bi bi-lock-fill {{ str_contains($chatClosedMessage ?? '', 'ditolak') || str_contains($chatClosedMessage ?? '', 'dibatalkan') ? 'text-danger' : 'text-success' }} me-2"></i>
                        Chat Ditutup — {{ $chatClosedMessage ?? 'Pengaduan telah diselesaikan.' }} Riwayat percakapan dapat dilihat, namun pengiriman pesan baru tidak tersedia.
                    </span>
                </div>
            @else
                <form id="sendForm" class="composer" action="javascript:void(0)" method="post" onsubmit="return false;">
                    <input type="file" id="imageFileInput" accept="image/*" style="display:none">
                    <button type="button" class="btn-attach" onclick="document.getElementById('imageFileInput').click()" title="Lampirkan gambar">
                        <i class="bi bi-paperclip" style="font-size: 1.2rem;"></i>
                    </button>
                    <textarea id="messageInput" class="form-control" placeholder="Tulis pesan..." rows="1"></textarea>
                    <button type="submit" class="btn-send">
                        <i class="bi bi-send-fill" style="margin-left: 2px;"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>

@endsection

@section('modals')
    <!-- Lightbox -->
    <div class="lightbox-overlay" id="lightboxOverlay" onclick="closeLightbox(event)">
        <button class="lightbox-close" onclick="closeLightbox(event)">
            <i class="bi bi-x-lg"></i>
        </button>
        <img id="lightboxImg" src="" alt="Enlarged">
    </div>
@endsection

@section('scripts')
    @php
        $chatSendFrom = request()->query('from');
        $chatSendUrl = route('admin.laporan.chat.send', $laporan['id']);
        if (in_array($chatSendFrom, ['done', 'reject'], true)) {
            $chatSendUrl .= '?' . http_build_query(['from' => $chatSendFrom]);
        }
    @endphp
    <script>
        const isSuperAdmin = @json(Session::get('admin.role') === 'super_admin');
        const isChatClosed = @json($chatClosed ?? false);
        // --- CHAT LOGIC SCRIPTS ---
        const laporanId = @json($laporan['id']);
        const messagesUrl = @json(route('admin.laporan.chat.messages', $laporan['id']));
        const sendUrl = @json($chatSendUrl);
        const baseUrl = @json(url('/admin/laporan/' . $laporan['id'] . '/chat'));
        
        let lastMessageTime = 0; 
        let isFetching = false;
        let poller = null;

        // Interval polling dinamis
        const ACTIVE_INTERVAL = 1000;   // 1 detik saat tab aktif
        const HIDDEN_INTERVAL = 10000;  // 10 detik saat tab tidak aktif

        // Mark chat as read function
        function markRead() {
            fetch(`/admin/laporan/${laporanId}/chat/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).catch(e => console.error('Mark read error:', e));
        }

        // Initial mark read
        markRead();

        // Notification sound
        function playNotifSound() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                [0, 0.15].forEach((delay, i) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.frequency.value = i === 0 ? 587 : 880;
                    osc.type = 'sine';
                    gain.gain.setValueAtTime(0.15, ctx.currentTime + delay);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + delay + 0.3);
                    osc.start(ctx.currentTime + delay);
                    osc.stop(ctx.currentTime + delay + 0.3);
                });
            } catch(e) {}
        }

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
            if (typeof val === 'number') return new Date(val);
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
            document.querySelectorAll('.msg-menu.show').forEach(el => {
                if (el !== btn.nextElementSibling) el.classList.remove('show');
            });
            const menu = btn.nextElementSibling;
            menu.classList.toggle('show');
        }

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        let editingMessageId = null;

        function editMessage(msgId) {
            const bubble = document.getElementById(`bubble-${msgId}`);
            if (!bubble) return;
            
            // Ambil teks pesan (abaikan gambar jika ada)
            const textNode = bubble.querySelector('div') || bubble;
            const currentText = textNode.innerText.trim();
            
            editingMessageId = msgId;
            messageInput.value = currentText;
            
            // UI Feedback
            document.getElementById('editModeBar').classList.add('active');
            const btnSend = document.querySelector('.btn-send');
            btnSend.innerHTML = '<i class="bi bi-check-lg" style="font-size: 1.2rem;"></i>';
            btnSend.style.background = '#28a745';
            
            messageInput.style.height = 'auto';
            messageInput.style.height = Math.min(messageInput.scrollHeight, 120) + 'px';
            messageInput.focus();
            const len = messageInput.value.length;
            messageInput.setSelectionRange(len, len);
        }

        function cancelEditMode() {
            editingMessageId = null;
            messageInput.value = '';
            document.getElementById('editModeBar').classList.remove('active');
            
            const btnSend = document.querySelector('.btn-send');
            btnSend.innerHTML = '<i class="bi bi-send-fill" style="margin-left: 2px;"></i>';
            btnSend.style.background = '#FFCB05';
            btnSend.style.color = '#333';
            
            messageInput.style.height = 'auto';
            messageInput.focus();
        }

        function saveEdit() {
            if (!editingMessageId) return;
            
            const newText = messageInput.value.trim();
            const btn = document.querySelector('.btn-send');
            const originalContent = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            // Gunakan FormData untuk mendukung pengiriman file
            const formData = new FormData();
            formData.append('textMessage', newText);
            formData.append('_method', 'PUT'); // Spoofing PUT method untuk Laravel
            
            if (selectedFile) {
                formData.append('imageFile', selectedFile);
            }

            fetch(`${baseUrl}/${editingMessageId}`, {
                method: 'POST', // Tetap POST karena FormData + PUT sering bermasalah di PHP
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    Toast.fire({ icon: 'success', title: 'Pesan berhasil diubah' });
                    clearImagePreview();
                    cancelEditMode();
                    fetchMessages();
                } else {
                    Swal.fire('Error', data.message || 'Gagal mengupdate pesan', 'error');
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                }
            })
            .catch((e) => {
                console.error(e);
                Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                btn.disabled = false;
                btn.innerHTML = originalContent;
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
                            Toast.fire({ icon: 'success', title: 'Pesan berhasil dihapus' });
                            fetchMessages();
                        } else {
                            Swal.fire('Error', data.message || 'Gagal menghapus pesan', 'error');
                        }
                    })
                    .catch(() => Swal.fire('Error', 'Terjadi kesalahan sistem', 'error'));
                }
            });
        }

        function createMessageElement(m) {
            const isAdmin = (m.chatType === 'ADMIN' || (m.sender||'').toLowerCase() === 'admin');
            const role = isAdmin ? 'admin' : 'user';
            const text = m.textMessage || m.message || m.text || '';
            const imageUrl = (m.imageMessage && m.imageMessage !== 'null' && m.imageMessage.trim() !== '') ? m.imageMessage : null;
            const timeVal = m.createdAt || m.created_at || new Date();
            const timeOnly = formatTimeOnly(timeVal);
            const msgId = m.chatId || m.id; 
            
            const item = document.createElement('div');
            item.className = `msg ${role}`;
            if (msgId) item.id = `msg-${msgId}`;
            
            const avatarHtml = `<div class="avatar"><i class="bi bi-person-fill"></i></div>`;
            const name = role === 'admin' ? 'Admin' : (m.sender_name || 'User');
            let statusText = (m.messageStatus || 'terkirim').toLowerCase();

            let bubbleContent = '';
            if (imageUrl) {
                bubbleContent += `<img class="chat-img" src="${escapeHtml(imageUrl)}" alt="Gambar" onclick="openLightbox('${escapeHtml(imageUrl)}')" loading="lazy">`;
            }
            if (text) {
                const marginTop = imageUrl ? ' style="margin-top: 6px;"' : '';
                bubbleContent += `<div${marginTop}>${escapeHtml(String(text))}</div>`;
            }
            if (!bubbleContent) bubbleContent = '&nbsp;';

            let actionsHtml = '';
            if (role === 'admin' && msgId && !isSuperAdmin && !isChatClosed) { 
                actionsHtml = `
                    <div class="actions">
                        <button class="action-btn" onclick="toggleMenu(this)">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <div class="msg-menu">
                            <button class="msg-menu-item" onclick="editMessage('${msgId}')"> 
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
                    <div class="bubble" id="bubble-${msgId}">${bubbleContent}</div>
                    <div class="status">
                        ${statusText.includes('teredit') ? '<span class="badge-edited">teredit</span>' : ''}
                        ${escapeHtml(statusText.replace('teredit', '').trim())}
                    </div>
                </div>
                ${actionsHtml}
            `;
            return item;
        }

        function renderMessages(messages) {
            const box = document.getElementById('chatBox');
            box.innerHTML = '';
            let lastDate = '';
            const activeMessages = messages.filter(m => !m.isDeleted);
            
            activeMessages.forEach(m => {
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

        function processUpdates(messages) {
            if (!messages || !messages.length) return;
            const box = document.getElementById('chatBox');
            const wasAtBottom = box.scrollTop + box.clientHeight >= box.scrollHeight - 100;
            let lastDate = lastMessageTime ? dateKey(lastMessageTime) : '';

            messages.forEach(m => {
                const msgId = m.chatId || m.id;
                const existingEl = document.getElementById(`msg-${msgId}`);

                if (m.isDeleted) {
                    if (existingEl) existingEl.remove();
                    return;
                }

                if (existingEl) {
                    const bubble = document.getElementById(`bubble-${msgId}`);
                    if (bubble && !bubble.querySelector('textarea')) {
                        const newText = m.textMessage || m.message || '';
                        const imageUrl = (m.imageMessage && m.imageMessage !== 'null' && m.imageMessage.trim() !== '') ? m.imageMessage : null;
                        let newHtml = '';
                        if (imageUrl) newHtml += `<img class="chat-img" src="${escapeHtml(imageUrl)}" alt="Gambar" onclick="openLightbox('${escapeHtml(imageUrl)}')" loading="lazy">`;
                        if (newText) {
                            const marginTop = imageUrl ? ' style="margin-top: 6px;"' : '';
                            newHtml += `<div${marginTop}>${escapeHtml(newText)}</div>`;
                        }
                        if (!newHtml) newHtml = '&nbsp;';
                        bubble.innerHTML = newHtml;
                    }
                    const statusEl = existingEl.querySelector('.status');
                    if (statusEl) statusEl.innerText = (m.messageStatus || 'terkirim').toLowerCase();
                    return;
                }

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

                if ((m.chatType || '').toUpperCase() !== 'ADMIN') {
                    playNotifSound();
                    markRead();
                    Toast.fire({
                        icon: 'info',
                        title: `${m.sender_name || 'User'}: ${(m.textMessage || (m.imageMessage ? '📷 Gambar' : 'Pesan baru')).substring(0, 50)}`
                    });
                }
            });
            if (wasAtBottom) box.scrollTop = box.scrollHeight;
        }

        function fetchMessages() {
            if (isFetching) return;
            isFetching = true;
            const url = lastMessageTime ? `${messagesUrl}?since=${lastMessageTime}` : messagesUrl;
            
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                if (data && data.status === 'success') {
                    // 1. Sync Hard Deletes (Hapus yang sudah tidak ada di server)
                    if (data.validIds && data.validIds.length > 0) {
                        const localMessages = document.querySelectorAll('.msg[id^="msg-"]');
                        localMessages.forEach(el => {
                            const msgId = el.id.replace('msg-', '');
                            // Hanya cek sinkronisasi untuk 200 pesan terakhir agar tidak salah hapus history lama
                            if (!data.validIds.includes(msgId)) {
                                // Jika kita baru saja memuat history (lastMessageTime > 0), 
                                // maka aman untuk menghapus yang tidak ada di list valid terbaru
                                if (lastMessageTime > 0) {
                                    el.remove();
                                }
                            }
                        });
                    }

                    const msgs = data.messages || [];
                    if (msgs.length === 0) return;

                    let maxTime = lastMessageTime;
                    msgs.forEach(m => {
                        const actionTime = m.lastActionAt || m.createdAt || 0;
                        if (actionTime > maxTime) maxTime = actionTime;
                    });
                    
                    if (!lastMessageTime) renderMessages(msgs);
                    else processUpdates(msgs);
                    lastMessageTime = maxTime;
                }
            })
            .catch(console.error)
            .finally(() => { isFetching = false; });
        }

        const messageInput = document.getElementById('messageInput');
        const fromParam = "{{ request()->query('from') }}";
        let requireReasonMessage = (fromParam === 'reject' || fromParam === 'done') && messageInput;
        let reasonMessageSent = false;

        if (messageInput) {
            if (fromParam === 'reject') {
                messageInput.value = "Halo, terima kasih sudah menyampaikan pengaduan melalui aplikasi GESA.\n\nSetelah kami melakukan penelaahan, pengaduan ini kami tandai sebagai DITOLAK dengan alasan:\n- (isi alasan penolakan di sini)\n\nJika ada informasi tambahan atau koreksi, silakan sampaikan kembali melalui aplikasi ini.";
            } else if (fromParam === 'done') {
                messageInput.value = "Halo, terima kasih sudah menyampaikan pengaduan melalui aplikasi GESA.\n\nKami informasikan bahwa proses penanganan pengaduan ini telah SELESAI dengan ringkasan sebagai berikut:\n- (isi ringkasan tindak lanjut / hasil penyelesaian di sini)\n\nJika masih ada hal yang ingin ditanyakan atau ditambahkan, silakan balas pesan ini.";
            }

            if (requireReasonMessage) {
                messageInput.style.height = 'auto';
                messageInput.style.height = Math.min(messageInput.scrollHeight, 120) + 'px';
                messageInput.focus();
                const len = messageInput.value.length;
                messageInput.setSelectionRange(len, len);
            }

            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            });
        }

        let selectedFile = null;
        const imageFileInput = document.getElementById('imageFileInput');
        const previewBar = document.getElementById('imagePreviewBar');
        const previewImg = document.getElementById('previewImg');
        const previewName = document.getElementById('previewName');

        if (imageFileInput) {
            imageFileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (!file) return;
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire('Error', 'Ukuran gambar maksimal 5MB', 'error');
                    this.value = '';
                    return;
                }
                selectedFile = file;
                previewName.textContent = file.name;
                const reader = new FileReader();
                reader.onload = e => { previewImg.src = e.target.result; previewBar.classList.add('active'); };
                reader.readAsDataURL(file);
            });
        }

        function clearImagePreview() {
            selectedFile = null;
            if (imageFileInput) imageFileInput.value = '';
            previewImg.src = '';
            previewName.textContent = '';
            previewBar.classList.remove('active');
        }

        function openLightbox(url) {
            document.getElementById('lightboxImg').src = url;
            document.getElementById('lightboxOverlay').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox(e) {
            if (e && e.target && e.target.tagName === 'IMG') return;
            document.getElementById('lightboxOverlay').classList.remove('active');
            document.getElementById('lightboxImg').src = '';
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(e); });

        const sendForm = document.getElementById('sendForm');
        if (sendForm) {
            sendForm.addEventListener('submit', function() {
                const message = messageInput ? messageInput.value.trim() : '';
                if (!message && !selectedFile) return;

                // Jika sedang dalam mode edit, panggil fungsi saveEdit
                if (editingMessageId) {
                    saveEdit();
                    return;
                }

                const btn = this.querySelector('button[type="submit"]');
                const originalBtnContent = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                const formData = new FormData();
                if (message) formData.append('textMessage', message);
                if (selectedFile) formData.append('imageFile', selectedFile);

                fetch(sendUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success') {
                        if (messageInput) {
                            messageInput.value = '';
                            messageInput.style.height = 'auto';
                        }
                        clearImagePreview();
                        if (requireReasonMessage) reasonMessageSent = true;
                        if (data.chatClosed || requireReasonMessage) {
                            window.location.replace(baseUrl);
                            return;
                        }
                        fetchMessages();
                    } else Swal.fire('Error', data.message || 'Gagal mengirim pesan', 'error');
                })
                .catch(() => Swal.fire('Error', 'Terjadi kesalahan sistem', 'error'))
                .finally(() => { btn.disabled = false; btn.innerHTML = originalBtnContent; if (messageInput) messageInput.focus(); });
            });
        }

        if (messageInput && sendForm) {
            messageInput.addEventListener('keydown', e => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendForm.dispatchEvent(new Event('submit'));
                }
            });
        }

        document.addEventListener('click', e => {
            if (!requireReasonMessage || reasonMessageSent) return;
            const anchor = e.target.closest('a#backToLaporan');
            if (!anchor) return;
            e.preventDefault();
            Swal.fire({
                title: 'Pesan belum dikirim',
                text: 'Anda belum mengirim pesan alasan penolakan / status selesai. Tetap tinggalkan halaman tanpa mengirim pesan?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Tetap di halaman',
                cancelButtonText: 'Tinggalkan halaman'
            }).then(result => { if (result.isDismissed) window.location.href = anchor.getAttribute('href'); });
        });

        function startPolling() {
            const interval = document.hidden ? HIDDEN_INTERVAL : ACTIVE_INTERVAL;
            if (poller) return;
            poller = setInterval(fetchMessages, interval);
        }
        function stopPolling() { if (poller) clearInterval(poller); poller = null; }

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) stopPolling();
            else { fetchMessages(); stopPolling(); startPolling(); }
        });

        fetchMessages();
        startPolling();
    </script>
@endsection