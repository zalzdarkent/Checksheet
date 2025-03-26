<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DetailChecksheet;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardV2Controller extends BaseController
{
    public function index()
    {
        $model = new DetailChecksheet();
        
        $bulan = $this->request->getGet('bulan') ?? date('Y-m'); // Format: YYYY-MM
        $line = $this->request->getGet('line'); // Line yang dipilih
        $mesin = $this->request->getGet('mesin'); // Mesin yang dipilih

        // Get list of unique machines from checksheet table
        $machines = $model->db->query('
            SELECT DISTINCT c.mesin 
            FROM preuse_tb_detail_checksheet d 
            JOIN preuse_tb_checksheet c ON c.id = d.checksheet_id 
            WHERE c.mesin IS NOT NULL 
            ORDER BY c.mesin
        ')->getResultArray();

        // Join dengan tabel preuse_tb_checksheet untuk mengambil line
        $query = $model->select("preuse_tb_checksheet.line, preuse_tb_detail_checksheet.status, COUNT(*) as total")
            ->join('preuse_tb_checksheet', 'preuse_tb_checksheet.id = preuse_tb_detail_checksheet.checksheet_id')
            ->where("FORMAT(preuse_tb_detail_checksheet.tanggal, 'yyyy-MM') =", $bulan)
            ->whereIn('preuse_tb_detail_checksheet.status', ['OK', 'NG']);

        if (!empty($line)) {
            $query->where('preuse_tb_checksheet.line', $line);
        }

        if (!empty($mesin)) {
            $query->where('preuse_tb_checksheet.mesin', $mesin);
        }

        $query->groupBy('preuse_tb_checksheet.line, preuse_tb_detail_checksheet.status');
        $data = $query->findAll();

        // Struktur ulang data agar bisa digunakan di view
        $lines = ['1', '2', '3', '4', '5', '6', '7'];
        $chartData = [
            'OK' => array_fill(0, 7, 0),
            'NG' => array_fill(0, 7, 0)
        ];

        foreach ($data as $row) {
            $index = array_search($row['line'], $lines);
            if ($index !== false) {
                $chartData[$row['status']][$index] = (int) $row['total'];
            }
        }

        return view('layouts/dashboard_v2', [
            'title' => 'Dashboard v2',
            'chartData' => $chartData,
            'selectedBulan' => $bulan,
            'selectedLine' => $line,
            'selectedMesin' => $mesin,
            'machines' => $machines
        ]);
    }
}
