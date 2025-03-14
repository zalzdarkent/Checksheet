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

    <!-- Card untuk Input Mesin -->
    <div class="card ms-3 ms-md-5 mb-3" style="max-width: 800px;">
        <div class="card-body">
            <label class="form-label">Mesin</label>
            <div class="border p-2 rounded" id="mesinContainer">
                <input type="text" id="mesinInput" class="form-control border-0" placeholder="Ketik atau pilih mesin..." onkeydown="handleKeyDown(event)">
                <div id="selectedMesin" class="mt-2"></div>
            </div>
        </div>
    </div>

    <!-- Card untuk Form Utama -->
    <div class="card ms-3 ms-md-5" style="max-width: 800px;">
        <div class="card-body">
            <form id="dynamicForm" action="/master-checksheet/store" method="post">
                <?= csrf_field() ?>
                <div id="formContainer">
                    <input type="hidden" name="mesin" id="mesinData">
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
    let mesinList = []; // List mesin yang bisa dipilih
    let selectedMesin = [];

    function handleKeyDown(event) {
        let input = document.getElementById("mesinInput");
        let value = input.value.trim();

        if (event.key === "Enter" && value !== "") {
            event.preventDefault();
            addMesin(value);
        }
    }

    function addMesin(mesin) {
        if (!selectedMesin.includes(mesin)) {
            selectedMesin.push(mesin);

            let badge = document.createElement("span");
            badge.classList.add("badge", "bg-primary", "me-1");
            badge.textContent = mesin;

            let removeBtn = document.createElement("button");
            removeBtn.classList.add("btn-close", "btn-close-white", "ms-1");
            removeBtn.style.fontSize = "10px";
            removeBtn.onclick = function() {
                removeMesin(mesin, badge);
            };

            badge.appendChild(removeBtn);
            document.getElementById("selectedMesin").appendChild(badge);
        }
        document.getElementById("mesinInput").value = "";
    }

    function removeMesin(mesin, badge) {
        selectedMesin = selectedMesin.filter(item => item !== mesin);
        badge.remove();
    }

    function addForm() {
        let formContainer = document.getElementById("formContainer");
        let newForm = document.createElement("div");
        newForm.classList.add("row", "mb-3", "form-group");

        newForm.innerHTML = `
            <div class="col-md-4">
                <label class="form-label">Item Check</label>
                <input type="text" class="form-control" name="item_check[]">
            </div>
            <div class="col-md-4">
                <label class="form-label">Inspeksi</label>
                <input type="text" class="form-control" name="inspeksi[]">
            </div>
            <div class="col-md-4">
                <label class="form-label">Standar</label>
                <input type="text" class="form-control" name="standar[]">
            </div>
        `;

        formContainer.appendChild(newForm);
    }
</script>

<?= $this->endSection() ?>