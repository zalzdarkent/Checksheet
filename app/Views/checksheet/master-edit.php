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
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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

    function confirmDeleteRow(button) {
        Swal.fire({
            title: 'Hapus baris?',
            text: "Apakah Anda yakin ingin menghapus baris ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const row = button.closest('.item-row');
                if (document.querySelectorAll('.item-row').length > 1) {
                    row.remove();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Minimal harus ada satu baris data!'
                    });
                }
            }
        });
    }

    function addForm() {
        const container = document.getElementById("formContainer");
        const newRow = document.createElement("div");
        newRow.classList.add("row", "mb-3", "form-group", "item-row");
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

    function validateForm(event) {
        event.preventDefault();

        // Validasi judul
        const judul = document.getElementById("judul_checksheet").value.trim();
        if (!judul) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Judul checksheet harus diisi!'
            });
            return false;
        }

        // Validasi mesin
        if (selectedMesin.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Minimal satu mesin harus dipilih!'
            });
            return false;
        }

        // Validasi form items
        let valid = true;
        document.querySelectorAll('.item-row').forEach(row => {
            const inputs = row.querySelectorAll('input[type="text"]');
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });
        });

        if (!valid) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Semua field harus diisi!'
            });
            return false;
        }

        // Konfirmasi submit
        Swal.fire({
            title: 'Simpan Perubahan?',
            text: 'Pastikan data yang diisi sudah benar!',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('dynamicForm').submit();
            }
        });

        return false;
    }
</script>

<?= $this->endSection() ?>