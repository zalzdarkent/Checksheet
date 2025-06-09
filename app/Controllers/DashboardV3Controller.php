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
        $filterBulan = $this->request->getGet('filterBulan') ?? $currentMonthYear;
        $filterMesin = $this->request->getGet('filterMesin');

        // Extract year and month from filterBulan
        if ($filterBulan) {
            $filterYear = substr($filterBulan, 0, 4);
            $filterMonth = substr($filterBulan, 5, 2);
        } else {
            $filterYear = $currentYear;
            $filterMonth = $currentMonth;
        }

        // Build the date range for the selected month
        $startDate = sprintf('%s-%s-01', $filterYear, $filterMonth);
        $endDate = date('Y-m-t', strtotime($startDate));

        // Calculate the number of days in the selected month
        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $filterMonth, $filterYear);

        // Get all machines for the selected month
        $query = $this->checksheetModel->select("
            preuse_tb_checksheet.mesin,
            preuse_tb_checksheet.id_machine,
            preuse_tb_detail_checksheet.tanggal,
            CASE 
                WHEN COUNT(CASE WHEN preuse_tb_detail_checksheet.status = 'NG' THEN 1 END) > 0 THEN 'NG'
                WHEN COUNT(CASE WHEN preuse_tb_detail_checksheet.status = 'OK' THEN 1 END) > 0 THEN 'OK'
                ELSE 'EMPTY'
            END as status
        ")
        ->join('preuse_tb_detail_checksheet', 'preuse_tb_detail_checksheet.checksheet_id = preuse_tb_checksheet.id', 'left')
        ->where("preuse_tb_checksheet.bulan BETWEEN '$startDate' AND '$endDate'")
        ->groupBy('preuse_tb_checksheet.mesin, preuse_tb_checksheet.id_machine, preuse_tb_detail_checksheet.tanggal');

        if (!empty($filterMesin)) {
            $query->where("preuse_tb_checksheet.id_machine LIKE", "%-$filterMesin-%");
        }

        $results = $query->findAll();

        // Get unique machines for filter dropdown (always show all types)
        $machines = $this->checksheetModel->select("id_machine, mesin")
            ->distinct()
            ->where("bulan BETWEEN '$startDate' AND '$endDate'")
            ->orderBy('id_machine')
            ->findAll();

        // Process machines array to extract machine types
        $processedMachines = [];
        foreach ($machines as $machine) {
            $parts = explode('-', $machine['id_machine']);
            if (count($parts) >= 3) {
                $machineType = $parts[1]; // Get the middle part (PR2, PR1, UTY)
                if (!isset($processedMachines[$machineType])) {
                    $processedMachines[$machineType] = [
                        'id_machine' => $machineType,
                        'mesin' => $machineType
                    ];
                }
            }
        }

        // Process results into machine data array
        $machineData = [];
        foreach ($machines as $machine) {
            $machineId = $machine['id_machine'];
            
            // Skip machines that don't match the filter
            if (!empty($filterMesin) && strpos($machineId, "-$filterMesin-") === false) {
                continue;
            }

            $machineData[$machineId] = [
                'mesin' => $machine['mesin'],
                'id_machine' => $machineId,
                'days' => []
            ];

            // Initialize all days as EMPTY
            for ($day = 1; $day <= $jumlahHari; $day++) {
                $machineData[$machineId]['days'][$day] = 'EMPTY';
            }
        }

        // Update status for days that have data
        foreach ($results as $row) {
            $machineId = $row['id_machine'];
            if (isset($machineData[$machineId]) && !empty($row['tanggal'])) {
                $day = date('j', strtotime($row['tanggal']));
                $machineData[$machineId]['days'][$day] = $row['status'];
            }
        }

        $data = [
            'title' => 'Dashboard v3',
            'machineData' => array_values($machineData),
            'machines' => array_values($processedMachines),
            'filterBulan' => $filterBulan,
            'filterMesin' => $filterMesin,
            'jumlahHari' => $jumlahHari,
            'bulan' => sprintf('%s-%s', $filterYear, $filterMonth) // Format for view
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
        $builder = $db->table('preuse_tb_checksheet cs');
        $builder->select('dc.id as detail_id, dc.item_check, dc.inspeksi, dc.standar, dc.status, dc.is_resolved, scl.id as status_change_log_id');
        $builder->join('preuse_tb_detail_checksheet dc', 'cs.id = dc.checksheet_id');
        $builder->join('preuse_tb_status_change_log scl', 'dc.id = scl.detail_checksheet_id', 'left');
        $builder->where('cs.id_machine', $machineId);
        $builder->where('dc.tanggal', $date);
        $builder->where('dc.status', 'NG');
        $builder->orderBy('dc.created_at', 'DESC');
        
        $query = $builder->get();
        $result = $query->getResultArray();

        // Debug log
        log_message('debug', 'NG Details Query: ' . $builder->getCompiledSelect());
        log_message('debug', 'NG Details Result: ' . json_encode($result));

        return $this->response->setJSON($result);
    }
}
