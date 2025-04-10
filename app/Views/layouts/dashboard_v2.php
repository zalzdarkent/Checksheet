<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . ' | CBI' : 'CBI' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard v2</h1>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="mesin" class="form-label">Mesin</label>
                        <select class="form-select" id="mesin" name="mesin">
                            <option value="">Semua Mesin</option>
                            <?php foreach ($machines as $machine) : ?>
                                <option value="<?= esc($machine['mesin']) ?>" <?= ($selectedMesin == $machine['mesin']) ? 'selected' : '' ?>><?= esc($machine['mesin']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="line" class="form-label">Line</label>
                        <select class="form-select" id="line" name="line">
                            <option value="">Semua Line</option>
                            <?php for ($i = 1; $i <= 7; $i++) : ?>
                                <option value="<?= $i ?>" <?= ($selectedLine == $i) ? 'selected' : '' ?>>Line <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="month" class="form-label">Bulan dan Tahun</label>
                        <input type="month" class="form-control" id="month" name="bulan" value="<?= $selectedBulan ?>">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Column Chart -->
    <div class="card">
        <div class="card-body">
            <div id="columnChart"></div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        Highcharts.chart('columnChart', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Monthly Statistic'
            },
            xAxis: {
                categories: ['Line 1', 'Line 2', 'Line 3', 'Line 4', 'Line 5', 'Line 6', 'Line 7']
            },
            yAxis: {
                title: {
                    text: 'Jumlah Data'
                }
            },
            series: [{
                    name: 'OK',
                    data: <?= json_encode($chartData['OK']) ?>,
                    color: '#28a745'
                },
                {
                    name: 'NG',
                    data: <?= json_encode($chartData['NG']) ?>,
                    color: '#dc3545'
                }
            ],
            credits: {
                enabled: false // Ini buat ngilangin watermark Highcharts
            }
        });
    });
</script>

<?= $this->endSection() ?>