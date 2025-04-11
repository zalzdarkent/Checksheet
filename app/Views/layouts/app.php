<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/datatables/datatables.min.css') ?>">
    <!-- DataTables CSS -->
    <link rel="shortcut icon" href="<?= base_url('logo/CBI_logo.png') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/icon/bootstrap-icons.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/icon/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/jquery/jquery-ui.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/jquery/jquery-ui.min.js') ?>">
    <title><?= $this->renderSection('title') ?></title>
    <style>
        .custom-header {
            background-color: #72A0C1 !important;
            color: white !important;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?= $this->include('components/sidebar') ?>

            <!-- Main content -->
            <?= $this->renderSection('content') ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/jquery/jquery.dataTables.min.js') ?>"></script>

    <!-- Additional Scripts -->
    <?= $this->renderSection('scripts') ?>
    <script src="<?= base_url('assets/highcharts/highchart.js') ?>"></script>
    <script src="<?= base_url('assets/datatables/datatables.min.js') ?>"></script>
    <script src="<?= base_url('assets/sweetalert/sweetalert.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</body>

</html>