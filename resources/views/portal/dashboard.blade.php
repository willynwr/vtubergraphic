<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal Karyawan - VtuberGraphic</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { poppins: ['Poppins', 'sans-serif'] } } } }
    </script>
    <style>
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
        .animate-blink { animation: blink 1.5s infinite; }
    </style>
</head>
<body class="font-poppins bg-[#fef7ff] text-[#3d2b3a] min-h-screen pb-10 overflow-x-hidden">
    {{-- Background blobs --}}
    <div class="fixed inset-0 pointer-events-none -z-10 overflow-hidden">
        <div class="absolute -top-[60px] -right-10 w-[260px] h-[260px] bg-[#e87bb0] rounded-full blur-[100px] opacity-[0.08]"></div>
        <div class="absolute bottom-[100px] -left-[60px] w-[300px] h-[300px] bg-[#b388d9] rounded-full blur-[120px] opacity-[0.06]"></div>
        <div class="absolute top-1/2 -right-20 w-[250px] h-[250px] bg-[#7eb8e0] rounded-full blur-[100px] opacity-[0.05]"></div>
    </div>

    <div class="max-w-md lg:max-w-3xl xl:max-w-5xl mx-auto px-5 lg:px-8">
        {{-- Header --}}
        <div class="py-6 pb-4">
            <div class="flex items-center justify-between mb-5">
                <div class="text-lg font-black bg-gradient-to-br from-[#e87bb0] to-[#b388d9] bg-clip-text text-transparent">VtuberGraphic</div>
                <div class="flex items-center gap-1.5 py-1.5 px-3.5 bg-[#8dd4b01f] rounded-[10px] text-[11px] font-semibold text-[#2f7c57]">
                    <div class="w-[7px] h-[7px] bg-[#8dd4b0] rounded-full animate-blink"></div>
                    Online
                </div>
            </div>
            <div class="text-xs text-[#b8a0b0] uppercase tracking-[2px] mb-1">Portal Karyawan</div>
            <div class="text-2xl font-extrabold">Halo, {{ $employee->name }} 👋</div>
            <div class="text-xs text-[#8a6b80] mt-1">{{ $employee->position ?? '-' }} · {{ $employee->department ?? '-' }}</div>
        </div>

        {{-- Schedule Info --}}
        <div class="py-3.5 px-[18px] bg-[#b388d914] rounded-[14px] mb-5 flex items-center gap-3">
            <div class="w-9 h-9 rounded-[10px] bg-[#b388d926] text-[#b388d9] flex items-center justify-center shrink-0">
                <svg viewBox="0 0 24 24" class="w-[18px] h-[18px] stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M3 10h18"></path></svg>
            </div>
            <div class="text-[13px] font-medium text-[#8a6b80]">
                Jadwal libur: <strong class="text-[#3d2b3a]">{{ $employee->off_day_names ?: '-' }}</strong>
            </div>
        </div>

        {{-- Info Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
            <div class="p-[18px] bg-white/[0.92] rounded-[20px] transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_10px_30px_rgba(180,120,160,0.1)]">
                <div class="w-10 h-10 rounded-xl bg-[#b388d91f] text-[#b388d9] flex items-center justify-center mb-3">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M3 10h18"></path></svg>
                </div>
                <div class="text-[22px] font-extrabold text-[#b388d9]">{{ $remainingOffDays }}</div>
                <div class="text-[11px] text-[#b8a0b0] mt-0.5">Sisa Libur Bulan Ini</div>
            </div>
            <div class="p-[18px] bg-white/[0.92] rounded-[20px] transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_10px_30px_rgba(180,120,160,0.1)]">
                <div class="w-10 h-10 rounded-xl bg-[#f0b86e1f] text-[#a86d2b] flex items-center justify-center mb-3">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>
                </div>
                <div class="text-[22px] font-extrabold text-[#f0b86e]">{{ $pendingSwaps }}</div>
                <div class="text-[11px] text-[#b8a0b0] mt-0.5">Request Pending</div>
            </div>
        </div>

        {{-- Menu --}}
        <div class="text-[15px] font-bold mb-3.5">Menu</div>
        <div class="grid grid-cols-4 gap-3 mb-7">
            <a href="{{ route('portal.attendance') }}" class="flex flex-col items-center no-underline transition-all duration-300 hover:-translate-y-1 active:scale-95">
                <div class="w-[72px] h-[72px] lg:w-[100px] lg:h-[100px] rounded-[22px] lg:rounded-[26px] bg-[#8dd4b01f] text-[#2f7c57] flex flex-col items-center justify-center gap-1 lg:gap-1.5">
                    <svg viewBox="0 0 24 24" class="w-6 h-6 lg:w-8 lg:h-8 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>
                    <div class="text-[9px] lg:text-[11px] font-bold leading-tight">Absensi</div>
                </div>
            </a>
            <a href="{{ route('portal.schedule') }}" class="flex flex-col items-center no-underline transition-all duration-300 hover:-translate-y-1 active:scale-95">
                <div class="w-[72px] h-[72px] lg:w-[100px] lg:h-[100px] rounded-[22px] lg:rounded-[26px] bg-[#b388d91f] text-[#b388d9] flex flex-col items-center justify-center gap-1 lg:gap-1.5">
                    <svg viewBox="0 0 24 24" class="w-6 h-6 lg:w-8 lg:h-8 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M3 10h18"></path></svg>
                    <div class="text-[9px] lg:text-[11px] font-bold leading-tight">Jadwal</div>
                </div>
            </a>
            <a href="{{ route('portal.swap') }}" class="flex flex-col items-center no-underline transition-all duration-300 hover:-translate-y-1 active:scale-95">
                <div class="w-[72px] h-[72px] lg:w-[100px] lg:h-[100px] rounded-[22px] lg:rounded-[26px] bg-[#f0b86e1f] text-[#a86d2b] flex flex-col items-center justify-center gap-1 lg:gap-1.5">
                    <svg viewBox="0 0 24 24" class="w-6 h-6 lg:w-8 lg:h-8 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h10l-3-3"></path><path d="M17 17H7l3 3"></path></svg>
                    <div class="text-[9px] lg:text-[11px] font-bold leading-tight">Tukar Libur</div>
                </div>
            </a>
            <form method="POST" action="{{ route('portal.logout') }}" class="contents">
                @csrf
                <button type="submit" class="flex flex-col items-center bg-transparent border-none cursor-pointer font-poppins transition-all duration-300 hover:-translate-y-1 active:scale-95">
                    <div class="w-[72px] h-[72px] lg:w-[100px] lg:h-[100px] rounded-[22px] lg:rounded-[26px] bg-[#e870701f] text-[#e87070] flex flex-col items-center justify-center gap-1 lg:gap-1.5">
                        <svg viewBox="0 0 24 24" class="w-6 h-6 lg:w-8 lg:h-8 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        <div class="text-[9px] lg:text-[11px] font-bold leading-tight">Keluar</div>
                    </div>
                </button>
            </form>
        </div>

        {{-- Pending Requests --}}
        @if($pendingRequests->count() > 0)
        <div class="mb-5">
            <div class="flex items-center justify-between mb-3.5">
                <div class="text-[15px] font-bold">Request Pending</div>
                <span class="text-[11px] font-semibold py-1 px-3 rounded-lg bg-[#f0b86e26] text-[#a86d2b]">{{ $pendingRequests->count() }}</span>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-2.5">
                @foreach($pendingRequests as $req)
                <div class="p-4 bg-white/[0.92] rounded-2xl transition-all duration-200 hover:shadow-[0_8px_24px_rgba(180,120,160,0.08)]">
                    <div class="flex items-center justify-between mb-2">
                        <span class="inline-flex items-center gap-1 py-[3px] px-2.5 rounded-lg text-[10px] font-semibold bg-[#f0b86e26] text-[#a86d2b]">Menunggu</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm mb-2">
                        <span class="py-1 px-2 bg-[#e870700f] rounded-lg text-xs font-semibold text-[#b54e4e]">{{ $req->requested_date?->format('d M') }}</span>
                        <svg viewBox="0 0 24 24" class="w-4 h-4 text-[#b8a0b0] stroke-current fill-none stroke-2 shrink-0" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="M12 5l7 7-7 7"></path></svg>
                        <span class="py-1 px-2 bg-[#8dd4b00f] rounded-lg text-xs font-semibold text-[#2f7c57]">{{ $req->target_date?->format('d M') ?? '-' }}</span>
                    </div>
                    @if($req->swapWithEmployee)
                        <div class="text-[11px] text-[#8a6b80]">↔ {{ $req->swapWithEmployee->name }}</div>
                    @endif
                    <div class="text-xs text-[#b8a0b0] mt-1">{{ $req->reason }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Approved Requests --}}
        @if($approvedRequests->count() > 0)
        <div class="mt-5">
            <div class="flex items-center justify-between mb-3.5">
                <div class="text-[15px] font-bold">Request Disetujui</div>
                <span class="text-[11px] font-semibold py-1 px-3 rounded-lg bg-[#8dd4b026] text-[#2f7c57]">{{ $approvedRequests->count() }}</span>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-2.5">
                @foreach($approvedRequests->take(3) as $req)
                <div class="p-4 bg-white/[0.92] rounded-2xl transition-all duration-200 hover:shadow-[0_8px_24px_rgba(180,120,160,0.08)]">
                    <div class="flex items-center justify-between mb-2">
                        <span class="inline-flex items-center gap-1 py-[3px] px-2.5 rounded-lg text-[10px] font-semibold bg-[#8dd4b026] text-[#2f7c57]">Disetujui</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm mb-2">
                        <span class="py-1 px-2 bg-[#e870700f] rounded-lg text-xs font-semibold text-[#b54e4e]">{{ $req->requested_date?->format('d M') }}</span>
                        <svg viewBox="0 0 24 24" class="w-4 h-4 text-[#b8a0b0] stroke-current fill-none stroke-2 shrink-0" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="M12 5l7 7-7 7"></path></svg>
                        <span class="py-1 px-2 bg-[#8dd4b00f] rounded-lg text-xs font-semibold text-[#2f7c57]">{{ $req->target_date?->format('d M') ?? '-' }}</span>
                    </div>
                    @if($req->swapWithEmployee)
                        <div class="text-[11px] text-[#8a6b80]">↔ {{ $req->swapWithEmployee->name }}</div>
                    @endif
                    <div class="text-xs text-[#b8a0b0] mt-1">{{ $req->reason }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($pendingRequests->count() === 0 && $approvedRequests->count() === 0)
        <div class="flex flex-col items-center justify-center py-8 px-5 text-[#b8a0b0]">
            <div class="w-12 h-12 mx-auto mb-3 bg-[#ffe6f040] rounded-[14px] flex items-center justify-center">
                <svg viewBox="0 0 24 24" class="w-[22px] h-[22px] stroke-[#b8a0b0] fill-none stroke-[1.5]" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h10l-3-3"></path><path d="M17 17H7l3 3"></path></svg>
            </div>
            <p class="text-[13px] font-medium">Belum ada request tukar libur</p>
            <span class="text-[11px]">Gunakan menu Tukar Libur untuk mengajukan</span>
        </div>
        @endif
    </div>
</body>
</html>