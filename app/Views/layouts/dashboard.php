<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . '| CBI' : 'CBI'?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4 min-vh-100">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
    </div>

    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Checksheet</h5>
                    <h2 class="card-text"><?= $totalChecksheet ?? 0 ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total OK</h5>
                    <h2 class="card-text text-success"><?= $totalOK ?? 0 ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total NG</h5>
                    <h2 class="card-text text-danger"><?= $totalNG ?? 0 ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Status Checksheet per Bulan</h5>
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Perbandingan Status</h5>
                    <canvas id="statusPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Data dari controller
const monthlyData = <?= json_encode($monthlyData ?? ['labels' => [], 'ok' => [], 'ng' => []]) ?>;
const statusData = <?= json_encode($statusData ?? ['ok' => 0, 'ng' => 0]) ?>;

// Monthly Chart
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: monthlyData.labels,
        datasets: [{
            label: 'OK',
            data: monthlyData.ok,
            backgroundColor: 'rgba(40, 167, 69, 0.5)',
            borderColor: 'rgb(40, 167, 69)',
            borderWidth: 1
        }, {
            label: 'NG',
            data: monthlyData.ng,
            backgroundColor: 'rgba(220, 53, 69, 0.5)',
            borderColor: 'rgb(220, 53, 69)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Status Pie Chart
const pieCtx = document.getElementById('statusPieChart').getContext('2d');
new Chart(pieCtx, {
    type: 'pie',
    data: {
        labels: ['OK', 'NG'],
        datasets: [{
            data: [statusData.ok, statusData.ng],
            backgroundColor: [
                'rgba(40, 167, 69, 0.5)',
                'rgba(220, 53, 69, 0.5)'
            ],
            borderColor: [
                'rgb(40, 167, 69)',
                'rgb(220, 53, 69)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
<?= $this->endSection() ?>