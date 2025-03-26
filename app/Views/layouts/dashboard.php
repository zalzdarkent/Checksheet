<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . '| CBI' : 'CBI' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .table th {
        border: none !important;
    }
    .clickable-cell {
        cursor: pointer;
    }
    .clickable-cell:hover {
        opacity: 0.8;
    }
    #ngDetailsTableBody tr {
        cursor: pointer;
    }
    #ngDetailsTableBody tr:hover {
        background-color: #f5f5f5;
    }
</style>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
    </div>

    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Temuan</h5>
                    <h2 class="card-text"><?php echo $totalChecksheet ?? 0; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Open</h5>
                    <h2 class="card-text text-warning"><?php echo $totalNG ?? 0; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Close</h5>
                    <h2 class="card-text text-success"><?php echo $totalOK ?? 0; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="table-responsive">
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th class="text-white text-uppercase bg-dark" style="border: none;"></th>
                    <th class="text-white text-uppercase bg-dark" style="border: none;">Line 1</th>
                    <th class="text-white text-uppercase bg-dark" style="border: none;">Line 2</th>
                    <th class="text-white text-uppercase bg-dark" style="border: none;">Line 3</th>
                    <th class="text-white text-uppercase bg-dark" style="border: none;">Line 4</th>
                    <th class="text-white text-uppercase bg-dark" style="border: none;">Line 5</th>
                    <th class="text-white text-uppercase bg-dark" style="border: none;">Line 6</th>
                    <th class="text-white text-uppercase bg-dark" style="border: none;">Line 7</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($machines as $machine): ?>
                    <tr>
                        <!-- Mesin -->
                        <th class="text-white text-uppercase bg-dark" style="border: none;">
                            <?= $machine['mesin'] ?>
                        </th>

                        <!-- Loop untuk 7 line -->
                        <?php for ($line = 1; $line <= 7; $line++): ?>
                            <?php
                            $key = $machine['mesin'] . '_' . $line;
                            if (isset($machineStatus[$key])) {
                                $statusData = $machineStatus[$key];
                                $status = $statusData['status'];  // Bisa "R" atau jumlah NG
                                $bgColor = $status === 'R' ? '#9CCC65' : '#efe846';  // Hijau atau kuning
                                $textColor = $status === 'R' ? 'text-white' : 'text-black';  // Kontras warna tulisan
                            ?>
                                <td class="<?= $textColor ?> <?= $status !== 'R' ? 'clickable-cell' : '' ?>" 
                                    style="background-color: <?= $bgColor ?> !important; text-align: center;"
                                    <?php if ($status !== 'R'): ?>
                                        data-mesin="<?= $machine['mesin'] ?>"
                                        data-line="<?= $line ?>"
                                        onclick="showNGDetails(this)"
                                    <?php endif; ?>>
                                    <?= $status ?>
                                </td>
                            <?php
                            } else {
                            ?>
                                <!-- Jika tidak ada status, kosongkan kolom -->
                                <td style="background-color: #f5f5f5;"></td>
                            <?php
                            }
                            ?>
                        <?php endfor; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal untuk menampilkan detail NG -->
    <div class="modal fade" id="ngDetailsModal" tabindex="-1" aria-labelledby="ngDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ngDetailsModalLabel">Detail Temuan NG</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Item Check</th>
                                <th>Inspeksi</th>
                                <th>Standar</th>
                            </tr>
                        </thead>
                        <tbody id="ngDetailsTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Script untuk menangani klik dan modal -->
<script>
async function showNGDetails(element) {
    const mesin = element.dataset.mesin;
    const line = element.dataset.line;
    
    try {
        const response = await fetch(`/dashboard/ng-details?mesin=${encodeURIComponent(mesin)}&line=${line}`);
        const data = await response.json();
        
        const tableBody = document.getElementById('ngDetailsTableBody');
        tableBody.innerHTML = '';
        
        data.forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.item_check}</td>
                <td>${item.inspeksi}</td>
                <td>${item.standar}</td>
            `;
            row.style.cursor = 'pointer';
            row.addEventListener('click', () => {
                window.location.href = `/daily-check/${item.checksheet_id}`;
            });
            tableBody.appendChild(row);
        });
        
        const modal = new bootstrap.Modal(document.getElementById('ngDetailsModal'));
        modal.show();
    } catch (error) {
        console.error('Error fetching NG details:', error);
        alert('Terjadi kesalahan saat mengambil detail NG');
    }
}
</script>

<?= $this->endSection() ?>