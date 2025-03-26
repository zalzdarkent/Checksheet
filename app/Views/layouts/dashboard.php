<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . '| CBI' : 'CBI' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .table th {
    border: none !important;
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
                <td class="<?= $textColor ?>" style="background-color: <?= $bgColor ?> !important; text-align: center;">
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
</main>

<?= $this->endSection() ?>