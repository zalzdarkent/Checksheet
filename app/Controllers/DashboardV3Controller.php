<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Checksheet;
use App\Models\DetailChecksheet;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardV3Controller extends BaseController
{
    protected $checksheetModel;
    protected $detailChecksheetModel;

    public function __construct()
    {
        $this->checksheetModel = new Checksheet();
        $this->detailChecksheetModel = new DetailChecksheet();
    }

    public function index()
    {
        // Get current month and year
        $currentMonth = date('m');
        $currentYear = date('Y');
        $currentMonthYear = $currentYear . '-' . $currentMonth;

        // Get filter parameters
        $filterBulan = $this->request->getGet('filterBulan') ?? $currentMonth;
        $filterMesin = $this->request->getGet('filterMesin');

        // Pastikan filterBulan adalah integer
        $filterBulan = (int) $filterBulan;

        // Buat format bulan-tahun untuk query (misal: 2025-04)
        $filterMonthFormatted = sprintf('%04d-%02d', $currentYear, $filterBulan);

        // Hitung jumlah hari dalam bulan yang difilter
        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $filterBulan, $currentYear);

        // Build query to get machine status data
        $query = $this->checksheetModel->select('
            preuse_tb_checksheet.mesin,
            preuse_tb_checksheet.id_machine,
            preuse_tb_detail_checksheet.tanggal,
            preuse_tb_detail_checksheet.status
        ')
            ->join('preuse_tb_detail_checksheet', 'preuse_tb_detail_checksheet.checksheet_id = preuse_tb_checksheet.id')
            ->where('preuse_tb_checksheet.bulan', $filterMonthFormatted);

        // Apply machine filter if provided
        if (!empty($filterMesin)) {
            $query->where('preuse_tb_checksheet.id_machine', $filterMesin);
        }

        $results = $query->findAll();

        // Process the data for the view
        $machineData = [];
        foreach ($results as $row) {
            $day = date('j', strtotime($row['tanggal'])); // ambil tanggal (1-31)
            $key = $row['mesin'] . '_' . $row['id_machine'];
            $machineData[$key]['mesin'] = $row['mesin'];
            $machineData[$key]['id_machine'] = $row['id_machine'];
            $machineData[$key]['days'][$day] = $row['status'];
        }

        // Get unique machines for the filter dropdown using SQL Server syntax
        $db = \Config\Database::connect();
        $machines = $db->query(
            "
            SELECT DISTINCT id_machine, mesin 
            FROM preuse_tb_checksheet 
            WHERE bulan = ?",
            [$currentMonthYear]
        )->getResultArray();

        $data = [
            'title' => 'Dashboard V3',
            'machineData' => $machineData,
            'machines' => $machines,
            'currentMonth' => $currentMonth,
            'filterBulan' => $filterBulan,
            'filterMesin' => $filterMesin,
            'jumlahHari' => $jumlahHari
        ];

        return view('layouts/dashboard_v3', $data);
    }
}
