<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4 min-vh-100">
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Daftar Item Status NG</h1>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success" role="alert">
            <?= session()->getFlashdata('success'); ?>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="ngTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Mesin</th>
                            <th>Item Check</th>
                            <th>Inspeksi</th>
                            <th>Standar</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($ngItems as $item): ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= $item['mesin']; ?></td>
                                <td><?= $item['item_check']; ?></td>
                                <td><?= $item['inspeksi']; ?></td>
                                <td><?= $item['standar']; ?></td>
                                <td><?= date('d-m-Y', strtotime($item['tanggal'])); ?></td>
                                <td><span class="badge badge-danger"><?= $item['previous_status']; ?></span></td>
                                <td>
                                    <a href="<?= base_url('detail-checksheet/change-status/' . $item['id']); ?>" class="btn btn-primary btn-sm">
                                        Ubah Status
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</main>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    $('#ngTable').DataTable();
});
</script>
<?= $this->endSection(); ?>

<?= $this->endSection(); ?>
