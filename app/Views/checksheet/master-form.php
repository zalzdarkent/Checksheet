<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . '| CBI' : 'CBI' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4 min-vh-100">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fs-7">Tambah Master Checksheet Pre-Use</h3>
    </div>

    <a href="javascript:history.back()" class="btn btn-secondary mb-3">Kembali</a>

    <!-- Card untuk Input Judul Checksheet -->
    <div class="card ms-3 ms-md-5 mb-3" style="max-width: 800px;">
        <div class="card-body">
            <label class="form-label">Judul Checksheet</label>
            <input type="text"
                id="judul_checksheet"
                name="judul_checksheet"
                class="form-control <?= session('errors.judul_checksheet') ? 'is-invalid' : '' ?>"
                placeholder="Masukkan judul checksheet..."
                value="<?= old('judul_checksheet') ?>"
                required>
            <?php if (session('errors.judul_checksheet')) : ?>
                <div class="invalid-feedback">
                    <?= session('errors.judul_checksheet') ?>
                </div>
            <?php endif ?>
        </div>
    </div>

    <!-- Card untuk Input Mesin -->
    <!-- filepath: d:\Aplikasi-Codeigniter\new-checksheet\app\Views\checksheet\master-form.php -->
    <div class="card ms-3 ms-md-5 mb-3" style="max-width: 800px;">
        <div class="card-body">
            <label class="form-label">Mesin</label>
            <div class="input-group">
                <input type="text" id="mesinInput" class="form-control" list="mesinList" placeholder="Ketik ID atau nama mesin...">
                <button type="button" class="btn btn-primary" onclick="addMesin()">Tambah</button>
            </div>
            <datalist id="mesinList">
                <?php foreach ($mesinList as $mesin): ?>
                    <option value="<?= esc($mesin['name_machine']) ?>" data-id="<?= esc($mesin['id_machine']) ?>">
                        <?= esc($mesin['id_machine']) ?> - <?= esc($mesin['name_machine']) ?>
                    </option>
                <?php endforeach; ?>
            </datalist>
            <div id="selectedMesin" class="mt-2"></div>
            <div id="selectedIdMesin" class="mt-2"></div>
            <div id="mesinError" class="text-danger mt-2 d-none"></div>
        </div>
    </div>

    <!-- Card untuk Input Checkbox Horizontal -->

    <!-- Card untuk Form Utama -->
    <div class="card ms-3 ms-md-5" style="max-width: 800px;">
        <div class="card-body">
            <form id="dynamicForm" action="<?= base_url() ?>/master/store" method="post">
                <?= csrf_field() ?>

                <div class="card mb-3" style="max-width: 800px;">
                    <div class="card-body">
                        <h6 class="card-title mb-3">Tipe Pengecekan</h6>
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-4">
                                    <!-- Run Hour Checkbox -->
                                    <div class="form-check">
                                        <input type="hidden" name="run_hour" value="0">
                                        <input class="form-check-input" type="checkbox"
                                            name="run_hour" value="1"
                                            id="checkboxRunHour">
                                        <label class="form-check-label" for="checkboxRunHour">
                                            Run Hour
                                        </label>
                                    </div>

                                    <!-- Temperature Checkbox -->
                                    <div class="form-check">
                                        <input type="hidden" name="temperature" value="0">
                                        <input class="form-check-input" type="checkbox"
                                            name="temperature" value="1"
                                            id="checkboxTemperature">
                                        <label class="form-check-label" for="checkboxTemperature">
                                            Temperature
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="formContainer">
                    <input type="hidden" name="judul_checksheet" id="judul_checksheet_hidden">
                    <input type="hidden" name="mesin" id="mesinData">
                    <input type="hidden" name="id_machine" id="idMachineData">
                    <div class="row mb-3 form-group">
                        <div class="col-md-4">
                            <label for="item_check" class="form-label">Item Check</label>
                            <input type="text" class="form-control" name="item_check[]">
                        </div>
                        <div class="col-md-4">
                            <label for="inspeksi" class="form-label">Inspeksi</label>
                            <input type="text" class="form-control" name="inspeksi[]">
                        </div>
                        <div class="col-md-4">
                            <label for="standar" class="form-label">Standar</label>
                            <input type="text" class="form-control" name="standar[]">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-success me-2" onclick="addForm()">Tambah</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Menampilkan Pesan Error -->
    <?php if (session('errors')) : ?>
        <div class="alert alert-danger ms-3 ms-md-5 mt-3" style="max-width: 800px;">
            <ul class="mb-0">
                <?php foreach (session('errors') as $error) : ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>
