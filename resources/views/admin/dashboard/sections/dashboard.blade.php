<div class="page-section active" id="page-dashboard">
    <div class="topbar">
        <h1 class="topbar-title">Dashboard</h1>
        <div class="topbar-right">
            <div class="month-picker">
                <select id="selectMonth" onchange="loadData()">
                    @php $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']; @endphp
                    @foreach($months as $i => $m)
                        <option value="{{ $i + 1 }}" {{ $month == $i + 1 ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
                <select id="selectYear" onchange="loadData()">
                    @for($y = 2024; $y <= 2027; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <!-- <button class="btn-refresh" onclick="loadData()">
                <span class="btn-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path d="M20 11a8 8 0 0 0-14.9-4"></path>
                        <path d="M20 4v7h-7"></path>
                        <path d="M4 13a8 8 0 0 0 14.9 4"></path>
                        <path d="M4 20v-7h7"></path>
                    </svg>
                </span>
                Refresh
            </button> -->
            <!-- Live Clock -->
            <div class="user-portal-clock">
                <div class="user-portal-clock-time global-clock-time">--:--:--</div>
                <div class="user-portal-clock-divider"></div>
                <div class="user-portal-clock-date global-clock-date">Memuat...</div>
            </div>
        </div>
    </div>

    <div class="today-live">
        <div class="today-header">
            <div class="today-title">
                <div class="live-dot"></div>
                Status Hari Ini
            </div>
            <div class="today-date" id="todayDateText"></div>
        </div>
        <div class="today-stats" id="todayStats">
            <div class="today-stat">
                <div class="today-stat-value" id="todayPresent" style="color:var(--accent-green)">-</div>
                <div class="today-stat-label">Hadir</div>
            </div>
            <div class="today-stat">
                <div class="today-stat-value" id="todayOut" style="color:var(--accent-blue)">-</div>
                <div class="today-stat-label">Pulang</div>
            </div>
            <div class="today-stat">
                <div class="today-stat-value" id="todayIzin" style="color:var(--accent-orange)">-</div>
                <div class="today-stat-label">Izin</div>
            </div>
            <div class="today-stat">
                <div class="today-stat-value" id="todaySakit" style="color:var(--accent-red)">-</div>
                <div class="today-stat-label">Sakit</div>
            </div>
            <div class="today-stat">
                <div class="today-stat-value" id="todayNotPresent" style="color:var(--accent-pink)">-</div>
                <div class="today-stat-label">Belum Hadir</div>
            </div>
        </div>
    </div>

    {{-- Pending Swap Requests --}}
    @if(isset($pendingSwapRequests) && $pendingSwapRequests->count() > 0)
        <div class="card" style="margin-bottom: 28px; border: 1px solid rgba(240,184,110,0.3);">
            <div class="card-header">
                <div class="card-title">
                    <span class="card-title-icon" aria-hidden="true" style="color: var(--accent-orange);">
                        <svg viewBox="0 0 24 24">
                            <path d="M16 3l4 4-4 4"></path>
                            <path d="M20 7H8a4 4 0 0 0-4 4v1"></path>
                            <path d="M8 21l-4-4 4-4"></path>
                            <path d="M4 17h12a4 4 0 0 0 4-4v-1"></path>
                        </svg>
                    </span>
                    <span>Request Tukar Libur</span>
                </div>
                <span class="type-badge type-IZIN">{{ $pendingSwapRequests->count() }} menunggu</span>
            </div>
            <div class="card-body" style="padding:0;">
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Pengaju</th>
                                <th>Tanggal Libur</th>
                                <th>Tukar Dengan</th>
                                <th>Tanggal Target</th>
                                <th>Alasan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingSwapRequests as $swap)
                                <tr id="swap-row-{{ $swap->id }}">
                                    <td>
                                        <div>{{ $swap->employee?->name ?? '-' }}</div>
                                        <small
                                            style="color:var(--text-muted);">{{ $swap->employee?->department ?? '-' }}</small>
                                    </td>
                                    <td>{{ $swap->requested_date?->format('d M Y') }}</td>
                                    <td>{{ $swap->swapWithEmployee?->name ?? '-' }}</td>
                                    <td>{{ $swap->target_date?->format('d M Y') ?? '-' }}</td>
                                    <td>{{ Str::limit($swap->reason, 40) }}</td>
                                    <td>
                                        <div style="display:flex;gap:6px;">
                                            <button class="btn-detail" type="button" onclick="approveSwap({{ $swap->id }})">✓
                                                Setujui</button>
                                            <button class="btn-delete" type="button" onclick="rejectSwap({{ $swap->id }})">✕
                                                Tolak</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="stats-grid" id="statsGrid">
        <div class="stat-card card-present">
            <div class="stat-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M10 17l5-5-5-5"></path>
                    <path d="M4 12h11"></path>
                    <path d="M20 4v16"></path>
                </svg>
            </div>
            <div class="stat-value" id="statIn">{{ $summary['total_in'] }}</div>
            <div class="stat-label">Total Hadir (IN)</div>
        </div>
        <div class="stat-card card-out">
            <div class="stat-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M14 7l-5 5 5 5"></path>
                    <path d="M19 12H9"></path>
                    <path d="M4 4v16"></path>
                </svg>
            </div>
            <div class="stat-value" id="statOut">{{ $summary['total_out'] }}</div>
            <div class="stat-label">Total Pulang (OUT)</div>
        </div>
        <div class="stat-card card-izin">
            <div class="stat-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M7 4h7l4 4v12H7z"></path>
                    <path d="M14 4v4h4"></path>
                    <path d="M9 12h6"></path>
                    <path d="M9 16h4"></path>
                </svg>
            </div>
            <div class="stat-value" id="statIzin">{{ $summary['total_izin'] }}</div>
            <div class="stat-label">Total Izin</div>
        </div>
        <div class="stat-card card-sakit">
            <div class="stat-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path
                        d="M20.8 8.6c0 5.2-8.8 11.4-8.8 11.4S3.2 13.8 3.2 8.6A4.6 4.6 0 0 1 7.8 4c1.7 0 3.1.8 4.2 2.2C13.1 4.8 14.5 4 16.2 4a4.6 4.6 0 0 1 4.6 4.6z">
                    </path>
                    <path d="M12 8v4"></path>
                    <path d="M10 10h4"></path>
                </svg>
            </div>
            <div class="stat-value" id="statSakit">{{ $summary['total_sakit'] }}</div>
            <div class="stat-label">Total Sakit</div>
        </div>
        <div class="stat-card card-tukar">
            <div class="stat-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M16 3l4 4-4 4"></path>
                    <path d="M20 7H8a4 4 0 0 0-4 4v1"></path>
                    <path d="M8 21l-4-4 4-4"></path>
                    <path d="M4 17h12a4 4 0 0 0 4-4v-1"></path>
                </svg>
            </div>
            <div class="stat-value" id="statTukar">{{ $summary['total_tukar_libur'] }}</div>
            <div class="stat-label">Tukar Libur</div>
        </div>
    </div>

    <div class="content-grid">
        <div class="card">
            <div class="card-header">
                <div class="card-title"><span class="card-title-icon" aria-hidden="true"><svg viewBox="0 0 24 24">
                            <path d="M4 20h16"></path>
                            <path d="M7 16v-6"></path>
                            <path d="M12 16V8"></path>
                            <path d="M17 16v-3"></path>
                        </svg></span><span>Distribusi Absensi</span></div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title"><span class="card-title-icon" aria-hidden="true"><svg viewBox="0 0 24 24">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M17 8h5"></path>
                            <path d="M19.5 5.5v5"></path>
                        </svg></span><span>Statistik Karyawan</span></div>
            </div>

            {{-- Search --}}
            <div class="table-toolbar">
                <div class="table-search">
                    <div class="table-search-icon"><svg viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="M21 21l-4.35-4.35"></path>
                        </svg></div>
                    <input type="text" id="empStatsSearchInput" placeholder="Cari nama..."
                        oninput="filterEmpStatsTable()">
                </div>
                <span class="table-result-count" id="empStatsResultCount"></span>
            </div>

            <div class="card-body" style="padding:0;">
                <div class="table-wrapper" style="max-height: 300px;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>IN</th>
                                <th>OUT</th>
                                <th>Izin</th>
                                <th>Sakit</th>
                                <th>Rata²</th>
                            </tr>
                        </thead>
                        <tbody id="employeeStatsBody">
                            @foreach($employeeStats as $stat)
                                <tr data-search="{{ strtolower($stat['name']) }}">
                                    <td>{{ $stat['name'] }}</td>
                                    <td>{{ $stat['total_in'] }}</td>
                                    <td>{{ $stat['total_out'] }}</td>
                                    <td>{{ $stat['total_izin'] }}</td>
                                    <td>{{ $stat['total_sakit'] }}</td>
                                    <td>{{ $stat['avg_work_duration'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card employee-table-full">
        <div class="card-header">
            <div class="card-title"><span class="card-title-icon" aria-hidden="true"><svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="8"></circle>
                        <path d="M12 8v4l3 2"></path>
                    </svg></span><span>Riwayat Absensi Terbaru</span></div>
        </div>

        {{-- Search & Filter --}}
        <div class="table-toolbar">
            <div class="table-search">
                <div class="table-search-icon"><svg viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="M21 21l-4.35-4.35"></path>
                    </svg></div>
                <input type="text" id="recentAttSearchInput" placeholder="Cari nama / ID..."
                    oninput="filterRecentAttTable()">
            </div>
            <div class="table-filter">
                <select id="recentAttFilterType" onchange="filterRecentAttTable()">
                    <option value="">Semua Tipe</option>
                    <option value="IN">Masuk (IN)</option>
                    <option value="OUT">Pulang (OUT)</option>
                    <option value="IZIN">Izin</option>
                    <option value="SAKIT">Sakit</option>
                </select>
            </div>
            <div class="table-filter">
                <select id="recentAttFilterDept" onchange="filterRecentAttTable()">
                    <option value="">Semua Departemen</option>
                    @php $attDepts = $recentAttendances->map(fn($a) => $a->employee->department)->unique()->sort(); @endphp
                    @foreach($attDepts as $dept)
                        <option value="{{ $dept }}">{{ $dept }}</option>
                    @endforeach
                </select>
            </div>
            <span class="table-result-count" id="recentAttResultCount"></span>
        </div>

        <div class="card-body" style="padding:0;">
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>ID</th>
                            <th>Departemen</th>
                            <th>Tipe</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Jarak</th>
                        </tr>
                    </thead>
                    <tbody id="recentAttendanceBody">
                        @foreach($recentAttendances as $att)
                            <tr data-search="{{ strtolower($att->employee->name . ' ' . $att->employee->employee_id) }}"
                                data-type="{{ $att->type }}" data-dept="{{ $att->employee->department }}">
                                <td>{{ $att->employee->name }}</td>
                                <td>{{ $att->employee->employee_id }}</td>
                                <td>{{ $att->employee->department }}</td>
                                <td><span class="type-badge type-{{ $att->type }}">{{ $att->getTypeLabel() }}</span></td>
                                <td>{{ $att->date->format('d M Y') }}</td>
                                <td>{{ $att->time }}</td>
                                <td>{{ $att->distance_meters ? round($att->distance_meters) . 'm' : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>