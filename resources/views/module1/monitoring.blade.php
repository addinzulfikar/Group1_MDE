<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Module 1 - Warehouse & Package Monitoring</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios@1.6.2/dist/axios.min.js"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Navbar -->
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-blue-600">
                            <i class="fas fa-warehouse mr-2"></i>Module 1 Monitoring
                        </h1>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm text-gray-600">Warehouse & Package Management System</span>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            @if(isset($error))
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ $error }}
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Warehouses -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide">Total Warehouses</p>
                            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $total_warehouses }}</p>
                            <p class="text-xs text-green-600 mt-2">
                                <i class="fas fa-check-circle mr-1"></i>{{ $active_warehouses }} Active
                            </p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3">
                            <i class="fas fa-warehouse text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Packages -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide">Total Packages</p>
                            <p class="text-3xl font-bold text-green-600 mt-2">{{ $total_packages }}</p>
                            <p class="text-xs text-gray-600 mt-2">
                                <i class="fas fa-box mr-1"></i>Registered
                            </p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <i class="fas fa-boxes text-green-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Capacity -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide">Total Capacity</p>
                            <p class="text-3xl font-bold text-purple-600 mt-2">{{ $total_capacity }}</p>
                            <p class="text-xs text-gray-600 mt-2">
                                <i class="fas fa-arrow-up mr-1"></i>Units
                            </p>
                        </div>
                        <div class="bg-purple-100 rounded-full p-3">
                            <i class="fas fa-layer-group text-purple-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Overall Usage -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide">Usage Rate</p>
                            <p class="text-3xl font-bold text-orange-600 mt-2">{{ $overall_usage_percentage }}%</p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div class="bg-orange-600 h-2 rounded-full" style="width: {{ $overall_usage_percentage }}%"></div>
                            </div>
                        </div>
                        <div class="bg-orange-100 rounded-full p-3">
                            <i class="fas fa-chart-pie text-orange-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dimension Categories -->
            @if(count($packages_by_dimension) > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Small Packages -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-semibold">Small Packages</p>
                                <p class="text-2xl font-bold text-blue-600 mt-1">
                                    {{ $packages_by_dimension['small'] ?? 0 }}
                                </p>
                                <p class="text-xs text-gray-600 mt-2">Volume ≤ 1000 cm³</p>
                            </div>
                            <i class="fas fa-box text-blue-300 text-3xl"></i>
                        </div>
                    </div>

                    <!-- Medium Packages -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-semibold">Medium Packages</p>
                                <p class="text-2xl font-bold text-green-600 mt-1">
                                    {{ $packages_by_dimension['medium'] ?? 0 }}
                                </p>
                                <p class="text-xs text-gray-600 mt-2">Volume 1000 - 5000 cm³</p>
                            </div>
                            <i class="fas fa-boxes text-green-300 text-3xl"></i>
                        </div>
                    </div>

                    <!-- Large Packages -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-semibold">Large Packages</p>
                                <p class="text-2xl font-bold text-red-600 mt-1">
                                    {{ $packages_by_dimension['large'] ?? 0 }}
                                </p>
                                <p class="text-xs text-gray-600 mt-2">Volume > 5000 cm³</p>
                            </div>
                            <i class="fas fa-cube text-red-300 text-3xl"></i>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Warehouses Section -->
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-list mr-2"></i>Warehouse Management
                    </h2>
                    <button onclick="openWarehouseModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-plus mr-2"></i>Add Warehouse
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Capacity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Current Load</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Usage %</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($warehouses as $warehouse)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ $warehouse['warehouse_code'] }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $warehouse['warehouse_name'] }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $warehouse['location'] }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ number_format($warehouse['capacity'], 0) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ number_format($warehouse['current_load'], 0) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                @php
                                                    $percentage = $warehouse['usage_percentage'];
                                                    if ($percentage < 50) {
                                                        $color = 'bg-green-600';
                                                    } elseif ($percentage < 80) {
                                                        $color = 'bg-yellow-600';
                                                    } else {
                                                        $color = 'bg-red-600';
                                                    }
                                                @endphp
                                                <div class="{{ $color }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <span class="text-xs font-semibold">{{ $warehouse['usage_percentage'] }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        @if($warehouse['status'] === 'active')
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>Active
                                            </span>
                                        @else
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                <i class="fas fa-times-circle mr-1"></i>Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm space-x-2">
                                        <button onclick="editWarehouse({{ $warehouse['id'] }})" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition text-xs">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </button>
                                        <button onclick="deleteWarehouse({{ $warehouse['id'] }})" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition text-xs">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-600">
                                        No warehouses found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Packages Section -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-list mr-2"></i>Package Management
                    </h2>
                    <button onclick="openPackageModal()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                        <i class="fas fa-plus mr-2"></i>Register Package
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Tracking #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Sender</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Receiver</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Weight (kg)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Volume (cm³)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($packages as $package)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ $package['tracking_number'] }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $package['sender_name'] }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $package['receiver_name'] }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $package['weight'] }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ number_format($package['volume'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        @php
                                            $category = $package['dimension_category'];
                                            if ($category === 'small') {
                                                $badgeColor = 'bg-blue-100 text-blue-800';
                                            } elseif ($category === 'medium') {
                                                $badgeColor = 'bg-green-100 text-green-800';
                                            } else {
                                                $badgeColor = 'bg-red-100 text-red-800';
                                            }
                                        @endphp
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeColor }}">
                                            <i class="fas fa-tag mr-1"></i>{{ ucfirst($category) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        @php
                                            $status = $package['status'];
                                            if ($status === 'delivered') {
                                                $statusColor = 'bg-green-100 text-green-800';
                                            } elseif ($status === 'in_transit') {
                                                $statusColor = 'bg-blue-100 text-blue-800';
                                            } elseif ($status === 'pending') {
                                                $statusColor = 'bg-yellow-100 text-yellow-800';
                                            } else {
                                                $statusColor = 'bg-gray-100 text-gray-800';
                                            }
                                        @endphp
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm space-x-2">
                                        <button onclick="editPackage({{ $package['id'] }})" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition text-xs">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </button>
                                        <button onclick="deletePackage({{ $package['id'] }})" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition text-xs">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-600">
                                        No packages found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer Info -->
            <div class="mt-8 text-center text-sm text-gray-600">
                <p>Module 1: Warehouse & Package Management System</p>
                <p>Last updated: {{ date('Y-m-d H:i:s') }}</p>
            </div>
        </main>
    </div>

    <!-- Warehouse Modal -->
    <div id="warehouseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900" id="warehouseModalTitle">Add Warehouse</h3>
                <button onclick="closeWarehouseModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="warehouseForm" onsubmit="saveWarehouse(event)" class="px-6 py-4 space-y-4">
                <input type="hidden" id="warehouseId">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Warehouse Code</label>
                    <input type="text" id="warehouse_code" required class="w-full px-3 py-2 border border-gray-300 rounded mt-1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Warehouse Name</label>
                    <input type="text" id="warehouse_name" required class="w-full px-3 py-2 border border-gray-300 rounded mt-1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Location</label>
                    <input type="text" id="warehouse_location" required class="w-full px-3 py-2 border border-gray-300 rounded mt-1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Capacity</label>
                    <input type="number" id="warehouse_capacity" required class="w-full px-3 py-2 border border-gray-300 rounded mt-1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="warehouse_status" class="w-full px-3 py-2 border border-gray-300 rounded mt-1">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeWarehouseModal()" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" id="warehouseSubmitBtn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Package Modal -->
    <div id="packageModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4 my-8">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900" id="packageModalTitle">Register Package</h3>
                <button onclick="closePackageModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="packageForm" onsubmit="savePackage(event)" class="px-6 py-4 space-y-3">
                <input type="hidden" id="packageId">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tracking Number</label>
                    <input type="text" id="tracking_number" required class="w-full px-3 py-2 border border-gray-300 rounded mt-1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Sender Name</label>
                    <input type="text" id="sender_name" required class="w-full px-3 py-2 border border-gray-300 rounded mt-1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Receiver Name</label>
                    <input type="text" id="receiver_name" required class="w-full px-3 py-2 border border-gray-300 rounded mt-1">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Origin</label>
                        <input type="text" id="origin" required class="w-full px-3 py-2 border border-gray-300 rounded mt-1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Destination</label>
                        <input type="text" id="destination" required class="w-full px-3 py-2 border border-gray-300 rounded mt-1">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Weight (kg)</label>
                    <input type="number" id="weight" step="0.1" required class="w-full px-3 py-2 border border-gray-300 rounded mt-1">
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">L (cm)</label>
                        <input type="number" id="length" step="0.1" required class="w-full px-3 py-2 border border-gray-300 rounded mt-1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">W (cm)</label>
                        <input type="number" id="width" step="0.1" required class="w-full px-3 py-2 border border-gray-300 rounded mt-1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">H (cm)</label>
                        <input type="number" id="height" step="0.1" required class="w-full px-3 py-2 border border-gray-300 rounded mt-1">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Warehouse</label>
                    <select id="warehouse_id" required class="w-full px-3 py-2 border border-gray-300 rounded mt-1">
                        <option value="">Select Warehouse</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse['id'] }}">{{ $warehouse['warehouse_code'] }} - {{ $warehouse['warehouse_name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="package_status" class="w-full px-3 py-2 border border-gray-300 rounded mt-1">
                        <option value="registered">Registered</option>
                        <option value="in_transit">In Transit</option>
                        <option value="delivered">Delivered</option>
                        <option value="pending">Pending</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closePackageModal()" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" id="packageSubmitBtn" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const API_URL = '/api';

        // Warehouse Modal Functions
        function openWarehouseModal() {
            document.getElementById('warehouseId').value = '';
            document.getElementById('warehouseForm').reset();
            document.getElementById('warehouseModalTitle').textContent = 'Add Warehouse';
            document.getElementById('warehouseSubmitBtn').textContent = 'Save';
            document.getElementById('warehouseModal').classList.remove('hidden');
        }

        function closeWarehouseModal() {
            document.getElementById('warehouseModal').classList.add('hidden');
        }

        function editWarehouse(id) {
            // Validate ID
            if (!id || id <= 0) {
                alert('Error: Invalid warehouse ID');
                console.error('Invalid warehouse ID:', id);
                return;
            }
            
            axios.get(`${API_URL}/warehouse/${id}`)
                .then(response => {
                    console.log('Warehouse API Response:', response.data);
                    
                    // Check if response has success flag
                    if (!response.data || !response.data.success) {
                        alert('Error: Server response indicates failure');
                        console.error('Failed response:', response.data);
                        return;
                    }
                    
                    // Get data
                    const data = response.data.data;
                    
                    if (!data || typeof data !== 'object') {
                        alert('Error: Invalid data format from server. Got: ' + typeof data);
                        console.error('Invalid data structure:', data);
                        return;
                    }
                    
                    // Safely assign values
                    try {
                        const warehouseIdField = document.getElementById('warehouseId');
                        const codeField = document.getElementById('warehouse_code');
                        const nameField = document.getElementById('warehouse_name');
                        const locationField = document.getElementById('warehouse_location');
                        const capacityField = document.getElementById('warehouse_capacity');
                        const statusField = document.getElementById('warehouse_status');
                        
                        if (!warehouseIdField || !codeField || !nameField || !locationField || !capacityField || !statusField) {
                            alert('Error: Some form fields not found');
                            console.error('Missing form fields');
                            return;
                        }
                        
                        warehouseIdField.value = data.id || id;
                        codeField.value = data.warehouse_code || '';
                        nameField.value = data.warehouse_name || '';
                        locationField.value = data.location || '';
                        capacityField.value = data.capacity || '';
                        statusField.value = data.status || 'active';
                        
                        document.getElementById('warehouseModalTitle').textContent = 'Edit Warehouse';
                        document.getElementById('warehouseSubmitBtn').textContent = 'Update';
                        document.getElementById('warehouseModal').classList.remove('hidden');
                        
                        console.log('Form populated successfully');
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
                        console.error('Response status:', error.response.status);
                        console.error('Response data:', error.response.data);
                    } else if (error.request) {
                        errorMsg = 'No response from server';
                        console.error('No response:', error.request);
                    } else {
                        errorMsg = error.message;
                    }
                    
                    alert('Error: ' + errorMsg);
                });
        }

        function saveWarehouse(e) {
            e.preventDefault();
            
            const id = document.getElementById('warehouseId').value;
            const warehouse_code = document.getElementById('warehouse_code').value;
            const warehouse_name = document.getElementById('warehouse_name').value;
            const location = document.getElementById('warehouse_location').value;
            const capacity = document.getElementById('warehouse_capacity').value;
            const status = document.getElementById('warehouse_status').value;
            
            // Validation
            if (!warehouse_code.trim()) {
                alert('Warehouse code is required');
                return;
            }
            if (!warehouse_name.trim()) {
                alert('Warehouse name is required');
                return;
            }
            if (!location.trim()) {
                alert('Location is required');
                return;
            }
            if (!capacity || capacity <= 0) {
                alert('Capacity must be greater than 0');
                return;
            }
            
            const data = {
                warehouse_code: warehouse_code.trim(),
                warehouse_name: warehouse_name.trim(),
                location: location.trim(),
                capacity: parseInt(capacity),
                status: status
            };

            const method = id ? 'put' : 'post';
            const url = id ? `${API_URL}/warehouse/${id}` : `${API_URL}/warehouse`;

            axios[method](url, data)
                .then(response => {
                    if (response.data?.success) {
                        alert('Warehouse saved successfully!');
                        location.reload();
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
                        alert('Warehouse deleted successfully!');
                        location.reload();
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
            document.getElementById('packageModal').classList.remove('hidden');
        }

        function closePackageModal() {
            document.getElementById('packageModal').classList.add('hidden');
        }

        function editPackage(id) {
            // Validate ID
            if (!id || id <= 0) {
                alert('Error: Invalid package ID');
                console.error('Invalid package ID:', id);
                return;
            }
            
            axios.get(`${API_URL}/package/${id}`)
                .then(response => {
                    console.log('Package API Response:', response.data);
                    
                    // Check if response has success flag
                    if (!response.data || !response.data.success) {
                        alert('Error: Server response indicates failure');
                        console.error('Failed response:', response.data);
                        return;
                    }
                    
                    // Get data
                    const data = response.data.data;
                    
                    if (!data || typeof data !== 'object') {
                        alert('Error: Invalid data format from server. Got: ' + typeof data);
                        console.error('Invalid data structure:', data);
                        return;
                    }
                    
                    // Safely assign values
                    try {
                        const packageIdField = document.getElementById('packageId');
                        const trackingField = document.getElementById('tracking_number');
                        const senderField = document.getElementById('sender_name');
                        const receiverField = document.getElementById('receiver_name');
                        const originField = document.getElementById('origin');
                        const destField = document.getElementById('destination');
                        const weightField = document.getElementById('weight');
                        const lengthField = document.getElementById('length');
                        const widthField = document.getElementById('width');
                        const heightField = document.getElementById('height');
                        const warehouseField = document.getElementById('warehouse_id');
                        const statusField = document.getElementById('package_status');
                        
                        if (!packageIdField || !trackingField || !senderField || !receiverField || 
                            !originField || !destField || !weightField || !lengthField || 
                            !widthField || !heightField || !warehouseField || !statusField) {
                            alert('Error: Some form fields not found');
                            console.error('Missing form fields');
                            return;
                        }
                        
                        packageIdField.value = data.id || id;
                        trackingField.value = data.tracking_number || '';
                        senderField.value = data.sender_name || '';
                        receiverField.value = data.receiver_name || '';
                        originField.value = data.origin || '';
                        destField.value = data.destination || '';
                        weightField.value = data.weight || '';
                        lengthField.value = data.length || '';
                        widthField.value = data.width || '';
                        heightField.value = data.height || '';
                        warehouseField.value = data.warehouse_id || '';
                        statusField.value = data.package_status || 'registered';
                        
                        document.getElementById('packageModalTitle').textContent = 'Edit Package';
                        document.getElementById('packageSubmitBtn').textContent = 'Update';
                        document.getElementById('packageModal').classList.remove('hidden');
                        
                        console.log('Form populated successfully');
                    } catch (e) {
                        alert('Error populating form: ' + e.message);
                        console.error('Form population error:', e);
                    }
                })
                .catch(error => {
                    console.error('Axios error:', error);
                    let errorMsg = 'Failed to load package';
                    
                    if (error.response) {
                        errorMsg = error.response.data?.message || error.response.statusText || errorMsg;
                        console.error('Response status:', error.response.status);
                        console.error('Response data:', error.response.data);
                    } else if (error.request) {
                        errorMsg = 'No response from server';
                        console.error('No response:', error.request);
                    } else {
                        errorMsg = error.message;
                    }
                    
                    alert('Error: ' + errorMsg);
                });
        }

        function savePackage(e) {
            e.preventDefault();
            
            const id = document.getElementById('packageId').value;
            const tracking_number = document.getElementById('tracking_number').value;
            const sender_name = document.getElementById('sender_name').value;
            const receiver_name = document.getElementById('receiver_name').value;
            const origin = document.getElementById('origin').value;
            const destination = document.getElementById('destination').value;
            const weight = document.getElementById('weight').value;
            const length = document.getElementById('length').value;
            const width = document.getElementById('width').value;
            const height = document.getElementById('height').value;
            const warehouse_id = document.getElementById('warehouse_id').value;
            const package_status = document.getElementById('package_status').value;
            
            // Validation
            if (!tracking_number.trim()) {
                alert('Tracking number is required');
                return;
            }
            if (!sender_name.trim()) {
                alert('Sender name is required');
                return;
            }
            if (!receiver_name.trim()) {
                alert('Receiver name is required');
                return;
            }
            if (!origin.trim()) {
                alert('Origin is required');
                return;
            }
            if (!destination.trim()) {
                alert('Destination is required');
                return;
            }
            if (!weight || weight <= 0) {
                alert('Weight must be greater than 0');
                return;
            }
            if (!length || length <= 0 || !width || width <= 0 || !height || height <= 0) {
                alert('All dimensions (length, width, height) must be greater than 0');
                return;
            }
            if (!warehouse_id) {
                alert('Please select a warehouse');
                return;
            }
            
            const data = {
                tracking_number: tracking_number.trim(),
                sender_name: sender_name.trim(),
                receiver_name: receiver_name.trim(),
                origin: origin.trim(),
                destination: destination.trim(),
                weight: parseFloat(weight),
                length: parseFloat(length),
                width: parseFloat(width),
                height: parseFloat(height),
                warehouse_id: parseInt(warehouse_id),
                package_status: package_status
            };

            const method = id ? 'put' : 'post';
            const url = id ? `${API_URL}/package/${id}` : `${API_URL}/package/register`;

            axios[method](url, data)
                .then(response => {
                    if (response.data?.success) {
                        alert('Package saved successfully!');
                        location.reload();
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
                        alert('Package deleted successfully!');
                        location.reload();
                    })
                    .catch(error => {
                        alert('Error: ' + (error.response?.data?.error || 'Failed to delete package'));
                    });
            }
        }
    </script>
</body>
</html>
    <div class="min-h-screen">
        <!-- Navbar -->
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-blue-600">
                            <i class="fas fa-warehouse mr-2"></i>Module 1 Monitoring
                        </h1>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm text-gray-600">Warehouse & Package Management System</span>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            @if(isset($error))
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ $error }}
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Warehouses -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide">Total Warehouses</p>
                            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $total_warehouses }}</p>
                            <p class="text-xs text-green-600 mt-2">
                                <i class="fas fa-check-circle mr-1"></i>{{ $active_warehouses }} Active
                            </p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3">
                            <i class="fas fa-warehouse text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Packages -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide">Total Packages</p>
                            <p class="text-3xl font-bold text-green-600 mt-2">{{ $total_packages }}</p>
                            <p class="text-xs text-gray-600 mt-2">
                                <i class="fas fa-box mr-1"></i>Registered
                            </p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <i class="fas fa-boxes text-green-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Capacity -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide">Total Capacity</p>
                            <p class="text-3xl font-bold text-purple-600 mt-2">{{ $total_capacity }}</p>
                            <p class="text-xs text-gray-600 mt-2">
                                <i class="fas fa-arrow-up mr-1"></i>Units
                            </p>
                        </div>
                        <div class="bg-purple-100 rounded-full p-3">
                            <i class="fas fa-layer-group text-purple-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Overall Usage -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide">Usage Rate</p>
                            <p class="text-3xl font-bold text-orange-600 mt-2">{{ $overall_usage_percentage }}%</p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div class="bg-orange-600 h-2 rounded-full" style="width: {{ $overall_usage_percentage }}%"></div>
                            </div>
                        </div>
                        <div class="bg-orange-100 rounded-full p-3">
                            <i class="fas fa-chart-pie text-orange-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dimension Categories -->
            @if(count($packages_by_dimension) > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Small Packages -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-semibold">Small Packages</p>
                                <p class="text-2xl font-bold text-blue-600 mt-1">
                                    {{ $packages_by_dimension['small'] ?? 0 }}
                                </p>
                                <p class="text-xs text-gray-600 mt-2">Volume ≤ 1000 cm³</p>
                            </div>
                            <i class="fas fa-box text-blue-300 text-3xl"></i>
                        </div>
                    </div>

                    <!-- Medium Packages -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-semibold">Medium Packages</p>
                                <p class="text-2xl font-bold text-green-600 mt-1">
                                    {{ $packages_by_dimension['medium'] ?? 0 }}
                                </p>
                                <p class="text-xs text-gray-600 mt-2">Volume 1000 - 5000 cm³</p>
                            </div>
                            <i class="fas fa-boxes text-green-300 text-3xl"></i>
                        </div>
                    </div>

                    <!-- Large Packages -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-semibold">Large Packages</p>
                                <p class="text-2xl font-bold text-red-600 mt-1">
                                    {{ $packages_by_dimension['large'] ?? 0 }}
                                </p>
                                <p class="text-xs text-gray-600 mt-2">Volume > 5000 cm³</p>
                            </div>
                            <i class="fas fa-cube text-red-300 text-3xl"></i>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Warehouses Table -->
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-list mr-2"></i>Warehouse List
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Capacity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Current Load</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Usage %</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Packages</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($warehouses as $warehouse)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ $warehouse['warehouse_code'] }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $warehouse['warehouse_name'] }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $warehouse['location'] }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ number_format($warehouse['capacity'], 0) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ number_format($warehouse['current_load'], 0) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                @php
                                                    $percentage = $warehouse['usage_percentage'];
                                                    if ($percentage < 50) {
                                                        $color = 'bg-green-600';
                                                    } elseif ($percentage < 80) {
                                                        $color = 'bg-yellow-600';
                                                    } else {
                                                        $color = 'bg-red-600';
                                                    }
                                                @endphp
                                                <div class="{{ $color }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <span class="text-xs font-semibold">{{ $warehouse['usage_percentage'] }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $warehouse['package_count'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        @if($warehouse['status'] === 'active')
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>Active
                                            </span>
                                        @else
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                <i class="fas fa-times-circle mr-1"></i>Inactive
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-600">
                                        No warehouses found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Packages Table -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-list mr-2"></i>Package List
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Tracking #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Sender</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Receiver</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Weight (kg)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Volume (cm³)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Warehouse</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($packages as $package)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ $package['tracking_number'] }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $package['sender_name'] }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $package['receiver_name'] }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $package['weight'] }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ number_format($package['volume'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        @php
                                            $category = $package['dimension_category'];
                                            if ($category === 'small') {
                                                $badgeColor = 'bg-blue-100 text-blue-800';
                                            } elseif ($category === 'medium') {
                                                $badgeColor = 'bg-green-100 text-green-800';
                                            } else {
                                                $badgeColor = 'bg-red-100 text-red-800';
                                            }
                                        @endphp
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeColor }}">
                                            <i class="fas fa-tag mr-1"></i>{{ ucfirst($category) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $package['warehouse_name'] }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        @php
                                            $status = $package['status'];
                                            if ($status === 'delivered') {
                                                $statusColor = 'bg-green-100 text-green-800';
                                            } elseif ($status === 'in_transit') {
                                                $statusColor = 'bg-blue-100 text-blue-800';
                                            } elseif ($status === 'pending') {
                                                $statusColor = 'bg-yellow-100 text-yellow-800';
                                            } else {
                                                $statusColor = 'bg-gray-100 text-gray-800';
                                            }
                                        @endphp
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-600">
                                        No packages found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer Info -->
            <div class="mt-8 text-center text-sm text-gray-600">
                <p>Module 1: Warehouse & Package Management System</p>
                <p>Last updated: {{ date('Y-m-d H:i:s') }}</p>
            </div>
        </main>
    </div>
</body>
</html>
