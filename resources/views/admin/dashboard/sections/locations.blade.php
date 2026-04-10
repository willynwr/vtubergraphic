<div class="page-section" id="page-locations">
    <div class="topbar">
        <h1 class="topbar-title">Kelola Lokasi Kantor</h1>
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
            <div class="card-title"><span class="card-title-icon" aria-hidden="true"><svg viewBox="0 0 24 24">
                        <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11z"></path>
                        <circle cx="12" cy="10" r="2.5"></circle>
                    </svg></span><span>Daftar Lokasi</span></div>
        </div>

        {{-- Search --}}
        <div class="table-toolbar">
            <button class="btn-add" onclick="openAddLocationModal()"
                style="display:inline-flex;align-items:center;gap:8px;padding:10px 16px;">
                <span class="btn-icon" aria-hidden="true"><svg viewBox="0 0 24 24" style="width:16px;height:16px;">
                        <path d="M12 5v14"></path>
                        <path d="M5 12h14"></path>
                    </svg></span>
                Add Lokasi
            </button>
            <div class="table-search">
                <div class="table-search-icon"><svg viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="M21 21l-4.35-4.35"></path>
                    </svg></div>
                <input type="text" id="locSearchInput" placeholder="Cari nama lokasi..."
                    oninput="filterLocationTable()">
            </div>
            <span class="table-result-count" id="locResultCount">{{ count($locations ?? []) }} data</span>
        </div>

        <div class="card-body" style="padding:0;">
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Radius</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="locationListBody">
                        @foreach($locations ?? [] as $loc)
                            <tr id="loc-row-{{ $loc->id }}" data-search="{{ strtolower($loc->name) }}">
                                <td>{{ $loc->name }}</td>
                                <td>{{ $loc->latitude }}</td>
                                <td>{{ $loc->longitude }}</td>
                                <td>{{ $loc->radius_meters }}m</td>
                                <td><button class="btn-delete js-location-delete" type="button"
                                        data-location-id="{{ $loc->id }}">Hapus</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add Location Modal --}}
<div class="admin-modal-overlay" id="addLocationOverlay"
    onclick="if(event.target.id==='addLocationOverlay') closeAddLocationModal()">
    <div class="admin-modal-card" style="max-width:520px;">
        <div class="admin-modal-icon icon-confirm">
            <svg viewBox="0 0 24 24">
                <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11z"></path>
                <circle cx="12" cy="10" r="2.5"></circle>
            </svg>
        </div>
        <div class="admin-modal-title">Tambah Lokasi Kantor</div>
        <div style="display:flex;flex-direction:column;gap:14px;margin-top:16px;">
            <div>
                <label
                    style="display:block;font-size:12px;color:var(--text-secondary);margin-bottom:6px;font-weight:500;">Nama
                    Lokasi</label>
                <input type="text" id="newLocName" placeholder="Kantor Pusat"
                    style="width:100%;padding:12px 16px;border:1px solid var(--border);border-radius:12px;background:var(--bg-glass);color:var(--text-primary);font-size:14px;font-family:'Poppins',sans-serif;outline:none;transition:all 0.3s;">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label
                        style="display:block;font-size:12px;color:var(--text-secondary);margin-bottom:6px;font-weight:500;">Latitude</label>
                    <input type="number" step="any" id="newLocLat" placeholder="-6.2088"
                        style="width:100%;padding:12px 16px;border:1px solid var(--border);border-radius:12px;background:var(--bg-glass);color:var(--text-primary);font-size:14px;font-family:'Poppins',sans-serif;outline:none;transition:all 0.3s;">
                </div>
                <div>
                    <label
                        style="display:block;font-size:12px;color:var(--text-secondary);margin-bottom:6px;font-weight:500;">Longitude</label>
                    <input type="number" step="any" id="newLocLng" placeholder="106.8456"
                        style="width:100%;padding:12px 16px;border:1px solid var(--border);border-radius:12px;background:var(--bg-glass);color:var(--text-primary);font-size:14px;font-family:'Poppins',sans-serif;outline:none;transition:all 0.3s;">
                </div>
            </div>
            <div>
                <label
                    style="display:block;font-size:12px;color:var(--text-secondary);margin-bottom:6px;font-weight:500;">Radius
                    (meter)</label>
                <input type="number" id="newLocRadius" placeholder="1000" value="1000"
                    style="width:100%;padding:12px 16px;border:1px solid var(--border);border-radius:12px;background:var(--bg-glass);color:var(--text-primary);font-size:14px;font-family:'Poppins',sans-serif;outline:none;transition:all 0.3s;">
            </div>
            <button type="button" onclick="getMyLocation()"
                style="padding:10px 16px;border:1px solid rgba(6,182,212,0.2);border-radius:12px;background:rgba(6,182,212,0.08);color:var(--accent-cyan);font-family:'Poppins',sans-serif;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;justify-content:center;transition:all 0.2s;">
                <span class="btn-icon" aria-hidden="true"><svg viewBox="0 0 24 24">
                        <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11z"></path>
                        <circle cx="12" cy="10" r="2.5"></circle>
                    </svg></span>
                Gunakan Lokasi Saya
            </button>
        </div>
        <div class="admin-modal-actions" style="margin-top:20px;">
            <button class="admin-modal-btn btn-cancel-modal" onclick="closeAddLocationModal()">Batal</button>
            <button class="admin-modal-btn btn-primary-modal" onclick="addLocation()">Tambah Lokasi</button>
        </div>
    </div>
</div>