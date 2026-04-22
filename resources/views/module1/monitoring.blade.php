@extends('layouts.app')

@section('title', 'Modul 1 - Warehouse & Package Monitoring')
@section('meta_description', 'Monitoring kapasitas warehouse dan manajemen paket masuk/keluar.')
@section('active_nav', 'module1')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: bold; font-size: 1.5rem; }
        .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
        .card-icon { font-size: 2rem; opacity: 0.7; }
        .progress { height: 25px; }
        .table-hover tbody tr:hover { background-color: #f5f5f5; }
        .modal-header { background-color: #007bff; color: white; }
    </style>
@endpush

@section('content')
    <section class="container-fluid py-4">
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
                                <small class="text-success">
                                    <i class="fas fa-check-circle me-1"></i>{{ $active_warehouses }} Active
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
        @if(count($packages_by_dimension) > 0)
            <div class="row mb-4">
                <!-- Small Packages -->
                <div class="col-md-4 mb-4">
                    <div class="card border-start border-5 border-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted fw-semibold mb-1">Small Packages</h6>
                                    <h3 class="text-primary fw-bold">{{ $packages_by_dimension['small'] ?? 0 }}</h3>
                                    <small class="text-muted">Volume ≤ 1000 cm³</small>
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
                                    <small class="text-muted">Volume 1000 - 5000 cm³</small>
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
                                    <small class="text-muted">Volume > 5000 cm³</small>
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
                <button onclick="openWarehouseModal()" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-2"></i>Add Warehouse
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Linked Hub</th>
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
                                <td class="fw-semibold">{{ $warehouse['warehouse_code'] }}</td>
                                <td>{{ $warehouse['warehouse_name'] }}</td>
                                <td>{{ $warehouse['location'] }}</td>
                                <td>{{ $warehouse['hub_name'] ?? '-' }}</td>
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
                                    @if($warehouse['status'] === 'active')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Active
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-times-circle me-1"></i>Inactive
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
                                <td colspan="9" class="text-center text-muted py-4">No warehouses found</td>
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
                <button onclick="openPackageModal()" class="btn btn-success btn-sm">
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
                            <th>Volume (cm³)</th>
                            <th>Category</th>
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
                                <td colspan="8" class="text-center text-muted py-4">No packages found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

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
                            <label for="warehouse_code" class="form-label">Warehouse Code</label>
                            <input type="text" class="form-control" id="warehouse_code" required>
                        </div>
                        <div class="mb-3">
                            <label for="warehouse_name" class="form-label">Warehouse Name</label>
                            <input type="text" class="form-control" id="warehouse_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="warehouse_location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="warehouse_location" required>
                        </div>
                        <div class="mb-3">
                            <label for="warehouse_hub_id" class="form-label">Linked Hub</label>
                            <select class="form-select" id="warehouse_hub_id">
                                <option value="">- No Hub -</option>
                                @foreach($all_hubs as $hub)
                                    <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="warehouse_capacity" class="form-label">Capacity (Unit)</label>
                                    <input type="number" class="form-control" id="warehouse_capacity" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="warehouse_current_load" class="form-label">Current Load (Unit)</label>
                                    <input type="number" class="form-control" id="warehouse_current_load" required min="0">
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
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
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
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse['id'] }}">{{ $warehouse['warehouse_code'] }} - {{ $warehouse['warehouse_name'] }}</option>
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

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios@1.6.2/dist/axios.min.js"></script>
    <script>
        const API_URL = '/api/v1';
        const warehouseModal = new bootstrap.Modal(document.getElementById('warehouseModal'));
        const packageModal = new bootstrap.Modal(document.getElementById('packageModal'));

        // Warehouse Modal Functions
        function openWarehouseModal() {
            document.getElementById('warehouseId').value = '';
            document.getElementById('warehouseForm').reset();
            document.getElementById('warehouse_usage').value = 0;
            document.getElementById('warehouseModalTitle').textContent = 'Add Warehouse';
            document.getElementById('warehouseSubmitBtn').textContent = 'Save';
            warehouseModal.show();
        }

        function calculateUsagePercentage() {
            const capacity = parseFloat(document.getElementById('warehouse_capacity').value) || 0;
            const current_load = parseFloat(document.getElementById('warehouse_current_load').value) || 0;
            const usagePercentage = capacity > 0 ? ((current_load / capacity) * 100).toFixed(2) : 0;
            document.getElementById('warehouse_usage').value = usagePercentage;
        }

        // Add event listeners to update usage percentage
        document.getElementById('warehouse_capacity').addEventListener('input', calculateUsagePercentage);
        document.getElementById('warehouse_current_load').addEventListener('input', calculateUsagePercentage);

        function closeWarehouseModal() {
            warehouseModal.hide();
        }

        function editWarehouse(id) {
            if (!id || id <= 0) {
                alert('Error: Invalid warehouse ID');
                console.error('Invalid warehouse ID:', id);
                return;
            }
            
            axios.get(`${API_URL}/warehouse/${id}`)
                .then(response => {
                    console.log('Warehouse API Response:', response.data);
                    
                    if (!response.data || !response.data.success) {
                        alert('Error: Server response indicates failure');
                        console.error('Failed response:', response.data);
                        return;
                    }
                    
                    const data = response.data.data;
                    
                    if (!data || typeof data !== 'object') {
                        alert('Error: Invalid data format from server');
                        console.error('Invalid data structure:', data);
                        return;
                    }
                    
                    try {
                        document.getElementById('warehouseId').value = data.id || id;
                        document.getElementById('warehouse_code').value = data.warehouse_code || '';
                        document.getElementById('warehouse_name').value = data.warehouse_name || '';
                        document.getElementById('warehouse_location').value = data.location || '';
                        document.getElementById('warehouse_hub_id').value = data.hub_id || '';
                        document.getElementById('warehouse_capacity').value = data.capacity || '';
                        document.getElementById('warehouse_current_load').value = data.current_load || 0;
                        document.getElementById('warehouse_usage').value = data.usage_percentage || 0;
                        document.getElementById('warehouse_status').value = data.status || 'active';
                        
                        document.getElementById('warehouseModalTitle').textContent = 'Edit Warehouse';
                        document.getElementById('warehouseSubmitBtn').textContent = 'Update';
                        warehouseModal.show();
                    } catch (e) {
                        alert('Error populating form: ' + e.message);
                        console.error('Form population error:', e);
                    }
                })
                .catch(error => {
                    console.error('Axios error:', error);
                    let errorMsg = 'Failed to load warehouse';
                    
                    if (error.response) {
                        errorMsg = error.response.data?.message || error.response.statusText || errorMsg;
                    } else if (error.request) {
                        errorMsg = 'No response from server';
                    }
                    
                    alert('Error: ' + errorMsg);
                });
        }

        function saveWarehouse(e) {
            e.preventDefault();
            
            const id = document.getElementById('warehouseId').value;
            const warehouse_code = document.getElementById('warehouse_code').value;
            const warehouse_name = document.getElementById('warehouse_name').value;
            const warehouseLocation = document.getElementById('warehouse_location').value;
            const hub_id = document.getElementById('warehouse_hub_id').value;
            const capacity = document.getElementById('warehouse_capacity').value;
            const current_load = document.getElementById('warehouse_current_load').value;
            const status = document.getElementById('warehouse_status').value;
            
            if (!warehouse_code.trim() || !warehouse_name.trim() || !warehouseLocation.trim() || !capacity || capacity <= 0 || current_load === '' || current_load < 0) {
                alert('Please fill all required fields correctly. Current Load cannot be negative.');
                return;
            }
            
            if (parseInt(current_load) > parseInt(capacity)) {
                alert('Current Load cannot exceed Capacity');
                return;
            }
            
            const data = {
                warehouse_code: warehouse_code.trim(),
                warehouse_name: warehouse_name.trim(),
                location: warehouseLocation.trim(),
                hub_id: hub_id ? parseInt(hub_id) : null,
                capacity: parseInt(capacity),
                current_load: parseInt(current_load),
                status: status
            };

            const method = id ? 'put' : 'post';
            const url = id ? `${API_URL}/warehouse/${id}` : `${API_URL}/warehouse`;

            axios[method](url, data)
                .then(response => {
                    if (response.data?.success) {
                        alert('Warehouse saved successfully!');
                        window.location.reload();
                    } else {
                        alert('Error: ' + (response.data?.message || 'Unknown error occurred'));
                    }
                })
                .catch(error => {
                    console.error('Error saving warehouse:', error);
                    const errorMsg = error.response?.data?.message || error.message || 'Failed to save warehouse';
                    alert('Error: ' + errorMsg);
                });
        }

        function deleteWarehouse(id) {
            if (confirm('Are you sure you want to delete this warehouse?')) {
                axios.delete(`${API_URL}/warehouse/${id}`)
                    .then(response => {
                        if (response.data?.success) {
                            alert('Warehouse deleted successfully!');
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        alert('Error: ' + (error.response?.data?.error || 'Failed to delete warehouse'));
                    });
            }
        }

        // Package Modal Functions
        function openPackageModal() {
            document.getElementById('packageId').value = '';
            document.getElementById('packageForm').reset();
            document.getElementById('packageModalTitle').textContent = 'Register Package';
            document.getElementById('packageSubmitBtn').textContent = 'Register';
            packageModal.show();
        }

        function closePackageModal() {
            packageModal.hide();
        }

        function editPackage(id) {
            if (!id || id <= 0) {
                alert('Error: Invalid package ID');
                return;
            }
            
            axios.get(`${API_URL}/package/${id}`)
                .then(response => {
                    console.log('Package API Response:', response.data);
                    
                    if (!response.data || !response.data.success) {
                        alert('Error: Server response indicates failure');
                        return;
                    }
                    
                    const data = response.data.data;
                    
                    if (!data || typeof data !== 'object') {
                        alert('Error: Invalid data format from server');
                        return;
                    }
                    
                    try {
                        document.getElementById('packageId').value = data.id || id;
                        document.getElementById('tracking_number').value = data.tracking_number || '';
                        document.getElementById('sender_name').value = data.sender_name || '';
                        document.getElementById('receiver_name').value = data.receiver_name || '';
                        document.getElementById('origin').value = data.origin || '';
                        document.getElementById('destination').value = data.destination || '';
                        document.getElementById('weight').value = data.weight || '';
                        document.getElementById('length').value = data.length || '';
                        document.getElementById('width').value = data.width || '';
                        document.getElementById('height').value = data.height || '';
                        document.getElementById('warehouse_id').value = data.warehouse_id || '';
                        document.getElementById('package_status').value = data.package_status || 'registered';
                        
                        document.getElementById('packageModalTitle').textContent = 'Edit Package';
                        document.getElementById('packageSubmitBtn').textContent = 'Update';
                        packageModal.show();
                    } catch (e) {
                        alert('Error populating form: ' + e.message);
                    }
                })
                .catch(error => {
                    console.error('Axios error:', error);
                    let errorMsg = 'Failed to load package';
                    
                    if (error.response) {
                        errorMsg = error.response.data?.message || error.response.statusText || errorMsg;
                    }
                    
                    alert('Error: ' + errorMsg);
                });
        }

        function savePackage(e) {
            e.preventDefault();
            
            const id = document.getElementById('packageId').value;
            const data = {
                tracking_number: document.getElementById('tracking_number').value.trim(),
                sender_name: document.getElementById('sender_name').value.trim(),
                receiver_name: document.getElementById('receiver_name').value.trim(),
                origin: document.getElementById('origin').value.trim(),
                destination: document.getElementById('destination').value.trim(),
                weight: parseFloat(document.getElementById('weight').value),
                length: parseFloat(document.getElementById('length').value),
                width: parseFloat(document.getElementById('width').value),
                height: parseFloat(document.getElementById('height').value),
                warehouse_id: parseInt(document.getElementById('warehouse_id').value),
                package_status: document.getElementById('package_status').value
            };

            if (!data.tracking_number || !data.sender_name || !data.receiver_name || !data.origin || !data.destination) {
                alert('Please fill all required fields');
                return;
            }
            if (data.weight <= 0 || data.length <= 0 || data.width <= 0 || data.height <= 0) {
                alert('All numeric values must be greater than 0');
                return;
            }

            const method = id ? 'put' : 'post';
            const url = id ? `${API_URL}/package/${id}` : `${API_URL}/package/register`;

            axios[method](url, data)
                .then(response => {
                    if (response.data?.success) {
                        alert('Package saved successfully!');
                        window.location.reload();
                    } else {
                        alert('Error: ' + (response.data?.message || 'Unknown error occurred'));
                    }
                })
                .catch(error => {
                    console.error('Error saving package:', error);
                    const errorMsg = error.response?.data?.message || error.message || 'Failed to save package';
                    alert('Error: ' + errorMsg);
                });
        }

        function deletePackage(id) {
            if (confirm('Are you sure you want to delete this package?')) {
                axios.delete(`${API_URL}/package/${id}`)
                    .then(response => {
                        if (response.data?.success) {
                            alert('Package deleted successfully!');
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        alert('Error: ' + (error.response?.data?.error || 'Failed to delete package'));
                    });
            }
        }
    </script>
    @endpush
@endsection
