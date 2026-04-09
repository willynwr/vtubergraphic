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
        @keyframes slideInRight { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }
        .animate-blink { animation: blink 1.5s infinite; }
        .animate-slide-in { animation: slideInRight 0.3s ease; }
        .scrollbar-thin { scrollbar-width: thin; scrollbar-color: rgba(219,160,190,0.2) transparent; }
        .scrollbar-thin::-webkit-scrollbar { width: 6px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: rgba(219,160,190,0.2); border-radius: 3px; }
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
                <a href="/" class="flex items-center gap-3 py-3 px-3.5 rounded-xl text-[#8a6b80] no-underline text-sm font-medium transition-all duration-200 cursor-pointer mb-1 hover:bg-[#ffe6f040] hover:text-[#3d2b3a]">
                    <span class="w-6 h-6 inline-flex items-center justify-center"><svg viewBox="0 0 24 24" class="w-[18px] h-[18px] stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h4l2-3h4l2 3h4v12H4z"></path><circle cx="12" cy="13" r="3.5"></circle></svg></span>
                    Kembali ke Scanner
                </a>
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

    {{-- Toast --}}
    <div id="toast" class="fixed top-5 right-5 z-[300] hidden">
        <div class="px-6 py-4 rounded-[14px] text-sm font-medium shadow-[0_10px_30px_rgba(180,120,160,0.15)] animate-slide-in" id="toastInner"></div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('-translate-x-full');
            sidebar.classList.toggle('translate-x-0');
            overlay.classList.toggle('hidden');
        }

        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            const inner = document.getElementById('toastInner');
            inner.textContent = msg;
            inner.className = `px-6 py-4 rounded-[14px] text-sm font-medium shadow-[0_10px_30px_rgba(180,120,160,0.15)] animate-slide-in ${type === 'success' ? 'bg-[#8dd4b026] border border-[#8dd4b04d] text-[#2f7c57]' : 'bg-[#e8707026] border border-[#e870704d] text-[#b54e4e]'}`;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 4000);
        }
    </script>
    @yield('scripts')
</body>
</html>
