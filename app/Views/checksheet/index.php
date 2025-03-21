<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . '| CBI' : 'CBI' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4 min-vh-100">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fs-7">List Checksheet Pre-Use</h3>
        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">Tambah</a>
    </div>

    <div class="table-responsive">
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <table class="table table-bordered table-striped align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th class="custom-header">No</th>
                    <th class="custom-header">Mesin</th>
                    <th class="custom-header">Bulan</th>
                    <th class="custom-header">Dept.</th>
                    <th class="custom-header">Seksi</th>
                    <th class="custom-header">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($checksheets)) : ?>
                    <?php foreach ($checksheets as $index => $row) : ?>
                        <tr>
                            <td><?= (($currentPage - 1) * 10) + $index + 1 ?></td>
                            <td><?= esc($row['mesin']) ?></td>
                            <td><?= date('m-Y', strtotime($row['bulan'])) ?></td>
                            <td>
                                <?php
                                $warna = 'bg-secondary'; // Default warna
                                if ($row['departemen'] == 'MTN') $warna = 'bg-success';
                                if ($row['departemen'] == 'PRD') $warna = 'bg-primary';
                                if ($row['departemen'] == 'QA') $warna = 'bg-danger';
                                ?>
                                <span class="badge <?= $warna ?>"><?= esc($row['departemen']) ?></span>
                            </td>
                            <td>
                                <?php
                                $warna = 'bg-secondary'; // Default warna
                                if ($row['seksi'] == 'Prod. 1') $warna = 'bg-warning';
                                if ($row['seksi'] == 'Prod. 2') $warna = 'bg-primary';
                                if ($row['seksi'] == 'Prod. 3') $warna = 'bg-danger';
                                ?>
                                <span class="badge <?= $warna ?>"><?= esc($row['seksi']) ?></span>
                            </td>
                            <td>
                                <a href="/checksheet/table/<?= $row['id'] ?>" class="btn btn-info btn-sm">Detail</a>
                                <a href="/checksheet/edit/<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <form action="/checksheet/delete/<?= $row['id'] ?>" method="post" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="d-flex justify-content-end mt-3">
            <?= $pager ?>
        </div>
    </div>
</main>

<!-- Modal Tambah Data -->
<div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahModalLabel">Tambah Checksheet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/checksheet/store" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="mesin" class="form-label">Mesin</label>
                        <select class="form-select" id="mesin" name="mesin">
                            <option value="" selected>Pilih Mesin</option>
                            <?php foreach ($masters as $master): ?>
                                <?php $mesinList = json_decode($master['mesin'], true); ?>
                                <?php foreach ($mesinList as $index => $mesin): ?>
                                    <option value="<?= $master['id'] . '|' . $index; ?>">
                                        <?= $mesin; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="bulan" class="form-label">Bulan</label>
                        <input type="month" class="form-control" id="bulan" name="bulan" placeholder="MM-YYYY">
                    </div>

                    <div class="mb-3">
                        <label for="dept" class="form-label">Departemen</label>
                        <select class="form-select" id="dept" name="departemen">
                            <option value="" selected>Pilih Departemen</option>
                            <option value="MTN">MTN</option>
                            <option value="PRD">PRD</option>
                            <option value="QA">QA</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="seksi" class="form-label">Seksi</label>
                        <select class="form-select" id="seksi" name="seksi">
                            <option value="" selected>Pilih Seksi</option>
                            <option value="Prod. 1">Prod. 1</option>
                            <option value="Prod. 2">Prod. 2</option>
                            <option value="Prod. 3">Prod. 3</option>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>