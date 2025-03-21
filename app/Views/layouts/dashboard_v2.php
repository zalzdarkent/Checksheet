<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . '| CBI' : 'CBI' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard v2</h1>
    </div>

    <div class="row">
        <!-- Column Chart -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div id="columnChart"></div>
                </div>
            </div>
        </div>
        
        <!-- Pie Chart -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div id="pieChart"></div>
                </div>
            </div>
        </div>
        
        <!-- Line Chart -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div id="lineChart"></div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Include Highcharts -->
<script src="https://code.highcharts.com/highcharts.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Column Chart
    Highcharts.chart('columnChart', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Monthly Statistics'
        },
        xAxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
        },
        yAxis: {
            title: {
                text: 'Total Cases'
            }
        },
        series: [{
            name: 'OK',
            data: [45, 52, 38, 41, 47, 53],
            color: '#28a745'
        }, {
            name: 'NG',
            data: [12, 15, 8, 11, 7, 13],
            color: '#dc3545'
        }]
    });

    // Pie Chart
    Highcharts.chart('pieChart', {
        chart: {
            type: 'pie'
        },
        title: {
            text: 'Status Distribution'
        },
        series: [{
            name: 'Total',
            data: [
                {
                    name: 'OK',
                    y: 75,
                    color: '#28a745'
                },
                {
                    name: 'NG',
                    y: 25,
                    color: '#dc3545'
                }
            ]
        }]
    });

    // Line Chart
    Highcharts.chart('lineChart', {
        title: {
            text: 'Trend Analysis'
        },
        xAxis: {
            categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6']
        },
        yAxis: {
            title: {
                text: 'Number of Cases'
            }
        },
        series: [{
            name: 'Total Cases',
            data: [25, 35, 28, 42, 37, 45],
            color: '#007bff'
        }]
    });
});
</script>
<?= $this->endSection() ?>