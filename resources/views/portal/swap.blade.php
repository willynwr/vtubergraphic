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
        @keyframes toastIn { from { opacity: 0; transform: translate(-50%, -20px); } to { opacity: 1; transform: translate(-50%, 0); } }
        .animate-sheet-up { animation: sheetUp 0.3s ease; }
        .animate-toast { animation: toastIn 0.3s ease; }
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
                <div class="p-4 bg-white/[0.92] rounded-2xl transition-all duration-200 hover:shadow-[0_6px_20px_rgba(180,120,160,0.08)]">
                    <div class="flex items-center justify-between mb-2">
                        <span class="py-[3px] px-2.5 rounded-lg text-[10px] font-semibold
                            {{ $swap->status === 'APPROVED' ? 'bg-[#8dd4b026] text-[#2f7c57]' : ($swap->status === 'REJECTED' ? 'bg-[#e870701f] text-[#b54e4e]' : 'bg-[#f0b86e26] text-[#a86d2b]') }}">
                            {{ $swap->statusLabel() }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 text-sm font-semibold mb-2">
                        <span class="py-1 px-2 bg-[#e870700f] rounded-lg text-xs text-[#b54e4e]">{{ $swap->requested_date?->format('d M') }}</span>
                        <svg viewBox="0 0 24 24" class="w-4 h-4 text-[#b8a0b0] stroke-current fill-none stroke-2 shrink-0" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="M12 5l7 7-7 7"></path></svg>
                        <span class="py-1 px-2 bg-[#8dd4b00f] rounded-lg text-xs text-[#2f7c57]">{{ $swap->target_date?->format('d M') ?? '-' }}</span>
                    </div>
                    @if($swap->swapWithEmployee)
                        <div class="text-[11px] text-[#8a6b80] mb-1">↔ Tukar dengan <strong>{{ $swap->swapWithEmployee->name }}</strong></div>
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

            {{-- Swap with whom --}}
            <div class="mb-4">
                <label class="block text-xs text-[#8a6b80] mb-1.5 font-medium">👤 Tukar Dengan Karyawan</label>
                <select id="swapWithEmployeeId" class="w-full py-3.5 px-4 pr-10 border-none rounded-[14px] bg-[#ffe6f040] text-[#3d2b3a] text-sm font-poppins outline-none cursor-pointer appearance-none transition-all duration-300 focus:shadow-[0_0_0_3px_rgba(232,123,176,0.12)]" style="background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238a6b80' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E&quot;); background-repeat: no-repeat; background-position: right 16px center;">
                    <option value="">Pilih karyawan</option>
                    @foreach($colleagues as $colleague)
                        <option value="{{ $colleague->employee_id }}">{{ $colleague->name }} — {{ $colleague->position ?? '-' }}</option>
                    @endforeach
                </select>
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

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

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

        function showToast(msg, type = 'success') {
            const t = document.createElement('div');
            t.className = `fixed top-5 left-1/2 -translate-x-1/2 py-3.5 px-6 rounded-[14px] text-[13px] font-semibold z-[300] max-w-[90%] animate-toast ${type === 'success' ? 'bg-[#8dd4b0f2] text-[#1a4d35]' : 'bg-[#e87070f2] text-white'}`;
            t.textContent = msg;
            document.body.appendChild(t);
            setTimeout(() => t.remove(), 4000);
        }

        async function submitSwap() {
            const payload = {
                requested_date: document.getElementById('swapDate').value,
                target_date: document.getElementById('swapTargetDate').value,
                swap_with_employee_id: document.getElementById('swapWithEmployeeId').value,
                reason: document.getElementById('swapReason').value,
            };

            if (!payload.requested_date || !payload.target_date || !payload.swap_with_employee_id || !payload.reason) {
                showToast('Semua field wajib diisi.', 'error');
                return;
            }

            try {
                const res = await fetch('{{ route("portal.swap-requests.store") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (!res.ok) { showToast(data.message || 'Gagal.', 'error'); return; }
                showToast('Request berhasil dikirim!', 'success');
                setTimeout(() => window.location.reload(), 1500);
            } catch (e) { showToast('Error, coba lagi.', 'error'); }
        }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
        document.getElementById('swapModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
</body>
</html>
