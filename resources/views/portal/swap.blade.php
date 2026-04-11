<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tukar Libur - VtuberGraphic</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { poppins: ['Poppins', 'sans-serif'] } } } }
    </script>
    <style>
        @keyframes sheetUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
        .animate-sheet-up { animation: sheetUp 0.3s ease; }

        .modal-overlay-generic {
            background: rgba(61, 43, 58, 0.42);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            opacity: 0;
            pointer-events: none;
            transition: opacity 220ms ease;
        }
        .modal-overlay-generic.show {
            opacity: 1;
            pointer-events: auto;
        }
        .modal-card-generic {
            transform: translateY(10px) scale(0.96);
            opacity: 0;
            transition: transform 260ms cubic-bezier(0.22, 1, 0.36, 1), opacity 220ms ease;
        }
        .modal-overlay-generic.show .modal-card-generic {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
    </style>
</head>
<body class="font-poppins bg-[#fef7ff] text-[#3d2b3a] min-h-screen pb-10">
    {{-- Background blobs --}}
    <div class="fixed inset-0 pointer-events-none -z-10 overflow-hidden">
        <div class="absolute -top-[60px] -right-10 w-[260px] h-[260px] bg-[#f0b86e] rounded-full blur-[100px] opacity-[0.07]"></div>
        <div class="absolute bottom-[100px] -left-[60px] w-[300px] h-[300px] bg-[#e87bb0] rounded-full blur-[120px] opacity-[0.05]"></div>
    </div>

    <div class="max-w-md lg:max-w-3xl xl:max-w-5xl mx-auto px-5 lg:px-8">
        {{-- Header --}}
        <div class="flex items-center gap-3.5 py-5 pb-6">
            <a href="{{ route('portal.index') }}" class="w-10 h-10 rounded-xl bg-white/[0.92] border-none flex items-center justify-center no-underline text-[#3d2b3a] transition-all duration-200 hover:bg-[#ffe6f040] hover:-translate-y-0.5">
                <svg viewBox="0 0 24 24" class="w-[18px] h-[18px] stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"></path><path d="M12 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <div class="text-[11px] text-[#b8a0b0] uppercase tracking-[1.5px]">Dept. {{ $employee->department ?? '-' }}</div>
                <div class="text-xl font-extrabold">Tukar Libur</div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-2.5 mb-5">
            <div class="text-center py-4 px-2 bg-white/[0.92] rounded-2xl">
                <div class="text-[22px] font-extrabold text-[#f0b86e]">{{ $swapRequests->where('status', 'PENDING')->count() }}</div>
                <div class="text-[10px] text-[#b8a0b0] uppercase tracking-[0.5px] mt-0.5">Menunggu</div>
            </div>
            <div class="text-center py-4 px-2 bg-white/[0.92] rounded-2xl">
                <div class="text-[22px] font-extrabold text-[#8dd4b0]">{{ $swapRequests->where('status', 'APPROVED')->count() }}</div>
                <div class="text-[10px] text-[#b8a0b0] uppercase tracking-[0.5px] mt-0.5">Disetujui</div>
            </div>
            <div class="text-center py-4 px-2 bg-white/[0.92] rounded-2xl">
                <div class="text-[22px] font-extrabold text-[#e87070]">{{ $swapRequests->where('status', 'REJECTED')->count() }}</div>
                <div class="text-[10px] text-[#b8a0b0] uppercase tracking-[0.5px] mt-0.5">Ditolak</div>
            </div>
        </div>

        {{-- How it works --}}
        <div class="py-3 px-4 bg-[#b388d914] rounded-xl mb-5 text-xs text-[#8a6b80]">
            <strong class="text-[#b388d9]">Cara kerja:</strong> Pilih tanggal libur Anda → pilih rekan kerja → pilih tanggal kerja rekan yang ingin Anda tukar. Admin akan memproses permintaan Anda.
        </div>

        {{-- New Request Button --}}
        <button type="button" onclick="openModal()" class="flex items-center justify-center gap-2 w-full py-4 bg-gradient-to-r from-[#e87bb0] to-[#b388d9] border-none rounded-2xl text-white font-poppins text-sm font-bold cursor-pointer transition-all duration-300 mb-6 hover:-translate-y-0.5 hover:shadow-[0_8px_24px_rgba(232,123,176,0.3)]">
            <svg viewBox="0 0 24 24" class="w-[18px] h-[18px] stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Ajukan Tukar Libur
        </button>

        {{-- Request History --}}
        <div class="text-[15px] font-bold mb-3.5">Riwayat Permintaan</div>
        @if($swapRequests->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-2.5">
                @foreach($swapRequests as $swap)
                <div class="p-4 bg-white/[0.92] rounded-2xl transition-all duration-200 hover:shadow-[0_6px_20px_rgba(180,120,160,0.08)] {{ $swap->employee_id !== $employee->employee_id ? 'border-2 border-[#e87bb0]' : '' }}">
                    <div class="flex items-center justify-between mb-2">
                        <span class="py-[3px] px-2.5 rounded-lg text-[10px] font-semibold
                            {{ $swap->status === 'APPROVED' ? 'bg-[#8dd4b026] text-[#2f7c57]' : ($swap->status === 'REJECTED' ? 'bg-[#e870701f] text-[#b54e4e]' : 'bg-[#f0b86e26] text-[#a86d2b]') }}">
                            {{ $swap->statusLabel() }}
                        </span>
                        @if($swap->employee_id !== $employee->employee_id)
                            <span class="text-[10px] text-[#e87bb0] font-bold">Request dari {{ $swap->employee->name }}</span>
                        @endif
                    </div>
                    
                    @if($swap->employee_id === $employee->employee_id)
                        <div class="flex items-center gap-2 text-sm font-semibold mb-2">
                            <span class="py-1 px-2 bg-[#e870700f] rounded-lg text-xs text-[#b54e4e]">{{ $swap->requested_date?->format('d M') }} (Kerja)</span>
                            <svg viewBox="0 0 24 24" class="w-4 h-4 text-[#b8a0b0] stroke-current fill-none stroke-2 shrink-0" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="M12 5l7 7-7 7"></path></svg>
                            <span class="py-1 px-2 bg-[#8dd4b00f] rounded-lg text-xs text-[#2f7c57]">{{ $swap->target_date?->format('d M') ?? '-' }} (Libur)</span>
                        </div>
                        @if($swap->swapWithEmployee)
                            <div class="text-[11px] text-[#8a6b80] mb-1">↔ Tukar Target: <strong>{{ $swap->swapWithEmployee->name }}</strong></div>
                        @endif
                    @else
                        {{-- Karyawan ini adalah TARGET --}}
                        <div class="flex items-center gap-2 text-sm font-semibold mb-2">
                            <span class="py-1 px-2 bg-[#e870700f] rounded-lg text-xs text-[#b54e4e]">{{ $swap->target_date?->format('d M') ?? '-' }} (Kerja)</span>
                            <svg viewBox="0 0 24 24" class="w-4 h-4 text-[#b8a0b0] stroke-current fill-none stroke-2 shrink-0" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="M12 5l7 7-7 7"></path></svg>
                            <span class="py-1 px-2 bg-[#8dd4b00f] rounded-lg text-xs text-[#2f7c57]">{{ $swap->requested_date?->format('d M') }} (Libur)</span>
                        </div>
                        <div class="text-[11px] text-[#8a6b80] mb-1"><strong>{{ $swap->employee->name }}</strong></div>
                    @endif

                    <div class="text-xs text-[#b8a0b0] mt-1">{{ $swap->reason }}</div>
                    @if($swap->admin_note)
                        <div class="mt-2 py-2 px-2.5 bg-[#ffe6f040] rounded-lg text-[11px] text-[#8a6b80]">
                            Admin: {{ $swap->admin_note }}
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-[#b8a0b0]">
                <p class="text-[13px] font-medium">Belum ada permintaan tukar libur</p>
                <span class="text-[11px]">Klik tombol di atas untuk mengajukan</span>
            </div>
        @endif
    </div>

    {{-- Modal --}}
    <div class="hidden fixed inset-0 bg-[#3d2b3a80] backdrop-blur-[8px] z-[200] items-end lg:items-center justify-center" id="swapModal">
        <div class="w-full max-w-md lg:max-w-lg max-h-[90vh] bg-white rounded-t-3xl lg:rounded-3xl py-6 px-5 overflow-y-auto animate-sheet-up">
            <div class="w-10 h-1 bg-[#ffe6f040] rounded mx-auto mb-5 lg:hidden"></div>
            <div class="text-lg font-extrabold mb-1">Ajukan Tukar Libur</div>
            <div class="text-xs text-[#b8a0b0] mb-5">Pilih tanggal libur Anda dan tanggal kerja rekan yang ingin ditukar</div>

            <div class="flex items-center gap-2 py-3 px-3.5 bg-[#b388d914] rounded-xl text-[11px] text-[#b388d9] mb-4">
                <svg viewBox="0 0 24 24" class="w-4 h-4 stroke-current fill-none stroke-2 shrink-0" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                Tukar hanya dengan karyawan departemen {{ $employee->department ?? '-' }}
            </div>

            {{-- My off day date --}}
            <div class="mb-4">
                <label class="block text-xs text-[#8a6b80] mb-1.5 font-medium">📅 Tanggal Libur Saya (yang ditukar)</label>
                <input type="date" id="swapDate" class="w-full py-3.5 px-4 border-none rounded-[14px] bg-[#ffe6f040] text-[#3d2b3a] text-sm font-poppins outline-none transition-all duration-300 focus:shadow-[0_0_0_3px_rgba(232,123,176,0.12)]">
            </div>

            {{-- Target date --}}
            <div class="mb-4">
                <label class="block text-xs text-[#8a6b80] mb-1.5 font-medium">🎯 Tanggal Kerja Target (tanggal saya ingin masuk)</label>
                <input type="date" id="swapTargetDate" class="w-full py-3.5 px-4 border-none rounded-[14px] bg-[#ffe6f040] text-[#3d2b3a] text-sm font-poppins outline-none transition-all duration-300 focus:shadow-[0_0_0_3px_rgba(232,123,176,0.12)]">
            </div>

            {{-- Reason --}}
            <div class="mb-4">
                <label class="block text-xs text-[#8a6b80] mb-1.5 font-medium">📝 Alasan</label>
                <textarea id="swapReason" class="w-full py-3.5 px-4 border-none rounded-[14px] bg-[#ffe6f040] text-[#3d2b3a] text-sm font-poppins outline-none resize-y min-h-[90px] transition-all duration-300 focus:shadow-[0_0_0_3px_rgba(232,123,176,0.12)]" placeholder="Tuliskan alasan tukar libur..."></textarea>
            </div>

            <div class="flex gap-2.5 mt-5">
                <button type="button" onclick="closeModal()" class="flex-1 py-3.5 bg-[#ffe6f040] rounded-[14px] border-none text-[#3d2b3a] font-poppins text-[13px] font-semibold cursor-pointer">Batal</button>
                <button type="button" onclick="submitSwap()" class="flex-[2] py-3.5 bg-gradient-to-r from-[#e87bb0] to-[#b388d9] rounded-[14px] border-none text-white font-poppins text-[13px] font-bold cursor-pointer transition-all duration-300 hover:shadow-[0_6px_20px_rgba(232,123,176,0.3)]">Kirim Request</button>
            </div>
        </div>
    </div>

    {{-- Notice Modal (Success / Error) --}}
    <div id="noticeModalOverlay" class="modal-overlay-generic fixed inset-0 z-[400] flex items-center justify-center p-5" onclick="if(event.target.id==='noticeModalOverlay') closeNoticeModal()">
        <div class="modal-card-generic w-full max-w-sm rounded-[24px] border border-white/60 bg-white/[0.94] p-5 shadow-[0_20px_60px_rgba(61,43,58,0.22)]">
            <div class="pb-2 text-center">
                <div id="noticeIconWrap" class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#e87070] to-[#b388d9] text-white flex items-center justify-center mx-auto mb-3">
                    <svg id="noticeIconError" viewBox="0 0 24 24" class="w-6 h-6 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4"></path><path d="M12 16h.01"></path><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"></path></svg>
                    <svg id="noticeIconSuccess" viewBox="0 0 24 24" class="hidden w-6 h-6 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                </div>
                <div class="text-[18px] font-extrabold text-[#3d2b3a]" id="noticeTitle">Error</div>
            </div>
            <div class="text-center py-2 mb-4">
                <div class="text-[13px] text-[#7e6a79] leading-relaxed" id="noticeMessage">-</div>
            </div>
            <div class="flex items-center justify-center gap-2.5">
                <button type="button" onclick="closeNoticeModal()" class="flex-1 py-3 rounded-[14px] border-none bg-gradient-to-r from-[#e87bb0] to-[#b388d9] text-[13px] font-bold text-white transition-all duration-300 hover:shadow-[0_8px_20px_rgba(232,123,176,0.3)]">OK</button>
            </div>
        </div>
    </div>

    {{-- Confirm Modal --}}
    <div id="confirmModalOverlay" class="modal-overlay-generic fixed inset-0 z-[390] flex items-center justify-center p-5" onclick="if(event.target.id==='confirmModalOverlay') closeConfirmModal()">
        <div class="modal-card-generic w-full max-w-sm rounded-[24px] border border-white/60 bg-white/[0.94] p-5 shadow-[0_20px_60px_rgba(61,43,58,0.22)]">
            <div class="pb-2 text-center">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#e87bb0] to-[#b388d9] text-white flex items-center justify-center mx-auto mb-3">
                    <svg viewBox="0 0 24 24" class="w-6 h-6 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4"></path><path d="M12 16h.01"></path><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"></path></svg>
                </div>
                <div class="text-[18px] font-extrabold text-[#3d2b3a]" id="confirmTitle">Konfirmasi</div>
            </div>
            <div class="text-center py-2 mb-4">
                <div class="text-[13px] text-[#7e6a79] leading-relaxed" id="confirmMessage">Yakin?</div>
            </div>
            <div class="flex items-center justify-center gap-2.5">
                <button type="button" onclick="closeConfirmModal()" class="flex-1 py-3 rounded-[14px] border border-[#ead9e4] bg-[#fff7fb] text-[13px] font-semibold text-[#7e6a79] transition-all duration-200 hover:bg-[#ffeef7]">Batal</button>
                <button type="button" onclick="executeConfirmAction()" class="flex-[1.3] py-3 rounded-[14px] border-none bg-gradient-to-r from-[#e87bb0] to-[#b388d9] text-[13px] font-bold text-white transition-all duration-300 hover:shadow-[0_8px_20px_rgba(232,123,176,0.3)]">Ya, lanjut</button>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let noticeModalShouldReload = false;
        let pendingConfirmAction = null;

        function openModal() {
            const modal = document.getElementById('swapModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeModal() {
            const modal = document.getElementById('swapModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // ======= Notice Modal (replaces toast) =======
        function showNoticeModal(message, title = 'Error', variant = 'error', shouldReload = false) {
            const titleEl = document.getElementById('noticeTitle');
            const messageEl = document.getElementById('noticeMessage');
            const iconWrap = document.getElementById('noticeIconWrap');
            const iconError = document.getElementById('noticeIconError');
            const iconSuccess = document.getElementById('noticeIconSuccess');

            if (titleEl) titleEl.textContent = title;
            if (messageEl) messageEl.textContent = message;
            noticeModalShouldReload = shouldReload;

            if (iconWrap && iconError && iconSuccess) {
                if (variant === 'success') {
                    iconWrap.className = 'w-12 h-12 rounded-2xl bg-gradient-to-br from-[#8dd4b0] to-[#57b88b] text-white flex items-center justify-center mx-auto mb-3';
                    iconError.classList.add('hidden');
                    iconSuccess.classList.remove('hidden');
                } else {
                    iconWrap.className = 'w-12 h-12 rounded-2xl bg-gradient-to-br from-[#e87070] to-[#b388d9] text-white flex items-center justify-center mx-auto mb-3';
                    iconSuccess.classList.add('hidden');
                    iconError.classList.remove('hidden');
                }
            }

            const overlay = document.getElementById('noticeModalOverlay');
            if (overlay) requestAnimationFrame(() => overlay.classList.add('show'));
        }

        function closeNoticeModal() {
            const overlay = document.getElementById('noticeModalOverlay');
            if (overlay) overlay.classList.remove('show');
            if (noticeModalShouldReload) {
                noticeModalShouldReload = false;
                window.location.reload();
            }
        }

        // ======= Confirm Modal (replaces confirm()) =======
        function showConfirmModal(title, message, onConfirm) {
            document.getElementById('confirmTitle').textContent = title;
            document.getElementById('confirmMessage').textContent = message;
            pendingConfirmAction = onConfirm;
            const overlay = document.getElementById('confirmModalOverlay');
            if (overlay) requestAnimationFrame(() => overlay.classList.add('show'));
        }

        function closeConfirmModal() {
            const overlay = document.getElementById('confirmModalOverlay');
            if (overlay) overlay.classList.remove('show');
            pendingConfirmAction = null;
        }

        function executeConfirmAction() {
            const action = pendingConfirmAction;
            closeConfirmModal();
            if (typeof action === 'function') action();
        }

        // ======= Submit Swap =======
        function submitSwap() {
            const payload = {
                requested_date: document.getElementById('swapDate').value,
                target_date: document.getElementById('swapTargetDate').value,
                reason: document.getElementById('swapReason').value,
            };

            if (!payload.requested_date || !payload.target_date || !payload.reason) {
                showNoticeModal('Semua field wajib diisi.', 'Validasi Gagal');
                return;
            }

            showConfirmModal(
                'Konfirmasi Tukar Libur',
                'Kirim permintaan tukar libur ini?',
                async () => {
                    try {
                        const res = await fetch('{{ route("portal.swap-requests.store") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                            body: JSON.stringify(payload),
                        });
                        const data = await res.json();
                        if (!res.ok) {
                            showNoticeModal(data.message || 'Gagal mengirim request.', 'Gagal');
                            return;
                        }
                        closeModal();
                        showNoticeModal('Request tukar libur berhasil dikirim!', 'Berhasil', 'success', true);
                    } catch (e) {
                        showNoticeModal('Terjadi error, coba lagi.', 'Error');
                    }
                }
            );
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeModal();
                closeNoticeModal();
                closeConfirmModal();
            }
        });
        document.getElementById('swapModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
</body>
</html>
