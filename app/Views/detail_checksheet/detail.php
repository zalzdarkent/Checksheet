<?= $this->extend('layouts/app'); ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . ' | CBI' : 'CBI' ?>
<?= $this->endSection() ?>

<?= $this->section('content'); ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4 min-vh-100">
    <div class="container-fluid">
        <div class="card shadow-sm rounded-4 border-0">
            <div class="card-header bg-primary text-white rounded-top-4">
                <h5 class="mb-0">Detail Change Log</h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <tbody>
                            <tr>
                                <th class="bg-light w-25">ID</th>
                                <td><?= esc($log['id']) ?></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Item Check</th>
                                <td><?= esc($log['item_check']) ?></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Mesin</th>
                                <td><?= esc($log['mesin']) ?></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Previous Status</th>
                                <td>
                                    <span class="badge <?= $log['previous_status'] === 'NG' ? 'bg-danger' : 'bg-success' ?>">
                                        <?= esc($log['previous_status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">New Status</th>
                                <td>
                                    <span class="badge 
        <?= isset($log['new_status'])
            ? ($log['new_status'] === 'NG' ? 'bg-danger' : 'bg-success')
            : 'bg-secondary' ?>">
                                        <?= esc($log['new_status'] ?? '-') ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Reason</th>
                                <td><?= esc($log['reason'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Changed By</th>
                                <td><?= esc($log['changed_by'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Changed At</th>
                                <td>
                                    <?= isset($log['changed_at'])
                                        ? date('d F Y, H:i', strtotime($log['changed_at']))
                                        : '-' ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-end">
                    <a href="<?= base_url('open-ticket') ?>" class="btn btn-secondary rounded-pill px-4">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>