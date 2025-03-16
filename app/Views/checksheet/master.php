<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . '| CBI' : 'CBI' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4 min-vh-100">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fs-7">Master Checksheet Pre-Use</h3>
        <a href="/master-checksheet/tambah" class="btn btn-primary">Tambah</a>
    </div>

    <div class="table-responsive">
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('danger') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Mesin</th>
                    <th>Item Check</th>
                    <th>Item inspeksi</th>
                    <th>Standar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)) : ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data ditemukan.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($items as $key => $item) : ?>
                        <tr>
                            <td><?= $key + 1; ?></td>
                            <td>
                                <?php
                                $mesinList = json_decode($item['mesin'], true); // true agar hasilnya array asosiatif
                                if (is_array($mesinList)) :
                                    foreach ($mesinList as $mesin) :
                                ?>
                                        <span class="badge bg-success"><?= htmlspecialchars($mesin); ?></span>
                                    <?php
                                    endforeach;
                                else :
                                    ?>
                                    <span class="text-muted">Tidak ada mesin</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $item['item_check']; ?></td>
                            <td><?= $item['inspeksi']; ?></td>
                            <td><?= $item['standar']; ?></td>
                            <td>
                                <a href="<?= base_url('master/edit/' . $item['id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="<?= base_url('master/delete/' . $item['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?= $this->endSection() ?>