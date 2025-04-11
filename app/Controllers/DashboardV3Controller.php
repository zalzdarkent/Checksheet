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
    protected $db;

    public function __construct()
    {
        $this->checksheetModel = new Checksheet();
        $this->detailChecksheetModel = new DetailChecksheet();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // Get current month and year
        $currentMonth = date('m');
        $currentYear = date('Y');
        $currentMonthYear = $currentYear . '-' . $currentMonth;

        // Get filter parameters
        $filterBulan = $this->request->getGet('filterBulan') ?? $currentMonthYear; // Default to current month-year
        $filterMesin = $this->request->getGet('filterMesin');

        // Extract year and month from filterBulan if it's in 'YYYY-MM' format
        if ($filterBulan) {
            $filterYear = substr($filterBulan, 0, 4); // Extract year
            $filterMonth = substr($filterBulan, 5, 2); // Extract month
        } else {
            $filterYear = $currentYear;
            $filterMonth = $currentMonth;
        }

        // Build the formatted filter month-year for query (e.g., 2025-04)
        $filterMonthFormatted = sprintf('%04d-%02d', $filterYear, $filterMonth);

        // Calculate the number of days in the selected month
        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $filterMonth, $filterYear);

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
            // Extract the middle part of the machine ID (e.g., UTY from D-UTY-SCB-003)
            $machineType = $filterMesin;
            $query->where('preuse_tb_checksheet.id_machine LIKE', "%$machineType%");
        }

        $results = $query->findAll();

        // Process the data for the view
        $machineData = [];
        foreach ($results as $row) {
            $day = date('j', strtotime($row['tanggal'])); // Get day (1-31)
            $key = $row['mesin'] . '_' . $row['id_machine'];
            $machineData[$key]['mesin'] = $row['mesin'];
            $machineData[$key]['id_machine'] = $row['id_machine'];
            $machineData[$key]['days'][$day] = $row['status'];
        }

        // Get unique machines for the filter dropdown
        $machines = $this->db->query(
            "
            SELECT DISTINCT 
                id_machine,
                mesin 
            FROM preuse_tb_checksheet 
            WHERE bulan = ?
            ORDER BY id_machine",
            [$filterMonthFormatted]
        )->getResultArray();

        // Process the machines array to extract machine types
        $processedMachines = [];
        foreach ($machines as $machine) {
            // Extract the middle part (e.g., UTY from D-UTY-SCB-003)
            $parts = explode('-', $machine['id_machine']);
            if (count($parts) >= 3) {
                $machineType = $parts[1]; // Get the middle part (UTY, PR1, PR2)
                if (!isset($processedMachines[$machineType])) {
                    $processedMachines[$machineType] = [
                        'id_machine' => $machineType,
                        'mesin' => $machine['mesin']
                    ];
                }
            }
        }
        $machines = array_values($processedMachines);

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

    public function getNGDetails()
    {
        $machineId = $this->request->getGet('machine_id');
        $date = $this->request->getGet('date');
        
        // Pastikan parameter yang diperlukan ada
        if (!$machineId || !$date) {
            return $this->response->setJSON([]);
        }

        // Query untuk mendapatkan data NG
        $db = \Config\Database::connect();
        $builder = $db->table('preuse_tb_detail_checksheet dc');
        $builder->select('dc.item_check, dc.inspeksi, dc.standar, dc.status');
        $builder->join('preuse_tb_checksheet cs', 'dc.checksheet_id = cs.id');
        $builder->where('cs.id_machine', $machineId);
        $builder->where('dc.kolom', $date); // Gunakan kolom untuk tanggal
        $builder->where('dc.status', 'NG');
        
        $query = $builder->get();
        $result = $query->getResultArray();

        // Debug log
        log_message('debug', 'NG Details Query: ' . $builder->getCompiledSelect());
        log_message('debug', 'NG Details Result: ' . json_encode($result));

        return $this->response->setJSON($result);
    }
}
