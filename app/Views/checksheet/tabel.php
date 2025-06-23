<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . ' | CBI' : 'CBI' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
#checksheet-table {
    border-collapse: separate;
    border-spacing: 0;
}
/* Sticky columns for table */
.sticky-col {
    position: sticky;
    left: 0;
    background: #fff;
    z-index: 2;
}
.sticky-col-1 { left: 0; width: 40px; min-width: 40px; max-width: 40px; z-index: 3; }
.sticky-col-2 { left: 40px; width: 180px; min-width: 180px; max-width: 180px; }
.sticky-col-3 { left: 220px; width: 120px; min-width: 120px; max-width: 120px; }
.sticky-col-4 { left: 340px; width: 100px; min-width: 100px; max-width: 100px; }
tfoot .sticky-col, thead .sticky-col { z-index: 4; background: #f8f9fa; }
</style>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4">
    <h2 class="text-center">Checksheet <?= esc($master['judul_checksheet']) ?></h2>
    <a href="<?= base_url() ?>/checksheet" class="btn btn-secondary mb-3">Kembali</a>
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
                    <th class="p-1">Id Mesin</th>
                    <td class="p-1">: <?= esc($checksheet['id_machine']) ?></td>
                    <td class="p-1"></td>
                </tr>
                <tr>
                    <th class="p-1">Line</th>
                    <td class="p-1">
                        : <?= isset($checksheet['line']) ? ($checksheet['line'] == 0 ? 'Non Line' : esc($checksheet['line'])) : '-' ?>
                    </td>
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
            <!-- tabel sakti -->
            <table id="checksheet-table" class="table table-bordered table-striped align-middle text-center nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th class="custom-header sticky-col sticky-col-1" style="width:40px;min-width:40px;max-width:40px;">No</th>
                        <th class="custom-header sticky-col sticky-col-2" style="width:180px;min-width:180px;max-width:180px;">Item Check</th>
                        <th class="custom-header sticky-col sticky-col-3" style="width:120px;min-width:120px;max-width:120px;">Item Inspeksi</th>
                        <th class="custom-header sticky-col sticky-col-4" style="width:100px;min-width:100px;max-width:100px;">Standar</th>
                        <?php
                        $jumlahKolom = date('t', strtotime($checksheet['bulan']));
                        $bulan = $checksheet['bulan'];
                        for ($i = 1; $i <= $jumlahKolom; $i++):
                            $tanggalStr = $bulan . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                            $dayOfWeek = date('N', strtotime($tanggalStr)); // 6=Saturday, 7=Sunday
                            $isWeekend = ($dayOfWeek == 6 || $dayOfWeek == 7);
                        ?>
                            <th class="custom-header text-center align-middle"<?= $isWeekend ? ' style="background-color: red !important;"' : '' ?>><?= $i ?></th>
                            <input type="hidden" name="tanggal[<?= $i ?>]" value="<?= $i ?>">
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($detailMasters as $index => $row): ?>
                        <?php $isDeleted = in_array($row['item_check'], $deletedItemChecks ?? []); ?>
                        <tr class="<?= $isDeleted ? 'table-secondary' : '' ?>">
                            <td class="sticky-col sticky-col-1" style="width:40px;min-width:40px;max-width:40px;"><?= $no++; ?></td>
                            <td class="sticky-col sticky-col-2" style="width:180px;min-width:180px;max-width:180px;">
                                <?= esc($row['item_check']); ?>
                                <?php if ($isDeleted): ?>
                                    <span class="badge bg-secondary">Dihapus</span>
                                <?php endif; ?>
                                <input type="hidden" name="item_check[<?= $index ?>]" value="<?= esc($row['item_check']); ?>">
                            </td>
                            <td class="sticky-col sticky-col-3" style="width:120px;min-width:120px;max-width:120px;">
                                <?= esc($row['inspeksi']); ?>
                                <input type="hidden" name="inspeksi[<?= $index ?>]" value="<?= esc($row['inspeksi']); ?>">
                            </td>
                            <td class="sticky-col sticky-col-4" style="width:100px;min-width:100px;max-width:100px;">
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
                                        <input type="hidden" name="status[<?= $index ?>][<?= $i ?>]" id="status_<?= $index ?>_<?= $i ?>" value="<?= isset($status['status']) ? $status['status'] : '' ?>">

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
                                                <?php if (isset($statusArray[$row['item_check']][$i]['status']) && $statusArray[$row['item_check']][$i]['status'] == 'OK'): ?>
                                                    <span class="badge bg-success">OK</span>
                                                <?php elseif (isset($statusArray[$row['item_check']][$i]['status']) && $statusArray[$row['item_check']][$i]['status'] == 'NG'): ?>
                                                    <?php if (isset($statusArray[$row['item_check']][$i]['is_resolved']) && $statusArray[$row['item_check']][$i]['is_resolved'] !== null): ?>
                                                        <span class="badge bg-warning">NG</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">NG</span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-outline-success btn-sm <?= (isset($status['status']) && $status['status'] == 'OK') ? 'active' : '' ?>"
                                                    data-index="<?= $index ?>" data-col="<?= $i ?>" data-value="OK">OK</button>

                                                <?php
                                                if (empty($status['status'])) {
                                                ?>
                                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                                        data-index="<?= $index ?>" data-col="<?= $i ?>" data-value="NG">NG</button>
                                                    <?php
                                                } else {
                                                    if (isset($status['status']) && $status['is_resolved'] != null && $status['status'] == 'NG'): ?>
                                                        <button type="button" class="btn btn-outline-warning btn-sm active"
                                                            data-index="<?= $index ?>" data-col="<?= $i ?>" data-value="NG">NG</button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-outline-danger btn-sm <?= ($status['is_resolved'] == null && $status['status'] == 'NG') ? 'active' : '' ?>"
                                                            data-index="<?= $index ?>" data-col="<?= $i ?>" data-value="NG">NG</button>
                                                <?php endif;
                                                }
                                                ?>
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
                        <td class="sticky-col sticky-col-1" colspan="4" style="width:440px;min-width:440px;max-width:440px;"><label class="fw-bold">Diisi oleh (NPK): <span class="ms-1" style="cursor: help; color: #0d6efd; font-weight: bold;"
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
                                    <select class="form-select select2" name="npk[<?= $i ?>]">
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
                    <tr>
                        <td class="sticky-col sticky-col-1" colspan="4" style="width:440px;min-width:440px;max-width:440px;"><label class="fw-bold">Diisi oleh (GMT): <span class="ms-1" style="cursor: help; color: #0d6efd; font-weight: bold;"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    data-bs-title="Pilih GMT yang sesuai">(?)</span></label></td>
                        <?php for ($i = 1; $i <= $jumlahKolom; $i++): ?>
                            <td class="text-center">
                                <?php if ($isSubmitted): ?>
                                    <?php if (isset($gmtArray[$i]) && !empty($gmtArray[$i])): ?>
                                        <span class="badge bg-info">
                                            <?= $gmtArray[$i] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Belum diisi</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <select class="form-select select2" name="gmt[<?= $i ?>]">
                                        <option value="" disabled <?= !isset($gmtArray[$i]) ? 'selected' : '' ?>>Pilih GMT</option>
                                        <option value="Ardi Setio Nugroho" <?= (isset($gmtArray[$i]) && $gmtArray[$i] == 'Ardi Setio Nugroho') ? 'selected' : '' ?>>Ardi Setio Nugroho</option>
                                        <option value="Komarudin" <?= (isset($gmtArray[$i]) && $gmtArray[$i] == 'Komarudin') ? 'selected' : '' ?>>Komarudin</option>
                                        <option value="Yoga" <?= (isset($gmtArray[$i]) && $gmtArray[$i] == 'Yoga') ? 'selected' : '' ?>>Yoga</option>
                                        <option value="Parmugio" <?= (isset($gmtArray[$i]) && $gmtArray[$i] == 'Parmugio') ? 'selected' : '' ?>>Parmugio</option>
                                        <option value="Latif Febrianto" <?= (isset($gmtArray[$i]) && $gmtArray[$i] == 'Latif Febrianto') ? 'selected' : '' ?>>Latif Febrianto</option>
                                        <option value="Musbihin" <?= (isset($gmtArray[$i]) && $gmtArray[$i] == 'Musbihin') ? 'selected' : '' ?>>Musbihin</option>
                                        <option value="Narman" <?= (isset($gmtArray[$i]) && $gmtArray[$i] == 'Narman') ? 'selected' : '' ?>>Narman</option>
                                        <option value="Achmad S" <?= (isset($gmtArray[$i]) && $gmtArray[$i] == 'Achmad S') ? 'selected' : '' ?>>Achmad S</option>
                                        <option value="Subhan" <?= (isset($gmtArray[$i]) && $gmtArray[$i] == 'Subhan') ? 'selected' : '' ?>>Subhan</option>
                                        <option value="Irfan" <?= (isset($gmtArray[$i]) && $gmtArray[$i] == 'Irfan') ? 'selected' : '' ?>>Irfan</option>
                                        <option value="Johan" <?= (isset($gmtArray[$i]) && $gmtArray[$i] == 'Johan') ? 'selected' : '' ?>>Johan</option>
                                        <option value="Kiki" <?= (isset($gmtArray[$i]) && $gmtArray[$i] == 'Kiki') ? 'selected' : '' ?>>Kiki</option>
                                        <option value="Ficky" <?= (isset($gmtArray[$i]) && $gmtArray[$i] == 'Ficky') ? 'selected' : '' ?>>Ficky</option>
                                    </select>
                                <?php endif; ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                    <?php if ($showRunHour): ?>
                        <!-- Run Hour section -->
                        <tr>
                            <td colspan="4">
                                <label class="fw-bold">Run Hour:
                                    <span class="ms-1" style="cursor: help; color: #0d6efd; font-weight: bold;"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        data-bs-title="Hanya angka">(?)</span>
                                </label>
                            </td>
                            <?php for ($i = 1; $i <= $jumlahKolom; $i++): ?>
                                <td class="text-center">
                                    <?php if ($isSubmitted): ?>
                                        <span class="badge <?= isset($runHourArray[$row['item_check']][$i]) ? 'bg-info' : 'bg-secondary' ?>">
                                            <?= $runHourArray[$row['item_check']][$i] ?? 'Belum diisi' ?>
                                        </span>
                                    <?php else: ?>
                                        <input type="number" class="form-control form-control-sm"
                                            name="run_hour[<?= $i ?>]"
                                            value="<?= isset($runHourArray[$row['item_check']][$i]) ? $runHourArray[$row['item_check']][$i] : '' ?>">
                                    <?php endif; ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endif; ?>

                    <?php if ($showTemperature): ?>
                        <!-- Temperature section -->
                        <tr>
                            <td colspan="4">
                                <label class="fw-bold">Temperature:
                                    <span class="ms-1" style="cursor: help; color: #0d6efd; font-weight: bold;"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        data-bs-title="Hanya angka">(?)</span>
                                </label>
                            </td>
                            <?php for ($i = 1; $i <= $jumlahKolom; $i++): ?>
                                <td class="text-center">
                                    <?php if ($isSubmitted): ?>
                                        <span class="badge <?= isset($temperatureArray[$row['item_check']][$i]) ? 'bg-info' : 'bg-secondary' ?>">
                                            <?= $temperatureArray[$row['item_check']][$i] ?? 'Belum diisi' ?>
                                        </span>
                                    <?php else: ?>
                                        <input type="number" class="form-control form-control-sm"
                                            name="temperature[<?= $i ?>]"
                                            value="<?= isset($temperatureArray[$row['item_check']][$i]) ? $temperatureArray[$row['item_check']][$i] : '' ?>">
                                    <?php endif; ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endif; ?>

                    <?php if ($showRunLoad): ?>
                        <!-- Temperature section -->
                        <tr>
                            <td colspan="4">
                                <label class="fw-bold">Run Load:
                                    <span class="ms-1" style="cursor: help; color: #0d6efd; font-weight: bold;"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        data-bs-title="Hanya angka">(?)</span>
                                </label>
                            </td>
                            <?php for ($i = 1; $i <= $jumlahKolom; $i++): ?>
                                <td class="text-center">
                                    <?php if ($isSubmitted): ?>
                                        <span class="badge <?= isset($runLoadArray[$row['item_check']][$i]) ? 'bg-info' : 'bg-secondary' ?>">
                                            <?= $runLoadArray[$row['item_check']][$i] ?? 'Belum diisi' ?>
                                        </span>
                                    <?php else: ?>
                                        <input type="number" class="form-control form-control-sm"
                                            name="run_load[<?= $i ?>]"
                                            value="<?= isset($runLoadArray[$row['item_check']][$i]) ? $runLoadArray[$row['item_check']][$i] : '' ?>">
                                    <?php endif; ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endif; ?>
                </tfoot>
            </table>
        </div>
        <input type="hidden" name="checksheet_id" value="<?= $checksheet['id']; ?>">
        <?php if ($isSubmitted): ?>
            <button type="submit" class="btn btn-primary mt-3" disabled>Simpan</button>
            <button type="submit" class="btn btn-success mt-3" disabled>Kirim</button>
        <?php else: ?>
            <button type="submit" name="action" value="save" class="btn btn-primary mt-3" id="btn-save">Simpan</button>
            <button type="submit" name="action" value="submit" class="btn btn-success mt-3" id="btn-submit" disabled>Kirim</button>
        <?php endif; ?>
    </form>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Fungsi untuk mengecek apakah hari ini adalah akhir bulan
        function isEndOfMonth() {
            const today = new Date();
            const lastDayOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0).getDate();
            return today.getDate() >= lastDayOfMonth;
        }

        // Mengatur status enabled/disabled tombol Kirim
        const submitButton = document.getElementById('btn-submit');
        if (submitButton) {
            submitButton.disabled = !isEndOfMonth();
        }

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

        buttons.forEach(button => {
            button.addEventListener("click", function() {
                if (isSubmitted) return; // Jangan izinkan perubahan jika sudah disubmit
                const index = this.dataset.index;
                const col = this.dataset.col;
                const value = this.dataset.value;

                // Set nilai OK/NG di input hidden
                const inputStatus = document.querySelector(`#status_${index}_${col}`);
                inputStatus.value = value;

                // Update tampilan button
                const parentDiv = this.parentElement;
                parentDiv.querySelectorAll("button").forEach(btn => btn.classList.remove("active"));
                this.classList.add("active");

                // Wajib isi NPK
                const npkInput = document.querySelector(`select[name='npk[${col}]']`);
                if (npkInput) {
                    npkInput.setAttribute("required", "required");
                }
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

        // Debug data form sebelum submit
        console.log('Form Data:');
        const formData = new FormData(document.getElementById('checksheet-form'));
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

        // Cek NPK untuk kolom yang diisi
        document.querySelectorAll("select[name^='npk']").forEach(input => {
            const col = input.name.match(/\d+/)[0];
            let isChecked = false;

            document.querySelectorAll(`input[name^='status'][name*='[${col}]']`).forEach(statusInput => {
                if (statusInput.value !== "") {
                    isChecked = true;
                    console.log(`Status found for column ${col}:`, statusInput.value);
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