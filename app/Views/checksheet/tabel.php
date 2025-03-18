<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . '| CBI' : 'CBI' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4">
    <h2 class="text-center">Checksheet <?= esc($master['judul_checksheet']) ?></h2>
    <a href="/list-checksheet" class="btn btn-secondary mb-3">Kembali</a>
    <div class="card p-2 mt-3">
        <table class="table table-borderless" id="dataTable">
            <tbody>
                <tr>
                    <th class="p-1">Departemen</th>
                    <td class="p-1">: <?= esc($checksheet['departemen']) ?></td>
                    <td class="p-1"></td>
                    <th class="p-1">Mesin</th>
                    <td class="p-1">: <?= esc($checksheet['mesin']) ?></td>
                    <td class="p-1"></td>
                </tr>
                <tr>
                    <th class="p-1">Seksi</th>
                    <td class="p-1">: <?= esc($checksheet['seksi']) ?></td>
                    <td class="p-1"></td>
                    <th class="p-1">No Form</th>
                    <td class="p-1">:</td>
                    <td class="p-1"></td>
                </tr>
                <tr>
                    <th class="p-1">Line</th>
                    <td class="p-1">:</td>
                    <td class="p-1"></td>
                    <th class="p-1">Bulan</th>
                    <td class="p-1">: <?= strftime('%B %Y', strtotime($checksheet['bulan'])) ?></td>
                    <td class="p-1"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
        <h4 class="mb-0">Table Daily Check</h4>
    </div>
    <form id="checksheet-form" method="POST" action="<?= site_url('/checksheet/saveStatus'); ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="checksheet_id" value="<?= $checksheet['id']; ?>">
        <div class="table-responsive">
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('danger') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <table class="table table-bordered table-striped align-middle text-center">
                <thead>
                    <tr>
                        <th class="custom-header">No</th>
                        <th class="custom-header">Item Check</th>
                        <th class="custom-header">Item Inspeksi</th>
                        <th class="custom-header">Standar</th>
                        <?php
                        $jumlahKolom = date('t', strtotime($checksheet['bulan']));
                        for ($i = 1; $i <= $jumlahKolom; $i++) : ?>
                            <th class="custom-header text-center align-middle"><?= $i ?></th>
                            <input type="hidden" name="tanggal[<?= $i ?>]" value="<?= $i ?>">
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($detailMasters as $index => $row) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <?= esc($row['item_check']); ?>
                                <input type="hidden" name="item_check[<?= $index ?>]" value="<?= esc($row['item_check']); ?>">
                            </td>
                            <td>
                                <?= esc($row['inspeksi']); ?>
                                <input type="hidden" name="inspeksi[<?= $index ?>]" value="<?= esc($row['inspeksi']); ?>">
                            </td>
                            <td>
                                <?= esc($row['standar']); ?>
                                <input type="hidden" name="standar[<?= $index ?>]" value="<?= esc($row['standar']); ?>">
                            </td>
                            <?php
                            $jumlahKolom = date('t', strtotime($checksheet['bulan']));
                            for ($i = 1; $i <= $jumlahKolom; $i++) :
                                $status = $statusArray[$row['item_check']][$i] ?? null; // Cek status per item_check dan tanggal
                            ?>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <input type="hidden" name="status[<?= $index ?>][<?= $i ?>]" id="status_<?= $index ?>_<?= $i ?>" value="">

                                        <button type="button" class="btn btn-outline-success btn-sm <?= ($status == 'OK') ? 'active' : '' ?>"
                                            data-index="<?= $index ?>" data-col="<?= $i ?>" data-value="OK">OK</button>

                                        <button type="button" class="btn btn-outline-danger btn-sm <?= ($status == 'NG') ? 'active' : '' ?>"
                                            data-index="<?= $index ?>" data-col="<?= $i ?>" data-value="NG">NG</button>
                                    </div>
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"><label class="fw-bold">Diisi oleh (NPK): <span class="ms-1" style="cursor: help; color: #0d6efd; font-weight: bold;"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    data-bs-title="NPK hanya boleh diisi dengan angka">(?)</span></label></td>
                        <?php for ($i = 1; $i <= $jumlahKolom; $i++) : ?>
                            <td class="text-center">
                                <input type="text" class="form-control" pattern="[0-9]*" inputmode="numeric" name="npk[<?= $i ?>]"
                                    value="<?= isset($npkArray[$i]) ? esc($npkArray[$i]) : ''; ?>">
                            </td>
                        <?php endfor; ?>
                    </tr>
                </tfoot>
            </table>
        </div>
        <input type="hidden" name="checksheet_id" value="<?= $checksheet['id']; ?>">
        <?php if ($isSubmitted) : ?>
            <button type="submit" class="btn btn-primary mt-3" disabled>Simpan</button>
            <button type="submit" class="btn btn-success mt-3" disabled>Kirim</button>
        <?php else : ?>
            <button type="submit" name="action" value="save" class="btn btn-primary mt-3">Simpan</button>
            <button type="submit" name="action" value="submit" class="btn btn-success mt-3">Kirim</button>
        <?php endif; ?>
    </form>
