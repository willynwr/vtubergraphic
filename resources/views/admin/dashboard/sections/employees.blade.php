<div class="page-section" id="page-employees">
    <div class="topbar">
        <h1 class="topbar-title">Kelola Karyawan</h1>
    </div>

    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">
            <div class="card-title"><span class="card-title-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg></span><span>Tambah Karyawan Baru</span></div>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label>ID Karyawan</label>
                    <input type="text" id="newEmpId" placeholder="VTG-009">
                </div>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" id="newEmpName" placeholder="Nama karyawan">
                </div>
                <div class="form-group">
                    <label>Departemen</label>
                    <input type="text" id="newEmpDept" placeholder="Design / Tech / dll">
                </div>
                <div class="form-group">
                    <label>Posisi</label>
                    <input type="text" id="newEmpPos" placeholder="Animator / Designer / dll">
                </div>
            </div>
            <button class="btn-add" onclick="addEmployee()">Tambah Karyawan</button>
        </div>
    </div>

    <div class="card employee-table-full">
        <div class="card-header">
            <div class="card-title"><span class="card-title-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M9 4H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-4"></path><path d="M9 2v4"></path><path d="M15 2v4"></path><path d="M7 10h10"></path></svg></span><span>Daftar Karyawan</span></div>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>QR Code</th>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Departemen</th>
                            <th>Posisi</th>
                            <th>Total Absensi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="employeeListBody">
                        @foreach($employees as $emp)
                        <tr id="emp-row-{{ $emp->id }}">
                            <td>
                                <div class="qr-card">
                                    <img
                                        src="https://api.qrserver.com/v1/create-qr-code/?size=256x256&margin=12&data={{ rawurlencode($emp->employee_id) }}&bgcolor=ffffff&color=111111"
                                        class="qr-preview"
                                        alt="QR {{ $emp->employee_id }}"
                                        loading="lazy"
                                    >
                                    <div class="qr-card-label">
                                        <span>QR</span>
                                        <span class="qr-card-code">{{ $emp->employee_id }}</span>
                                    </div>
                                    <button
                                        class="btn-qr-download"
                                        type="button"
                                        data-employee-id="{{ $emp->employee_id }}"
                                        data-employee-name="{{ $emp->name }}"
                                        onclick="downloadEmployeeQr(this.dataset.employeeId, this.dataset.employeeName)"
                                    >
                                        Download
                                    </button>
                                </div>
                            </td>
                            <td>{{ $emp->employee_id }}</td>
                            <td>{{ $emp->name }}</td>
                            <td>{{ $emp->department }}</td>
                            <td>{{ $emp->position }}</td>
                            <td>{{ $emp->attendances_count ?? 0 }}</td>
                            <td>
                                <div style="display:flex;gap:6px;">
                                    <button class="btn-detail js-employee-detail" type="button" data-employee-id="{{ $emp->id }}">Detail</button>
                                    <button class="btn-delete js-employee-delete" type="button" data-employee-id="{{ $emp->id }}">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
