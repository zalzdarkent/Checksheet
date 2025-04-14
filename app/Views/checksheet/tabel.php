<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . ' | CBI' : 'CBI' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4">
    <h2 class="text-center">Checksheet <?= esc($master['judul_checksheet']) ?></h2>
    <a href="/checksheet" class="btn btn-secondary mb-3">Kembali</a>
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
                    <td class="p-1">: <?= esc($checksheet['line']) ?></td>
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
    <form id="checksheet-form" method="POST" action="<?= site_url('checksheet/save-status'); ?>" onsubmit="return validateForm(event)">
        <?= csrf_field() ?>
        <input type="hidden" name="checksheet_id" value="<?= $checksheet['id']; ?>">
        <div class="table-responsive">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
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
                        for ($i = 1; $i <= $jumlahKolom; $i++):
                            ?>
                            <th class="custom-header text-center align-middle"><?= $i ?></th>
                            <input type="hidden" name="tanggal[<?= $i ?>]" value="<?= $i ?>">
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($detailMasters as $index => $row): ?>
                        <?php $isDeleted = in_array($row['item_check'], $deletedItemChecks ?? []); ?>
                        <tr class="<?= $isDeleted ? 'table-secondary' : '' ?>">
                            <td><?= $no++; ?></td>
                            <td>
                                <?= esc($row['item_check']); ?>
                                <?php if ($isDeleted): ?>
                                    <span class="badge bg-secondary">Dihapus</span>
                                <?php endif; ?>
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
                            for ($i = 1; $i <= $jumlahKolom; $i++):
                                $status = $statusArray[$row['item_check']][$i] ?? null;
                                ?>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <input type="hidden" name="status[<?= $index ?>][<?= $i ?>]" id="status_<?= $index ?>_<?= $i ?>" value="">

                                        <?php if ($isDeleted): ?>
                                            <?php if ($status == 'OK'): ?>
                                                <span class="badge bg-success">OK</span>
                                            <?php elseif ($status == 'NG'): ?>
                                                <?php if (isset($statusArray[$row['item_check']]['is_resolved']) && $statusArray[$row['item_check']]['is_resolved'] !== null): ?>
                                                    <span class="badge bg-warning">NG</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">NG</span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if ($isSubmitted): ?>
                                                <?php if ($status == 'OK'): ?>
                                                    <span class="badge bg-success">OK</span>
                                                <?php elseif ($status == 'NG'): ?>
                                                    <?php if (isset($statusArray[$row['item_check']]['is_resolved']) && $statusArray[$row['item_check']]['is_resolved'] !== null): ?>
                                                        <span class="badge bg-warning">NG</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">NG</span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-outline-success btn-sm <?= ($status == 'OK') ? 'active' : '' ?>"
                                                    data-index="<?= $index ?>" data-col="<?= $i ?>" data-value="OK">OK</button>

                                                <?php if ($status == 'NG' && isset($statusArray[$row['item_check']]['is_resolved']) && $statusArray[$row['item_check']]['is_resolved'] !== null): ?>
                                                    <button type="button" class="btn btn-outline-warning btn-sm active"
                                                        data-index="<?= $index ?>" data-col="<?= $i ?>" data-value="NG">NG</button>
                                                <?php else: ?>
                                                    <button type="button" class="btn btn-outline-danger btn-sm <?= ($status == 'NG') ? 'active' : '' ?>"
                                                        data-index="<?= $index ?>" data-col="<?= $i ?>" data-value="NG">NG</button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
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
                                    data-bs-title="Pilih NPK yang sesuai">(?)</span></label></td>
                        <?php for ($i = 1; $i <= $jumlahKolom; $i++): ?>
                            <td class="text-center">
                                <?php if ($isSubmitted): ?>
                                    <?php 
                                    $selectedNPK = $npkArray[$i] ?? '';
                                    $selectedKaryawan = null;
                                    if ($selectedNPK) {
                                        foreach ($karyawanList as $karyawan) {
                                            if ($karyawan['id'] == $selectedNPK) {
                                                $selectedKaryawan = $karyawan;
                                                break;
                                            }
                                        }
                                    }
                                    ?>
                                    <?php if ($selectedKaryawan): ?>
                                        <span class="badge bg-info">
                                            <?= $selectedKaryawan['npk'] ?> - <?= $selectedKaryawan['nama'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Belum diisi</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <select class="form-select" name="npk[<?= $i ?>]">
                                        <option value="">Pilih NPK</option>
                                        <?php foreach ($karyawanList as $karyawan): ?>
                                            <option value="<?= $karyawan['id'] ?>" <?= ($npkArray[$i] ?? '') == $karyawan['id'] ? 'selected' : '' ?>>
                                                <?= $karyawan['npk'] ?> - <?= $karyawan['nama'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                </tfoot>
            </table>
        </div>
        <input type="hidden" name="checksheet_id" value="<?= $checksheet['id']; ?>">
        <?php if ($isSubmitted): ?>
            <button type="submit" class="btn btn-primary mt-3" disabled>Simpan</button>
            <button type="submit" class="btn btn-success mt-3" disabled>Kirim</button>
        <?php else: ?>
            <button type="submit" name="action" value="save" class="btn btn-primary mt-3" id="btn-save">Simpan</button>
            <button type="submit" name="action" value="submit" class="btn btn-success mt-3" id="btn-submit">Kirim</button>
        <?php endif; ?>
    </form>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

        const buttons = document.querySelectorAll(".btn-outline-success, .btn-outline-danger, .btn-outline-warning");
        let filledColumns = [];

        // Cek apakah form sudah disubmit sebelumnya
        const isSubmitted = <?= $isSubmitted ? 'true' : 'false' ?>;
        if (isSubmitted) {
            document.querySelectorAll('select[name^="npk"]').forEach(select => {
                select.disabled = true;
            });
            document.querySelectorAll('.btn-outline-success, .btn-outline-danger, .btn-outline-warning').forEach(btn => {
                btn.disabled = true;
            });
        }

        // Set status aktif berdasarkan data yang tersimpan
        document.querySelectorAll('input[name^="status"]').forEach(input => {
            const status = input.value;
            if (status) {
                const index = input.name.match(/\[(\d+)\]\[(\d+)\]/)[1];
                const col = input.name.match(/\[(\d+)\]\[(\d+)\]/)[2];
                const button = document.querySelector(`button[data-index="${index}"][data-col="${col}"][data-value="${status}"]`);
                if (button) {
                    button.classList.add('active');
                }
            }
        });

        buttons.forEach(button => {
            button.addEventListener("click", function() {
                if (isSubmitted) return; // Jangan izinkan perubahan jika sudah disubmit
                const index = this.dataset.index;
                const col = this.dataset.col;
                const value = this.dataset.value;

                // Cek apakah sudah ada kolom lain yang diisi
                if (filledColumns.length > 0 && !filledColumns.includes(col)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian!',
                        text: 'Anda hanya dapat mengisi satu kolom pada satu waktu. Silakan simpan data terlebih dahulu sebelum mengisi kolom berikutnya.',
                    });
                    return;
                }

                // Set nilai OK/NG di input hidden
                const inputStatus = document.querySelector(`#status_${index}_${col}`);
                inputStatus.value = value;

                // Tambahkan kolom yang diisi ke array
                if (!filledColumns.includes(col) && value !== "") {
                    filledColumns.push(col);
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
                    filledColumns = filledColumns.filter(item => item !== col);
                    document.querySelector("#filled_columns").value = filledColumns.join(",");
                }

                // Update tampilan button
                
                const parentDiv = this.parentElement;
                parentDiv.querySelectorAll("button").forEach(btn => btn.classList.remove("active"));
                this.classList.add("active");

                // Wajib isi NPK
                const npkInput = document.querySelector(`input[name='npk[${col}]']`);
                npkInput.setAttribute("required", "required");
            });
        });

        // Reset status saat halaman di-reload
        window.addEventListener("pageshow", function(event) {
            if (event.persisted || window.performance && window.performance.navigation.type === 1) {
                filledColumns = [];
            }
        });
    });

    function validateForm(event) {
        event.preventDefault();
        let valid = true;
        let npkMissing = false;
        let action = event.submitter.value;

        // Cek NPK untuk kolom yang diisi
        document.querySelectorAll("input[name^='npk']").forEach(input => {
            const col = input.name.match(/\d+/)[0];
            let isChecked = false;

            document.querySelectorAll(`input[name^='status'][name*='[${col}]']`).forEach(statusInput => {
                if (statusInput.value !== "") {
                    isChecked = true;
                }
            });

            if (isChecked && input.value.trim() === "") {
                valid = false;
                npkMissing = true;
                input.classList.add("is-invalid");
            } else {
                input.classList.remove("is-invalid");
            }
        });

        if (!valid) {
            if (npkMissing) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Harap isi NPK untuk kolom yang telah diisi OK/NG!',
                });
            }
            return false;
        }

        // Konfirmasi submit
        let title = action === 'submit' ? 'Kirim Data?' : 'Simpan Data?';
        let text = action === 'submit' ?
            'Data yang sudah dikirim tidak dapat diubah kembali!' :
            'Pastikan data yang diisi sudah benar!';

        Swal.fire({
            title: title,
            text: text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: action === 'submit' ? '#198754' : '#0d6efd',
            cancelButtonColor: '#dc3545',
            confirmButtonText: action === 'submit' ? 'Ya, Kirim!' : 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create a hidden input for the action
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = action;
                document.getElementById('checksheet-form').appendChild(actionInput);
                
                // Submit the form
                document.getElementById('checksheet-form').submit();
            }
        });

        return false;
    }
</script>

<?= $this->endSection() ?>