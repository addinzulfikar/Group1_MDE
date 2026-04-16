<footer class="footer-custom mt-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 mb-2 mb-md-0">
                <span class="text-white fw-bold me-2">
                    <i class="bi bi-box-seam-fill" style="color: #3b82f6;"></i> SiPaket Tim 1
                </span>
                &copy; {{ date('Y') }} - Tugas UTS Arsitektur Backend Lanjut
            </div>
            <div class="col-md-6 text-md-end">
                <a href="{{ url('/home') }}" class="me-3">Home</a>
                <a href="{{ url('/module-1-monitor') }}" class="me-3">Warehouse</a>
                <a href="{{ url('/tracking') }}" class="me-3">Tracking</a>
                <a href="{{ url('/module-3') }}" class="me-3">Auth</a>
                <a href="{{ url('/') }}">Fleet</a>
            </div>
        </div>
    </div>
</footer>
