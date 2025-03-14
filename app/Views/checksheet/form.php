<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . '| CBI' : 'CBI' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4 min-vh-100">
    <div class="card p-3 mt-3">
        <h5 class="text-center">Tambah Item</h5>
        <form method="post" action="/checksheet/store">
            <div class="mb-3">
                <label for="departemen" class="form-label">Departemen</label>
                <select id="departemen" name="departemen" class="form-select">
                    <option value="" disabled selected>Pilih</option>
                    <option value="Produksi">Produksi</option>
                    <option value="Maintenance">Maintenance</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="seksi" class="form-label">Seksi</label>
                <input type="text" class="form-control" id="seksi" name="seksi" placeholder="Masukkan Bagian Peralatan">
            </div>
            <div class="mb-3">
                <label for="line" class="form-label">Line</label>
                <input type="text" class="form-control" id="line" name="line" placeholder="Masukkan Line">
            </div>
            <div class="mb-3">
                <label for="mesin" class="form-label">Mesin</label>
                <input type="text" class="form-control" id="mesin" name="mesin" placeholder="Masukkan Mesin">
            </div>
            <div class="mb-3">
                <label for="bulan" class="form-label">Bulan</label>
                <select id="bulan" name="bulan" class="form-select">
                    <option value="" disabled selected>Pilih</option>
                    <option value="Januari">Januari</option>
                    <option value="Februari">Februari</option>
                </select>
            </div>
            <div class="text-center">
                <button type="button" id="tambahItemBtn" class="btn btn-primary">Tambah Item Check</button>
            </div>

            <!-- Form Tambahan, Default Disembunyikan -->
            <div id="formTambahan" style="display: none;">
                <div class="mb-3">
                    <label for="itemCheck" class="form-label">Item Check</label>
                    <input type="text" class="form-control" id="itemCheck" name="item_check" placeholder="Masukkan Item Check">
                </div>
                <div class="mb-3">
                    <label for="itemInspeksi" class="form-label">Item Inspeksi</label>
                    <input type="text" class="form-control" id="itemInspeksi" name="item_inspeksi" placeholder="Masukkan Item Inspeksi">
                </div>
                <div class="mb-3">
                    <label for="standar" class="form-label">Standar</label>
                    <input type="text" class="form-control" id="standar" name="standar" placeholder="Masukkan Standar">
                </div>
            </div>
            <div class="text-center pt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</main>

<script>
    document.getElementById('tambahItemBtn').addEventListener('click', function() {
        document.getElementById('formTambahan').style.display = 'block';
    });
</script>

<?= $this->endSection() ?>