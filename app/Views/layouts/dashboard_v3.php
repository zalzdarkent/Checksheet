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
                        <option value="<?= $machine['id_machine'] ?>" <?= $filterMesin == $machine['id_machine'] ? 'selected' : '' ?>>
                            <?= $machine['id_machine'] ?> - <?= $machine['mesin'] ?>
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