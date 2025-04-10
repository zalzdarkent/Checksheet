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
</style>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard v3</h1>
    </div>

    <form class="mb-4 p-4 bg-light rounded-4 shadow-sm border" method="get">
        <div class="row g-4 align-items-end">
            <!-- Filter Bulan -->
            <div class="col-md-4">
                <label for="filterBulan" class="form-label fw-semibold text-primary">🗓️ Filter Bulan</label>
                <select id="filterBulan" name="filterBulan" class="form-select rounded-3 shadow-sm">
                    <option value="1" <?= $filterBulan == '1' ? 'selected' : '' ?>>Januari</option>
                    <option value="2" <?= $filterBulan == '2' ? 'selected' : '' ?>>Februari</option>
                    <option value="3" <?= $filterBulan == '3' ? 'selected' : '' ?>>Maret</option>
                    <option value="4" <?= $filterBulan == '4' ? 'selected' : '' ?>>April</option>
                    <option value="5" <?= $filterBulan == '5' ? 'selected' : '' ?>>Mei</option>
                    <option value="6" <?= $filterBulan == '6' ? 'selected' : '' ?>>Juni</option>
                    <option value="7" <?= $filterBulan == '7' ? 'selected' : '' ?>>Juli</option>
                    <option value="8" <?= $filterBulan == '8' ? 'selected' : '' ?>>Agustus</option>
                    <option value="9" <?= $filterBulan == '9' ? 'selected' : '' ?>>September</option>
                    <option value="10" <?= $filterBulan == '10' ? 'selected' : '' ?>>Oktober</option>
                    <option value="11" <?= $filterBulan == '11' ? 'selected' : '' ?>>November</option>
                    <option value="12" <?= $filterBulan == '12' ? 'selected' : '' ?>>Desember</option>
                </select>
            </div>

            <!-- Filter Mesin -->
            <div class="col-md-4">
                <label for="filterMesin" class="form-label fw-semibold text-primary">⚙️ ID Mesin</label>
                <select id="filterMesin" name="filterMesin" class="form-select rounded-3 shadow-sm">
                    <option value="">Semua Mesin</option>
                    <?php foreach ($machines as $machine): ?>
                        <option value="<?= $machine['id_machine'] ?>" <?= $filterMesin == $machine['id_machine'] ? 'selected' : '' ?>>
                            <?= $machine['id_machine'] ?> - <?= $machine['mesin'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Tombol Filter -->
            <div class="col-md-4">
                <label class="form-label d-block invisible">Filter</label>
                <button type="submit" class="btn btn-gradient-primary w-100 fw-semibold shadow-sm rounded-3">
                    🔍 Filter
                </button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>Mesin</th>
                    <th>ID Mesin</th>
                    <!-- kolom bulan -->
                    <?php for ($day = 1; $day <= $jumlahHari; $day++): ?>
                        <th><?= $day ?></th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($machineData as $machine): ?>
                    <tr>
                        <td><?= $machine['mesin'] ?></td>
                        <td><?= $machine['id_machine'] ?></td>
                        <?php for ($day = 1; $day <= $jumlahHari; $day++): ?>
                            <td>
                                <?php if (isset($machine['days'][$day])): ?>
                                    <?php if ($machine['days'][$day] == 'OK'): ?>
                                        <span class="badge bg-success">OK</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">NG</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?= $this->endSection() ?>