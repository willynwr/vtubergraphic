@extends('admin.layout')
@section('title', 'Jadwal & Tukar Libur')

@section('head')
<style>
    .sched-toolbar {
        display: flex; align-items: center; gap: 10px; padding: 14px 24px; flex-wrap: wrap;
    }
    .sched-search {
        width: 260px; margin-left: auto; position: relative;
    }
    .sched-search-icon {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        width: 16px; height: 16px; color: #b8a0b0; pointer-events: none;
    }
    .sched-search-icon svg {
        width: 16px; height: 16px; stroke: currentColor; fill: none;
        stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
    }
    .sched-search input {
        width: 100%; padding: 10px 14px 10px 40px;
        border: 1px solid rgba(219,160,190,0.2); border-radius: 12px;
        background: rgba(255,230,240,0.25); color: #3d2b3a;
        font-size: 13px; font-family: 'Poppins', sans-serif; outline: none;
        transition: all 0.3s;
    }
    .sched-search input:focus {
        border-color: #e87bb0; box-shadow: 0 0 0 3px rgba(232,123,176,0.08);
    }
    .sched-search input::placeholder { color: #b8a0b0; }
    .sched-filter select {
        padding: 10px 32px 10px 14px; border: 1px solid rgba(219,160,190,0.2);
        border-radius: 12px; background: rgba(255,230,240,0.25); color: #3d2b3a;
        font-size: 12px; font-family: 'Poppins', sans-serif; font-weight: 500;
        outline: none; cursor: pointer; transition: all 0.3s;
        appearance: none; -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238a6b80' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 12px center;
    }
    .sched-filter select:focus {
        border-color: #e87bb0; box-shadow: 0 0 0 3px rgba(232,123,176,0.08);
    }
    .sched-result-count {
        font-size: 11px; color: #b8a0b0; padding: 0 4px; white-space: nowrap;
    }
    .user-portal-clock {
        display: inline-flex; align-items: center; gap: 14px; background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(255, 255, 255, 0.8); border-radius: 18px; padding: 8px 16px;
        color: #3d2b3a; box-shadow: 0 8px 24px rgba(61,43,58,0.12);
        backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); height: 44px;
    }
    .user-portal-clock-time { font-size: 18px; font-weight: 800; color: #e87bb0; font-variant-numeric: tabular-nums; }
    .user-portal-clock-divider { width: 1px; height: 24px; background: #ead9e4; }
    .user-portal-clock-date { font-size: 11px; color: #7e6a79; font-weight: 500; }
</style>
@endsection


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
        <div class="user-portal-clock">
            <div class="user-portal-clock-time global-clock-time">--:--:--</div>
            <div class="user-portal-clock-divider"></div>
            <div class="user-portal-clock-date global-clock-date">Memuat...</div>
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

            {{-- Search & Filter --}}
            <div class="sched-toolbar">
                <div class="sched-search">
                    <div class="sched-search-icon"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><path d="M21 21l-4.35-4.35"></path></svg></div>
                    <input type="text" id="schedStandaloneSearch" placeholder="Cari nama karyawan..." oninput="filterStandaloneSchedule()">
                </div>
                <div class="sched-filter">
                    <select id="schedStandaloneDept" onchange="filterStandaloneSchedule()">
                        <option value="">Semua Departemen</option>
                        @php $depts = ($schedules ?? collect())->map(fn($s) => $s->employee?->department)->filter()->unique()->sort(); @endphp
                        @foreach($depts as $dept)
                            <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <span class="sched-result-count" id="schedStandaloneCount">{{ $groupedSchedules->count() }} karyawan</span>
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
                    <tbody id="schedStandaloneBody" class="divide-y divide-[#f1dce6] bg-white">
                        @forelse($employees as $emp)
                        @php
                            $empSchedules = ($schedules ?? collect())->filter(fn($s) => $s->employee_id == $emp->employee_id);
                        @endphp
                        <tr id="schedule-emp-{{ $emp->id }}" class="transition-colors hover:bg-[#fff5f9]" data-search="{{ strtolower($emp->name) }}" data-dept="{{ $emp->department ?? '' }}">
                            <td class="px-6 py-3 font-medium text-[#3d2b3a]">{{ $emp->name }}</td>
                            <td class="px-6 py-3 text-[#8a6b80]">{{ $emp->department ?? '-' }}</td>
                            <td class="px-6 py-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    @foreach($empSchedules as $schedule)
                                        <select
                                            class="rounded-lg border border-[#dba0be66] bg-white px-3 py-1.5 text-xs font-semibold text-[#8a6b80] outline-none transition focus:border-[#e87bb0]"
                                            data-schedule-id="{{ $schedule->id }}"
                                            data-employee-id="{{ $schedule->employee_id }}"
                                            data-original-day="{{ $schedule->day_of_week }}"
                                            onchange="updateSingleScheduleDay(this)"
                                        >
                                            @foreach(\App\Models\OffDay::DAY_NAMES as $num => $name)
                                                <option value="{{ $num }}" {{ (int) $schedule->day_of_week === (int) $num ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    @endforeach
                                    
                                    @if(count($empSchedules) < 2)
                                        {{-- Dropdown Tambah Hari Libur Baru --}}
                                        <select
                                            class="rounded-lg border border-dashed border-[#dba0be] bg-transparent px-3 py-1.5 text-xs font-semibold text-[#e87bb0] outline-none transition focus:border-[#e87bb0] cursor-pointer"
                                            onchange="addNewScheduleDay(this, '{{ $emp->employee_id }}')"
                                        >
                                            <option value="" disabled selected>+ Tambah</option>
                                            @foreach(\App\Models\OffDay::DAY_NAMES as $num => $name)
                                                <option value="{{ $num }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-6 py-8 text-center text-[#8a6b80]">Belum ada karyawan.</td></tr>
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

        {{-- Search & Filter --}}
        <div class="sched-toolbar">
            <div class="sched-search">
                <div class="sched-search-icon"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><path d="M21 21l-4.35-4.35"></path></svg></div>
                <input type="text" id="swapStandaloneSearch" placeholder="Cari nama pengaju..." oninput="filterStandaloneSwap()">
            </div>
            <div class="sched-filter">
                <select id="swapStandaloneStatus" onchange="filterStandaloneSwap()">
                    <option value="">Semua Status</option>
                    <option value="PENDING">Pending</option>
                    <option value="APPROVED">Disetujui</option>
                    <option value="REJECTED">Ditolak</option>
                </select>
            </div>
            <span class="sched-result-count" id="swapStandaloneCount"></span>
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
                <tbody id="swapStandaloneBody" class="divide-y divide-[#f1dce6] bg-white">
                    @forelse($swapRequests as $swapRequest)
                    <tr id="swap-row-{{ $swapRequest->id }}" class="transition-colors hover:bg-[#fff5f9]" data-search="{{ strtolower($swapRequest->employee?->name ?? '') }}" data-status="{{ $swapRequest->status }}">
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
    // Generic filter helper
    function filterRows(tbodyId, searchId, filters, countId) {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;
        const searchVal = (document.getElementById(searchId)?.value || '').toLowerCase().trim();
        const rows = tbody.querySelectorAll('tr[data-search]');
        let visible = 0;
        rows.forEach(row => {
            const searchOk = !searchVal || row.dataset.search.includes(searchVal);
            let filterOk = true;
            for (const f of filters) {
                const val = document.getElementById(f.selectId)?.value || '';
                if (val && row.dataset[f.dataAttr] !== val) { filterOk = false; break; }
            }
            row.style.display = (searchOk && filterOk) ? '' : 'none';
            if (searchOk && filterOk) visible++;
        });
        const el = document.getElementById(countId);
        if (el) el.textContent = `${visible} dari ${rows.length} data`;
    }

    function filterStandaloneSchedule() {
        filterRows('schedStandaloneBody', 'schedStandaloneSearch', [
            { selectId: 'schedStandaloneDept', dataAttr: 'dept' }
        ], 'schedStandaloneCount');
    }

    function filterStandaloneSwap() {
        filterRows('swapStandaloneBody', 'swapStandaloneSearch', [
            { selectId: 'swapStandaloneStatus', dataAttr: 'status' }
        ], 'swapStandaloneCount');
    }

    async function addNewScheduleDay(selectEl, employeeId) {
        const newDay = Number(selectEl.value);
        if (isNaN(newDay)) return;
        showConfirmModal('Tambah Hari Libur', 'Tambahkan hari libur ini?', async () => {
            try {
                const addRes = await fetch('/admin/schedules', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ employee_id: employeeId, day_of_week: newDay }),
                });
                if (!addRes.ok) {
                    showNoticeModal('Gagal menambahkan hari libur baru.', 'Gagal');
                    selectEl.value = '';
                    return;
                }
                showNoticeModal('Hari libur baru berhasil ditambahkan.', 'Berhasil', 'success', true);
            } catch (err) {
                showNoticeModal('Gagal menambahkan hari libur.', 'Error');
                selectEl.value = '';
            }
        });
    }

    async function updateSingleScheduleDay(selectEl) {
        const scheduleId = Number(selectEl.dataset.scheduleId);
        const employeeId = selectEl.dataset.employeeId;
        const newDay = Number(selectEl.value);
        const oldDay = Number(selectEl.dataset.originalDay);

        if (!scheduleId || !employeeId) {
            showNoticeModal('Data jadwal tidak valid.', 'Validasi Gagal');
            return;
        }

        if (newDay === oldDay) return;

        const row = selectEl.closest('tr');
        const allValues = Array.from(row.querySelectorAll('select[data-original-day]')).map((el) => Number(el.value));
        if (new Set(allValues).size !== allValues.length) {
            showNoticeModal('Hari libur tidak boleh duplikat.', 'Validasi Gagal');
            selectEl.value = String(oldDay);
            return;
        }

        showConfirmModal('Ubah Hari Libur', 'Yakin ingin mengubah hari libur karyawan ini?', async () => {
            try {
                const addRes = await fetch('/admin/schedules', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ employee_id: employeeId, day_of_week: newDay }),
                });

                if (!addRes.ok) {
                    showNoticeModal('Gagal menyimpan hari libur baru.', 'Gagal');
                    selectEl.value = String(oldDay);
                    return;
                }

                const deleteRes = await fetch(`/admin/schedules/${scheduleId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                });

                if (!deleteRes.ok) {
                    showNoticeModal('Gagal mengganti hari libur lama.', 'Gagal');
                    selectEl.value = String(oldDay);
                    return;
                }

                showNoticeModal('Hari libur berhasil diubah.', 'Berhasil', 'success', true);
            } catch (err) {
                showNoticeModal('Gagal memperbarui hari libur.', 'Error');
                selectEl.value = String(oldDay);
            }
        });
    }

    async function approveSwap(id) {
        showConfirmModal('Setujui Tukar Libur', 'Tukar libur ini hanya bisa disetujui dari dashboard utama (karena butuh pilihan target). Harap approve dari dashboard.', () => {});
    }

    async function rejectSwap(id) {
        showConfirmModal('Tolak Tukar Libur', 'Tolak permintaan tukar libur ini?', async () => {
            try {
                const res = await fetch(`/admin/swap-requests/${id}/reject`, {
                    method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken },
                });
                if (!res.ok) { showNoticeModal('Gagal menolak.', 'Gagal'); return; }
                showNoticeModal('Request berhasil ditolak.', 'Berhasil', 'success', true);
            } catch (err) { showNoticeModal('Gagal menolak.', 'Error'); }
        });
    }
</script>
@endsection