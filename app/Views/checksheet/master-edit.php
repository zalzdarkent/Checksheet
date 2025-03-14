<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4">
    <h2>Edit Data</h2>

    <div class="card">
        <div class="card-body">
            <form action="<?= base_url('/master/update/' . $item['id']); ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="POST">
                <div class="mb-3">
                    <label for="mesin" class="form-label">Mesin</label>
                    <input type="text" class="form-control" id="mesin" name="mesin" value="<?= implode(',', json_decode($item['mesin'])); ?>">
                </div>

                <div class="mb-3">
                    <label for="item_check" class="form-label">Item Check</label>
                    <input type="text" class="form-control" id="item_check" name="item_check" value="<?= $item['item_check']; ?>">
                </div>

                <div class="mb-3">
                    <label for="inspeksi" class="form-label">Inspeksi</label>
                    <input type="text" class="form-control" id="inspeksi" name="inspeksi" value="<?= $item['inspeksi']; ?>">
                </div>

                <div class="mb-3">
                    <label for="standar" class="form-label">Standar</label>
                    <input type="text" class="form-control" id="standar" name="standar" value="<?= $item['standar']; ?>">
                </div>

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="<?= base_url('/master-checksheet/index'); ?>" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</main>
<?= $this->endSection() ?>