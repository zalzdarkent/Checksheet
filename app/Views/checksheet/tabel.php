<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . '| CBI' : 'CBI' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$jumlahKolom = 31; // Jumlah kolom yang diinginkan

function OkNg($jumlahKolom)
{
    for ($i = 0; $i < $jumlahKolom; $i++) {
        echo <<<HTML
        <td class="text-center">
            <div class="d-flex justify-content-center gap-2">
                <button class="btn btn-outline-success btn-sm">OK</button>
                <button class="btn btn-outline-danger btn-sm">NG</button>
            </div>
        </td>
        HTML;
    }
}
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4">
    <h2 class="text-center">Checksheet</h2>
    <div class="card p-2 mt-3">
        <table class="table table-borderless" id="dataTable">
            <tbody>
                <tr>
                    <th class="p-1">Departemen</th>
                    <td class="p-1">: <?= esc($checksheet['departemen']) ?></td>
                    <td class="p-1"></td>
                    <th class="p-1">Mesin</th>
                    <td class="p-1">: </td>
                    <td class="p-1"></td>
                </tr>
                <tr>
                    <th class="p-1">Seksi</th>
                    <td class="p-1">: <?= esc($checksheet['seksi']) ?></td>
                    <td class="p-1"></td>
                    <th class="p-1">No Form</th>
                    <td class="p-1">:</td>
                    <td class="p-1"></td>
                </tr>
                <tr>
                    <th class="p-1">Line</th>
                    <td class="p-1">:</td>
                    <td class="p-1"></td>
                    <th class="p-1">Bulan</th>
                    <td class="p-1">: <?= esc($checksheet['bulan']) ?></td>
                    <td class="p-1"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
        <h4 class="mb-0">Table Daily Check</h4>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle text-center">
            <thead>
                <tr>
                    <th class="custom-header">No</th>
                    <th class="custom-header">Item Check</th>
                    <th class="custom-header">Item Inspeksi</th>
                    <th class="custom-header">Standar</th>
                    <?php
                    for ($i = 1; $i <= $jumlahKolom; $i++) {
                        echo "<th class='custom-header text-center align-middle'>$i</th>";
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php foreach ($masterData as $row) : ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= esc($row['item_check']); ?></td>
                        <td><?= esc($row['inspeksi']); ?></td>
                        <td><?= esc($row['standar']); ?></td>
                        <?php OkNg($jumlahKolom); ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4"><label class="fw-bold">Diisi oleh (NPK):</label></td>
                    <?php for ($i = 0; $i < $jumlahKolom; $i++) : ?>
                        <td class="text-center"><input type="text" class="form-control"></td>
                    <?php endfor; ?>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="container mt-4 pb-5">
        <div class="d-flex gap-2">
            <button class="btn btn-primary">Simpan</button>
            <button class="btn btn-success">Kirim</button>
        </div>
    </div>
</main>
<?= $this->endSection() ?>