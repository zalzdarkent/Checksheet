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

    public function __construct()
    {
        $this->checksheetModel = new Checksheet();
        $this->detailChecksheetModel = new DetailChecksheet();
    }

    public function index()
    {
        $db = \Config\Database::connect();

        // Total Checksheet (dari tabel tb_checksheet)
        $totalChecksheet = $db->table('tb_checksheet')->countAllResults();

        // Total OK dan NG (dari tabel tb_detail_checksheet)
        $totalOK = $db->table('tb_detail_checksheet')
                     ->where('status', 'OK')
                     ->countAllResults();
        
        $totalNG = $db->table('tb_detail_checksheet')
                     ->where('status', 'NG')
                     ->countAllResults();

        // Data bulanan (6 bulan terakhir)
        $monthlyData = $this->getMonthlyData();

        // Data status untuk pie chart
        $statusData = [
            'ok' => $totalOK,
            'ng' => $totalNG
        ];

        $data = [
            'title' => 'Dashboard',
            'totalChecksheet' => $totalChecksheet,
            'totalOK' => $totalOK,
            'totalNG' => $totalNG,
            'monthlyData' => $monthlyData,
            'statusData' => $statusData
        ];

        return view('layouts/dashboard', $data);
    }

    private function getMonthlyData()
    {
        $db = \Config\Database::connect();
        
        // Ambil data 6 bulan terakhir
        $months = [];
        $okData = [];
        $ngData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $months[] = date('M Y', strtotime($date));
            
            // Query untuk data bulanan menggunakan Query Builder
            $monthStart = date('Y-m-01 00:00:00', strtotime("-$i months"));
            $monthEnd = date('Y-m-t 23:59:59', strtotime("-$i months"));
            
            // Count OK untuk bulan ini
            $okCount = $db->table('tb_detail_checksheet')
                         ->where('status', 'OK')
                         ->where('CONVERT(DATE, created_at) >=', $monthStart)
                         ->where('CONVERT(DATE, created_at) <=', $monthEnd)
                         ->countAllResults();
            
            // Count NG untuk bulan ini
            $ngCount = $db->table('tb_detail_checksheet')
                         ->where('status', 'NG')
                         ->where('CONVERT(DATE, created_at) >=', $monthStart)
                         ->where('CONVERT(DATE, created_at) <=', $monthEnd)
                         ->countAllResults();
            
            $okData[] = $okCount;
            $ngData[] = $ngCount;
        }
        
        return [
            'labels' => $months,
            'ok' => $okData,
            'ng' => $ngData
        ];
    }
}
