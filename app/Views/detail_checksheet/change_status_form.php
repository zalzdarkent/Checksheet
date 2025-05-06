<?= $this->extend('layouts/app'); ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . ' | CBI' : 'CBI' ?>
<?= $this->endSection() ?>

<?= $this->section('content'); ?>
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #273749, #4a6b8a);
    }
</style>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4 min-vh-100">
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Ubah Status Tiket</h1>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger" role="alert">
                <?= session()->getFlashdata('error'); ?>
            </div>
        <?php endif; ?>

        <div class="card shadow mb-4">
            <div class="card-header bg-gradient-primary text-white py-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-ui-checks-grid fs-4 me-2"></i>
                    <h5 class="card-title mb-0">Form Perubahan Status</h5>
                </div>
            </div>
            <div class="card-body">
                <form action="<?= base_url('open-ticket/update-status/' . $log['id']); ?>" method="post">
                    <?= csrf_field(); ?>

                    <div class="row">
                        <!-- Kolom Kiri - Informasi Item -->
                        <div class="col-md-6">
                            <div class="card bg-light mb-3">
                                <div class="card-header bg-gradient-primary">
                                    <h6 class="mb-0 text-white">Informasi Item</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-semibold">Mesin</label>
                                        <input type="text" class="form-control bg-white" value="<?= $mesin; ?>" readonly>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-semibold">Item Check</label>
                                        <input type="text" class="form-control bg-white" value="<?= $log['item_check']; ?>" readonly>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-semibold">Inspeksi</label>
                                        <input type="text" class="form-control bg-white" value="<?= $log['inspeksi']; ?>" readonly>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-semibold">Standar</label>
                                        <input type="text" class="form-control bg-white" value="<?= $log['standar']; ?>" readonly>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-semibold">Tanggal</label>
                                        <input type="text" class="form-control bg-white" value="<?= $log['tanggal']; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan - Form Perubahan -->
                        <div class="col-md-6">
                            <div class="card bg-light mb-3">
                                <div class="card-header bg-gradient-primary">
                                    <h6 class="mb-0 text-white">Form Perubahan</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-semibold">Status Awal</label>
                                        <input type="text" class="form-control bg-white" value="NG" readonly>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="new_status" class="form-label fw-semibold">Status Baru <span class="text-danger">*</span></label>
                                        <select class="form-select" id="new_status" name="new_status" required>
                                            <option value="">Pilih Status</option>
                                            <option value="OK">OK</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="reason" class="form-label fw-semibold">Alasan Perubahan <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="npk" class="form-label fw-semibold">NPK <span class="text-danger">*</span></label>
                                        <!-- filepath: d:\Aplikasi-Codeigniter\new-checksheet\app\Views\detail_checksheet\change_status_form.php -->
                                        <select name="npk" class="form-select" required>
                                            <option value="">Pilih NPK</option>
                                            <?php foreach ($karyawanList as $karyawan): ?>
                                                <option value="<?= $karyawan['npk']; ?>"><?= $karyawan['npk']; ?> - <?= $karyawan['nama']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="<?= base_url('open-ticket'); ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>