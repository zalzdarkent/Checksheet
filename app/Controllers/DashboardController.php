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
        // Query untuk menghitung total temuan NG
        $query = $this->db->query("WITH StatusSequence AS (
            SELECT 
                item_check,
                STRING_AGG(status, ',') WITHIN GROUP (ORDER BY kolom) AS status_sequence
            FROM preuse_tb_detail_checksheet
            WHERE status = 'NG'
            GROUP BY item_check
        )
        SELECT 
            item_check,
            status_sequence
        FROM StatusSequence");

        $results = $query->getResultArray();

        $totalTemuan = 0;
        foreach ($results as $row) {
            $statusSequence = explode(',', $row['status_sequence']);
            $temuanCount = 0;
            $isLastOK = true;

            foreach ($statusSequence as $status) {
                if ($status === 'NG' && $isLastOK) {
                    $temuanCount++;
                }
                $isLastOK = ($status === 'OK');
            }
            $totalTemuan += $temuanCount;
        }

        // Query untuk menghitung status Open (NG terakhir)
        $openCount = $this->db->query("WITH LastStatus AS (
            SELECT 
                item_check,
                MAX(kolom) AS max_kolom
            FROM preuse_tb_detail_checksheet
            GROUP BY item_check
        )
        SELECT COUNT(*) AS open_count
        FROM preuse_tb_detail_checksheet t2
        INNER JOIN LastStatus t3
            ON t2.item_check = t3.item_check
            AND t2.kolom = t3.max_kolom
        WHERE t2.status = 'NG'")->getRow()->open_count;

        // Query untuk menghitung status Close (OK terakhir)
        $closeCount = $this->db->query("WITH LastStatus AS (
            SELECT 
                item_check,
                MAX(kolom) AS max_kolom
            FROM preuse_tb_detail_checksheet
            GROUP BY item_check
        )
        SELECT COUNT(*) AS close_count
        FROM preuse_tb_detail_checksheet t2
        INNER JOIN LastStatus t3
            ON t2.item_check = t3.item_check
            AND t2.kolom = t3.max_kolom
        WHERE t2.status = 'OK'")->getRow()->close_count;

        // Data Chart
        $monthlyData = $this->getMonthlyData();

        $data = [
            'title' => 'Dashboard',
            'totalChecksheet' => $totalTemuan,
            'totalNG' => $openCount,
            'totalOK' => $closeCount,
            'monthlyData' => $monthlyData,
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
