<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4 min-vh-100">
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Daftar Item Status NG</h1>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                <?= session()->getFlashdata('success'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-1"></i>
                <?= session()->getFlashdata('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-nowrap mb-0" id="ngTable" width="100%" cellspacing="0">
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
                                    <td>
                                        <?php if ($item['new_status'] === 'OK'): ?>
                                            <span class="badge bg-warning text-dark"><?= $item['previous_status']; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><?= $item['previous_status']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($item['new_status'] !== 'OK'): ?>
                                            <a href="<?= base_url('open-ticket/change-status/' . $item['id']); ?>" class="btn btn-primary btn-sm" title="Ubah Status">
                                                Ubah Status
                                            </a>
                                        <?php else: ?>
                                            <span class="badge bg-success">Resolved</span>
                                        <?php endif; ?>
                                        <a href="<?= base_url('open-ticket/change-log/' . $item['id']); ?>" class="btn btn-info btn-sm" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
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