<?php

use App\Models\StatusChangeLog;

$hideMenus = isset($_GET['line']);
?>

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
            <a href="<?= base_url() ?>/">
                <img src="/logo/CBI_logo.png" alt="CBI Logo" class="img-fluid mb-2" style="max-width: 130px;">
            </a>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="nav flex-column py-3">
            <a href="<?= base_url() ?>/" class="nav-link sidebar-link" data-route="/">
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard
            </a>
            <a href="<?= base_url() ?>/dashboard-v2" class="nav-link sidebar-link" data-route="dashboard-v2">
                <i class="bi bi-graph-up me-2"></i>
                Dashboard v2
            </a>
            <a href="<?= base_url() ?>/dashboard-v3" class="nav-link sidebar-link" data-route="dashboard-v3">
                <i class="bi bi-globe2 me-2"></i>
                Dashboard v3
            </a>
            <a href="<?= base_url() ?>/master" class="nav-link sidebar-link" data-route="master">
                <i class="bi bi-card-checklist me-2"></i>
                Master Checksheet
            </a>
            <a href="<?= base_url() ?>/checksheet" class="nav-link sidebar-link" data-route="checksheet">
                <i class="bi bi-clipboard-check me-2"></i>
                Checksheet
            </a>
            <a href="<?= base_url() ?>/open-ticket" class="nav-link sidebar-link position-relative" data-route="open-ticket">
                <i class="bi bi-ticket-detailed me-2"></i>
                Open Ticket
                <?php
                $statusChangeLogModel = new StatusChangeLog();
                $totalLogs = $statusChangeLogModel->where('previous_status', 'NG')
                    ->where('new_status IS NULL')
                    ->countAllResults();
                ?>
                <span class="position-absolute translate-middle badge rounded-pill bg-danger" style="top: 50%; right: 15px;">
                    <?= esc($totalLogs) ?>
                </span>
            </a>
        </div>
    </div>
</div>

<nav class="col-md-3 col-lg-2 d-none d-md-block sidebar-bg text-white min-vh-100 p-0">
    <div class="text-center py-4 border-bottom border-secondary">
        <a href="<?= base_url() ?>/">
            <img src="<?= base_url() ?>logo/CBI_logo.png" alt="CBI Logo" class="img-fluid mb-2" style="max-width: 130px;">
        </a>
    </div>
    <div class="nav flex-column py-3">
        <?php if (!$hideMenus): ?>
            <div class="nav-item">
                <a href="#" class="nav-link sidebar-link" data-bs-toggle="collapse" data-bs-target="#dashboardSubmenu">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Dashboard
                    <i class="bi bi-chevron-down float-end"></i>
                </a>
                <div class="collapse" id="dashboardSubmenu">
                    <div class="nav flex-column ms-3">
                        <a href="<?= base_url() ?>" class="nav-link sidebar-link" data-route="/">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Dashboard v1
                        </a>
                        <a href="<?= base_url() ?>dashboard-v2" class="nav-link sidebar-link" data-route="dashboard-v2">
                            <i class="bi bi-graph-up me-2"></i>
                            Dashboard v2
                        </a>
                        <a href="<?= base_url() ?>dashboard-v3" class="nav-link sidebar-link" data-route="dashboard-v3">
                            <i class="bi bi-globe2 me-2"></i>
                            Dashboard v3
                        </a>
                    </div>
                </div>
            </div>
            <a href="<?= base_url() ?>/master" class="nav-link sidebar-link" data-route="master">
                <i class="bi bi-card-checklist me-2"></i>
                Master Checksheet
            </a>
        <?php endif; ?>
        <a href="<?= base_url() ?>/checksheet" class="nav-link sidebar-link" data-route="checksheet">
            <i class="bi bi-clipboard-check me-2"></i>
            Checksheet
        </a>
        <?php if (!$hideMenus): ?>
            <a href="<?= base_url() ?>/open-ticket" class="nav-link sidebar-link position-relative" data-route="open-ticket">
                <i class="bi bi-ticket-detailed me-2"></i>
                Open Ticket
                <?php
                $statusChangeLogModel = new StatusChangeLog();
                $totalLogs = $statusChangeLogModel->where('previous_status', 'NG')
                    ->where('new_status IS NULL')
                    ->countAllResults();
                ?>
                <span class="position-absolute translate-middle badge rounded-pill bg-danger" style="top: 50%; right: 15px;">
                    <?= esc($totalLogs) ?>
                </span>
            </a>
        <?php endif; ?>
    </div>

    <!-- Notification Toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="ngToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                <strong class="me-auto">NG Alert</strong>
                <small class="text-muted">just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <span id="toastMessage"></span>
            </div>
        </div>
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

    .toast {
        background-color: #fff;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .toast-header {
        background-color: #fff;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
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
            // Jika ini adalah submenu item, buka parent collapse
            const parentCollapse = link.closest('.collapse');
            if (parentCollapse) {
                parentCollapse.classList.add('show');
            }
        }
    });

    let lastNGCount = 0;
    let lastChecksheetNG = 0;
    let lastTicketNG = 0;
    const toast = new bootstrap.Toast(document.getElementById('ngToast'));

    function updateNGBadgeCount() {
        fetch('/api/ng-count')
            .then(response => response.json())
            .then(data => {
                // Update badges
                const checksheetBadge = document.querySelector('.checksheet-badge');
                const ticketBadge = document.querySelector('.ticket-badge');

                if (data.checksheet_ng > 0) {
                    checksheetBadge.style.display = 'inline-block';
                    checksheetBadge.textContent = data.checksheet_ng;
                } else {
                    checksheetBadge.style.display = 'none';
                }

                if (data.ticket_ng > 0) {
                    ticketBadge.style.display = 'inline-block';
                    ticketBadge.textContent = data.ticket_ng;
                } else {
                    ticketBadge.style.display = 'none';
                }

                // Show toast if there are new NG items
                if (data.count > lastNGCount) {
                    let message = '';
                    if (data.checksheet_ng > lastChecksheetNG) {
                        message += `New NG Checksheet: ${data.checksheet_ng - lastChecksheetNG}\n`;
                    }
                    if (data.ticket_ng > lastTicketNG) {
                        message += `New Open Ticket: ${data.ticket_ng - lastTicketNG}`;
                    }

                    document.getElementById('toastMessage').textContent = message;
                    toast.show();
                }

                // Update last counts
                lastNGCount = data.count;
                lastChecksheetNG = data.checksheet_ng;
                lastTicketNG = data.ticket_ng;
            })
            .catch(error => console.error('Error fetching NG count:', error));
    }

    // Update badge count every 10 seconds
    updateNGBadgeCount();
    setInterval(updateNGBadgeCount, 10000);
</script>