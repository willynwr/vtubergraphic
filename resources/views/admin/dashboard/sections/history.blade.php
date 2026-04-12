<div class="page-section {{ (isset($activePage) && $activePage === 'history') ? 'active' : '' }}" id="page-history" style="{{ (isset($activePage) && $activePage === 'history') ? 'display:block;' : 'display:none;' }}">
    <div class="topbar">
        <h1 class="topbar-title">Riwayat Absensi Lengkap</h1>
        <div class="topbar-right" style="display:flex;align-items:center;">
            <div class="user-portal-clock">
                <div class="user-portal-clock-time global-clock-time">--:--:--</div>
                <div class="user-portal-clock-divider"></div>
                <div class="user-portal-clock-date global-clock-date">Memuat...</div>
            </div>
        </div>
    </div>
    <div class="card employee-table-full">
        <div class="card-header">
            <div class="card-title"><span class="card-title-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"></circle><path d="M12 8v4l3 2"></path></svg></span><span>Semua Data Absensi</span></div>
        </div>

        {{-- Search & Filter --}}
        <div class="table-toolbar">
            <div class="table-search">
                <div class="table-search-icon"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><path d="M21 21l-4.35-4.35"></path></svg></div>
                <input type="text" id="histSearchInput" placeholder="Cari nama / ID karyawan..." oninput="filterHistoryTable()">
            </div>
            <div class="table-filter">
                <select id="histFilterType" onchange="filterHistoryTable()">
                    <option value="">Semua Tipe</option>
                    <option value="IN">Masuk (IN)</option>
                    <option value="OUT">Pulang (OUT)</option>
                    <option value="IZIN">Izin</option>
                    <option value="SAKIT">Sakit</option>
                </select>
            </div>
            <div class="table-filter">
                <select id="histFilterDate" onchange="filterHistoryTable()">
                    <option value="">Semua Tanggal</option>
                    <option value="today">Hari Ini</option>
                    <option value="week">7 Hari Terakhir</option>
                    <option value="month">30 Hari Terakhir</option>
                </select>
            </div>
            <span class="table-result-count" id="histResultCount"></span>
        </div>

        <div class="card-body" style="padding:0;">
            <div class="table-wrapper" style="max-height: 600px;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>ID</th>
                            <th>Tipe</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Jarak</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody id="historyBody">
                        @foreach($recentAttendances as $att)
                        <tr data-search="{{ strtolower($att->employee->name . ' ' . $att->employee->employee_id) }}" data-type="{{ $att->type }}" data-date="{{ $att->date->format('Y-m-d') }}">
                            <td>{{ $att->employee->name }}</td>
                            <td>{{ $att->employee->employee_id }}</td>
                            <td><span class="type-badge type-{{ $att->type }}">{{ $att->getTypeLabel() }}</span></td>
                            <td>{{ $att->date->format('d M Y') }}</td>
                            <td>{{ $att->time }}</td>
                            <td>{{ $att->distance_meters ? round($att->distance_meters) . 'm' : '-' }}</td>
                            <td>{{ $att->note ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
