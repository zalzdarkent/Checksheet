<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . '| CBI' : 'CBI' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard v2</h1>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                <div class="col-md-6">
                    <label for="line" class="form-label">Line</label>
                    <select class="form-select" id="line" name="line">
                        <option value="">Pilih Line</option>
                        <option value="1">Line 1</option>
                        <option value="2">Line 2</option>
                        <option value="3">Line 3</option>
                        <option value="4">Line 4</option>
                        <option value="5">Line 5</option>
                        <option value="6">Line 6</option>
                        <option value="7">Line 7</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="month" class="form-label">Bulan dan Tahun</label>
                    <input type="month" class="form-control" id="month" name="month">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Filter</button>
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

<!-- Include Highcharts -->
<!-- <script src="https://code.highcharts.com/highcharts.js"></script> -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Column Chart
    Highcharts.chart('columnChart', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Yearly Statistics'
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
});
</script>
<?= $this->endSection() ?>