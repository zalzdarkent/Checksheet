<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4">
    <h2>Edit Checksheet</h2>

    <div class="card">
        <div class="card-body">
            <form action="/checksheet/update/<?= $checksheet['id'] ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="POST">

                <div class="mb-3">
                    <label for="mesin" class="form-label">Mesin</label>
                    <select class="form-select" id="mesin" name="mesin">
                        <option value="A" <?= $checksheet['mesin'] == 'A' ? 'selected' : '' ?>>A</option>
                        <option value="B" <?= $checksheet['mesin'] == 'B' ? 'selected' : '' ?>>B</option>
                        <option value="C" <?= $checksheet['mesin'] == 'C' ? 'selected' : '' ?>>C</option>
                        <option value="D" <?= $checksheet['mesin'] == 'D' ? 'selected' : '' ?>>D</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="bulan" class="form-label">Bulan</label>
                    <input type="month" class="form-control" id="bulan" name="bulan" value="<?= date('Y-m', strtotime($checksheet['bulan'])) ?>">
                </div>

                <div class="mb-3">
                    <label for="departemen" class="form-label">Departemen</label>
                    <select class="form-select" id="departemen" name="departemen">
                        <option value="MTN" <?= $checksheet['departemen'] == 'MTN' ? 'selected' : '' ?>>MTN</option>
                        <option value="PRD" <?= $checksheet['departemen'] == 'PRD' ? 'selected' : '' ?>>PRD</option>
                        <option value="QA" <?= $checksheet['departemen'] == 'QA' ? 'selected' : '' ?>>QA</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="seksi" class="form-label">Seksi</label>
                    <select class="form-select" id="seksi" name="seksi">
                        <option value="Prod. 1" <?= $checksheet['seksi'] == 'Prod. 1' ? 'selected' : '' ?>>Prod. 1</option>
                        <option value="Prod. 2" <?= $checksheet['seksi'] == 'Prod. 2' ? 'selected' : '' ?>>Prod. 2</option>
                        <option value="Prod. 3" <?= $checksheet['seksi'] == 'Prod. 3' ? 'selected' : '' ?>>Prod. 3</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
                <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</main>
<?= $this->endSection() ?>