<div class="page-section {{ (isset($activePage) && $activePage === 'schedules') ? 'active' : '' }}" id="page-schedules" style="{{ (isset($activePage) && $activePage === 'schedules') ? 'display:block;' : 'display:none;' }}">
    <div class="topbar">
        <h1 class="topbar-title">Jadwal & Tukar Libur</h1>
        <div class="topbar-right" style="display:flex;align-items:center;">
            <div class="user-portal-clock">
                <div class="user-portal-clock-time global-clock-time">--:--:--</div>
                <div class="user-portal-clock-divider"></div>
                <div class="user-portal-clock-date global-clock-date">Memuat...</div>
            </div>
        </div>
    </div>

    @php
        $scheduleStats = $stats ?? [
            'total_schedules' => 0,
            'pending_requests' => 0,
            'approved_requests' => 0,
            'rejected_requests' => 0,
        ];
        $groupedSchedules = ($schedules ?? collect())
            ->filter(fn ($schedule) => $schedule->employee)
            ->groupBy('employee_id');
    @endphp

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card card-tukar">
            <div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M3 10h18"></path></svg></div>
            <div class="stat-value">{{ $scheduleStats['total_schedules'] }}</div>
            <div class="stat-label">Total Jadwal</div>
        </div>
        <div class="stat-card card-izin">
            <div class="stat-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg></div>
            <div class="stat-value" style="color:var(--accent-orange)">{{ $scheduleStats['pending_requests'] }}</div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card card-present">
            <div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"></path></svg></div>
            <div class="stat-value" style="color:var(--accent-green)">{{ $scheduleStats['approved_requests'] }}</div>
            <div class="stat-label">Disetujui</div>
        </div>
        <div class="stat-card card-sakit">
            <div class="stat-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M15 9l-6 6"></path><path d="M9 9l6 6"></path></svg></div>
            <div class="stat-value" style="color:var(--accent-red)">{{ $scheduleStats['rejected_requests'] }}</div>
            <div class="stat-label">Ditolak</div>
        </div>
    </div>

    {{-- Schedule Table --}}
    <div class="card employee-table-full">
            <div class="card-header">
                <div class="card-title">
                    <span class="card-title-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M3 10h18"></path></svg></span>
                    <span>Daftar Jadwal Libur</span>
                </div>
            </div>

            {{-- Search & Filter --}}
            <div class="table-toolbar">
                <div class="table-search">
                    <div class="table-search-icon"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><path d="M21 21l-4.35-4.35"></path></svg></div>
                    <input type="text" id="schedSearchInput" placeholder="Cari nama karyawan..." oninput="filterScheduleTable()">
                </div>
                <div class="table-filter">
                    <select id="schedFilterDept" onchange="filterScheduleTable()">
                        <option value="">Semua Departemen</option>
                        @php $schedDepts = ($schedules ?? collect())->map(fn($s) => $s->employee?->department)->filter()->unique()->sort(); @endphp
                        @foreach($schedDepts as $dept)
                            <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <span class="table-result-count" id="schedResultCount">{{ $groupedSchedules->count() }} karyawan</span>
            </div>

            <div class="card-body" style="padding:0;">
                <div class="table-wrapper" style="max-height:350px;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Karyawan</th>
                                <th>Departemen</th>
                                <th>Hari Libur</th>
                            </tr>
                        </thead>
                        <tbody id="scheduleTableBody">
                            @forelse($employees as $emp)
                            @php
                                $empSchedules = ($schedules ?? collect())->filter(fn($s) => $s->employee_id == $emp->employee_id);
                            @endphp
                            <tr id="sched-emp-{{ $emp->id }}" data-search="{{ strtolower($emp->name) }}" data-dept="{{ $emp->department }}">
                                <td>{{ $emp->name }}</td>
                                <td>{{ $emp->department ?? '-' }}</td>
                                <td>
                                    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                                        @foreach($empSchedules as $schedule)
                                            <select
                                                class="schedule-day-select"
                                                style="padding:6px 10px;border:1px solid var(--border);border-radius:10px;background:#fff;color:var(--text-primary);font-family:'Poppins';font-size:12px;"
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
                                                class="schedule-day-add"
                                                style="padding:6px 10px;border:1px dashed #dba0be;border-radius:10px;background:none;color:#e87bb0;font-family:'Poppins';font-size:12px;cursor:pointer;outline:none;"
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
                            <tr><td colspan="3" style="text-align:center;padding:24px;">Belum ada karyawan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
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
            @if(($swapRequests ?? collect())->where('status','PENDING')->count() > 0)
                <span class="type-badge type-IZIN">{{ ($swapRequests ?? collect())->where('status','PENDING')->count() }} menunggu</span>
            @endif
        </div>

        {{-- Search & Filter --}}
        <div class="table-toolbar">
            <div class="table-search">
                <div class="table-search-icon"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><path d="M21 21l-4.35-4.35"></path></svg></div>
                <input type="text" id="swapSearchInput" placeholder="Cari nama pengaju..." oninput="filterSwapTable()">
            </div>
            <div class="table-filter">
                <select id="swapFilterStatus" onchange="filterSwapTable()">
                    <option value="">Semua Status</option>
                    <option value="PENDING">Pending</option>
                    <option value="APPROVED">Disetujui</option>
                    <option value="REJECTED">Ditolak</option>
                </select>
            </div>
            <span class="table-result-count" id="swapResultCount"></span>
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
                    <tbody id="swapTableBody">
                        @forelse(($swapRequests ?? collect()) as $sw)
                        <tr id="swap-sched-{{ $sw->id }}" data-search="{{ strtolower($sw->employee?->name ?? '') }}" data-status="{{ $sw->status }}">
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
                                    <button class="btn-detail" type="button" onclick="approveSwap({{ $sw->id }}, '{{ $sw->employee->department ?? '' }}', '{{ \Carbon\Carbon::parse($sw->target_date)->format('Y-m-d') }}')">✓</button>
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
