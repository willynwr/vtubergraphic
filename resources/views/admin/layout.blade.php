<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - VtuberGraphic</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { poppins: ['Poppins', 'sans-serif'] } } } }
    </script>
    <style>
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
        .animate-blink { animation: blink 1.5s infinite; }
        .scrollbar-thin { scrollbar-width: thin; scrollbar-color: rgba(219,160,190,0.2) transparent; }
        .scrollbar-thin::-webkit-scrollbar { width: 6px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: rgba(219,160,190,0.2); border-radius: 3px; }

        .admin-layout-modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(61, 43, 58, 0.42);
            backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
            z-index: 500; display: flex; align-items: center; justify-content: center;
            padding: 20px; opacity: 0; pointer-events: none; transition: opacity 220ms ease;
        }
        .admin-layout-modal-overlay.show { opacity: 1; pointer-events: auto; }
        .admin-layout-modal-card {
            width: 100%; max-width: 400px; border-radius: 24px;
            border: 1px solid rgba(255,255,255,0.6); background: rgba(255,255,255,0.94);
            padding: 24px; box-shadow: 0 20px 60px rgba(61,43,58,0.22);
            transform: translateY(10px) scale(0.96); opacity: 0;
            transition: transform 260ms cubic-bezier(0.22, 1, 0.36, 1), opacity 220ms ease;
        }
        .admin-layout-modal-overlay.show .admin-layout-modal-card { transform: translateY(0) scale(1); opacity: 1; }
        .admin-layout-modal-icon {
            width: 48px; height: 48px; border-radius: 16px;
            display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;
        }
        .admin-layout-modal-icon.icon-error { background: linear-gradient(135deg, #e87070, #b388d9); color: white; }
        .admin-layout-modal-icon.icon-success { background: linear-gradient(135deg, #8dd4b0, #57b88b); color: white; }
        .admin-layout-modal-icon.icon-confirm { background: linear-gradient(135deg, #e87bb0, #b388d9); color: white; }
        .admin-layout-modal-icon svg { width: 22px; height: 22px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
        .admin-layout-modal-title { font-size: 18px; font-weight: 800; text-align: center; color: #3d2b3a; margin-bottom: 6px; }
        .admin-layout-modal-message { font-size: 13px; text-align: center; color: #8a6b80; line-height: 1.6; margin-bottom: 18px; }
        .admin-layout-modal-actions { display: flex; align-items: center; justify-content: center; gap: 10px; }
        .admin-layout-modal-btn { flex: 1; padding: 12px; border-radius: 14px; font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; text-align: center; }
        .admin-layout-modal-btn.btn-cancel-modal { background: #fff7fb; border: 1px solid #ead9e4; color: #8a6b80; }
        .admin-layout-modal-btn.btn-cancel-modal:hover { background: #ffeef7; }
        .admin-layout-modal-btn.btn-primary-modal { background: linear-gradient(135deg, #e87bb0, #b388d9); border: none; color: white; font-weight: 700; }
        .admin-layout-modal-btn.btn-primary-modal:hover { box-shadow: 0 8px 20px rgba(232, 123, 176, 0.3); }
    </style>
    @yield('head')
</head>
<body class="font-poppins bg-[#fef7ff] text-[#3d2b3a] min-h-screen">
    <div class="flex min-h-screen">
        {{-- Burger menu (mobile) --}}
        <button class="fixed top-4 left-4 z-[60] w-11 h-11 rounded-xl bg-white/90 border border-[#dba0be33] cursor-pointer items-center justify-center text-xl flex md:hidden" id="burgerMenu" onclick="toggleSidebar()">☰</button>
        <div class="hidden fixed inset-0 bg-black/50 z-[45]" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        {{-- Sidebar --}}
        <aside class="w-[260px] bg-[#fff5f9] border-r border-[#dba0be33] py-6 px-4 flex flex-col fixed top-0 left-0 h-screen z-50 transition-transform duration-300 -translate-x-full md:translate-x-0" id="sidebar">
            <div class="text-[22px] font-black bg-gradient-to-br from-[#e87bb0] to-[#b388d9] bg-clip-text text-transparent px-2 mb-1">VtuberGraphic</div>
            <div class="text-[11px] text-[#b8a0b0] px-2 tracking-[2px] uppercase mb-8">Admin Panel</div>

            <nav class="flex-1">
                <div class="text-[10px] text-[#b8a0b0] uppercase tracking-[2px] px-3 mb-2.5">Menu</div>

                @php $currentRoute = Route::currentRouteName(); @endphp

                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 py-3 px-3.5 rounded-xl no-underline text-sm font-medium transition-all duration-200 cursor-pointer mb-1 {{ $currentRoute === 'admin.dashboard' ? 'bg-[#e87bb01f] text-[#e87bb0]' : 'text-[#8a6b80] hover:bg-[#ffe6f040] hover:text-[#3d2b3a]' }}">
                    <span class="w-6 h-6 inline-flex items-center justify-center"><svg viewBox="0 0 24 24" class="w-[18px] h-[18px] stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h16"></path><path d="M6 20V10"></path><path d="M12 20V4"></path><path d="M18 20v-7"></path></svg></span>
                    Dashboard
                </a>
                <a href="{{ route('admin.schedules') }}" class="flex items-center gap-3 py-3 px-3.5 rounded-xl no-underline text-sm font-medium transition-all duration-200 cursor-pointer mb-1 {{ $currentRoute === 'admin.schedules' ? 'bg-[#e87bb01f] text-[#e87bb0]' : 'text-[#8a6b80] hover:bg-[#ffe6f040] hover:text-[#3d2b3a]' }}">
                    <span class="w-6 h-6 inline-flex items-center justify-center"><svg viewBox="0 0 24 24" class="w-[18px] h-[18px] stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M3 10h18"></path></svg></span>
                    Jadwal & Tukar Libur
                </a>

                <div class="text-[10px] text-[#b8a0b0] uppercase tracking-[2px] px-3 mb-2.5 mt-6">Lainnya</div>

            </nav>

            <div class="pt-4 border-t border-[#dba0be33]">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2.5 w-full text-[#b8a0b0] bg-transparent border-none text-[13px] py-2.5 px-2 rounded-[10px] font-poppins cursor-pointer transition-all duration-200 hover:text-[#e87070] hover:bg-[#e870700a]">
                        <span class="w-6 h-6 inline-flex items-center justify-center"><svg viewBox="0 0 24 24" class="w-[18px] h-[18px] stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg></span>
                        Logout Admin
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 md:ml-[260px] py-7 px-4 md:px-8 pt-[70px] md:pt-7">
            @yield('content')
        </main>
    </div>

    <!-- Notice Modal (Success / Error) -->
    <div class="admin-layout-modal-overlay" id="adminLayoutNoticeOverlay" onclick="if(event.target.id==='adminLayoutNoticeOverlay') closeAdminLayoutNotice()">
        <div class="admin-layout-modal-card">
            <div class="admin-layout-modal-icon" id="adminLayoutNoticeIcon">
                <svg id="adminLayoutNoticeIconError" viewBox="0 0 24 24"><path d="M12 8v4"></path><path d="M12 16h.01"></path><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"></path></svg>
                <svg id="adminLayoutNoticeIconSuccess" viewBox="0 0 24 24" style="display:none;"><path d="M20 6 9 17l-5-5"></path></svg>
            </div>
            <div class="admin-layout-modal-title" id="adminLayoutNoticeTitle">Error</div>
            <div class="admin-layout-modal-message" id="adminLayoutNoticeMessage">-</div>
            <div class="admin-layout-modal-actions">
                <button class="admin-layout-modal-btn btn-primary-modal" onclick="closeAdminLayoutNotice()">OK</button>
            </div>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div class="admin-layout-modal-overlay" id="adminLayoutConfirmOverlay" onclick="if(event.target.id==='adminLayoutConfirmOverlay') closeAdminLayoutConfirm()">
        <div class="admin-layout-modal-card">
            <div class="admin-layout-modal-icon icon-confirm">
                <svg viewBox="0 0 24 24"><path d="M12 8v4"></path><path d="M12 16h.01"></path><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"></path></svg>
            </div>
            <div class="admin-layout-modal-title" id="adminLayoutConfirmTitle">Konfirmasi</div>
            <div class="admin-layout-modal-message" id="adminLayoutConfirmMessage">Yakin?</div>
            <div class="admin-layout-modal-actions">
                <button class="admin-layout-modal-btn btn-cancel-modal" onclick="closeAdminLayoutConfirm()">Batal</button>
                <button class="admin-layout-modal-btn btn-primary-modal" onclick="executeAdminLayoutConfirm()">Ya, lanjut</button>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let adminLayoutNoticeShouldReload = false;
        let pendingAdminLayoutConfirmAction = null;

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('-translate-x-full');
            sidebar.classList.toggle('translate-x-0');
            overlay.classList.toggle('hidden');
        }

        function showNoticeModal(message, title = 'Error', variant = 'error', shouldReload = false) {
            const titleEl = document.getElementById('adminLayoutNoticeTitle');
            const messageEl = document.getElementById('adminLayoutNoticeMessage');
            const iconWrap = document.getElementById('adminLayoutNoticeIcon');
            const iconError = document.getElementById('adminLayoutNoticeIconError');
            const iconSuccess = document.getElementById('adminLayoutNoticeIconSuccess');
            if (titleEl) titleEl.textContent = title;
            if (messageEl) messageEl.textContent = message;
            adminLayoutNoticeShouldReload = shouldReload;
            if (iconWrap && iconError && iconSuccess) {
                if (variant === 'success') {
                    iconWrap.className = 'admin-layout-modal-icon icon-success';
                    iconError.style.display = 'none'; iconSuccess.style.display = '';
                } else {
                    iconWrap.className = 'admin-layout-modal-icon icon-error';
                    iconSuccess.style.display = 'none'; iconError.style.display = '';
                }
            }
            const overlay = document.getElementById('adminLayoutNoticeOverlay');
            if (overlay) requestAnimationFrame(() => overlay.classList.add('show'));
        }

        function closeAdminLayoutNotice() {
            const overlay = document.getElementById('adminLayoutNoticeOverlay');
            if (overlay) overlay.classList.remove('show');
            if (adminLayoutNoticeShouldReload) { adminLayoutNoticeShouldReload = false; window.location.reload(); }
        }

        function showConfirmModal(title, message, onConfirm) {
            document.getElementById('adminLayoutConfirmTitle').textContent = title;
            document.getElementById('adminLayoutConfirmMessage').textContent = message;
            pendingAdminLayoutConfirmAction = onConfirm;
            const overlay = document.getElementById('adminLayoutConfirmOverlay');
            if (overlay) requestAnimationFrame(() => overlay.classList.add('show'));
        }

        function closeAdminLayoutConfirm() {
            const overlay = document.getElementById('adminLayoutConfirmOverlay');
            if (overlay) overlay.classList.remove('show');
            pendingAdminLayoutConfirmAction = null;
        }

        function executeAdminLayoutConfirm() {
            const action = pendingAdminLayoutConfirmAction;
            closeAdminLayoutConfirm();
            if (typeof action === 'function') action();
        }

        // Backward compatibility alias
        function showToast(msg, type = 'success') {
            showNoticeModal(msg, type === 'success' ? 'Berhasil' : 'Gagal', type === 'success' ? 'success' : 'error');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') { closeAdminLayoutNotice(); closeAdminLayoutConfirm(); }
        });

        // Live Clock
        function updateGlobalClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            document.querySelectorAll('.global-clock-time').forEach(el => el.textContent = timeStr);
            document.querySelectorAll('.global-clock-date').forEach(el => el.textContent = dateStr);
        }
        setInterval(updateGlobalClock, 1000);
        updateGlobalClock();
    </script>
    @yield('scripts')
</body>
</html>
