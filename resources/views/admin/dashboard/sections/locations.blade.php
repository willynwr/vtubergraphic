<div class="page-section" id="page-locations">
    <div class="topbar">
        <h1 class="topbar-title">Kelola Lokasi Kantor</h1>
    </div>

    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">
            <div class="card-title"><span class="card-title-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg></span><span>Tambah Lokasi</span></div>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Nama Lokasi</label>
                    <input type="text" id="newLocName" placeholder="Kantor Pusat">
                </div>
                <div class="form-group">
                    <label>Latitude</label>
                    <input type="number" step="any" id="newLocLat" placeholder="-6.2088">
                </div>
                <div class="form-group">
                    <label>Longitude</label>
                    <input type="number" step="any" id="newLocLng" placeholder="106.8456">
                </div>
                <div class="form-group">
                    <label>Radius (meter)</label>
                    <input type="number" id="newLocRadius" placeholder="1000" value="1000">
                </div>
            </div>
            <div style="display:flex;gap:12px;align-items:center">
                <button class="btn-add" onclick="addLocation()">Tambah Lokasi</button>
                <button class="btn-add" style="background:rgba(6,182,212,0.15);color:var(--accent-cyan);display:inline-flex;align-items:center;gap:8px;" onclick="getMyLocation()"><span class="btn-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11z"></path><circle cx="12" cy="10" r="2.5"></circle></svg></span><span>Gunakan Lokasi Saya</span></button>
            </div>
        </div>
    </div>

    <div class="card employee-table-full">
        <div class="card-header">
            <div class="card-title"><span class="card-title-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11z"></path><circle cx="12" cy="10" r="2.5"></circle></svg></span><span>Daftar Lokasi</span></div>
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
                        <tr id="loc-row-{{ $loc->id }}">
                            <td>{{ $loc->name }}</td>
                            <td>{{ $loc->latitude }}</td>
                            <td>{{ $loc->longitude }}</td>
                            <td>{{ $loc->radius_meters }}m</td>
                            <td><button class="btn-delete js-location-delete" type="button" data-location-id="{{ $loc->id }}">Hapus</button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
