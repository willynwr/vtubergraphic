<div class="page-section" id="page-history">
    <div class="topbar">
        <h1 class="topbar-title">Riwayat Absensi Lengkap</h1>
    </div>
    <div class="card employee-table-full">
        <div class="card-header">
            <div class="card-title"><span class="card-title-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"></circle><path d="M12 8v4l3 2"></path></svg></span><span>Semua Data Absensi</span></div>
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
                        <tr>
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
