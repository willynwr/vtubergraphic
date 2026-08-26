<div class="page-section {{ (isset($activePage) && $activePage === 'employees') ? 'active' : '' }}" id="page-employees" style="{{ (isset($activePage) && $activePage === 'employees') ? 'display:block;' : 'display:none;' }}">
    <div class="topbar">
        <h1 class="topbar-title">Kelola Karyawan</h1>
        <div class="topbar-right" style="display:flex;align-items:center;gap:12px;">
            <div class="user-portal-clock">
                <div class="user-portal-clock-time global-clock-time">--:--:--</div>
                <div class="user-portal-clock-divider"></div>
                <div class="user-portal-clock-date global-clock-date">Memuat...</div>
            </div>
        </div>
    </div>

    <div class="card employee-table-full">
        <div class="card-header">
            <div class="card-title"><span class="card-title-icon" aria-hidden="true"><svg viewBox="0 0 24 24">
                        <path d="M9 4H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-4"></path>
                        <path d="M9 2v4"></path>
                        <path d="M15 2v4"></path>
                        <path d="M7 10h10"></path>
                    </svg></span><span>Daftar Karyawan</span></div>
        </div>

        {{-- Search & Filter --}}
        <div class="table-toolbar">
            <button class="btn-add" onclick="openAddEmployeeModal()"
                style="display:inline-flex;align-items:center;gap:8px;padding:10px 16px;">
                <span class="btn-icon" aria-hidden="true"><svg viewBox="0 0 24 24" style="width:16px;height:16px;">
                        <path d="M12 5v14"></path>
                        <path d="M5 12h14"></path>
                    </svg></span>
                Add Karyawan
            </button>
            <div class="table-search">
                <div class="table-search-icon"><svg viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="M21 21l-4.35-4.35"></path>
                    </svg></div>
                <input type="text" id="empSearchInput" placeholder="Cari nama / ID karyawan..."
                    oninput="filterEmployeeTable()">
            </div>
            <div class="table-filter">
                <select id="empFilterDept" onchange="filterEmployeeTable()">
                    <option value="">Semua Departemen</option>
                    @php $departments = $employees->pluck('department')->unique()->sort(); @endphp
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}">{{ $dept }}</option>
                    @endforeach
                </select>
            </div>
            <span class="table-result-count" id="empResultCount">{{ $employees->count() }} data</span>
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
                            <tr id="emp-row-{{ $emp->id }}"
                                data-search="{{ strtolower($emp->name . ' ' . $emp->employee_id) }}"
                                data-dept="{{ $emp->department }}">
                                <td>
                                    <div class="qr-card">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=256x256&margin=10&qzone=1&ecc=H&data={{ rawurlencode($emp->employee_id) }}&bgcolor=ffffff&color=6b3f73"
                                            class="qr-preview" alt="QR {{ $emp->employee_id }}" loading="lazy">
                                        <div class="qr-card-label">{{ $emp->name }} - {{ $emp->employee_id }}</div>
                                        <button class="btn-qr-download" type="button"
                                            data-employee-id="{{ $emp->employee_id }}" data-employee-name="{{ $emp->name }}"
                                            onclick="downloadEmployeeQr(this.dataset.employeeId, this.dataset.employeeName)">
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
                                        <button class="btn-detail js-employee-detail" type="button"
                                            data-employee-id="{{ $emp->id }}">Detail</button>
                                        <button class="btn-delete js-employee-delete" type="button"
                                            data-employee-id="{{ $emp->id }}">Hapus</button>
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

{{-- Add Employee Modal --}}
<div class="admin-modal-overlay" id="addEmployeeOverlay"
    onclick="if(event.target.id==='addEmployeeOverlay') closeAddEmployeeModal()">
    <div class="admin-modal-card" style="max-width:520px;">
        <div class="admin-modal-icon icon-confirm">
            <svg viewBox="0 0 24 24">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M17 8h5"></path>
                <path d="M19.5 5.5v5"></path>
            </svg>
        </div>
        <div class="admin-modal-title">Tambah Karyawan Baru</div>
        <div style="display:flex;flex-direction:column;gap:14px;margin-top:16px;">
            <div>
                <label
                    style="display:block;font-size:12px;color:var(--text-secondary);margin-bottom:6px;font-weight:500;">ID
                    Karyawan</label>
                <input type="text" id="newEmpId" placeholder="VTG-009"
                    style="width:100%;padding:12px 16px;border:1px solid var(--border);border-radius:12px;background:var(--bg-glass);color:var(--text-primary);font-size:14px;font-family:'Poppins',sans-serif;outline:none;transition:all 0.3s;">
            </div>
            <div>
                <label
                    style="display:block;font-size:12px;color:var(--text-secondary);margin-bottom:6px;font-weight:500;">Nama
                    Lengkap</label>
                <input type="text" id="newEmpName" placeholder="Nama karyawan"
                    style="width:100%;padding:12px 16px;border:1px solid var(--border);border-radius:12px;background:var(--bg-glass);color:var(--text-primary);font-size:14px;font-family:'Poppins',sans-serif;outline:none;transition:all 0.3s;">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label
                        style="display:block;font-size:12px;color:var(--text-secondary);margin-bottom:6px;font-weight:500;">Departemen</label>
                    <input type="text" id="newEmpDept" placeholder="Design / Tech"
                        style="width:100%;padding:12px 16px;border:1px solid var(--border);border-radius:12px;background:var(--bg-glass);color:var(--text-primary);font-size:14px;font-family:'Poppins',sans-serif;outline:none;transition:all 0.3s;">
                </div>
                <div>
                    <label
                        style="display:block;font-size:12px;color:var(--text-secondary);margin-bottom:6px;font-weight:500;">Posisi</label>
                    <input type="text" id="newEmpPos" placeholder="Animator / dll"
                        style="width:100%;padding:12px 16px;border:1px solid var(--border);border-radius:12px;background:var(--bg-glass);color:var(--text-primary);font-size:14px;font-family:'Poppins',sans-serif;outline:none;transition:all 0.3s;">
                </div>
            </div>
        </div>
        <div class="admin-modal-actions" style="margin-top:20px;">
            <button class="admin-modal-btn btn-cancel-modal" onclick="closeAddEmployeeModal()">Batal</button>
            <button class="admin-modal-btn btn-primary-modal" onclick="addEmployee()">Tambah Karyawan</button>
        </div>
    </div>
</div>