<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . ' | CBI' : 'CBI' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    /* Gradient Button */
    .btn-gradient-primary {
        background: linear-gradient(135deg, #4e73df, #224abe);
        color: #fff;
        border: none;
        transition: all 0.3s ease;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
    }

    .btn-gradient-primary:hover {
        background: linear-gradient(135deg, #5b7eff, #3655c3);
        color: #fff;
        /* Tetap terang */
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(78, 115, 223, 0.45);
    }

    /* Rounded, shadowed selects */
    select.form-select {
        border-radius: 0.75rem;
        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.1);
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    select.form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #273749, #4a6b8a);
    }
    
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table th {
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    .table td, .table th {
        vertical-align: middle;
    }
    
    .status-badge {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .status-badge:hover {
        transform: scale(1.1);
    }
    
    .status-ok {
        background-color: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }
    
    .status-ng {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
    
    .status-empty {
        background-color: rgba(108, 117, 125, 0.1);
        color: #6c757d;
    }
    
    .badge {
        font-weight: 500;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }
    
    .badge:hover {
        transform: translateY(-2px);
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            margin: 0 -1rem;
            padding: 0 1rem;
        }
        
        .table th, .table td {
            padding: 0.75rem 0.5rem;
            font-size: 0.9rem;
        }
        
        .status-badge {
            width: 28px;
            height: 28px;
            font-size: 1rem;
        }
        
        .badge {
            padding: 0.5rem 0.75rem;
            font-size: 0.8rem;
        }
    }
    
    @media (max-width: 576px) {
        .table th, .table td {
            padding: 0.5rem 0.25rem;
            font-size: 0.85rem;
        }
        
        .status-badge {
            width: 24px;
            height: 24px;
            font-size: 0.9rem;
        }
        
        .badge {
            padding: 0.4rem 0.6rem;
            font-size: 0.75rem;
        }
    }
</style>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard v3</h1>
    </div>

    <form class="mb-4 p-4 bg-light rounded-4 shadow-sm border" method="get">
        <div class="row g-4">
            <!-- Filter Bulan -->
            <div class="col-md-4">
                <label for="filterBulan" class="form-label fw-semibold text-primary">🗓️ Filter Bulan</label>
                <input type="month" id="filterBulan" name="filterBulan" class="form-control rounded-3 shadow-sm" value="<?= $filterBulan ?>">
            </div>

            <!-- Filter Mesin -->
            <div class="col-md-4">
                <label for="filterMesin" class="form-label fw-semibold text-primary">⚙️ ID Mesin</label>
                <select id="filterMesin" name="filterMesin" class="form-select rounded-3 shadow-sm">
                    <option value="">Semua Mesin</option>
                    <?php foreach ($machines as $machine): ?>
                        <?php 
                        // Extract the middle part of the machine ID (e.g., UTY from D-UTY-SCB-003)
                        $parts = explode('-', $machine['id_machine']);
                        $machineType = count($parts) >= 3 ? $parts[1] : $machine['id_machine'];
                        ?>
                        <option value="<?= $machineType ?>" <?= $filterMesin == $machineType ? 'selected' : '' ?>>
                            <?= $machineType ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Tombol Filter -->
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-gradient-primary w-100 fw-semibold shadow-sm rounded-3">
                    🔍 Filter
                </button>
            </div>
        </div>

        <!-- Tombol Reset -->
        <div class="row mt-3">
            <div class="col-md-4 offset-md-4">
                <a href="<?= current_url() ?>" class="btn btn-outline-danger w-100 fw-semibold shadow-sm rounded-3">
                    🔄 Reset Filter
                </a>
            </div>
        </div>
    </form>

    <!-- Search Box -->
    <div class="mb-4">
        <div class="input-group" style="max-width: 400px;">
            <span class="input-group-text bg-gradient-primary text-white border-0">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" id="searchMachine" class="form-control shadow-sm" placeholder="Cari berdasarkan ID Mesin atau Nama Mesin...">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
            <thead>
                <tr class="bg-gradient-primary text-white">
                    <th class="py-3 px-4 rounded-start-4">Mesin</th>
                    <th class="py-3 px-4">ID Mesin</th>
                    <?php for ($day = 1; $day <= $jumlahHari; $day++): ?>
                        <th class="py-3 px-4 <?= $day === $jumlahHari ? 'rounded-end-4' : '' ?>">
                            <div class="d-flex flex-column align-items-center">
                                <span class="fw-normal"><?= $day ?></span>
                                <small class="text-white-50"><?= date('D', strtotime("2024-01-$day")) ?></small>
                            </div>
                        </th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($machineData as $machine): ?>
                    <tr class="border-bottom">
                        <td class="py-3 px-4">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="bi bi-cpu-fill text-primary me-2"></i>
                                <span class="fw-semibold"><?= $machine['mesin'] ?></span>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                                <i class="bi bi-tag-fill me-1"></i> <?= $machine['id_machine'] ?>
                            </span>
                        </td>
                        <?php for ($day = 1; $day <= $jumlahHari; $day++): ?>
                            <td class="py-3 px-4">
                                <?php if (isset($machine['days'][$day])): ?>
                                    <?php if ($machine['days'][$day] == 'OK'): ?>
                                        <div class="status-badge status-ok" data-bs-toggle="tooltip" title="Status OK">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="status-badge status-ng" data-bs-toggle="tooltip" title="Status NG">
                                            <i class="bi bi-x-circle-fill"></i>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="status-badge status-empty" data-bs-toggle="tooltip" title="Belum ada data">
                                        <i class="bi bi-dash-circle"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Initialize autocomplete
        $("#searchMachine").autocomplete({
            source: function(request, response) {
                // Get all machine data from the table
                var machines = [];
                $("table tbody tr").each(function() {
                    var machineName = $(this).find("td:first-child span").text().trim();
                    var machineId = $(this).find("td:nth-child(2) .badge").text().trim();
                    machines.push({
                        label: machineId + " - " + machineName,
                        value: machineId,
                        machineName: machineName
                    });
                });

                // Filter results
                var results = $.ui.autocomplete.filter(machines, request.term);
                response(results);
            },
            minLength: 2,
            select: function(event, ui) {
                // Highlight the selected row
                $("table tbody tr").removeClass("table-primary");
                $("table tbody tr").each(function() {
                    var machineId = $(this).find("td:nth-child(2) .badge").text().trim();
                    if (machineId === ui.item.value) {
                        $(this).addClass("table-primary");
                        // Scroll to the row
                        $('html, body').animate({
                            scrollTop: $(this).offset().top - 100
                        }, 500);
                    }
                });
            },
            focus: function(event, ui) {
                $("#searchMachine").val(ui.item.label);
                return false;
            }
        }).data("ui-autocomplete")._renderItem = function(ul, item) {
            return $("<li>")
                .append(`<div class="d-flex align-items-center">
                    <span class="badge bg-primary bg-opacity-10 text-primary me-2">${item.value}</span>
                    <span>${item.machineName}</span>
                </div>`)
                .appendTo(ul);
        };

        // Clear search and highlighting when clicking outside
        $(document).click(function(e) {
            if (!$(e.target).closest("#searchMachine").length) {
                $("#searchMachine").val("");
                $("table tbody tr").removeClass("table-primary");
            }
        });
    });
</script>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<!-- jQuery UI JS -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Initialize autocomplete
        $("#searchMachine").autocomplete({
            source: function(request, response) {
                // Get all machine data from the table
                var machines = [];
                $("table tbody tr").each(function() {
                    var machineName = $(this).find("td:first-child span").text().trim();
                    var machineId = $(this).find("td:nth-child(2) .badge").text().trim();
                    machines.push({
                        label: machineId + " - " + machineName,
                        value: machineId,
                        machineName: machineName
                    });
                });

                // Filter results
                var results = $.ui.autocomplete.filter(machines, request.term);
                response(results);
            },
            minLength: 2,
            select: function(event, ui) {
                // Highlight the selected row
                $("table tbody tr").removeClass("table-primary");
                $("table tbody tr").each(function() {
                    var machineId = $(this).find("td:nth-child(2) .badge").text().trim();
                    if (machineId === ui.item.value) {
                        $(this).addClass("table-primary");
                        // Scroll to the row
                        $('html, body').animate({
                            scrollTop: $(this).offset().top - 100
                        }, 500);
                    }
                });
            },
            focus: function(event, ui) {
                $("#searchMachine").val(ui.item.label);
                return false;
            }
        }).data("ui-autocomplete")._renderItem = function(ul, item) {
            return $("<li>")
                .append(`<div class="d-flex align-items-center">
                    <span class="badge bg-primary bg-opacity-10 text-primary me-2">${item.value}</span>
                    <span>${item.machineName}</span>
                </div>`)
                .appendTo(ul);
        };

        // Clear search and highlighting when clicking outside
        $(document).click(function(e) {
            if (!$(e.target).closest("#searchMachine").length) {
                $("#searchMachine").val("");
                $("table tbody tr").removeClass("table-primary");
            }
        });
    });
</script>
<?= $this->endSection() ?>