</main>

<script>
    // Tambahkan ini di dalam event listener DOMContentLoaded yang sudah ada
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    
    document.addEventListener("DOMContentLoaded", function() {
        const buttons = document.querySelectorAll(".btn-outline-success, .btn-outline-danger");
        // Tambahkan array untuk melacak kolom yang diisi
        let filledColumns = [];

        buttons.forEach(button => {
            button.addEventListener("click", function() {
                const index = this.dataset.index;
                const col = this.dataset.col;
                const value = this.dataset.value;

                // Set nilai OK/NG di input hidden
                const inputStatus = document.querySelector(`#status_${index}_${col}`);
                inputStatus.value = value;

                // Tambahkan kolom yang diisi ke array jika belum ada
                if (!filledColumns.includes(col) && value !== "") {
                    filledColumns.push(col);

                    // Buat atau update hidden input untuk menyimpan kolom yang diisi
                    let filledInput = document.querySelector("#filled_columns");
                    if (!filledInput) {
                        filledInput = document.createElement("input");
                        filledInput.type = "hidden";
                        filledInput.id = "filled_columns";
                        filledInput.name = "filled_columns";
                        document.getElementById("checksheet-form").appendChild(filledInput);
                    }
                    filledInput.value = filledColumns.join(",");
                } else if (value === "" && filledColumns.includes(col)) {
                    // Hapus dari array jika nilainya kosong
                    filledColumns = filledColumns.filter(item => item !== col);
                    document.querySelector("#filled_columns").value = filledColumns.join(",");
                }

                // Update tampilan button
                const parentDiv = this.parentElement;
                parentDiv.querySelectorAll("button").forEach(btn => btn.classList.remove("active"));
                this.classList.add("active");

                // Wajib isi NPK jika OK/NG sudah dipilih
                const npkInput = document.querySelector(`input[name='npk[${col}]']`);
                npkInput.setAttribute("required", "required");
            });
        });

        document.querySelector("#checksheet-form").addEventListener("submit", function(e) {
            let valid = true;

            // Cek jika ada kolom OK/NG yang sudah terisi tetapi NPK kosong
            document.querySelectorAll("input[name^='npk']").forEach(input => {
                const col = input.name.match(/\d+/)[0]; // Ambil nomor kolom
                let isChecked = false;

                document.querySelectorAll(`input[name^='status'][name*='[${col}]']`).forEach(statusInput => {
                    if (statusInput.value !== "") {
                        isChecked = true;
                    }
                });

                if (isChecked && input.value.trim() === "") {
                    valid = false;
                    input.classList.add("is-invalid");
                } else {
                    input.classList.remove("is-invalid");
                }
            });

            if (!valid) {
                e.preventDefault();
                alert("Harap isi NPK untuk kolom yang telah diisi OK/NG!");
            }
        });
    });
</script>
<?= $this->endSection() ?>