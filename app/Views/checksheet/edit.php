<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . ' | CBI' : 'CBI' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4">
    <h2>Edit Checksheet</h2>

    <div class="card">
        <div class="card-body">
            <form action="<?=base_url()?>/checksheet/update/<?= $checksheet['id'] ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="POST">

                <div class="mb-3">
                    <label for="mesin" class="form-label">Mesin</label>
                    <select class="form-select" id="mesin" name="mesin" required onchange="updateIdMachine(this)">
                        <option value="" selected>Pilih Mesin</option>
                        <?php foreach ($masters as $master): ?>
                            <?php 
                                $mesinList = json_decode($master['mesin'], true);
                                $idMachineList = json_decode($master['id_machine'], true);
                            ?>
                            <?php foreach ($mesinList as $index => $mesin): ?>
                                <option value="<?= $master['id'] . '|' . $index; ?>" 
                                        data-id-machine="<?= $idMachineList[$index] ?? '' ?>"
                                        <?= ($mesin == $checksheet['mesin']) ? 'selected' : '' ?>>
                                    <?= $mesin; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Silakan pilih mesin</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">ID Mesin</label>
                    <div class="input-group">
                        <input type="text" class="form-control bg-light" id="idMachineInput" name="id_machine" readonly value="<?= esc($checksheet['id_machine']) ?>">
                        <span class="input-group-text bg-primary text-white" id="idMachineBadge">
                            <i class="bi bi-tag"></i> <?= esc($checksheet['id_machine']) ?>
                        </span>
                    </div>
                    <small class="text-muted">ID mesin akan otomatis terisi saat memilih mesin</small>
                </div>

                <div class="mb-3">
                    <label for="line" class="form-label">Line</label>
                    <select class="form-select" id="line" name="line" required>
                        <?php for ($i = 1; $i <= 7; $i++): ?>
                            <option value="<?= $i ?>" <?= $checksheet['line'] == $i ? 'selected' : '' ?>>Line <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="bulan" class="form-label">Bulan</label>
                    <input type="month" class="form-control" id="bulan" name="bulan" value="<?= date('Y-m', strtotime($checksheet['bulan'])) ?>">
                </div>

                <div class="mb-3">
                    <label for="departemen" class="form-label">Departemen</label>
                    <select class="form-select" id="departemen" name="departemen">
                        <option value="MTN" <?= $checksheet['departemen'] == 'MTN' ? 'selected' : '' ?>>MTN</option>
                        <option value="PRD" <?= $checksheet['departemen'] == 'PRD' ? 'selected' : '' ?>>PRD</option>
                        <option value="QA" <?= $checksheet['departemen'] == 'QA' ? 'selected' : '' ?>>QA</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="seksi" class="form-label">Seksi</label>
                    <select class="form-select" id="seksi" name="seksi">
                        <option value="Prod. 1" <?= $checksheet['seksi'] == 'Prod. 1' ? 'selected' : '' ?>>Prod. 1</option>
                        <option value="Prod. 2" <?= $checksheet['seksi'] == 'Prod. 2' ? 'selected' : '' ?>>Prod. 2</option>
                        <option value="Prod. 3" <?= $checksheet['seksi'] == 'Prod. 3' ? 'selected' : '' ?>>Prod. 3</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</main>

<script>
    function updateIdMachine(select) {
        const selectedOption = select.options[select.selectedIndex];
        const idMachine = selectedOption.getAttribute('data-id-machine');
        const idMachineInput = document.getElementById('idMachineInput');
        const idMachineBadge = document.getElementById('idMachineBadge');
        
        if (idMachine) {
            idMachineInput.value = idMachine;
            idMachineBadge.innerHTML = `<i class="bi bi-tag"></i> ${idMachine}`;
            idMachineBadge.classList.remove('bg-secondary');
            idMachineBadge.classList.add('bg-primary');
        } else {
            idMachineInput.value = '';
            idMachineBadge.innerHTML = '<i class="bi bi-tag"></i>';
            idMachineBadge.classList.remove('bg-primary');
            idMachineBadge.classList.add('bg-secondary');
        }
    }

    // Initialize id_machine display on page load
    document.addEventListener('DOMContentLoaded', function() {
        const mesinSelect = document.getElementById('mesin');
        if (mesinSelect) {
            updateIdMachine(mesinSelect);
        }
    });
</script>
<?= $this->endSection() ?>