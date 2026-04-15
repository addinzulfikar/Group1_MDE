<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian - Tracking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        .navbar-brand { 
            font-weight: bold;
            font-size: 0.9rem;
        }
        .navbar-brand i {
            font-size: 1.2rem;
            margin-right: 0.3rem;
        }
        @media (min-width: 576px) {
            .navbar-brand {
                font-size: 1.1rem;
            }
            .navbar-brand i {
                font-size: 1.5rem;
            }
        }
        .result-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            transition: all 0.3s;
            cursor: pointer;
        }
        .result-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        .tracking-number {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #0d6efd;
        }
        .status-badge {
            padding: 0.5em 0.75em;
            font-size: 0.85em;
            font-weight: bold;
        }
        .status-pending { background-color: #ffc107; }
        .status-in-transit { background-color: #0d6efd; }
        .status-in-hub { background-color: #17a2b8; }
        .status-on-delivery { background-color: #fd7e14; }
        .status-delivered { background-color: #28a745; }
        .status-failed { background-color: #dc3545; }
        /* Pagination - Override Bootstrap 5 CSS Variables */
        .d-flex nav .pagination {
            --bs-pagination-padding-x: 0.4rem;
            --bs-pagination-padding-y: 0.25rem;
            --bs-pagination-font-size: 0.8rem;
            gap: 0.2rem;
        }
        .d-flex nav .pagination .page-link {
            min-width: auto;
            line-height: 1;
        }
        .d-flex nav .pagination svg {
            width: 0.75rem;
            height: 0.75rem;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 p-2 p-md-3 shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="/"><i class="bi bi-box-seam"></i> <span class="d-none d-sm-inline">Logistik Tim 1</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="ms-auto">
                <a href="/tracking/search" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i> <span class="d-none d-md-inline">Cari Lagi</span></a>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid container-lg pb-5">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-1"><i class="bi bi-search"></i> Hasil Pencarian</h2>
            <p class="text-muted">Ditemukan {{ $results->total() }} paket untuk "<strong>{{ $keyword }}</strong>"</p>
        </div>
    </div>

    @if($results->count() > 0)
    <!-- Results -->
    <div class="row">
        @foreach($results as $shipment)
        <div class="col-12 mb-3">
            <a href="{{ route('tracking.show', $shipment->tracking_number) }}" class="text-decoration-none text-dark">
                <div class="result-card p-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="mb-2">
                                <span class="tracking-number">{{ $shipment->tracking_number }}</span>
                                <span class="badge status-badge status-{{ $shipment->status }}">
                                    @switch($shipment->status)
                                        @case('pending')
                                            Menunggu
                                            @break
                                        @case('in_transit')
                                            Dalam Perjalanan
                                            @break
                                        @case('in_hub')
                                            Di Hub
                                            @break
                                        @case('on_delivery')
                                            Pengiriman
                                            @break
                                        @case('delivered')
                                            Terkirim
                                            @break
                                        @case('failed')
                                            Gagal
                                            @break
                                    @endswitch
                                </span>
                            </div>
                            
                            <div class="row g-2 g-md-3">
                                <div class="col-12 col-md-4">
                                    <small class="text-muted">Pengirim</small>
                                    <h6 class="fw-bold mb-1">{{ substr($shipment->sender_name, 0, 25) }}</h6>
                                    <small class="text-muted">{{ $shipment->sender_phone }}</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <small class="text-muted">Penerima</small>
                                    <h6 class="fw-bold mb-1">{{ substr($shipment->receiver_name, 0, 25) }}</h6>
                                    <small class="text-muted">{{ $shipment->receiver_phone }}</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <small class="text-muted">Berat / Dimensi</small>
                                    <h6 class="fw-bold mb-1">{{ $shipment->weight }} kg</h6>
                                    <small class="text-muted">{{ $shipment->length }}x{{ $shipment->width }}x{{ $shipment->height }}cm</small>
                                </div>
                            </div>

                            <hr class="my-2">
                            <small class="text-muted"><i class="bi bi-calendar"></i> Terdaftar: {{ $shipment->created_at->format('d M Y H:i') }}</small>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary btn-sm">
                                <i class="bi bi-arrow-right"></i> Lihat Detail
                            </button>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4 mb-4">
        {{ $results->links() }}
    </div>
    @else
    <!-- No Results -->
    <div class="row">
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                <h4 class="mt-4 fw-bold">Paket Tidak Ditemukan</h4>
                <p class="text-muted mb-3">Tidak ada paket yang cocok dengan pencarian Anda</p>
                <a href="/tracking/search" class="btn btn-primary">
                    <i class="bi bi-search"></i> Coba Pencarian Lagi
                </a>
            </div>
        </div>
    </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
