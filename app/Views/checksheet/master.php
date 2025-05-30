<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . ' | CBI' : 'CBI' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4 min-vh-100">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="m-0 font-weight-bold">Master Checksheet Pre-Use</h5>
                <a href="<?= base_url() ?>/master/create" class="btn btn-primary btn-sm px-4 rounded-pill">
                    <i class="bi bi-plus-circle"></i> Tambah
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i>
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    <?= session()->getFlashdata('danger') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table id="myTable" class="table table-hover align-middle text-nowrap mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>Judul Checksheet</th>
                            <th>Mesin</th>
                            <th>Run Hour</th>
                            <th>Temperature</th>
                            <th>Running Load</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($items)) : ?>
                            <tr>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center">Tidak ada data</td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($items as $key => $item) : ?>
                                <tr>
                                    <td class="text-center"><?= $key + 1 ?></td>
                                    <td><?= $item['judul_checksheet']; ?></td>
                                    <td>
                                        <?php
                                        $mesinList = json_decode($item['mesin'], true);
                                        if (is_array($mesinList)) :
                                            foreach ($mesinList as $mesin) :
                                        ?>
                                                <span class="badge bg-primary rounded-pill"><?= htmlspecialchars($mesin); ?></span>
                                            <?php
                                            endforeach;
                                        else :
                                            ?>
                                            <span class="text-muted fst-italic">Tidak ada mesin</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($item['run_hour'] == 1): ?>
                                            <span class="badge bg-success">Yes</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($item['temperature'] == 1): ?>
                                            <span class="badge bg-success">Yes</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($item['run_load'] == 1): ?>
                                            <span class="badge bg-success">Yes</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url() ?>/master/edit/<?= $item['id']; ?>" class="btn btn-warning btn-sm rounded-pill px-3">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="<?= base_url() ?>/master/delete/<?= $item['id']; ?>" class="btn btn-danger btn-sm rounded-pill px-3 ms-1" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?= $this->section('scripts') ?>
<style>
    .dataTables_wrapper .dataTables_length select {
        padding: 0.375rem 2.25rem 0.375rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 0.25rem;
        border-color: #dee2e6;
    }

    .dataTables_wrapper .dataTables_filter input {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 0.25rem;
        border-color: #dee2e6;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.375rem 0.75rem;
        margin: 0 0.2rem;
        border-radius: 0.25rem !important;
        border: none !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #0d6efd !important;
        color: white !important;
        border: none !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #0b5ed7 !important;
        color: white !important;
        border: none !important;
    }

    .table> :not(caption)>*>* {
        padding: 0.75rem;
    }

    .table thead tr th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
</style>
<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            pageLength: 10,
            ordering: true,
            responsive: true,
            columnDefs: [{
                    orderable: true,
                    targets: 3
                },
                {
                    orderable: false,
                    targets: 2
                }
            ],
            dom: '<"row align-items-center mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                '<"row"<"col-sm-12"tr>>' +
                '<"row align-items-center mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "Semua"]
            ],
            order: [
                [0, 'asc']
            ]
        });
    });
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>