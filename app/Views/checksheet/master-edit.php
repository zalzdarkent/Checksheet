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

    <!-- Card untuk Input Judul Checksheet -->
    <div class="card ms-3 ms-md-5 mb-3" style="max-width: 800px;">
        <div class="card-body">
            <label class="form-label">Judul Checksheet</label>
            <input type="text" class="form-control" name="judul" id="judul_checksheet" value="<?= htmlspecialchars($item['judul_checksheet'] ?? '') ?>">
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

        // Cari mesin dari list
        let mesinObj = mesinList.find(m => m.name_machine === mesinNama);

        if (!mesinObj) {
            errorEl.textContent = "Mesin tidak valid. Pilih dari daftar yang tersedia.";
            errorEl.classList.remove("d-none");
            return;
        }

        // Cek apakah mesin sudah dipilih sebelumnya
        if (selectedMesin.includes(mesinNama)) {
            errorEl.textContent = "Mesin ini sudah dipilih sebelumnya. Silakan pilih mesin lain.";
            errorEl.classList.remove("d-none");
            return;
        }

        selectedMesin.push(mesinNama);
        selectedIdMesin.push(mesinObj.id_machine);
        updateMesinDisplay();
        input.value = "";
        errorEl.classList.add("d-none");
    }

    function removeMesin(mesinNama) {
        const index = selectedMesin.indexOf(mesinNama);
        if (index !== -1) {
            selectedMesin.splice(index, 1);
            selectedIdMesin.splice(index, 1);
            updateMesinDisplay();
        }
    }

    function updateMesinDisplay() {
        let mesinContainer = document.getElementById("selectedMesin");
        let idMesinContainer = document.getElementById("selectedIdMesin");
        let mesinDataInput = document.getElementById("mesinData");
        let idMachineDataInput = document.getElementById("idMachineData");

        mesinContainer.innerHTML = "";
        idMesinContainer.innerHTML = "";

        selectedMesin.forEach((mesin, index) => {
            // Badge nama mesin
            let badge = document.createElement("span");
            badge.classList.add("badge", "bg-primary", "me-1", "mb-1");
            badge.innerHTML = `${mesin} <button type="button" class="btn-close btn-close-white" style="font-size: 0.5em;" onclick="removeMesin('${mesin}')"></button>`;
            mesinContainer.appendChild(badge);

            // Badge ID mesin
            let badgeId = document.createElement("span");
            badgeId.classList.add("badge", "bg-secondary", "me-1", "mb-1");
            badgeId.textContent = selectedIdMesin[index];
            idMesinContainer.appendChild(badgeId);
        });

        mesinDataInput.value = JSON.stringify(selectedMesin);
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

    function confirmDeleteRow(button) {
        if (document.querySelectorAll('.item-row').length > 1) {
            if (confirm('Apakah Anda yakin ingin menghapus baris ini?')) {
                button.closest('.item-row').remove();
            }
        } else {
            alert('Minimal harus ada satu baris item check!');
        }
    }

    function validateForm(event) {
        event.preventDefault();

        // Validasi judul
        const judul = document.getElementById("judul_checksheet").value.trim();
        if (!judul) {
            alert("Judul checksheet harus diisi!");
            return false;
        }

        // Validasi mesin
        if (selectedMesin.length === 0) {
            alert("Minimal harus memilih satu mesin!");
            return false;
        }

        // Validasi item check, inspeksi, dan standar
        const itemChecks = document.getElementsByName("item_check[]");
        const inspeksi = document.getElementsByName("inspeksi[]");
        const standar = document.getElementsByName("standar[]");

        for (let i = 0; i < itemChecks.length; i++) {
            if (!itemChecks[i].value.trim()) {
                alert("Item Check tidak boleh kosong!");
                itemChecks[i].focus();
                return false;
            }
            if (!inspeksi[i].value.trim()) {
                alert("Inspeksi tidak boleh kosong!");
                inspeksi[i].focus();
                return false;
            }
            if (!standar[i].value.trim()) {
                alert("Standar tidak boleh kosong!");
                standar[i].focus();
                return false;
            }
        }

        // Jika semua validasi berhasil, submit form
        document.getElementById("dynamicForm").submit();
    }
</script>

<?= $this->endSection() ?>