@push('styles')
<style>
    .m4-hero {
        border-radius: 20px;
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #60a5fa 100%);
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .m4-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.22), transparent 45%);
    }

    .m4-hero-content {
        position: relative;
        z-index: 2;
    }

    .soft-card {
        border: 1px solid var(--border);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
    }

    .hub-monitor-card {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .kpi-card {
        border: 1px solid var(--border);
        border-radius: 16px;
        background: #fff;
        height: 100%;
    }

    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .kpi-icon.kpi-icon-fleet {
        background: #eff6ff;
        color: #2563eb;
    }

    .kpi-icon.kpi-icon-hub {
        background: #ecfdf5;
        color: #16a34a;
    }

    .kpi-icon.kpi-icon-idle {
        background: #f0fdf4;
        color: #15803d;
    }

    .kpi-icon.kpi-icon-transit {
        background: #fef3c7;
        color: #b45309;
    }

    .pill-info {
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.3rem 0.7rem;
        border: 1px solid #bfdbfe;
        background: #eff6ff;
        color: #1d4ed8;
    }

    .pill-info.pill-hub,
    .pill-info.pill-idle {
        border-color: #bbf7d0;
        background: #ecfdf5;
        color: #166534;
    }

    .pill-info.pill-transit {
        border-color: #fde68a;
        background: #fef3c7;
        color: #92400e;
    }

    .hub-progress {
        height: 10px;
        border-radius: 999px;
        background: #eef2f7;
    }

    .hub-progress .progress-bar {
        border-radius: 999px;
    }

    .hub-scroll-area {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
    }

    .table thead th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
    }

    .fleet-row-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #eff6ff;
        color: #2563eb;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #dbeafe;
    }

    .fleet-status-select {
        width: 150px;
    }

    .fleet-hub-select {
        width: 190px;
    }

    .search-mini {
        min-width: 220px;
    }

    @media (max-width: 992px) {
        .search-mini {
            min-width: 100%;
        }
    }
</style>
@endpush
