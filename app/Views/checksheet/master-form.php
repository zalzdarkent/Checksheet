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
            <input type="text" id="judul_checksheet" name="judul_checksheet" class="form-control" placeholder="Masukkan judul checksheet..." required>
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
                    <?php endforeach; ?>
            </datalist>
            <div id="selectedMesin" class="mt-2"></div>
            <div id="selectedIdMesin" class="mt-2"></div>
        </div>
        <small id="mesinError" class="text-danger d-none">Mesin tidak valid. Pilih dari daftar yang tersedia.</small>
    </div>

    <!-- Card untuk Form Utama -->
    <div class="card ms-3 ms-md-5" style="max-width: 800px;">
        <div class="card-body">
            <form id="dynamicForm" action="/master/store" method="post">
                <?= csrf_field() ?>
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
        let mesinNama = input.value.trim();
        let errorEl = document.getElementById("mesinError");

        // Cari mesin dari list
        let mesinObj = mesinList.find(m => m.name_machine === mesinNama);

        if (!mesinObj) {
            errorEl.textContent = "Mesin tidak valid. Pilih dari daftar yang tersedia.";
            errorEl.classList.remove("d-none");
            return;
        }

        if (!selectedMesin.find(m => m.name_machine === mesinNama)) {
            selectedMesin.push({
                name_machine: mesinObj.name_machine,
                id_machine: mesinObj.id_machine
            });
            updateMesinDisplay();
            input.value = "";
            errorEl.classList.add("d-none");
        }
    }

    function removeMesin(mesinNama) {
        selectedMesin = selectedMesin.filter(item => itfem.name_machine !== mesinNama);
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
            badge.innerHTML = `${mesin.name_machine} <button type="button" class="btn-close btn-close-white" style="font-size: 0.5em;" onclick="removeMesin('${mesin.name_machine}')"></button>`;
            mesinContainer.appendChild(badge);

            // Badge ID mesin
            let badgeId = document.createElement("span");
            badgeId.classList.add("badge", "bg-secondary", "me-1", "mb-1");
            badgeId.textContent = `${mesin.id_machine}`;
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
        `;
        container.appendChild(newRow);
    }
</script>

<?= $this->endSection() ?>