</main>

<script>
    const mesinList = <?= json_encode($mesinList) ?>;
    document.getElementById("judul_checksheet").addEventListener("input", function() {
        document.getElementById("judul_checksheet_hidden").value = this.value;
    });

    let selectedMesin = [];

    // Handle enter key pada input mesin
    document.getElementById("mesinInput").addEventListener("keydown", function(event) {
        if (event.key === "Enter") {
            event.preventDefault();
            addMesin();
        }
    });

    document.getElementById("mesinInput").addEventListener("input", function() {
        document.getElementById("mesinError").classList.add("d-none");
    });

    function addMesin() {
        let input = document.getElementById("mesinInput");
        let mesinInput = input.value.trim();
        let errorEl = document.getElementById("mesinError");

        // Cari mesin berdasarkan name_machine atau id_machine
        let mesinObj = mesinList.find(m => m.name_machine === mesinInput || m.id_machine === mesinInput);

        if (!mesinObj) {
            // Tampilkan pesan error jika mesin tidak ditemukan
            errorEl.textContent = "Mesin tidak ditemukan. Pastikan ID atau nama mesin sesuai dengan daftar.";
            errorEl.classList.remove("d-none");
            return;
        }

        // Cek apakah mesin sudah dipilih sebelumnya
        if (selectedMesin.find(m => m.id_machine === mesinObj.id_machine)) {
            errorEl.textContent = "Mesin ini sudah dipilih sebelumnya. Silakan pilih mesin lain.";
            errorEl.classList.remove("d-none");
            return;
        }

        selectedMesin.push({
            name_machine: mesinObj.name_machine,
            id_machine: mesinObj.id_machine
        });
        updateMesinDisplay();
        input.value = "";
        errorEl.classList.add("d-none");
    }

    function removeMesin(idMachine) {
        selectedMesin = selectedMesin.filter(item => item.id_machine !== idMachine);
        updateMesinDisplay();
    }

    function updateMesinDisplay() {
        let mesinContainer = document.getElementById("selectedMesin");
        let idMesinContainer = document.getElementById("selectedIdMesin");
        let mesinDataInput = document.getElementById("mesinData");
        let idMachineDataInput = document.getElementById("idMachineData");

        mesinContainer.innerHTML = "";
        idMesinContainer.innerHTML = "";

        selectedMesin.forEach(mesin => {
            // Badge nama mesin
            let badge = document.createElement("span");
            badge.classList.add("badge", "bg-primary", "me-1", "mb-1");
            badge.innerHTML = `${mesin.name_machine} <button type="button" class="btn-close btn-close-white" style="font-size: 0.5em;" onclick="removeMesin('${mesin.id_machine}')"></button>`;
            mesinContainer.appendChild(badge);

            // Badge ID mesin
            let badgeId = document.createElement("span");
            badgeId.classList.add("badge", "bg-secondary", "me-1", "mb-1");
            badgeId.textContent = mesin.id_machine;
            idMesinContainer.appendChild(badgeId);
        });

        mesinDataInput.value = JSON.stringify(selectedMesin.map(m => m.name_machine));
        idMachineDataInput.value = JSON.stringify(selectedMesin.map(m => m.id_machine));
    }

    function addForm() {
        let container = document.getElementById("formContainer");
        let newRow = document.createElement("div");
        newRow.className = "row mb-3 form-group";
        newRow.innerHTML = `
            <div class="col-md-4">
                <label for="item_check" class="form-label">Item Check</label>
                <input type="text" class="form-control" name="item_check[]" required>
                <div class="invalid-feedback">Item check harus diisi</div>
            </div>
            <div class="col-md-4">
                <label for="inspeksi" class="form-label">Inspeksi</label>
                <input type="text" class="form-control" name="inspeksi[]" required>
                <div class="invalid-feedback">Inspeksi harus diisi</div>
            </div>
            <div class="col-md-4">
                <label for="standar" class="form-label">Standar</label>
                <input type="text" class="form-control" name="standar[]" required>
                <div class="invalid-feedback">Standar harus diisi</div>
            </div>
        `;
        container.appendChild(newRow);
    }

    // Ubah event listener submit pada form
    document.getElementById('dynamicForm').addEventListener('submit', function(e) {
    let isValid = true;
    
    // Validasi judul
    const judulInput = document.getElementById('judul_checksheet');
    if (!judulInput.value.trim()) {
        judulInput.classList.add('is-invalid');
        isValid = false;
    } else {
        judulInput.classList.remove('is-invalid');
    }

    // Validasi mesin
    const mesinInput = document.getElementById('mesinInput');
    const errorEl = document.getElementById('mesinError');
    
    if (selectedMesin.length === 0) {
        if (mesinInput.value.trim()) {
            // Ada text di input tapi belum di-add
            errorEl.textContent = 'Klik tombol Tambah atau tekan Enter untuk menambahkan mesin yang dipilih';
            errorEl.classList.remove('d-none');
            mesinInput.classList.add('is-invalid');
        } else {
            // Input kosong dan tidak ada mesin yang dipilih
            errorEl.textContent = 'Minimal satu mesin harus dipilih';
            errorEl.classList.remove('d-none');
            mesinInput.classList.add('is-invalid');
        }
        isValid = false;
    } else {
        mesinInput.classList.remove('is-invalid');
    }

    // Validasi item check, inspeksi, dan standar
    const itemChecks = document.getElementsByName('item_check[]');
    const inspeksi = document.getElementsByName('inspeksi[]');
    const standar = document.getElementsByName('standar[]');
    
    for (let i = 0; i < itemChecks.length; i++) {
        // Validasi Item Check
        if (!itemChecks[i].value.trim()) {
            itemChecks[i].classList.add('is-invalid');
            if (!itemChecks[i].nextElementSibling) {
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = 'Item check harus diisi';
                itemChecks[i].parentNode.appendChild(feedback);
            }
            isValid = false;
        } else {
            itemChecks[i].classList.remove('is-invalid');
        }

        // Validasi Inspeksi
        if (!inspeksi[i].value.trim()) {
            inspeksi[i].classList.add('is-invalid');
            if (!inspeksi[i].nextElementSibling) {
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = 'Inspeksi harus diisi';
                inspeksi[i].parentNode.appendChild(feedback);
            }
            isValid = false;
        } else {
            inspeksi[i].classList.remove('is-invalid');
        }

        // Validasi Standar
        if (!standar[i].value.trim()) {
            standar[i].classList.add('is-invalid');
            if (!standar[i].nextElementSibling) {
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = 'Standar harus diisi';
                standar[i].parentNode.appendChild(feedback);
            }
            isValid = false;
        } else {
            standar[i].classList.remove('is-invalid');
        }
    }

    if (!isValid) {
        e.preventDefault();
    }
});

// Tambahkan event listener untuk menghapus class invalid saat input diubah
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('form-control')) {
        if (e.target.value.trim()) {
            e.target.classList.remove('is-invalid');
        }
    }
});

// Tambahkan event listener untuk input mesin
document.getElementById("mesinInput").addEventListener("input", function() {
    // Hapus pesan error hanya jika input kosong
    if (!this.value.trim()) {
        document.getElementById("mesinError").classList.add("d-none");
        this.classList.remove('is-invalid');
    }
});
</script>

<?= $this->endSection() ?>