@extends('admin.layout')
@section('title', 'Jadwal & Tukar Libur')

@section('content')
    {{-- Topbar --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-7">
        <div>
            <p class="text-[10px] uppercase tracking-[3px] text-[#b8a0b0] mb-1">Manajemen</p>
            <h1 class="text-2xl md:text-[26px] font-extrabold">Jadwal Libur & Tukar Jadwal</h1>
            <p class="text-sm text-[#8a6b80] mt-1">Kelola hari libur mingguan karyawan dan proses permintaan tukar jadwal.</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-7">
        <div class="p-5 bg-white/90 border border-[#dba0be33] rounded-[20px] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_12px_40px_rgba(180,120,160,0.12)]">
            <div class="w-10 h-10 rounded-xl bg-[#b388d91f] text-[#b388d9] flex items-center justify-center mb-3">
                <svg viewBox="0 0 24 24" class="w-5 h-5 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M3 10h18"></path></svg>
            </div>
            <div class="text-3xl font-extrabold text-[#3d2b3a] tabular-nums">{{ $stats['total_schedules'] }}</div>
            <div class="text-[13px] text-[#8a6b80] font-medium">Total Jadwal</div>
        </div>
        <div class="p-5 bg-white/90 border border-[#dba0be33] rounded-[20px] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_12px_40px_rgba(180,120,160,0.12)]">
            <div class="w-10 h-10 rounded-xl bg-[#f0b86e1f] text-[#f0b86e] flex items-center justify-center mb-3">
                <svg viewBox="0 0 24 24" class="w-5 h-5 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>
            </div>
            <div class="text-3xl font-extrabold text-[#f0b86e] tabular-nums">{{ $stats['pending_requests'] }}</div>
            <div class="text-[13px] text-[#8a6b80] font-medium">Pending</div>
        </div>
        <div class="p-5 bg-white/90 border border-[#dba0be33] rounded-[20px] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_12px_40px_rgba(180,120,160,0.12)]">
            <div class="w-10 h-10 rounded-xl bg-[#8dd4b01f] text-[#8dd4b0] flex items-center justify-center mb-3">
                <svg viewBox="0 0 24 24" class="w-5 h-5 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"></path></svg>
            </div>
            <div class="text-3xl font-extrabold text-[#8dd4b0] tabular-nums">{{ $stats['approved_requests'] }}</div>
            <div class="text-[13px] text-[#8a6b80] font-medium">Disetujui</div>
        </div>
        <div class="p-5 bg-white/90 border border-[#dba0be33] rounded-[20px] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_12px_40px_rgba(180,120,160,0.12)]">
            <div class="w-10 h-10 rounded-xl bg-[#e870701f] text-[#e87070] flex items-center justify-center mb-3">
                <svg viewBox="0 0 24 24" class="w-5 h-5 stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M15 9l-6 6"></path><path d="M9 9l6 6"></path></svg>
            </div>
            <div class="text-3xl font-extrabold text-[#e87070] tabular-nums">{{ $stats['rejected_requests'] }}</div>
            <div class="text-[13px] text-[#8a6b80] font-medium">Ditolak</div>
        </div>
    </div>

    {{-- Add Schedule + Schedule Table --}}
    <div class="grid gap-5 xl:grid-cols-5 mb-7">
        {{-- Add Form --}}
        <section class="xl:col-span-2 bg-white/90 border border-[#dba0be33] rounded-[20px] overflow-hidden">
            <div class="py-5 px-6 pb-4 border-b border-[#dba0be33]">
                <h2 class="text-[15px] font-bold flex items-center gap-2">
                    <span class="w-5 h-5 inline-flex items-center justify-center text-[#e87bb0]"><svg viewBox="0 0 24 24" class="w-[18px] h-[18px] stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg></span>
                    Tambah Hari Libur
                </h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-[#8a6b80] mb-2 uppercase tracking-wider">Karyawan</label>
                    <select id="employeeId" class="w-full rounded-xl border border-[#dba0be33] bg-white px-4 py-3 text-sm text-[#3d2b3a] outline-none transition-all duration-200 focus:border-[#e87bb0] focus:shadow-[0_0_0_3px_rgba(232,123,176,0.1)]">
                        <option value="">Pilih karyawan</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->employee_id }}">{{ $employee->name }} — {{ $employee->department }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#8a6b80] mb-2 uppercase tracking-wider">Hari Libur</label>
                    <select id="dayOfWeek" class="w-full rounded-xl border border-[#dba0be33] bg-white px-4 py-3 text-sm text-[#3d2b3a] outline-none transition-all duration-200 focus:border-[#e87bb0] focus:shadow-[0_0_0_3px_rgba(232,123,176,0.1)]">
                        <option value="">Pilih hari</option>
                        @foreach($dayNames as $num => $name)
                            <option value="{{ $num }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" onclick="saveSchedule()" class="w-full rounded-xl bg-gradient-to-r from-[#e87bb0] to-[#b388d9] px-5 py-3 text-sm font-semibold text-white transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_14px_30px_rgba(232,123,176,0.22)]">Simpan Jadwal</button>
            </div>
        </section>

        {{-- Schedule Table --}}
        <section class="xl:col-span-3 bg-white/90 border border-[#dba0be33] rounded-[20px] overflow-hidden">
            <div class="py-5 px-6 pb-4 border-b border-[#dba0be33]">
                <h2 class="text-[15px] font-bold flex items-center gap-2">
                    <span class="w-5 h-5 inline-flex items-center justify-center text-[#b388d9]"><svg viewBox="0 0 24 24" class="w-[18px] h-[18px] stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M3 10h18"></path></svg></span>
                    Daftar Jadwal Libur Mingguan
                </h2>
            </div>
            <div class="max-h-[420px] overflow-auto scrollbar-thin">
                <table class="min-w-full text-left text-sm">
                    <thead class="sticky top-0 bg-[#fff5f9] text-xs uppercase tracking-wider text-[#8a6b80]">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Karyawan</th>
                            <th class="px-6 py-3 font-semibold">Departemen</th>
                            <th class="px-6 py-3 font-semibold">Hari Libur</th>
                            <th class="px-6 py-3 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f1dce6] bg-white">
                        @forelse($schedules as $schedule)
                        <tr id="schedule-row-{{ $schedule->id }}" class="transition-colors hover:bg-[#fff5f9]">
                            <td class="px-6 py-3 font-medium text-[#3d2b3a]">{{ $schedule->employee?->name ?? '-' }}</td>
                            <td class="px-6 py-3 text-[#8a6b80]">{{ $schedule->employee?->department ?? '-' }}</td>
                            <td class="px-6 py-3"><span class="rounded-full bg-[#e87bb01a] px-3 py-1 text-xs font-semibold text-[#e87bb0]">{{ $schedule->day_name }}</span></td>
                            <td class="px-6 py-3">
                                <button type="button" onclick="deleteSchedule({{ $schedule->id }})" class="rounded-lg border border-[#e8707040] bg-[#e870701a] px-3 py-1.5 text-xs font-semibold text-[#b54e4e] transition hover:bg-[#e8707033]">Hapus</button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-[#8a6b80]">Belum ada jadwal libur.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    {{-- Swap Requests --}}
    <section class="bg-white/90 border border-[#dba0be33] rounded-[20px] overflow-hidden">
        <div class="py-5 px-6 pb-4 border-b border-[#dba0be33] flex items-center justify-between">
            <h2 class="text-[15px] font-bold flex items-center gap-2">
                <span class="w-5 h-5 inline-flex items-center justify-center text-[#f0b86e]"><svg viewBox="0 0 24 24" class="w-[18px] h-[18px] stroke-current fill-none stroke-2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 3l4 4-4 4"></path><path d="M20 7H8a4 4 0 0 0-4 4v1"></path><path d="M8 21l-4-4 4-4"></path><path d="M4 17h12a4 4 0 0 0 4-4v-1"></path></svg></span>
                Permintaan Tukar Libur
            </h2>
            @if($swapRequests->where('status', 'PENDING')->count() > 0)
                <span class="px-3 py-1 rounded-full bg-[#f0b86e33] text-[#a86d2b] text-xs font-semibold">{{ $swapRequests->where('status', 'PENDING')->count() }} menunggu</span>
            @endif
        </div>
        <div class="max-h-[500px] overflow-auto scrollbar-thin">
            <table class="min-w-full text-left text-sm">
                <thead class="sticky top-0 bg-[#fff5f9] text-xs uppercase tracking-wider text-[#8a6b80]">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Pengaju</th>
                        <th class="px-6 py-3 font-semibold">Tanggal Libur Saya</th>
                        <th class="px-6 py-3 font-semibold">Tukar Dengan</th>
                        <th class="px-6 py-3 font-semibold">Tanggal Target</th>
                        <th class="px-6 py-3 font-semibold">Alasan</th>
                        <th class="px-6 py-3 font-semibold">Status</th>
                        <th class="px-6 py-3 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f1dce6] bg-white">
                    @forelse($swapRequests as $swapRequest)
                    <tr id="swap-row-{{ $swapRequest->id }}" class="transition-colors hover:bg-[#fff5f9]">
                        <td class="px-6 py-3">
                            <div class="font-medium text-[#3d2b3a]">{{ $swapRequest->employee?->name ?? '-' }}</div>
                            <div class="text-[11px] text-[#b8a0b0]">{{ $swapRequest->employee?->department ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-3 text-[#3d2b3a] font-medium">{{ $swapRequest->requested_date?->format('d M Y') }}</td>
                        <td class="px-6 py-3">
                            <div class="font-medium text-[#3d2b3a]">{{ $swapRequest->swapWithEmployee?->name ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-3 text-[#3d2b3a] font-medium">{{ $swapRequest->target_date?->format('d M Y') ?? '-' }}</td>
                        <td class="px-6 py-3 max-w-[200px] truncate text-[#8a6b80]">{{ $swapRequest->reason }}</td>
                        <td class="px-6 py-3">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $swapRequest->status === 'APPROVED' ? 'bg-[#8dd4b033] text-[#2f7c57]' : ($swapRequest->status === 'REJECTED' ? 'bg-[#e870701f] text-[#b54e4e]' : 'bg-[#f0b86e33] text-[#a86d2b]') }}">
                                {{ $swapRequest->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            @if($swapRequest->status === 'PENDING')
                            <div class="flex gap-2">
                                <button type="button" onclick="approveSwap({{ $swapRequest->id }})" class="rounded-lg bg-[#8dd4b0] px-3 py-1.5 text-xs font-semibold text-white transition hover:opacity-90">✓ Setujui</button>
                                <button type="button" onclick="rejectSwap({{ $swapRequest->id }})" class="rounded-lg bg-[#e87070] px-3 py-1.5 text-xs font-semibold text-white transition hover:opacity-90">✕ Tolak</button>
                            </div>
                            @else
                            <span class="text-xs text-[#b8a0b0]">Selesai</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-8 text-center text-[#8a6b80]">Belum ada permintaan tukar jadwal.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@section('scripts')
<script>
    async function saveSchedule() {
        const payload = {
            employee_id: document.getElementById('employeeId').value,
            day_of_week: document.getElementById('dayOfWeek').value,
        };
        if (!payload.employee_id || payload.day_of_week === '') {
            showToast('Karyawan dan hari libur wajib dipilih.', 'error'); return;
        }
        try {
            const res = await fetch('{{ route("admin.schedules.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(payload),
            });
            const data = await res.json();
            if (!res.ok) { showToast(data.message || 'Gagal.', 'error'); return; }
            showToast('Jadwal berhasil disimpan!');
            setTimeout(() => window.location.reload(), 1000);
        } catch (err) { showToast('Gagal menyimpan.', 'error'); }
    }

    async function deleteSchedule(id) {
        if (!confirm('Hapus jadwal libur ini?')) return;
        try {
            const res = await fetch(`/admin/schedules/${id}`, {
                method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken },
            });
            if (!res.ok) { showToast('Gagal menghapus.', 'error'); return; }
            document.getElementById(`schedule-row-${id}`)?.remove();
            showToast('Jadwal berhasil dihapus!');
        } catch (err) { showToast('Gagal menghapus.', 'error'); }
    }

    async function approveSwap(id) {
        if (!confirm('Setujui permintaan tukar libur ini?')) return;
        try {
            const res = await fetch(`/admin/swap-requests/${id}/approve`, {
                method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken },
            });
            if (!res.ok) { showToast('Gagal.', 'error'); return; }
            showToast('Berhasil disetujui!');
            setTimeout(() => window.location.reload(), 1000);
        } catch (err) { showToast('Gagal.', 'error'); }
    }

    async function rejectSwap(id) {
        if (!confirm('Tolak permintaan tukar libur ini?')) return;
        try {
            const res = await fetch(`/admin/swap-requests/${id}/reject`, {
                method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken },
            });
            if (!res.ok) { showToast('Gagal.', 'error'); return; }
            showToast('Request ditolak.');
            setTimeout(() => window.location.reload(), 1000);
        } catch (err) { showToast('Gagal.', 'error'); }
    }
</script>
@endsection