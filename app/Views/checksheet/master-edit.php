<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . ' | CBI' : 'CBI' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4 min-vh-100">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fs-7">Edit Master Checksheet Pre-Use</h3>
    </div>

    <a href="javascript:history.back()" class="btn btn-secondary mb-3">Kembali</a>

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

    <!-- Container untuk pesan error -->
    <div id="errorContainer" class="ms-3 ms-md-5 mb-3" style="max-width: 800px;">
        <!-- Pesan error akan ditampilkan di sini -->
    </div>

    <!-- Card untuk Input Judul Checksheet -->
    <div class="card ms-3 ms-md-5 mb-3" style="max-width: 800px;">
        <div class="card-body">
            <label class="form-label">Judul Checksheet</label>
            <input type="text"
                class="form-control"
                name="judul"
                id="judul_checksheet"
                value="<?= htmlspecialchars($item['judul_checksheet'] ?? '') ?>">
        </div>
    </div>

    <!-- Card untuk Input Mesin -->
    <div class="card ms-3 ms-md-5 mb-3" style="max-width: 800px;">
        <div class="card-body">
            <label class="form-label">Mesin</label>
            <div class="input-group">
                <input type="text" id="mesinInput" class="form-control" list="mesinList" placeholder="Ketik atau pilih mesin...">
                <button type="button" class="btn btn-primary" onclick="addMesin()">Tambah</button>
            </div>
            <datalist id="mesinList">
                <?php foreach ($mesinList as $mesin): ?>
                    <option value="<?= esc($mesin['name_machine']) ?>" data-id="<?= esc($mesin['id_machine']) ?>">
                        <?= esc($mesin['id_machine']) ?> - <?= esc($mesin['name_machine']) ?>
                    </option>
                <?php endforeach; ?>
            </datalist>
            <div id="selectedMesin" class="mt-2">
                <?php
                $selectedMesin = json_decode($item['mesin'] ?? '[]', true);
                foreach ($selectedMesin as $mesin) :
                ?>
                    <span class="badge bg-primary me-1 mb-1">
                        <?= htmlspecialchars($mesin) ?>
                        <button type="button" class="btn-close btn-close-white" style="font-size: 0.5em;" onclick="removeMesin('<?= htmlspecialchars($mesin) ?>')"></button>
                    </span>
                <?php endforeach; ?>
            </div>
            <div id="selectedIdMesin" class="mt-2">
                <?php
                $selectedIdMesin = json_decode($item['id_machine'] ?? '[]', true);
                foreach ($selectedIdMesin as $id_mesin) :
                ?>
                    <span class="badge bg-secondary me-1 mb-1">
                        <?= htmlspecialchars($id_mesin) ?>
                    </span>
                <?php endforeach; ?>
            </div>
            <div id="mesinError" class="text-danger mt-2 d-none"></div>
        </div>
    </div>

    <!-- Card untuk Form Utama -->
    <div class="card ms-3 ms-md-5" style="max-width: 800px;">
        <div class="card-body">
            <form id="dynamicForm" action="<?= base_url('/master/update/' . $item['id']); ?>" method="post" onsubmit="return validateForm(event)">
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
                                            id="checkboxRunHour"
                                            <?= $run_hour ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="checkboxRunHour">
                                            Run Hour
                                        </label>
                                    </div>

                                    <!-- Temperature Checkbox -->
                                    <div class="form-check">
                                        <input type="hidden" name="temperature" value="0">
                                        <input class="form-check-input" type="checkbox"
                                            name="temperature" value="1"
                                            id="checkboxTemperature"
                                            <?= $temperature ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="checkboxTemperature">
                                            Temperature
                                        </label>
                                    </div>
                                    
                                    <!-- Temperature Checkbox -->
                                    <div class="form-check">
                                        <input type="hidden" name="run_load" value="0">
                                        <input class="form-check-input" type="checkbox"
                                            name="run_load" value="1"
                                            id="checkboxRunLoad"
                                            <?= $run_load ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="checkboxRunLoad">
                                            Running Load
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="judul" id="judul_checksheet_hidden" value="<?= htmlspecialchars($item['judul_checksheet'] ?? '') ?>">
                <input type="hidden" name="mesin" id="mesinData" value='<?= json_encode($selectedMesin) ?>'>
                <input type="hidden" name="mesin_id" id="idMachineData" value='<?= json_encode($selectedIdMesin) ?>'>
                <div id="formContainer">
                    <?php if (!empty($itemChecks)) : ?>
                        <?php foreach ($itemChecks as $index => $check) : ?>
                            <div class="row mb-3 form-group item-row">
                                <div class="col-md-4">
                                    <label class="form-label">Item Check</label>
                                    <input type="text" class="form-control" name="item_check[]" value="<?= htmlspecialchars($check) ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Inspeksi</label>
                                    <input type="text" class="form-control" name="inspeksi[]" value="<?= htmlspecialchars($inspeksiList[$index] ?? '') ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Standar</label>
                                    <input type="text" class="form-control" name="standar[]" value="<?= htmlspecialchars($standarList[$index] ?? '') ?>">
                                </div>
                                <div class="col-md-1 d-flex align-items-end mb-2">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeleteRow(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="row mb-3 form-group item-row">
                            <div class="col-md-4">
                                <label class="form-label">Item Check</label>
                                <input type="text" class="form-control" name="item_check[]">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Inspeksi</label>
                                <input type="text" class="form-control" name="inspeksi[]">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Standar</label>
                                <input type="text" class="form-control" name="standar[]">
                            </div>
                            <div class="col-md-1 d-flex align-items-end mb-2">
                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeleteRow(this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-success me-2" onclick="addForm()">Tambah</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    // Inisialisasi data mesin dari database
    const mesinList = <?= json_encode($mesinList) ?>;
    document.getElementById("judul_checksheet").addEventListener("input", function() {
        document.getElementById("judul_checksheet_hidden").value = this.value;
    });

    let selectedMesin = <?= json_encode($selectedMesin) ?>;
    let selectedIdMesin = <?= json_encode($selectedIdMesin) ?>;

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
        let mesinNama = input.value.trim();
        let errorEl = document.getElementById("mesinError");

        let mesinListFiltered = mesinList.filter(m => m.name_machine === mesinNama || m.id_machine === mesinNama);

        if (mesinListFiltered.length === 0) {
            errorEl.textContent = "Mesin tidak valid. Pilih dari daftar yang tersedia.";
            errorEl.classList.remove("d-none");
            return;
        }

        let mesinToAdd = mesinListFiltered.find(m => !selectedIdMesin.includes(m.id_machine));

        if (!mesinToAdd) {
            errorEl.textContent = "Semua mesin dengan nama/ID ini sudah dipilih sebelumnya.";
            errorEl.classList.remove("d-none");
            return;
        }

        selectedIdMesin.push(mesinToAdd.id_machine);

        updateMesinDisplay();
        input.value = "";
        errorEl.classList.add("d-none");
    }

    function removeMesin(id_machine) {
        selectedIdMesin = selectedIdMesin.filter(id => id !== id_machine);
        updateMesinDisplay();
    }

    function updateMesinDisplay() {
        let mesinContainer = document.getElementById("selectedMesin");
        let idMesinContainer = document.getElementById("selectedIdMesin");
        let mesinDataInput = document.getElementById("mesinData");
        let idMachineDataInput = document.getElementById("idMachineData");

        mesinContainer.innerHTML = "";
        idMesinContainer.innerHTML = "";

        selectedIdMesin.forEach(id => {
            let mesin = mesinList.find(m => m.id_machine === id);

            // Badge nama mesin
            let badge = document.createElement("span");
            badge.classList.add("badge", "bg-primary", "me-1", "mb-1");
            badge.innerHTML = `${mesin.name_machine} <button type="button" class="btn-close btn-close-white" style="font-size: 0.5em;" onclick="removeMesin('${id}')"></button>`;
            mesinContainer.appendChild(badge);

            // Badge ID mesin
            let badgeId = document.createElement("span");
            badgeId.classList.add("badge", "bg-secondary", "me-1", "mb-1");
            badgeId.textContent = id;
            idMesinContainer.appendChild(badgeId);
        });

        // Kirim data ke hidden input (kalau perlu simpan nama mesin juga)
        let selectedNamaMesin = selectedIdMesin.map(id => {
            let mesin = mesinList.find(m => m.id_machine === id);
            return mesin ? mesin.name_machine : "";
        });

        mesinDataInput.value = JSON.stringify(selectedNamaMesin);
        idMachineDataInput.value = JSON.stringify(selectedIdMesin);
    }

    function addForm() {
        let container = document.getElementById("formContainer");
        let newRow = document.createElement("div");
        newRow.className = "row mb-3 form-group item-row";
        newRow.innerHTML = `
            <div class="col-md-4">
                <label class="form-label">Item Check</label>
                <input type="text" class="form-control" name="item_check[]">
            </div>
            <div class="col-md-4">
                <label class="form-label">Inspeksi</label>
                <input type="text" class="form-control" name="inspeksi[]">
            </div>
            <div class="col-md-3">
                <label class="form-label">Standar</label>
                <input type="text" class="form-control" name="standar[]">
            </div>
            <div class="col-md-1 d-flex align-items-end mb-2">
                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeleteRow(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(newRow);
    }

    // Ganti fungsi validateForm yang ada dengan yang baru
    function validateForm(event) {
        event.preventDefault();
        let isValid = true;
        let errorMessages = [];

        // Reset error container
        const errorContainer = document.getElementById('errorContainer');
        errorContainer.innerHTML = '';

        // Validasi judul
        const judul = document.getElementById("judul_checksheet");
        if (!judul.value.trim()) {
            judul.classList.add('is-invalid');
            errorMessages.push("Judul checksheet harus diisi!");
            isValid = false;
        } else {
            judul.classList.remove('is-invalid');
        }

        // Validasi mesin
        const mesinInput = document.getElementById("mesinInput");
        if (selectedMesin.length === 0) {
            mesinInput.classList.add('is-invalid');
            document.getElementById("mesinError").textContent = "Minimal harus memilih satu mesin!";
            document.getElementById("mesinError").classList.remove("d-none");
            errorMessages.push("Minimal harus memilih satu mesin!");
            isValid = false;
        } else {
            mesinInput.classList.remove('is-invalid');
            document.getElementById("mesinError").classList.add("d-none");
        }

        // Validasi item check, inspeksi, dan standar
        const rows = document.getElementsByClassName('item-row');
        if (rows.length === 0) {
            errorMessages.push("Minimal harus ada satu item check!");
            isValid = false;
        }

        for (let i = 0; i < rows.length; i++) {
            const itemCheck = rows[i].querySelector('input[name="item_check[]"]');
            const inspeksi = rows[i].querySelector('input[name="inspeksi[]"]');
            const standar = rows[i].querySelector('input[name="standar[]"]');

            // Validasi Item Check
            if (!itemCheck.value.trim()) {
                itemCheck.classList.add('is-invalid');
                errorMessages.push(`Item Check baris ke-${i + 1} harus diisi!`);
                if (!itemCheck.nextElementSibling) {
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = `Item Check baris ke-${i + 1} harus diisi!`;
                    itemCheck.parentNode.appendChild(feedback);
                }
                isValid = false;
            } else {
                itemCheck.classList.remove('is-invalid');
            }

            // Validasi Inspeksi
            if (!inspeksi.value.trim()) {
                inspeksi.classList.add('is-invalid');
                errorMessages.push(`Inspeksi baris ke-${i + 1} harus diisi!`);
                if (!inspeksi.nextElementSibling) {
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = `Inspeksi baris ke-${i + 1} harus diisi!`;
                    inspeksi.parentNode.appendChild(feedback);
                }
                isValid = false;
            } else {
                inspeksi.classList.remove('is-invalid');
            }

            // Validasi Standar
            if (!standar.value.trim()) {
                standar.classList.add('is-invalid');
                errorMessages.push(`Standar baris ke-${i + 1} harus diisi!`);
                if (!standar.nextElementSibling) {
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = `Standar baris ke-${i + 1} harus diisi!`;
                    standar.parentNode.appendChild(feedback);
                }
                isValid = false;
            } else {
                standar.classList.remove('is-invalid');
            }
        }

        // Tampilkan alert jika ada error
        if (!isValid) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger mb-0';
            alertDiv.innerHTML = `
                <strong>Mohon perbaiki kesalahan berikut:</strong>
                <ul class="mb-0">
                    ${errorMessages.map(msg => `<li>${msg}</li>`).join('')}
                </ul>
            `;

            // Tampilkan error di container
            const errorContainer = document.getElementById('errorContainer');
            errorContainer.innerHTML = '';
            errorContainer.appendChild(alertDiv);

            // Scroll ke error container
            errorContainer.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            return false;
        }

        // Submit form jika validasi berhasil
        document.getElementById("dynamicForm").submit();
    }

    // Tambahkan event listener untuk menghapus pesan error saat input berubah
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('form-control')) {
            e.target.classList.remove('is-invalid');
            const errorContainer = document.getElementById('errorContainer');
            if (errorContainer) {
                errorContainer.innerHTML = '';
            }
        }
    });

    // Modifikasi fungsi confirmDeleteRow
    function confirmDeleteRow(button) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) {
            if (confirm('Apakah Anda yakin ingin menghapus baris ini? Data yang sudah dihapus tidak dapat dikembalikan.')) {
                button.closest('.item-row').remove();
            }
        } else {
            alert('Minimal harus ada satu baris item check! Tidak dapat menghapus baris terakhir.');
        }
    }
</script>

<?= $this->endSection() ?>