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
                <tr>
                    <td>1</td>
                    <td>
                        <span class="badge bg-success">A</span>
                        <span class="badge bg-danger">B</span>
                    </td>
                    <td>Selang Gas</td>
                    <td>Inspeksi A</td>
                    <td>Standar A</td>
                    <td>
                        <a href="#" class="btn btn-info btn-sm">Detail</a>
                        <a href="#" class="btn btn-warning btn-sm">Edit</a>
                        <a href="#" class="btn btn-danger btn-sm">Hapus</a>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>
                        <span class="badge bg-success">A</span>
                        <span class="badge bg-danger">B</span>
                    </td>
                    <td>Air Sylinder</td>
                    <td>Inspkesi B</td>
                    <td>Standar B</td>
                    <td>
                        <a href="#" class="btn btn-info btn-sm">Detail</a>
                        <a href="#" class="btn btn-warning btn-sm">Edit</a>
                        <a href="#" class="btn btn-danger btn-sm">Hapus</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</main>
<?= $this->endSection() ?>