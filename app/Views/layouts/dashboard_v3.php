<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . ' | CBI' : 'CBI' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard v3</h1>
    </div>

    <form class="mb-3" method="get">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="filterBulan" class="form-label">Filter Bulan</label>
                <select id="filterBulan" name="filterBulan" class="form-select">
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
            <div class="col-md-4">
                <label for="filterMesin" class="form-label">ID Mesin</label>
                <select id="filterMesin" name="filterMesin" class="form-select">
                    <option value="">Semua Mesin</option>
                    <?php foreach ($machines as $machine): ?>
                        <option value="<?= $machine['id_machine'] ?>" <?= $filterMesin == $machine['id_machine'] ? 'selected' : '' ?>>
                            <?= $machine['id_machine'] ?> - <?= $machine['mesin'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>Mesin</th>
                    <th>ID Mesin</th>
                    <?php for ($day = 1; $day <= 31; $day++): ?>
                        <th><?= $day ?></th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($machineData as $machine): ?>
                    <tr>
                        <td><?= $machine['mesin'] ?></td>
                        <td><?= $machine['id_machine'] ?></td>
                        <?php for ($day = 1; $day <= 31; $day++): ?>
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