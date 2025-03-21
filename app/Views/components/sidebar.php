<nav class="navbar navbar-dark bg-dark d-md-none">
    <div class="container-fluid">
        <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
            ☰
        </button>
    </div>
</nav>

<!-- Sidebar (Offcanvas di mobile, sidebar di desktop) -->
<div class="offcanvas offcanvas-start sidebar-bg text-white d-md-none" id="offcanvasSidebar">
    <div class="offcanvas-header border-bottom border-secondary">
        <div class="text-center w-100">
            <a href="/">
                <img src="/logo/CBI_logo.png" alt="CBI Logo" class="img-fluid mb-2" style="max-width: 130px;">
            </a>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="nav flex-column py-3">
            <a href="/" class="nav-link sidebar-link" data-route="/">
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard
            </a>
            <a href="/dashboard-v2" class="nav-link sidebar-link" data-route="dashboard-v2">
                <i class="bi bi-graph-up me-2"></i>
                Dashboard v2
            </a>
            <a href="/master" class="nav-link sidebar-link" data-route="master">
                <i class="bi bi-card-checklist me-2"></i>
                Master Checksheet
            </a>
            <a href="/checksheet" class="nav-link sidebar-link" data-route="checksheet">
                <i class="bi bi-clipboard-check me-2"></i>
                Checksheet
            </a>
        </div>
    </div>
</div>

<nav class="col-md-3 col-lg-2 d-none d-md-block sidebar-bg text-white min-vh-100 p-0">
    <div class="text-center py-4 border-bottom border-secondary">
        <a href="/">
            <img src="/logo/CBI_logo.png" alt="CBI Logo" class="img-fluid mb-2" style="max-width: 130px;">
        </a>
    </div>
    <div class="nav flex-column py-3">
        <a href="/" class="nav-link sidebar-link" data-route="/">
            <i class="bi bi-speedometer2 me-2"></i>
            Dashboard
        </a>
        <a href="/dashboard-v2" class="nav-link sidebar-link" data-route="dashboard-v2">
            <i class="bi bi-graph-up me-2"></i>
            Dashboard v2
        </a>
        <a href="/master" class="nav-link sidebar-link" data-route="master">
            <i class="bi bi-card-checklist me-2"></i>
            Master Checksheet
        </a>
        <a href="/checksheet" class="nav-link sidebar-link" data-route="checksheet">
            <i class="bi bi-clipboard-check me-2"></i>
            Checksheet
        </a>
    </div>
</nav>

<style>
    .sidebar-bg {
        background: linear-gradient(135deg, #1e2a3a 0%, #2c3e50 100%);
    }

    /* Hover effect */
    .sidebar-link {
        color: #e9ecef !important;
        padding: 0.8rem 1.5rem;
        transition: all 0.3s ease;
        position: relative;
        font-size: 0.95rem;
    }

    .sidebar-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: #ffffff !important;
        padding-left: 1.8rem;
    }

    /* Aktif link */
    .sidebar-link.active {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff !important;
        font-weight: 500;
        border-left: 4px solid #3498db;
    }

    .sidebar-link.active:hover {
        padding-left: 1.5rem;
    }

    /* Logo responsive */
    @media (max-width: 768px) {
        .offcanvas img {
            max-width: 110px;
        }
    }

    /* Smooth transition untuk semua elemen */
    * {
        transition: all 0.2s ease-in-out;
    }
</style>

<script>
    // Tambahkan class "active" berdasarkan route saat ini
    const currentUrl = window.location.pathname;
    const baseRoute = currentUrl.split('/')[1]; // Ambil segment pertama dari URL

    document.querySelectorAll(".sidebar-link").forEach(link => {
        const routeAttr = link.getAttribute("data-route");
        if (
            (routeAttr === "/" && currentUrl === "/") || // Untuk dashboard
            (routeAttr !== "/" && currentUrl.startsWith(`/${routeAttr}`)) // Untuk route lainnya
        ) {
            link.classList.add("active");
        }
    });
</script>
