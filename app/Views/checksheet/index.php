<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . ' | CBI' : 'CBI' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #273749, #4a6b8a);
    }

    .btn-cbi {
        background: linear-gradient(135deg, #273749, #4a6b8a);
    }

    .btn-cbi:hover {
        background: linear-gradient(135deg, #4a6b8a, #273749);
        color: #ffffff;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
</style>
<?php
$hideMenus = isset($_GET['line']);
?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4 min-vh-100">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="m-0 font-weight-bold">List Checksheet Pre-Use</h5>
                <a href="#" class="btn btn-primary btn-sm px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#tambahModal">
                    <i class="bi bi-plus-circle me-1"></i> Tambah
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Flash messages -->
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i>
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Add Filter Form -->
            <div class="card mb-3">
                <div class="card-body">
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Mesin</label>
                            <select class="form-select form-select-sm" id="filterMesin">
                                <option value="">Semua Mesin</option>
                                <?php
                                $uniqueMesin = array_unique(array_column($checksheets, 'mesin'));
                                foreach ($uniqueMesin as $mesin):
                                ?>
                                    <option value="<?= $mesin ?>"><?= $mesin ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ID Mesin</label>
                            <select class="form-select form-select-sm" id="filterIdMesin">
                                <option value="">Semua ID Mesin</option>
                                <?php
                                $uniqueIdMachine = array_unique(array_column($checksheets, 'id_machine'));
                                foreach ($uniqueIdMachine as $id):
                                ?>
                                    <option value="<?= $id ?>"><?= $id ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Line</label>
                            <select class="form-select form-select-sm" id="filterLine">
                                <option value="">Semua Line</option>
                                <option value="0">Non Line</option>
                                <?php for ($i = 1; $i <= 7; $i++): ?>
                                    <option value="<?= $i ?>">Line <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Bulan</label>
                            <input type="month" class="form-control form-control-sm" id="filterBulan">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Departemen</label>
                            <select class="form-select form-select-sm" id="filterDept">
                                <option value="">Semua Dept</option>
                                <option value="MTN">MTN</option>
                                <option value="PRD">PRD</option>
                                <option value="QA">QA</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Seksi</label>
                            <select class="form-select form-select-sm" id="filterSeksi">
                                <option value="">Semua Seksi</option>
                                <option value="Prod. 1">Prod. 1</option>
                                <option value="Prod. 2">Prod. 2</option>
                                <option value="Prod. 3">Prod. 3</option>
                                <option value="MTN">MTN</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-secondary btn-sm" id="resetFilter">Reset Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table id="myTable" class="table table-hover align-middle text-nowrap mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>Mesin</th>
                            <th>Id Mesin</th>
                            <th>Line</th>
                            <th>Bulan</th>
                            <th>Dept.</th>
                            <th>Seksi</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($checksheets)) : ?>
                            <?php foreach ($checksheets as $index => $row) : ?>
                                <tr>
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td><?= esc($row['mesin']) ?></td>
                                    <td><?= esc($row['id_machine']) ?></td>
                                    <td class="text-center">
                                        <?= isset($row['line']) ? ($row['line'] == 0 ? 'Non Line' : esc($row['line'])) : '-' ?>
                                    </td>
                                    <td><?= date('F Y', strtotime($row['bulan'])) ?></td>
                                    <td>
                                        <?php
                                        $warna = 'bg-secondary';
                                        if ($row['departemen'] == 'MTN') $warna = 'bg-success';
                                        if ($row['departemen'] == 'PRD') $warna = 'bg-primary';
                                        if ($row['departemen'] == 'QA') $warna = 'bg-danger';
                                        ?>
                                        <span class="badge <?= $warna ?> rounded-pill"><?= esc($row['departemen']) ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $warna = 'bg-secondary';
                                        if ($row['seksi'] == 'Prod. 1') $warna = 'bg-warning';
                                        if ($row['seksi'] == 'Prod. 2') $warna = 'bg-primary';
                                        if ($row['seksi'] == 'Prod. 3') $warna = 'bg-danger';
                                        if ($row['seksi'] == 'MTN') $warna = 'bg-success';
                                        ?>
                                        <span class="badge <?= $warna ?> rounded-pill"><?= esc($row['seksi']) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url() ?>/checksheet/table/<?= $row['id'] ?>" class="btn btn-info btn-sm rounded-pill px-3">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= base_url() ?>/checksheet/edit/<?= $row['id'] ?>" class="btn btn-warning btn-sm rounded-pill px-3 ms-1">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <?php if (!$hideMenus): ?>
                                                <form action="<?= base_url() ?>/checksheet/delete/<?= $row['id'] ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3 ms-1">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center">Tidak ada data</td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal Tambah Data -->
<div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="tambahModalLabel">Tambah Checksheet</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url() ?>/checksheet/store" method="post" class="needs-validation" novalidate>
                    <?= csrf_field() ?>
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
                                    <option value="<?= $master['id'] . '|' . $index; ?>" data-id-machine="<?= $idMachineList[$index] ?? '' ?>">
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
                            <input type="text" class="form-control bg-light" id="idMachineInput" name="id_machine" readonly>
                            <span class="input-group-text bg-gradient-primary text-white">
                                <i class="bi bi-tag"></i>
                            </span>
                        </div>
                        <small class="badge bg-success text-white">ID mesin akan otomatis terisi saat mesin dipilih</small>
                    </div>

                    <div class="mb-3">
                        <label for="line" class="form-label">Line</label>
                        <select class="form-select" id="line" name="line" required>
                            <option value="" selected>Pilih Line</option>
                            <option value="0">Non Line</option> <!-- Tambahin ini -->
                            <?php for ($i = 1; $i <= 7; $i++): ?>
                                <option value="<?= $i ?>">Line <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                        <div class="invalid-feedback">Silakan pilih line</div>
                    </div>

                    <div class="mb-3">
                        <label for="bulan" class="form-label">Bulan</label>
                        <input type="month" class="form-control" id="bulan" name="bulan" required>
                        <div class="invalid-feedback">Silakan pilih bulan</div>
                    </div>

                    <div class="mb-3">
                        <label for="dept" class="form-label">Departemen</label>
                        <select class="form-select" id="dept" name="departemen" required>
                            <option value="" selected>Pilih Departemen</option>
                            <option value="MTN">MTN</option>
                            <option value="PRD">PRD</option>
                            <option value="QA">QA</option>
                        </select>
                        <div class="invalid-feedback">Silakan pilih departemen</div>
                    </div>

                    <div class="mb-3">
                        <label for="seksi" class="form-label">Seksi</label>
                        <select class="form-select" id="seksi" name="seksi" required>
                            <option value="" selected>Pilih Seksi</option>
                            <option value="Prod. 1">Prod. 1</option>
                            <option value="Prod. 2">Prod. 2</option>
                            <option value="Prod. 3">Prod. 3</option>
                            <option value="MTN">MTN</option>
                        </select>
                        <div class="invalid-feedback">Silakan pilih seksi</div>
                    </div>

                    <!-- Modal Tambah Data -->
                    <div class="modal-footer px-0 pb-0 d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-cbi text-white">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<style>
    .dataTables_wrapper .dataTables_length select {
        padding: 0.375rem 2.25rem 0.375rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 0.25rem;
        border-color: #dee2e6;
    }

    .dataTables_wrapper .dataTables_filter input {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 0.25rem;
        border-color: #dee2e6;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.375rem 0.75rem;
        margin: 0 0.2rem;
        border-radius: 0.25rem !important;
        border: none !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #0d6efd !important;
        color: white !important;
        border: none !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #0b5ed7 !important;
        color: white !important;
        border: none !important;
    }

    .table> :not(caption)>*>* {
        padding: 0.75rem;
    }

    .table thead tr th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
</style>
<!-- DataTables JS -->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
    $(document).ready(function() {
        // Fungsi untuk memperbarui opsi filter
        function updateFilterOptions() {
            // Update filter Mesin
            var uniqueMesin = [...new Set($('#myTable tbody tr td:nth-child(2)').map(function() {
                return $(this).text();
            }).get())];
            var currentMesin = $('#filterMesin').val();
            $('#filterMesin').empty().append('<option value="">Semua Mesin</option>');
            uniqueMesin.forEach(function(mesin) {
                $('#filterMesin').append(`<option value="${mesin}">${mesin}</option>`);
            });
            $('#filterMesin').val(currentMesin);

            // Update filter ID Mesin
            var uniqueIdMesin = [...new Set($('#myTable tbody tr td:nth-child(3)').map(function() {
                return $(this).text();
            }).get())];
            var currentIdMesin = $('#filterIdMesin').val();
            $('#filterIdMesin').empty().append('<option value="">Semua ID Mesin</option>');
            uniqueIdMesin.forEach(function(id) {
                $('#filterIdMesin').append(`<option value="${id}">${id}</option>`);
            });
            $('#filterIdMesin').val(currentIdMesin);

            // Simpan nilai filter yang aktif
            var activeFilters = {
                mesin: $('#filterMesin').val(),
                idMesin: $('#filterIdMesin').val(),
                line: $('#filterLine').val(),
                bulan: $('#filterBulan').val(),
                dept: $('#filterDept').val(),
                seksi: $('#filterSeksi').val()
            };

            return activeFilters;
        }

        // Initialize DataTable
        var table = $('#myTable').DataTable({
            pageLength: 10,
            ordering: true,
            responsive: true,
            columnDefs: [{
                orderable: false,
                targets: 7 // Ubah ke 7 karena kolom aksi adalah kolom ke-8
            }, {
                type: 'date',
                targets: 4 // Ubah ke 4 karena kolom bulan adalah kolom ke-5
            }],
            dom: '<"row align-items-center mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                '<"row"<"col-sm-12"tr>>' +
                '<"row align-items-center mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "Semua"]
            ],
            order: [
                [0, 'asc']
            ],
            drawCallback: function() {
                updateFilterOptions();
            }
        });

        // Custom filtering function yang diperbaiki
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var mesin = $('#filterMesin').val();
            var idMesin = $('#filterIdMesin').val();
            var line = $('#filterLine').val();
            var bulan = $('#filterBulan').val();
            var dept = $('#filterDept').val();
            var seksi = $('#filterSeksi').val();

            var $row = $(table.row(dataIndex).node());
            var rowMesin = data[1]; // Mesin
            var rowIdMesin = data[2]; // ID Mesin
            var rowLine = data[3].replace('Non Line', '0'); // Line
            var rowBulan = data[4]; // Bulan
            
            // Mengambil teks dari badge dengan cara yang lebih baik
            var rowDept = $($row.find('td:eq(5)').html()).text().trim();
            var rowSeksi = $($row.find('td:eq(6)').html()).text().trim();

            // Debug untuk memeriksa nilai yang diambil
            /*
            console.log('Filter Values:', {
                dept: dept,
                rowDept: rowDept,
                seksi: seksi,
                rowSeksi: rowSeksi
            });
            */

            // Pengecekan filter
            if (mesin && rowMesin !== mesin) return false;
            if (idMesin && rowIdMesin !== idMesin) return false;
            if (line && rowLine.toString() !== line.toString()) return false;
            
            if (bulan) {
                var filterDate = moment(bulan + '-01');
                var rowDate = moment(rowBulan, 'MMMM YYYY');
                if (!rowDate.isSame(filterDate, 'month')) return false;
            }
            
            // Pengecekan departemen dan seksi yang diperbaiki
            if (dept && rowDept !== dept) return false;
            if (seksi && rowSeksi !== seksi) return false;

            return true;
        });

        // Event handler untuk filter dengan tambahan debug
        $('#filterForm select, #filterForm input').on('change', function() {
            var filterType = $(this).attr('id');
            var filterValue = $(this).val();
            
            // Debug untuk memeriksa perubahan filter
            /*
            console.log('Filter Changed:', {
                type: filterType,
                value: filterValue
            });
            */
            
            table.draw();
        });

        // Reset filter dengan menyimpan state
        $('#resetFilter').on('click', function() {
            $('#filterForm')[0].reset();
            table.draw();
        });

        // Refresh table setelah modal ditutup
        $('#tambahModal').on('hidden.bs.modal', function() {
            location.reload(); // Refresh halaman untuk memastikan data terbaru
        });

        // Form validation
        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    });

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
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>