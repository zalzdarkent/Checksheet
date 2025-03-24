<?= $this->extend('layouts/app') ?>

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
                <option value="PW">
                <option value="ALT">
                <option value="COS">
                <option value="HSM">
                <option value="ENV">
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
    document.getElementById("judul_checksheet").addEventListener("input", function() {
        document.getElementById("judul_checksheet_hidden").value = this.value;
    });

    let selectedMesin = <?= json_encode($selectedMesin) ?>;

    // Handle enter key pada input mesin
    document.getElementById("mesinInput").addEventListener("keydown", function(event) {
        if (event.key === "Enter") {
            event.preventDefault();
            addMesin();
        }
    });

    function addMesin() {
        let input = document.getElementById("mesinInput");
        let mesin = input.value.trim();
        
        if (mesin && !selectedMesin.includes(mesin)) {
            selectedMesin.push(mesin);
            updateMesinDisplay();
            input.value = "";
        }
    }

    function removeMesin(mesin) {
        selectedMesin = selectedMesin.filter(item => item !== mesin);
        updateMesinDisplay();
    }

    function updateMesinDisplay() {
        let container = document.getElementById("selectedMesin");
        let mesinData = document.getElementById("mesinData");
        
        // Update tampilan badge
        container.innerHTML = "";
        selectedMesin.forEach(mesin => {
            let badge = document.createElement("span");
            badge.classList.add("badge", "bg-primary", "me-1", "mb-1");
            badge.innerHTML = `${mesin} <button type="button" class="btn-close btn-close-white" style="font-size: 0.5em;" onclick="removeMesin('${mesin}')"></button>`;
            container.appendChild(badge);
        });

        // Update hidden input
        mesinData.value = JSON.stringify(selectedMesin);
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