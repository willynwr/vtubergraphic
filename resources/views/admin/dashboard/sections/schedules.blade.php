<div class="page-section" id="page-schedules">
    <div class="topbar">
        <h1 class="topbar-title">Jadwal & Tukar Libur</h1>
    </div>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card card-tukar">
            <div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M3 10h18"></path></svg></div>
            <div class="stat-value">{{ \App\Models\OffDay::count() }}</div>
            <div class="stat-label">Total Jadwal</div>
        </div>
        <div class="stat-card card-izin">
            <div class="stat-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg></div>
            <div class="stat-value" style="color:var(--accent-orange)">{{ \App\Models\ScheduleSwapRequest::where('status','PENDING')->count() }}</div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card card-present">
            <div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"></path></svg></div>
            <div class="stat-value" style="color:var(--accent-green)">{{ \App\Models\ScheduleSwapRequest::where('status','APPROVED')->count() }}</div>
            <div class="stat-label">Disetujui</div>
        </div>
        <div class="stat-card card-sakit">
            <div class="stat-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M15 9l-6 6"></path><path d="M9 9l6 6"></path></svg></div>
            <div class="stat-value" style="color:var(--accent-red)">{{ \App\Models\ScheduleSwapRequest::where('status','REJECTED')->count() }}</div>
            <div class="stat-label">Ditolak</div>
        </div>
    </div>

    {{-- Add schedule + table --}}
    <div class="content-grid">
        {{-- Add Form --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <span class="card-title-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg></span>
                    <span>Tambah Hari Libur</span>
                </div>
            </div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:var(--text-secondary);margin-bottom:6px;text-transform:uppercase;letter-spacing:1px;">Karyawan</label>
                        <select id="schedEmployeeId" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:12px;font-family:'Poppins';font-size:13px;color:var(--text-primary);outline:none;background:#fff;">
                            <option value="">Pilih karyawan</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->employee_id }}">{{ $emp->name }} — {{ $emp->department }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:var(--text-secondary);margin-bottom:6px;text-transform:uppercase;letter-spacing:1px;">Hari Libur</label>
                        <select id="schedDayOfWeek" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:12px;font-family:'Poppins';font-size:13px;color:var(--text-primary);outline:none;background:#fff;">
                            <option value="">Pilih hari</option>
                            @foreach(\App\Models\OffDay::DAY_NAMES as $num => $name)
                                <option value="{{ $num }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" onclick="saveSchedule()" class="btn-detail" style="width:100%;justify-content:center;padding:12px;border-radius:14px;background:var(--gradient-primary);color:#fff;border:none;font-weight:600;">Simpan Jadwal</button>
                </div>
            </div>
        </div>

        {{-- Schedule Table --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <span class="card-title-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M3 10h18"></path></svg></span>
                    <span>Daftar Jadwal Libur</span>
                </div>
            </div>
            <div class="card-body" style="padding:0;">
                <div class="table-wrapper" style="max-height:350px;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Karyawan</th>
                                <th>Departemen</th>
                                <th>Hari Libur</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="scheduleTableBody">
                            @php $allSchedules = \App\Models\OffDay::with('employee')->orderBy('employee_id')->orderBy('day_of_week')->get(); @endphp
                            @forelse($allSchedules as $schedule)
                            <tr id="sched-row-{{ $schedule->id }}">
                                <td>{{ $schedule->employee?->name ?? '-' }}</td>
                                <td>{{ $schedule->employee?->department ?? '-' }}</td>
                                <td><span class="type-badge type-TUKAR_LIBUR">{{ $schedule->day_name }}</span></td>
                                <td><button class="btn-delete" type="button" onclick="deleteSchedule({{ $schedule->id }})">Hapus</button></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" style="text-align:center;padding:24px;">Belum ada jadwal.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Swap Requests --}}
    <div class="card employee-table-full" style="margin-top:20px;">
        <div class="card-header">
            <div class="card-title">
                <span class="card-title-icon" aria-hidden="true" style="color:var(--accent-orange);"><svg viewBox="0 0 24 24"><path d="M16 3l4 4-4 4"></path><path d="M20 7H8a4 4 0 0 0-4 4v1"></path><path d="M8 21l-4-4 4-4"></path><path d="M4 17h12a4 4 0 0 0 4-4v-1"></path></svg></span>
                <span>Permintaan Tukar Libur</span>
            </div>
            @php $allSwaps = \App\Models\ScheduleSwapRequest::with(['employee','swapWithEmployee'])->orderByDesc('created_at')->get(); @endphp
            @if($allSwaps->where('status','PENDING')->count() > 0)
                <span class="type-badge type-IZIN">{{ $allSwaps->where('status','PENDING')->count() }} menunggu</span>
            @endif
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrapper" style="max-height:450px;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Pengaju</th>
                            <th>Tanggal Libur</th>
                            <th>Tukar Dengan</th>
                            <th>Tanggal Target</th>
                            <th>Alasan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allSwaps as $sw)
                        <tr id="swap-sched-{{ $sw->id }}">
                            <td>
                                <div>{{ $sw->employee?->name ?? '-' }}</div>
                                <small style="color:var(--text-muted);">{{ $sw->employee?->department ?? '-' }}</small>
                            </td>
                            <td>{{ $sw->requested_date?->format('d M Y') }}</td>
                            <td>{{ $sw->swapWithEmployee?->name ?? '-' }}</td>
                            <td>{{ $sw->target_date?->format('d M Y') ?? '-' }}</td>
                            <td style="max-width:150px;">{{ Str::limit($sw->reason, 35) }}</td>
                            <td>
                                <span class="type-badge {{ $sw->status === 'APPROVED' ? 'type-IN' : ($sw->status === 'REJECTED' ? 'type-SAKIT' : 'type-IZIN') }}">
                                    {{ $sw->statusLabel() }}
                                </span>
                            </td>
                            <td>
                                @if($sw->status === 'PENDING')
                                <div style="display:flex;gap:6px;">
                                    <button class="btn-detail" type="button" onclick="approveSwap({{ $sw->id }})">✓</button>
                                    <button class="btn-delete" type="button" onclick="rejectSwap({{ $sw->id }})">✕</button>
                                </div>
                                @else
                                    <span style="font-size:11px;color:var(--text-muted);">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" style="text-align:center;padding:24px;">Belum ada permintaan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
