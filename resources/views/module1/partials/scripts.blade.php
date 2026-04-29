@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios@1.6.2/dist/axios.min.js"></script>
<script>
    const API_URL = '/api/v1';
    const warehouseModal = new bootstrap.Modal(document.getElementById('warehouseModal'));
    const packageModal = new bootstrap.Modal(document.getElementById('packageModal'));
    const fleetTrackModal = new bootstrap.Modal(document.getElementById('fleetTrackModal'));

    // --- Warehouse Functions ---
    function openWarehouseModal() {
        document.getElementById('warehouseId').value = '';
        document.getElementById('warehouseForm').reset();
        document.getElementById('warehouse_usage').value = 0;
        document.getElementById('warehouse_usage_text').textContent = '0%';
        document.getElementById('warehouse_usage_bar').style.width = '0%';
        document.getElementById('warehouse_usage_bar').className = 'progress-bar bg-success';
        document.getElementById('warehouseModalTitle').textContent = 'Add Warehouse';
        document.getElementById('warehouseSubmitBtn').textContent = 'Save';
        warehouseModal.show();
    }

    function calculateUsagePercentage() {
        const capacity = parseFloat(document.getElementById('warehouse_capacity').value) || 0;
        const current_load = parseFloat(document.getElementById('warehouse_current_load').value) || 0;
        let usagePercentage = capacity > 0 ? ((current_load / capacity) * 100).toFixed(2) : 0;
        if(usagePercentage > 100) usagePercentage = 100;
        
        document.getElementById('warehouse_usage').value = usagePercentage;
        document.getElementById('warehouse_usage_text').textContent = usagePercentage + '%';
        
        const bar = document.getElementById('warehouse_usage_bar');
        bar.style.width = usagePercentage + '%';
        if (usagePercentage < 50) bar.className = 'progress-bar bg-success';
        else if (usagePercentage < 80) bar.className = 'progress-bar bg-warning';
        else bar.className = 'progress-bar bg-danger';
    }

    document.getElementById('warehouse_capacity').addEventListener('input', calculateUsagePercentage);
    document.getElementById('warehouse_current_load').addEventListener('input', calculateUsagePercentage);

    function closeWarehouseModal() {
        warehouseModal.hide();
    }

    function editWarehouse(id) {
        axios.get(`${API_URL}/warehouse/${id}`)
            .then(response => {
                const data = response.data.data;
                document.getElementById('warehouseId').value = data.id || id;
                document.getElementById('warehouse_name').value = data.warehouse_name || '';
                document.getElementById('warehouse_location').value = data.location || '';
                document.getElementById('warehouse_hub_id').value = data.hub_id || '';
                document.getElementById('warehouse_capacity').value = data.capacity || '';
                document.getElementById('warehouse_current_load').value = data.current_load || 0;
                document.getElementById('warehouse_status').value = data.status || 'active';
                
                calculateUsagePercentage();
                
                document.getElementById('warehouseModalTitle').textContent = 'Edit Warehouse';
                document.getElementById('warehouseSubmitBtn').textContent = 'Update';
                warehouseModal.show();
            })
            .catch(error => alert('Error loading warehouse data'));
    }

    function saveWarehouse(e) {
        e.preventDefault();
        const id = document.getElementById('warehouseId').value;
        const current_load = document.getElementById('warehouse_current_load').value;
        const capacity = document.getElementById('warehouse_capacity').value;

        if (parseInt(current_load) > parseInt(capacity)) {
            alert('Current Load cannot exceed Capacity');
            return;
        }

        const data = {
            warehouse_name: document.getElementById('warehouse_name').value.trim(),
            location: document.getElementById('warehouse_location').value.trim(),
            hub_id: document.getElementById('warehouse_hub_id').value ? parseInt(document.getElementById('warehouse_hub_id').value) : null,
            capacity: parseInt(capacity),
            current_load: parseInt(current_load),
            status: document.getElementById('warehouse_status').value
        };

        const method = id ? 'put' : 'post';
        const url = id ? `${API_URL}/warehouse/${id}` : `${API_URL}/warehouse`;

        axios[method](url, data)
            .then(response => {
                if (response.data.success) {
                    window.location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                }
            })
            .catch(error => alert('Error saving warehouse'));
    }

    function deleteWarehouse(id) {
        if (confirm('Are you sure you want to delete this warehouse?')) {
            axios.delete(`${API_URL}/warehouse/${id}`)
                .then(response => {
                    if (response.data.success) {
                        window.location.reload();
                    }
                })
                .catch(error => alert('Failed to delete warehouse'));
        }
    }

    // --- Package Functions ---
    function openPackageModal() {
        document.getElementById('packageId').value = '';
        document.getElementById('packageForm').reset();
        document.getElementById('packageModalTitle').textContent = 'Register Package';
        document.getElementById('packageSubmitBtn').textContent = 'Register';
        document.getElementById('volPreview').style.display = 'none';
        document.getElementById('fleet_fit_badge').style.display = 'none';
        
        loadFleetOptions(); // Load fleets for dropdown
        packageModal.show();
    }

    function closePackageModal() {
        packageModal.hide();
    }

    function editPackage(id) {
        axios.get(`${API_URL}/package/${id}`)
            .then(response => {
                const data = response.data.data;
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
                
                updateVolumetric();
                loadFleetOptions();
                
                packageModal.show();
            })
            .catch(error => alert('Error loading package'));
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

        const method = id ? 'put' : 'post';
        const url = id ? `${API_URL}/package/${id}` : `${API_URL}/package/register`;

        axios[method](url, data)
            .then(response => {
                if (response.data.success) {
                    window.location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                }
            })
            .catch(error => alert('Error saving package'));
    }

    function deletePackage(id) {
        if (confirm('Are you sure you want to delete this package?')) {
            axios.delete(`${API_URL}/package/${id}`)
                .then(response => {
                    if (response.data.success) {
                        window.location.reload();
                    }
                })
                .catch(error => alert('Failed to delete package'));
        }
    }

    // --- Volumetric Calculation & Fleet Capacity Check ---
    let currentEffectiveWeight = 0;

    function updateVolumetric() {
        const l = parseFloat(document.getElementById('length').value) || 0;
        const w = parseFloat(document.getElementById('width').value) || 0;
        const h = parseFloat(document.getElementById('height').value) || 0;
        const act = parseFloat(document.getElementById('weight').value) || 0;

        if(l>0 && w>0 && h>0) {
            document.getElementById('volPreview').style.display = 'block';
            
            const vol = l * w * h;
            const volWeight = vol / 5000;
            currentEffectiveWeight = Math.max(act, volWeight);
            const isVolumetric = volWeight > act;

            document.getElementById('prev_volume').innerHTML = vol.toFixed(2) + ' cm&sup3;';
            document.getElementById('prev_vol_weight').innerHTML = volWeight.toFixed(2) + ' kg';
            document.getElementById('prev_actual').innerHTML = act.toFixed(2) + ' kg';
            document.getElementById('prev_effective').innerHTML = currentEffectiveWeight.toFixed(2) + ' kg';
            
            const basisEl = document.getElementById('prev_basis');
            if(isVolumetric) {
                basisEl.className = 'badge-volumetric';
                basisEl.textContent = 'volumetrik';
            } else {
                basisEl.className = 'badge-actual';
                basisEl.textContent = 'aktual';
            }

            checkFleetCapacity();
        } else {
            document.getElementById('volPreview').style.display = 'none';
        }
    }

    let fleetsCache = [];
    function loadFleetOptions() {
        axios.get(`${API_URL}/fleet`)
            .then(response => {
                if (response.data && response.data.data) {
                    fleetsCache = response.data.data.data || response.data.data; // Handle pagination structure if exists
                    const select = document.getElementById('fleet_check_select');
                    select.innerHTML = '<option value="">-- Pilih Armada --</option>';
                    
                    fleetsCache.forEach(fleet => {
                        select.innerHTML += `<option value="${fleet.id}" data-cap="${fleet.capacity}">${fleet.plate_number} (${fleet.type}) - Kapasitas: ${fleet.capacity} kg</option>`;
                    });
                }
            })
            .catch(error => console.error('Gagal memuat armada', error));
    }

    function checkFleetCapacity() {
        const select = document.getElementById('fleet_check_select');
        const badge = document.getElementById('fleet_fit_badge');
        
        if (!select.value || currentEffectiveWeight <= 0) {
            badge.style.display = 'none';
            return;
        }

        const selectedOption = select.options[select.selectedIndex];
        const capacity = parseFloat(selectedOption.getAttribute('data-cap'));

        badge.style.display = 'inline-block';
        if (currentEffectiveWeight <= capacity) {
            badge.className = 'fit-badge fit-ok';
            badge.innerHTML = '<i class="bi bi-check-circle me-1"></i>MUAT';
        } else {
            badge.className = 'fit-badge fit-no';
            badge.innerHTML = '<i class="bi bi-x-circle me-1"></i>TIDAK MUAT';
        }
    }

    // --- Live Fleet Tracking Integration ---
    function openFleetModal(hubId, hubName) {
        document.getElementById('fleetModalHubLabel').textContent = `Menampilkan armada di ${hubName} (Hub ID: ${hubId}).`;
        document.getElementById('fleetModalContent').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted small">Memuat data armada...</p></div>';
        fleetTrackModal.show();

        axios.get(`${API_URL}/fleet`)
            .then(response => {
                const allFleets = response.data.data.data || response.data.data;
                const hubFleets = allFleets.filter(f => f.current_hub_id == hubId);
                
                let html = '';
                if (hubFleets.length === 0) {
                    html = '<div class="alert alert-light text-center border text-muted"><i class="bi bi-truck text-muted opacity-50 d-block fs-3 mb-2"></i>Tidak ada armada di hub ini saat ini.</div>';
                } else {
                    hubFleets.forEach(f => {
                        let statusHtml = '';
                        if(f.status === 'idle') statusHtml = '<span class="status-badge-idle">Idle</span>';
                        else if(f.status === 'in_transit') statusHtml = '<span class="status-badge-in_transit">In Transit</span>';
                        else statusHtml = '<span class="status-badge-maintenance">Maintenance</span>';
                        
                        html += `
                        <div class="fleet-info-row">
                            <div class="fleet-info-icon"><i class="bi bi-truck"></i></div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="text-dark">${f.plate_number}</strong>
                                    ${statusHtml}
                                </div>
                                <div class="text-muted small">
                                    Tipe: <span class="text-capitalize">${f.type}</span> &bull; Kapasitas: ${f.capacity} kg
                                </div>
                            </div>
                        </div>`;
                    });
                }
                
                document.getElementById('fleetModalContent').innerHTML = html;
            })
            .catch(error => {
                document.getElementById('fleetModalContent').innerHTML = '<div class="alert alert-danger">Gagal memuat data armada. Coba lagi.</div>';
            });
    }
</script>
@endpush
