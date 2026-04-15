<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modul 4: Fleet & Hub Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif;}
        .navbar-brand { font-weight: bold; }
        .card-stat { border-left: 4px solid #0d6efd; transition: 0.3s; }
        .card-stat:hover { transform: translateY(-5px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .status-badge { font-size: 0.85em; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 p-3 shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="bi bi-box-seam"></i> Logistik Tim 1</a>
        <div class="ms-auto text-white">
            <span>💻 Modul 4: Fleet Management & Hub Monitoring</span>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-2 fw-bold">Dashboard Pusat Operasional</h2>
            <p class="text-muted">Pantau pergerakan armada harian dan awasi kapasitas gudang untuk menghindari penumpukan barang secara *real-time*.</p>
        </div>
        
        <div class="col-md-6 mb-3">
            <div class="card card-stat border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 text-primary">
                        <i class="bi bi-truck" style="font-size: 3rem;"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1 text-uppercase fw-bold">Total Armada Kendaraan</h6>
                        <h2 class="mb-0 fw-bold">{{ $fleets->total() }} <span style="font-size: 1rem; color: #6c757d;">Unit Aktif</span></h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card card-stat border-0 shadow-sm" style="border-left-color: #198754;">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 text-success">
                        <i class="bi bi-buildings" style="font-size: 3rem;"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1 text-uppercase fw-bold">Titik Transit Gudang (Hub)</h6>
                        <h2 class="mb-0 fw-bold">{{ $hubs->count() }} <span style="font-size: 1rem; color: #6c757d;">Lokasi Nasional</span></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Hub Capacity Panel -->
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-archive-fill text-warning me-2"></i> Monitor Kapasitas Gudang</h5>
                    <form method="GET" action="/" class="d-flex" style="max-width: 250px;">
                        <input type="hidden" name="search_fleet" value="{{ request('search_fleet') }}">
                        <input type="text" name="search_hub" class="form-control form-control-sm me-2" placeholder="Cari lokasi gudang..." value="{{ request('search_hub') }}">
                        <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
                    </form>
                </div>
                <div class="card-body p-0" style="max-height: 550px; overflow-y: auto;">
                    <ul class="list-group list-group-flush">
                        @foreach($hubs->take(15) as $hub)
                        @php
                            $percentage = ($hub->capacity > 0) ? round(($hub->current_load / $hub->capacity) * 100) : 0;
                            $color = 'success';
                            if($percentage >= 90) $color = 'danger';
                            elseif($percentage >= 70) $color = 'warning';
                        @endphp
                        <li class="list-group-item p-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong class="fs-6">{{ $hub->name }}</strong>
                                <span class="badge bg-{{ $color }} status-badge px-2 py-1"><i class="bi bi-pie-chart-fill"></i> {{ $percentage }}% Terisi</span>
                            </div>
                            <div class="progress rounded-pill bg-light" style="height: 12px; border: 1px solid #e9ecef;">
                                <div class="progress-bar bg-{{ $color }} progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="mt-2 text-muted fw-semibold" style="font-size: 0.85em;">
                                Beban saat ini: <span class="text-dark">{{ number_format($hub->current_load) }}</span> / Kapasitas: <span class="text-dark">{{ number_format($hub->capacity) }}</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer bg-light border-0 text-center">
                    <button class="btn btn-sm btn-outline-secondary w-100" onclick="alert('Ini contoh tampilan sebagian gudang. Terdapat {{ $hubs->count() }} hub keseluruhan.')">Lihat Semua Gudang</button>
                </div>
            </div>
        </div>

        <!-- Fleet Realtime Panel -->
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-geo-alt-fill text-danger me-2"></i> Live Fleet Tracking</h5>
                    <div class="d-flex align-items-center">
                        <form method="GET" action="/" class="d-flex me-3" style="max-width: 300px;">
                            <input type="hidden" name="search_hub" value="{{ request('search_hub') }}">
                            <input type="text" name="search_fleet" class="form-control form-control-sm me-2" placeholder="Cari nopol B 1234 XYZ..." value="{{ request('search_fleet') }}">
                            <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
                        </form>
                        <button class="btn btn-sm btn-primary me-2 shadow-sm rounded-pill text-nowrap" data-bs-toggle="modal" data-bs-target="#addFleetModal">
                            <i class="bi bi-plus-circle"></i> Tambah Armada
                        </button>
                        <span class="badge bg-primary text-white rounded-pill px-3 shadow-sm text-nowrap">Hal {{ $fleets->currentPage() }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">No. Polisi</th>
                                    <th>Tipe</th>
                                    <th>Status</th>
                                    <th>Lokasi Terakhir</th>
                                    <th class="pe-4">Laporan API</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fleets as $fleet)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 border me-2"><i class="bi bi-truck"></i></div>
                                            <strong>{{ $fleet->plate_number }}</strong>
                                        </div>
                                    </td>
                                    <td><span class="text-capitalize text-muted fw-semibold">{{ $fleet->type }}</span></td>
                                    <td>
                                        <select class="form-select form-select-sm border-secondary shadow-sm" style="width: 140px; display: inline-block;" onchange="updateStatus({{ $fleet->id }}, this.value)">
                                            <option value="idle" {{ $fleet->status == 'idle' ? 'selected' : '' }}>🟢 Idle</option>
                                            <option value="in_transit" {{ $fleet->status == 'in_transit' ? 'selected' : '' }}>🔵 In Transit</option>
                                            <option value="maintenance" {{ $fleet->status == 'maintenance' ? 'selected' : '' }}>🟠 Maintenance</option>
                                        </select>
                                    </td>
                                    <td class="text-muted fw-medium">
                                        <select class="form-select form-select-sm border-primary shadow-sm" style="width: 170px;" onchange="relocateFleet({{ $fleet->id }}, this.value, this)">
                                            @foreach($allHubs as $hub_option)
                                                <option value="{{ $hub_option->id }}" {{ $fleet->current_hub_id == $hub_option->id ? 'selected' : '' }}>
                                                    📍 {{ $hub_option->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="pe-4">
                                        <button class="btn btn-sm btn-primary rounded-pill shadow-sm" onclick="viewApi('{{ url('api/fleet/'.$fleet->id.'/duration') }}')">
                                            <i class="bi bi-clock-history"></i> Cek Transit API
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 d-flex justify-content-between px-4 pb-4">
                    {{ $fleets->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Armada Manual -->
<div class="modal fade" id="addFleetModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white border-0">
        <h5 class="modal-title fw-bold"><i class="bi bi-truck me-2"></i> Registrasi Armada Baru</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4 bg-light">
        <form id="addFleetForm">
            <div class="mb-3">
                <label class="form-label fw-bold text-muted small">Nomor Polisi (Plat)</label>
                <input type="text" class="form-control" name="plate_number" required placeholder="Contoh: B 1234 XYZ">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold text-muted small">Tipe Kendaraan</label>
                <select class="form-select" name="type" required>
                    <option value="motorcycle">Sepeda Motor</option>
                    <option value="van">Mobil Van</option>
                    <option value="truck">Truk Ekspedisi</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold text-muted small">Kapasitas Daya Angkut (Paket)</label>
                <input type="number" class="form-control" name="capacity" required placeholder="Contoh: 500">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold text-muted small">Penempatan Gudang Awal (Hub)</label>
                <select class="form-select" name="current_hub_id" required>
                    @foreach($allHubs as $hub)
                        <option value="{{ $hub->id }}">{{ $hub->name }} (Sisa: {{ $hub->capacity - $hub->current_load }})</option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" name="status" value="idle">
        </form>
      </div>
      <div class="modal-footer border-0 bg-white">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary px-4" onclick="submitFleet()">Simpan Kendaraan</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function viewApi(url) {
        Swal.fire({
            title: 'Fetching Data Backend...',
            text: 'Memanggil API Laporan Durasi Transit Modul 4...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
                fetch(url)
                    .then(response => response.json())
                    .then(res => {
                        let historyCount = res.data.history ? res.data.history.length : 0;
                        Swal.fire({
                            icon: 'success',
                            title: '📊 Laporan API Diterima',
                            html: `
                                <div class="text-start mt-3">
                                    <div class="alert alert-light border">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Total Pergerakan Truk:</span>
                                            <strong>\${historyCount} x Berpindah Hub</strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Rata-rata Waktu Transit:</span>
                                            <span class="badge bg-primary fs-6">\${res.data.average_duration_hours} Jam</span>
                                        </div>
                                    </div>
                                    <small class="text-muted"><i class="bi bi-info-circle"></i> Endpoint dipanggil: <br><code>\${url}</code></small>
                                </div>
                            `,
                            confirmButtonText: 'Tutup',
                            confirmButtonColor: '#0d6efd'
                        })
                    })
                    .catch(err => {
                        Swal.fire('Error', 'Gagal memanggil API endpoint: ' + url, 'error')
                    })
            }
        })
    }

    function submitFleet() {
        let form = document.getElementById('addFleetForm');
        if(!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        let formData = new FormData(form);
        let object = {};
        formData.forEach((value, key) => object[key] = value);
        let json = JSON.stringify(object);
        
        Swal.fire({
            title: 'Menyimpan Armada Baru...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch('/api/fleet', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: json
        })
        .then(async response => {
            let res = await response.json();
            if(!response.ok) throw new Error(res.message || 'Terjadi kesalahan penambahan data');
            return res;
        })
        .then(res => {
            Swal.fire('Sukses!', res.message, 'success').then(() => location.reload());
        })
        .catch(err => {
            Swal.fire('Gagal', err.message, 'error');
        });
    }

    function updateStatus(id, newStatus) {
        Swal.fire({
            title: 'Memperbarui Status...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch(`/api/fleet/${id}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(async response => {
            let res = await response.json();
            if(!response.ok) throw new Error(res.message);
            return res;
        })
        .then(res => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Edit Armada',
                html: `${res.message}<br><br><small class="text-muted"><i class="bi bi-info-circle"></i> Jika truk <b>Berangkat (In Transit)</b>, muatan gudang otomatis berkurang karena dibawa truk.<br>Jika truk <b>Tiba (Idle)</b>, muatan dibongkar sehingga kapasitas gudang terisi otomatis.</small>`
            }).then(() => location.reload());
        })
        .catch(err => {
            Swal.fire('Gagal', err.message, 'error');
        });
    }

    function relocateFleet(id, newHubId, selectElement) {
        Swal.fire({
            title: 'Konfirmasi Relokasi',
            text: 'Truk akan melakukan pejalanan pindah ke gudang ini. Sinkronisasi antar gudang akan terjadi. Lanjutkan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Pindahkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memindahkan Armada...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                fetch(`/api/fleet/${id}/relocate`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ new_hub_id: newHubId })
                })
                .then(async response => {
                    let res = await response.json();
                    if(!response.ok) throw new Error(res.message);
                    return res;
                })
                .then(res => {
                    Swal.fire('Relokasi Berhasil!', res.message, 'success').then(() => location.reload());
                })
                .catch(err => {
                    Swal.fire('Gagal Relokasi', err.message, 'error');
                    // Reset selected option back to what it was requires saving the old option, for now just reload
                    location.reload(); 
                });
            } else {
                // Return select to original visual state if cancelled (simple page reload is safest fallback)
                location.reload();
            }
        });
    }
</script>
</body>
</html>
