<nav class="navbar navbar-dark bg-dark d-md-none">
    <div class="container-fluid">
        <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
            ☰
        </button>
    </div>
</nav>

<!-- Sidebar (Offcanvas di mobile, sidebar di desktop) -->
<div class="offcanvas offcanvas-start bg-dark text-white d-md-none" id="offcanvasSidebar">
    <div class="offcanvas-header">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <a href="/" class="nav-link sidebar-link py-2">Dashboard</a>
        <a href="/list-checksheet" class="nav-link sidebar-link py-2">Checksheet</a>
        <a href="/master-checksheet/inde" class="nav-link sidebar-link py-2">Master Checksheet</a>
    </div>
</div>

<nav class="col-md-3 col-lg-2 d-none d-md-block bg-dark text-white min-vh-100 p-3">
    <a href="/" class="nav-link sidebar-link py-2">Dashboard</a>
    <a href="/list-checksheet" class="nav-link sidebar-link py-2">Checksheet</a>
    <a href="/master-checksheet/index" class="nav-link sidebar-link py-2">Master Checksheet</a>
</nav>

<style>
    /* Hover effect */
    .sidebar-link:hover {
        background-color: #6c757d; /* Abu-abu terang */
        color: white !important;
        border-radius: 5px;
    }

    /* Aktif link */
    .sidebar-link.active {
        background-color: white !important;
        color: black !important;
        font-weight: bold;
        border-radius: 5px;
    }
</style>

<script>
    // Tambahkan class "active" berdasarkan URL saat ini
    const currentUrl = window.location.pathname;
    
    document.querySelectorAll(".sidebar-link").forEach(link => {
        if (link.getAttribute("href") === currentUrl) {
            link.classList.add("active");
        }
    });
</script>
