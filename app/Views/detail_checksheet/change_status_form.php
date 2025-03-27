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
            <form action="<?= base_url('open-ticket/update-status/' . $log['id']); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="form-group mb-3">
                    <label>Item Check</label>
                    <input type="text" class="form-control bg-secondary bg-opacity-25" value="<?= $log['item_check']; ?>" readonly>
                </div>

                <div class="form-group mb-3">
                    <label>Status Awal</label>
                    <input type="text" class="form-control bg-secondary bg-opacity-25" value="NG" readonly>
                </div>

                <div class="form-group mb-3">
                    <label for="new_status">Status Baru <span class="text-danger">*</span></label>
                    <select class="form-select" id="new_status" name="new_status" required>
                        <option value="">Pilih Status</option>
                        <option value="OK">OK</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="reason">Alasan Perubahan <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                </div>

                <div class="form-group mb-3">
                    <label for="npk">NPK <span class="text-danger">*</span></label>
                    <select class="form-select" id="npk" name="npk" required>
                        <option value="">Pilih NPK</option>
                        <option value="12345">12345 - Operator 1</option>
                        <option value="23456">23456 - Operator 2</option>
                        <option value="34567">34567 - Operator 3</option>
                        <option value="45678">45678 - Operator 4</option>
                        <option value="56789">56789 - Operator 5</option>
                    </select>
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
