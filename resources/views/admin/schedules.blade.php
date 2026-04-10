@extends('admin.layout')
@section('title', 'Jadwal & Tukar Libur')

@section('content')
    @php
        $groupedSchedules = ($schedules ?? collect())
            ->filter(fn ($schedule) => $schedule->employee)
            ->groupBy('employee_id');
    @endphp

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

    {{-- Schedule Table --}}
    <div class="mb-7">
        <section class="bg-white/90 border border-[#dba0be33] rounded-[20px] overflow-hidden">
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
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f1dce6] bg-white">
                        @forelse($groupedSchedules as $employeeSchedules)
                        @php
                            $firstSchedule = $employeeSchedules->first();
                        @endphp
                        <tr id="schedule-group-{{ $loop->index }}" class="transition-colors hover:bg-[#fff5f9]">
                            <td class="px-6 py-3 font-medium text-[#3d2b3a]">{{ $firstSchedule->employee?->name ?? '-' }}</td>
                            <td class="px-6 py-3 text-[#8a6b80]">{{ $firstSchedule->employee?->department ?? '-' }}</td>
                            <td class="px-6 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @foreach($employeeSchedules as $schedule)
                                        <select
                                            class="rounded-lg border border-[#dba0be66] bg-white px-3 py-1.5 text-xs font-semibold text-[#8a6b80] outline-none transition focus:border-[#e87bb0]"
                                            data-schedule-id="{{ $schedule->id }}"
                                            data-employee-id="{{ $firstSchedule->employee_id }}"
                                            data-original-day="{{ $schedule->day_of_week }}"
                                            onchange="updateSingleScheduleDay(this)"
                                        >
                                            @foreach(\App\Models\OffDay::DAY_NAMES as $num => $name)
                                                <option value="{{ $num }}" {{ (int) $schedule->day_of_week === (int) $num ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-6 py-8 text-center text-[#8a6b80]">Belum ada jadwal libur.</td></tr>
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
    async function updateSingleScheduleDay(selectEl) {
        const scheduleId = Number(selectEl.dataset.scheduleId);
        const employeeId = selectEl.dataset.employeeId;
        const newDay = Number(selectEl.value);
        const oldDay = Number(selectEl.dataset.originalDay);

        if (!scheduleId || !employeeId) {
            showToast('Data jadwal tidak valid.', 'error');
            return;
        }

        if (newDay === oldDay) {
            return;
        }

        const row = selectEl.closest('tr');
        const allValues = Array.from(row.querySelectorAll('select[data-original-day]')).map((el) => Number(el.value));
        if (new Set(allValues).size !== allValues.length) {
            showToast('Hari libur tidak boleh duplikat.', 'error');
            selectEl.value = String(oldDay);
            return;
        }

        try {
            const addRes = await fetch('/admin/schedules', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ employee_id: employeeId, day_of_week: newDay }),
            });

            if (!addRes.ok) {
                showToast('Gagal menyimpan hari libur baru.', 'error');
                selectEl.value = String(oldDay);
                return;
            }

            const deleteRes = await fetch(`/admin/schedules/${scheduleId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken },
            });

            if (!deleteRes.ok) {
                showToast('Gagal mengganti hari libur lama.', 'error');
                selectEl.value = String(oldDay);
                return;
            }

            showToast('Hari libur berhasil diubah.');
            setTimeout(() => window.location.reload(), 400);
        } catch (err) {
            showToast('Gagal memperbarui hari libur.', 'error');
            selectEl.value = String(oldDay);
        }
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