<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Checksheet;
use App\Models\DetailChecksheet;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
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
        // Get today's date in Y-m-d format
        $today = date('Y-m-d');

        // Query untuk menghitung total temuan berdasarkan sequence NG
        $query = $this->db->query("
            WITH ItemSequences AS (
                SELECT 
                    dc.checksheet_id,
                    dc.item_check,
                    STRING_AGG(dc.status, ',') WITHIN GROUP (ORDER BY dc.kolom) as status_sequence
                FROM preuse_tb_detail_checksheet dc
                GROUP BY dc.checksheet_id, dc.item_check
            )
            SELECT 
                checksheet_id,
                item_check,
                status_sequence
            FROM ItemSequences");

        $results = $query->getResultArray();
        
        $totalTemuan = 0;
        $openItems = 0;
        $closedItems = 0;

        foreach ($results as $row) {
            $statusSequence = explode(',', $row['status_sequence']);
            $temuanCount = 0;
            $isInNGSequence = false;
            $lastStatus = '';

            foreach ($statusSequence as $status) {
                if ($status === 'NG') {
                    if (!$isInNGSequence) {
                        $temuanCount++;
                        $isInNGSequence = true;
                    }
                } else if ($status === 'OK') {
                    $isInNGSequence = false;
                }
                $lastStatus = $status;
            }

            $totalTemuan += $temuanCount;
            
            // If last status is 'NG', it's open
            if ($lastStatus === 'NG') {
                $openItems++;
            } 
            // If last status is 'OK', it's closed
            else if ($lastStatus === 'OK') {
                $closedItems++;
            }
        }

        // Get machine status for each line for today only
        $machineStatus = $this->db->query("
            WITH TodayChecksheets AS (
                SELECT 
                    cs.mesin,
                    cs.line,
                    cs.id as checksheet_id
                FROM preuse_tb_checksheet cs
                JOIN preuse_tb_detail_checksheet dc ON cs.id = dc.checksheet_id
                WHERE CAST(dc.tanggal AS DATE) = ?
                GROUP BY cs.mesin, cs.line, cs.id
            )
            SELECT 
                tc.mesin,
                tc.line,
                CASE 
                    WHEN EXISTS (
                        SELECT 1 
                        FROM preuse_tb_detail_checksheet dc 
                        WHERE dc.checksheet_id = tc.checksheet_id 
                        AND dc.status = 'NG'
                        AND CAST(dc.tanggal AS DATE) = ?
                    ) THEN 'NG'
                    ELSE 'R'
                END as status
            FROM TodayChecksheets tc
            ORDER BY tc.mesin, tc.line", [$today, $today])->getResultArray();

        // Convert to associative array for easier access in view
        $machineStatusMap = [];
        foreach ($machineStatus as $status) {
            $machineStatusMap[$status['mesin'] . '_' . $status['line']] = $status['status'];
        }

        // Get unique machines
        $machines = $this->db->query("
            SELECT DISTINCT mesin 
            FROM preuse_tb_checksheet 
            ORDER BY mesin")->getResultArray();

        // Data Chart
        $monthlyData = $this->getMonthlyData();

        $data = [
            'title' => 'Dashboard ',
            'totalChecksheet' => $totalTemuan,
            'totalNG' => $openItems,
            'totalOK' => $closedItems,
            'monthlyData' => $monthlyData,
            'machines' => $machines,
            'machineStatus' => $machineStatusMap
        ];

        return view('layouts/dashboard', $data);
    }

    private function getMonthlyData()
    {
        // Ambil data 6 bulan terakhir
        $months = [];
        $okData = [];
        $ngData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $months[] = date('M Y', strtotime("-$i months"));

            // Query untuk status OK
            $okCount = $this->db->table('preuse_tb_detail_checksheet')
                ->where('status', 'OK')
                ->where("FORMAT(created_at, 'yyyy-MM')", $date)
                ->countAllResults();

            // Query untuk status NG
            $ngCount = $this->db->table('preuse_tb_detail_checksheet')
                ->where('status', 'NG')
                ->where("FORMAT(created_at, 'yyyy-MM')", $date)
                ->countAllResults();

            $okData[] = $okCount;
            $ngData[] = $ngCount;
        }

        return [
            'months' => $months,
            'okData' => $okData,
            'ngData' => $ngData
        ];
    }

    public function dashboardV2()
    {
        $data = [
            'title' => 'Dashboard v2',
        ];

        return view('layouts/dashboard_v2', $data);
    }
}
