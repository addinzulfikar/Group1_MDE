<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Module 1 - Warehouse & Package Management System">
    <title>Module 1 - Warehouse & Package Monitoring</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios@1.6.2/dist/axios.min.js"></script>

    <style>
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: bold; font-size: 1.5rem; }
        .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
        .card-icon { font-size: 2rem; opacity: 0.7; }
        .progress { height: 25px; }
        .table-hover tbody tr:hover { background-color: #f5f5f5; }
        .modal-header { background-color: #007bff; color: white; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <i class="fas fa-warehouse me-2"></i>Module 1 Monitoring
            </a>
            <span class="text-muted ms-auto">Warehouse &amp; Package Management System</span>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container-fluid py-4">
        <!-- Error Message -->
        @if(isset($error))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ $error }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <!-- Total Warehouses -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted text-uppercase fw-semibold mb-2">Total Warehouses</h6>
                                <h2 class="text-primary fw-bold">{{ $total_warehouses }}</h2>
                                <small>
                                    <span class="text-success"><i class="fas fa-circle me-1" style="font-size:.6rem"></i>{{ $available_warehouses }} Available</span>
                                    &nbsp;
                                    <span class="text-warning"><i class="fas fa-circle me-1" style="font-size:.6rem"></i>{{ $full_warehouses }} Full</span>
                                    &nbsp;
                                    <span class="text-danger"><i class="fas fa-circle me-1" style="font-size:.6rem"></i>{{ $overload_warehouses }} Overload</span>
                                </small>
                            </div>
                            <i class="fas fa-warehouse card-icon text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Packages -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted text-uppercase fw-semibold mb-2">Total Packages</h6>
                                <h2 class="text-success fw-bold">{{ $total_packages }}</h2>
                                <small class="text-muted">
                                    <i class="fas fa-box me-1"></i>Registered
                                </small>
                            </div>
                            <i class="fas fa-boxes card-icon text-success"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Capacity -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted text-uppercase fw-semibold mb-2">Total Capacity</h6>
                                <h2 class="text-info fw-bold">{{ $total_capacity }}</h2>
                                <small class="text-muted">
                                    <i class="fas fa-arrow-up me-1"></i>Units
                                </small>
                            </div>
                            <i class="fas fa-layer-group card-icon text-info"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overall Usage -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted text-uppercase fw-semibold mb-2">Usage Rate</h6>
                                <h2 class="text-warning fw-bold">{{ $overall_usage_percentage }}%</h2>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $overall_usage_percentage }}%"></div>
                                </div>
                            </div>
                            <i class="fas fa-chart-pie card-icon text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dimension Categories -->
        @if(isset($packages_by_dimension))
            <div class="row mb-4">
                <!-- Small Packages -->
                <div class="col-md-4 mb-4">
                    <div class="card border-start border-5 border-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted fw-semibold mb-1">Small Packages</h6>
                                    <h3 class="text-primary fw-bold">{{ $packages_by_dimension['small'] ?? 0 }}</h3>
                                    <small class="text-muted">Volume &le; 1000 cm&sup3;</small>
                                </div>
                                <i class="fas fa-box card-icon text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Medium Packages -->
                <div class="col-md-4 mb-4">
                    <div class="card border-start border-5 border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted fw-semibold mb-1">Medium Packages</h6>
                                    <h3 class="text-success fw-bold">{{ $packages_by_dimension['medium'] ?? 0 }}</h3>
                                    <small class="text-muted">Volume 1000 – 5000 cm&sup3;</small>
                                </div>
                                <i class="fas fa-boxes card-icon text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Large Packages -->
                <div class="col-md-4 mb-4">
                    <div class="card border-start border-5 border-danger">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted fw-semibold mb-1">Large Packages</h6>
                                    <h3 class="text-danger fw-bold">{{ $packages_by_dimension['large'] ?? 0 }}</h3>
                                    <small class="text-muted">Volume &gt; 5000 cm&sup3;</small>
                                </div>
                                <i class="fas fa-cube card-icon text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Warehouses Section -->
        <div class="card mb-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Warehouse Management
                </h5>
                <button id="btn-add-warehouse" onclick="openWarehouseModal()" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-2"></i>Add Warehouse
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Capacity</th>
                            <th>Current Load</th>
                            <th>Usage %</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($warehouses as $warehouse)
                            <tr>
                                <td class="fw-semibold">{{ $warehouse['warehouse_name'] }}</td>
                                <td>{{ $warehouse['location'] }}</td>
                                <td>{{ number_format($warehouse['capacity'], 0) }}</td>
                                <td>{{ number_format($warehouse['current_load'], 0) }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @php
                                            $percentage = $warehouse['usage_percentage'];
                                            if ($percentage < 50) {
                                                $barColor = 'success';
                                            } elseif ($percentage < 80) {
                                                $barColor = 'warning';
                                            } else {
                                                $barColor = 'danger';
                                            }
                                        @endphp
                                        <div class="progress flex-grow-1" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $barColor }}" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="small fw-semibold">{{ $percentage }}%</span>
                                    </div>
                                </td>
                                <td>
                                    @if($warehouse['status'] === 'available')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Available
                                        </span>
                                    @elseif($warehouse['status'] === 'full')
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-exclamation-circle me-1"></i>Full
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle me-1"></i>Overload
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <button onclick="editWarehouse({{ $warehouse['id'] }})" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteWarehouse({{ $warehouse['id'] }})" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No warehouses found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Packages Section -->
        <div class="card">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Package Management
                </h5>
                <button id="btn-add-package" onclick="openPackageModal()" class="btn btn-success btn-sm">
                    <i class="fas fa-plus me-2"></i>Register Package
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tracking #</th>
                            <th>Sender</th>
                            <th>Receiver</th>
                            <th>Weight (kg)</th>
                            <th>Volume (cm&sup3;)</th>
                            <th>Category</th>
                            <th>Warehouse</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($packages as $package)
                            <tr>
                                <td class="fw-semibold">{{ $package['tracking_number'] }}</td>
                                <td>{{ $package['sender_name'] }}</td>
                                <td>{{ $package['receiver_name'] }}</td>
                                <td>{{ $package['weight'] }}</td>
                                <td>{{ number_format($package['volume'], 2) }}</td>
                                <td>
                                    @php
                                        $category = $package['dimension_category'];
                                        if ($category === 'small') {
                                            $badgeColor = 'info';
                                        } elseif ($category === 'medium') {
                                            $badgeColor = 'success';
                                        } else {
                                            $badgeColor = 'danger';
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $badgeColor }}">{{ ucfirst($category) }}</span>
                                </td>
                                <td>{{ $package['warehouse_name'] }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst($package['status']) }}</span>
                                </td>
                                <td>
                                    <button onclick="editPackage({{ $package['id'] }})" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deletePackage({{ $package['id'] }})" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No packages found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Warehouse Modal -->
    <div class="modal fade" id="warehouseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="warehouseModalTitle">Add Warehouse</h5>
                    <button type="button" class="btn-close btn-close-white" onclick="closeWarehouseModal()"></button>
                </div>
                <form id="warehouseForm" onsubmit="saveWarehouse(event)">
                    <div class="modal-body">
                        <input type="hidden" id="warehouseId">
                        <div class="mb-3">
                            <label for="warehouse_name" class="form-label">Warehouse Name</label>
                            <input type="text" class="form-control" id="warehouse_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="warehouse_location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="warehouse_location" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="warehouse_capacity" class="form-label">Capacity (Unit)</label>
                                    <input type="number" class="form-control" id="warehouse_capacity" required min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="warehouse_current_load" class="form-label">Current Load</label>
                                    <input type="text" class="form-control" id="warehouse_current_load" readonly placeholder="Auto-calculated">
                                    <div class="form-text"><i class="fas fa-info-circle me-1"></i>Calculated from package count</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="warehouse_usage" class="form-label">Usage %</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="warehouse_usage" readonly>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="warehouse_status" class="form-label">Status</label>
                                    <select class="form-select" id="warehouse_status">
                                        <option value="available">Available</option>
                                        <option value="full">Full</option>
                                        <option value="overload">Overload</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeWarehouseModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="warehouseSubmitBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Package Modal -->
    <div class="modal fade" id="packageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="packageModalTitle">Register Package</h5>
                    <button type="button" class="btn-close btn-close-white" onclick="closePackageModal()"></button>
                </div>
                <form id="packageForm" onsubmit="savePackage(event)">
                    <div class="modal-body">
                        <input type="hidden" id="packageId">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tracking_number" class="form-label">Tracking Number</label>
                                <input type="text" class="form-control" id="tracking_number" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="warehouse_id" class="form-label">Warehouse</label>
                                <select class="form-select" id="warehouse_id" required>
                                    <option value="">-- Select Warehouse --</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse['id'] }}">{{ $warehouse['warehouse_name'] }} ({{ ucfirst($warehouse['status']) }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sender_name" class="form-label">Sender Name</label>
                                <input type="text" class="form-control" id="sender_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="receiver_name" class="form-label">Receiver Name</label>
                                <input type="text" class="form-control" id="receiver_name" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="origin" class="form-label">Origin</label>
                                <input type="text" class="form-control" id="origin" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="destination" class="form-label">Destination</label>
                                <input type="text" class="form-control" id="destination" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="weight" class="form-label">Weight (kg)</label>
                                <input type="number" class="form-control" id="weight" step="0.01" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="package_status" class="form-label">Status</label>
                                <select class="form-select" id="package_status">
                                    <option value="registered">Registered</option>
                                    <option value="shipped">Shipped</option>
                                    <option value="delivered">Delivered</option>
                                </select>
                            </div>
                        </div>
                        <h6 class="border-top pt-3 mt-3 mb-3">Dimensions</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="length" class="form-label">Length (cm)</label>
                                <input type="number" class="form-control" id="length" step="0.01" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="width" class="form-label">Width (cm)</label>
                                <input type="number" class="form-control" id="width" step="0.01" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="height" class="form-label">Height (cm)</label>
                                <input type="number" class="form-control" id="height" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closePackageModal()">Cancel</button>
                        <button type="submit" class="btn btn-success" id="packageSubmitBtn">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const API_URL = '/api';
        const warehouseModal = new bootstrap.Modal(document.getElementById('warehouseModal'));
        const packageModal   = new bootstrap.Modal(document.getElementById('packageModal'));

        /* ─── Warehouse Modal ─── */
        function openWarehouseModal() {
            document.getElementById('warehouseId').value = '';
            document.getElementById('warehouseForm').reset();
            document.getElementById('warehouse_usage').value = '';
            document.getElementById('warehouse_current_load').value = '';
            document.getElementById('warehouseModalTitle').textContent = 'Add Warehouse';
            document.getElementById('warehouseSubmitBtn').textContent  = 'Save';
            warehouseModal.show();
        }

        function closeWarehouseModal() { warehouseModal.hide(); }

        function editWarehouse(id) {
            if (!id || id <= 0) { alert('Error: Invalid warehouse ID'); return; }

            axios.get(`${API_URL}/warehouse/${id}`)
                .then(response => {
                    if (!response.data?.success) { alert('Error: Server response indicates failure'); return; }
                    const d = response.data.data;
                    document.getElementById('warehouseId').value          = d.id || id;
                    document.getElementById('warehouse_name').value        = d.warehouse_name || '';
                    document.getElementById('warehouse_location').value    = d.location || '';
                    document.getElementById('warehouse_capacity').value    = d.capacity || '';
                    document.getElementById('warehouse_current_load').value = d.current_load ?? '–';
                    document.getElementById('warehouse_usage').value       = d.usage_percentage != null ? d.usage_percentage : '';
                    document.getElementById('warehouse_status').value      = d.status || 'available';
                    document.getElementById('warehouseModalTitle').textContent = 'Edit Warehouse';
                    document.getElementById('warehouseSubmitBtn').textContent  = 'Update';
                    warehouseModal.show();
                })
                .catch(err => alert('Error: ' + (err.response?.data?.message || 'Failed to load warehouse')));
        }

        function saveWarehouse(e) {
            e.preventDefault();
            const id   = document.getElementById('warehouseId').value;
            const name = document.getElementById('warehouse_name').value.trim();
            const loc  = document.getElementById('warehouse_location').value.trim();
            const cap  = parseInt(document.getElementById('warehouse_capacity').value);
            const stat = document.getElementById('warehouse_status').value;

            if (!name || !loc || !cap || cap < 1) {
                alert('Please fill all required fields correctly.');
                return;
            }

            const data   = { warehouse_name: name, location: loc, capacity: cap, status: stat };
            const method = id ? 'put' : 'post';
            const url    = id ? `${API_URL}/warehouse/${id}` : `${API_URL}/warehouse`;

            const btn = document.getElementById('warehouseSubmitBtn');
            btn.disabled = true;
            btn.textContent = 'Saving…';

            axios[method](url, data)
                .then(res => {
                    if (res.data?.success) {
                        alert('Warehouse saved successfully!');
                        window.location.reload();
                    } else {
                        alert('Error: ' + (res.data?.message || 'Unknown error'));
                    }
                })
                .catch(err => alert('Error: ' + (err.response?.data?.message || err.message || 'Failed to save warehouse')))
                .finally(() => { btn.disabled = false; btn.textContent = id ? 'Update' : 'Save'; });
        }

        function deleteWarehouse(id) {
            if (confirm('Are you sure you want to delete this warehouse?')) {
                axios.delete(`${API_URL}/warehouse/${id}`)
                    .then(res => { if (res.data?.success) { alert('Warehouse deleted successfully!'); window.location.reload(); } })
                    .catch(err => alert('Error: ' + (err.response?.data?.error || 'Failed to delete warehouse')));
            }
        }

        /* ─── Package Modal ─── */
        function openPackageModal() {
            document.getElementById('packageId').value = '';
            document.getElementById('packageForm').reset();
            document.getElementById('packageModalTitle').textContent = 'Register Package';
            document.getElementById('packageSubmitBtn').textContent  = 'Register';
            packageModal.show();
        }

        function closePackageModal() { packageModal.hide(); }

        function editPackage(id) {
            if (!id || id <= 0) { alert('Error: Invalid package ID'); return; }

            axios.get(`${API_URL}/package/${id}`)
                .then(res => {
                    if (!res.data?.success) { alert('Error: Server response indicates failure'); return; }
                    const d = res.data.data;
                    document.getElementById('packageId').value       = d.id || id;
                    document.getElementById('tracking_number').value = d.tracking_number || '';
                    document.getElementById('sender_name').value     = d.sender_name || '';
                    document.getElementById('receiver_name').value   = d.receiver_name || '';
                    document.getElementById('origin').value          = d.origin || '';
                    document.getElementById('destination').value     = d.destination || '';
                    document.getElementById('weight').value          = d.weight || '';
                    document.getElementById('length').value          = d.length || '';
                    document.getElementById('width').value           = d.width || '';
                    document.getElementById('height').value          = d.height || '';
                    document.getElementById('warehouse_id').value    = d.warehouse_id || '';
                    document.getElementById('package_status').value  = d.package_status || 'registered';
                    document.getElementById('packageModalTitle').textContent = 'Edit Package';
                    document.getElementById('packageSubmitBtn').textContent  = 'Update';
                    packageModal.show();
                })
                .catch(err => alert('Error: ' + (err.response?.data?.message || 'Failed to load package')));
        }

        function savePackage(e) {
            e.preventDefault();
            const id = document.getElementById('packageId').value;
            const data = {
                tracking_number: document.getElementById('tracking_number').value.trim(),
                sender_name:     document.getElementById('sender_name').value.trim(),
                receiver_name:   document.getElementById('receiver_name').value.trim(),
                origin:          document.getElementById('origin').value.trim(),
                destination:     document.getElementById('destination').value.trim(),
                weight:          parseFloat(document.getElementById('weight').value),
                length:          parseFloat(document.getElementById('length').value),
                width:           parseFloat(document.getElementById('width').value),
                height:          parseFloat(document.getElementById('height').value),
                warehouse_id:    parseInt(document.getElementById('warehouse_id').value),
                package_status:  document.getElementById('package_status').value,
            };

            if (!data.tracking_number || !data.sender_name || !data.receiver_name || !data.origin || !data.destination) {
                alert('Please fill all required fields'); return;
            }
            if (data.weight <= 0 || data.length <= 0 || data.width <= 0 || data.height <= 0) {
                alert('All numeric values must be greater than 0'); return;
            }
            if (!data.warehouse_id) { alert('Please select a warehouse'); return; }

            const btn    = document.getElementById('packageSubmitBtn');
            const method = id ? 'put' : 'post';
            const url    = id ? `${API_URL}/package/${id}` : `${API_URL}/package/register`;

            btn.disabled = true;
            btn.textContent = 'Saving…';

            axios[method](url, data)
                .then(res => {
                    if (res.data?.success) {
                        alert('Package saved successfully!');
                        window.location.reload();
                    } else {
                        alert('Error: ' + (res.data?.message || 'Unknown error'));
                    }
                })
                .catch(err => alert('Error: ' + (err.response?.data?.message || err.message || 'Failed to save package')))
                .finally(() => { btn.disabled = false; btn.textContent = id ? 'Update' : 'Register'; });
        }

        function deletePackage(id) {
            if (confirm('Are you sure you want to delete this package?')) {
                axios.delete(`${API_URL}/package/${id}`)
                    .then(res => { if (res.data?.success) { alert('Package deleted successfully!'); window.location.reload(); } })
                    .catch(err => alert('Error: ' + (err.response?.data?.error || 'Failed to delete package')));
            }
        }
    </script>
</body>
</html>
