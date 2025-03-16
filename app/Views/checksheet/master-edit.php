<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4 min-vh-100">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fs-7">Edit Master Checksheet Pre-Use</h3>
    </div>

    <a href="javascript:history.back()" class="btn btn-secondary mb-3">Kembali</a>

    <!-- Card untuk Input Mesin -->
    <div class="card ms-3 ms-md-5 mb-3" style="max-width: 800px;">
        <div class="card-body">
            <label class="form-label">Mesin</label>
            <div class="border p-2 rounded" id="mesinContainer">
                <input type="text" id="mesinInput" class="form-control border-0" placeholder="Ketik atau pilih mesin..." onkeydown="handleKeyDown(event)">
                <div id="selectedMesin" class="mt-2">
                    <?php
                    $selectedMesin = json_decode($item['mesin'] ?? '[]', true);
                    foreach ($selectedMesin as $mesin) :
                    ?>
                        <span class="badge bg-primary me-1">
                            <?= htmlspecialchars($mesin) ?>
                            <button type="button" class="btn-close btn-close-white ms-1" style="font-size: 10px;" onclick="removeMesin('<?= htmlspecialchars($mesin) ?>', this)"></button>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Card untuk Form Utama -->
    <div class="card ms-3 ms-md-5" style="max-width: 800px;">
        <div class="card-body">
            <form id="dynamicForm" action="<?= base_url('/master/update/' . $item['id']); ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="mesin" id="mesinData" value='<?= json_encode($selectedMesin) ?>'>

                <div id="formContainer">
                    <?php
                    $itemChecks = is_array($item['item_check']) ? $item['item_check'] : explode(',', $item['item_check']);
                    $inspeksiList = is_array($item['inspeksi']) ? $item['inspeksi'] : explode(',', $item['inspeksi']);
                    $standarList = is_array($item['standar']) ? $item['standar'] : explode(',', $item['standar']);

                    if (!is_array($itemChecks)) {
                        $itemChecks = [];
                    }
                    if (!is_array($inspeksiList)) {
                        $inspeksiList = [];
                    }
                    if (!is_array($standarList)) {
                        $standarList = [];
                    }

                    foreach ($itemChecks as $index => $check) :
                    ?>
                        <div class="row mb-3 form-group">
                            <div class="col-md-4">
                                <label class="form-label">Item Check</label>
                                <input type="text" class="form-control" name="item_check[]" value="<?= htmlspecialchars($check) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Inspeksi</label>
                                <input type="text" class="form-control" name="inspeksi[]" value="<?= htmlspecialchars($inspeksiList[$index] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Standar</label>
                                <input type="text" class="form-control" name="standar[]" value="<?= htmlspecialchars($standarList[$index] ?? '') ?>">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-success me-2" onclick="addForm()">Tambah</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    let selectedMesin = <?= json_encode($selectedMesin) ?>;

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
        document.getElementById("mesinData").value = JSON.stringify(selectedMesin);
    }

    function removeMesin(mesin, badge) {
        selectedMesin = selectedMesin.filter(item => item !== mesin);
        badge.remove();
        document.getElementById("mesinData").value = JSON.stringify(selectedMesin);
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