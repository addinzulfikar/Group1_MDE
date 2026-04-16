<div class="modal fade" id="addFleetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-truck me-2"></i>Registrasi Armada Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <form id="addFleetForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Nomor Polisi</label>
                        <input type="text" class="form-control" name="plate_number" required placeholder="Contoh: B 1234 XYZ">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Tipe Kendaraan</label>
                        <select class="form-select" name="type" required>
                            <option value="motorcycle">Sepeda Motor</option>
                            <option value="van">Mobil Van</option>
                            <option value="truck">Truk Ekspedisi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Kapasitas Daya Angkut (kg)</label>
                        <input type="number" class="form-control" name="capacity" required placeholder="Contoh: 1000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Hub Awal</label>
                        <select class="form-select" name="current_hub_id" required>
                            @foreach($allHubs as $hub)
                                <option value="{{ $hub->id }}">{{ $hub->name }} (Sisa: {{ max(0, $hub->capacity - $hub->current_load) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="status" value="idle">
                </form>
            </div>
            <div class="modal-footer border-0 bg-white">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary px-4" onclick="submitFleet()">Simpan</button>
            </div>
        </div>
    </div>
</div>
