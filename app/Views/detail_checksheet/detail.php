<?= $this->extend('layouts/app'); ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . ' | CBI' : 'CBI' ?>
<?= $this->endSection() ?>

<?= $this->section('content'); ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4 min-vh-100">
    <div class="container-fluid">
        <div class="card shadow-lg rounded-4 border-0 overflow-hidden">
            <div class="card-header bg-gradient-primary text-white py-3 rounded-top-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-file-earmark-text-fill fs-4 me-2"></i>
                    <h5 class="mb-0 fw-semibold">Detail Change Log</h5>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <tbody>
                            <tr class="border-bottom">
                                <th class="bg-light-subtle p-3 rounded-start-4 fw-semibold text-primary w-25">ID</th>
                                <td class="p-3 rounded-end-4"><?= esc($log['id']) ?></td>
                            </tr>
                            <tr class="border-bottom">
                                <th class="bg-light-subtle p-3 rounded-start-4 fw-semibold text-primary">Item Check</th>
                                <td class="p-3 rounded-end-4"><?= esc($log['item_check']) ?></td>
                            </tr>
                            <tr class="border-bottom">
                                <th class="bg-light-subtle p-3 rounded-start-4 fw-semibold text-primary">Mesin</th>
                                <td class="p-3 rounded-end-4"><?= esc($log['mesin']) ?></td>
                            </tr>
                            <tr class="border-bottom">
                                <th class="bg-light-subtle p-3 rounded-start-4 fw-semibold text-primary">Previous Status</th>
                                <td class="p-3 rounded-end-4">
                                    <span class="badge <?= $log['previous_status'] === 'NG' ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success' ?> px-3 py-2 rounded-pill">
                                        <i class="bi <?= $log['previous_status'] === 'NG' ? 'bi-x-circle-fill' : 'bi-check-circle-fill' ?> me-1"></i>
                                        <?= esc($log['previous_status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <th class="bg-light-subtle p-3 rounded-start-4 fw-semibold text-primary">New Status</th>
                                <td class="p-3 rounded-end-4">
                                    <span class="badge <?= isset($log['new_status'])
                                        ? ($log['new_status'] === 'NG' ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success')
                                        : 'bg-secondary bg-opacity-10 text-secondary' ?> px-3 py-2 rounded-pill">
                                        <i class="bi <?= isset($log['new_status'])
                                            ? ($log['new_status'] === 'NG' ? 'bi-x-circle-fill' : 'bi-check-circle-fill')
                                            : 'bi-dash-circle-fill' ?> me-1"></i>
                                        <?= esc($log['new_status'] ?? '-') ?>
                                    </span>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <th class="bg-light-subtle p-3 rounded-start-4 fw-semibold text-primary">Reason</th>
                                <td class="p-3 rounded-end-4">
                                    <div class="bg-light-subtle p-3 rounded-4">
                                        <?= esc($log['reason'] ?? '-') ?>
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <th class="bg-light-subtle p-3 rounded-start-4 fw-semibold text-primary">Changed By</th>
                                <td class="p-3 rounded-end-4">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-fill text-primary me-2"></i>
                                        <?= esc($log['changed_by'] ?? '-') ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light-subtle p-3 rounded-start-4 fw-semibold text-primary">Changed At</th>
                                <td class="p-3 rounded-end-4">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-clock-fill text-primary me-2"></i>
                                        <?= isset($log['changed_at'])
                                            ? date('d F Y, H:i', strtotime($log['changed_at']))
                                            : '-' ?>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-end">
                    <a href="<?= base_url('open-ticket') ?>" class="btn btn-outline-primary rounded-pill px-4 py-2">
                        <i class="bi bi-arrow-left me-2"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #273749, #4a6b8a);
    }
    
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(39, 55, 73, 0.15) !important;
    }
    
    .badge {
        font-weight: 500;
        letter-spacing: 0.5px;
    }
    
    @media (max-width: 768px) {
        .w-25 {
            width: 35% !important;
        }
    }
    
    @media (max-width: 576px) {
        .w-25 {
            width: 40% !important;
        }
        
        .card-body {
            padding: 1.5rem !important;
        }
        
        .p-3 {
            padding: 0.75rem !important;
        }
    }
</style>
<?= $this->endSection(); ?>