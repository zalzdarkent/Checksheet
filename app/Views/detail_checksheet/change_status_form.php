<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4 min-vh-100">
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Ubah Status Item</h1>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger" role="alert">
            <?= session()->getFlashdata('error'); ?>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="<?= base_url('detail-checksheet/update-status/' . $log['id']); ?>" method="post">
                <div class="form-group">
                    <label>Item Check</label>
                    <input type="text" class="form-control" value="<?= $log['item_check']; ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Status Awal</label>
                    <input type="text" class="form-control" value="NG" readonly>
                </div>

                <div class="form-group">
                    <label for="new_status">Status Baru *</label>
                    <select class="form-control" id="new_status" name="new_status" required>
                        <option value="">Pilih Status</option>
                        <option value="OK">OK</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="reason">Alasan Perubahan *</label>
                    <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                </div>

                <div class="form-group">
                    <label for="npk">NPK *</label>
                    <input type="text" class="form-control" id="npk" name="npk" required>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="<?= base_url('open-ticket'); ?>" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
</main>
<?= $this->endSection(); ?>
