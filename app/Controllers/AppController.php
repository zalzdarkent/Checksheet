<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Checksheet;
use App\Models\Master;
use CodeIgniter\HTTP\ResponseInterface;

class AppController extends BaseController
{
    protected $checksheetModel;
    protected $db;

    public function __construct()
    {
        $this->checksheetModel = new Checksheet();
        $this->db = \Config\Database::connect();
    }
    public function checksheet()
    {
        $db = \Config\Database::connect();
        $pager = \Config\Services::pager();

        // Set jumlah item per halaman
        $perPage = 10;
        
        // Hitung total records untuk pagination
        $totalRecords = $db->table('preuse_tb_checksheet')
            ->countAllResults();

        // Ambil nomor halaman dari URL, default ke halaman 1
        $page = $this->request->getGet('page') ?? 1;

        // Query dengan pagination
        $checksheets = $db->table('preuse_tb_checksheet')
            ->select('preuse_tb_checksheet.*, preuse_tb_master.mesin as master_mesin, preuse_tb_master.id as master_id')
            ->join('preuse_tb_master', 'preuse_tb_checksheet.master_id = preuse_tb_master.id', 'left')
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        foreach ($checksheets as &$checksheet) {
            $checksheet['mesin'] = $checksheet['mesin'] ?? 'Unknown';
        }

        $masters = $db->table('preuse_tb_master')->get()->getResultArray();

        return view('checksheet/index', [
            'checksheets' => $checksheets,
            'masters' => $masters,
            'pager' => $pager->makeLinks($page, $perPage, $totalRecords, 'bootstrap_pager'),
            'currentPage' => $page
        ]);
    }

    public function tableChecksheet()
    {
        $data['title'] = 'Tabel Checksheet';
        return view('checksheet/tabel', $data);
    }

    public function dashboard()
    {
        $data['title'] = 'Dashboard ';
        return view('layouts/dashboard', $data);
    }

    public function checksheetCreate()
    {
        $data['title'] = 'Form Checksheet ';
        return view('checksheet/form', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();

        // Aturan validasi
        $rules = [
            'bulan'      => 'required',
            'departemen' => 'required',
            'seksi'      => 'required',
            'mesin'      => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Ambil data dari form
        $mesinValue = $this->request->getPost('mesin'); // Format: "master_id|index"
        $bulan = $this->request->getPost('bulan');
        
        list($master_id, $mesin_index) = explode('|', $mesinValue); // Pisahkan ID Master dan Index Mesin

        // Ambil nama mesin berdasarkan index di preuse_tb_master
        $master = $this->db->table('preuse_tb_master')->where('id', $master_id)->get()->getRowArray();
        if (!$master) {
            return redirect()->back()->withInput()->with('error', 'Data master tidak ditemukan!');
        }

        $mesinList = json_decode($master['mesin'], true);
        $mesinName = $mesinList[$mesin_index] ?? 'Unknown';

        // Cek apakah kombinasi mesin dan bulan sudah ada
    $existingChecksheet = $this->db->table('preuse_tb_checksheet')
            ->where('master_id', $master_id)
            ->where('mesin', $mesinName)
            ->where('bulan', $bulan)
            ->get()
            ->getRowArray();

        if ($existingChecksheet) {
            $bulanFormatted = date('F Y', strtotime($bulan));
            return redirect()->back()->withInput()
                ->with('error', "Checksheet untuk mesin '{$mesinName}' pada bulan {$bulanFormatted} sudah ada!");
        }

        // Data yang akan disimpan
        $data = [
            'bulan'      => $bulan,
            'departemen' => $this->request->getPost('departemen'),
            'seksi'      => $this->request->getPost('seksi'),
            'master_id'  => $master_id,
            'mesin'      => $mesinName,
        ];

        // Simpan ke database
        $this->db->table('preuse_tb_checksheet')->insert($data);

        return redirect()->to('/checksheet')->with('success', 'Data berhasil disimpan!');
    }

    public function detail($id)
    {
        $db = \Config\Database::connect();

        // Ambil data checksheet berdasarkan ID
        $checksheet = $db->table('preuse_tb_checksheet')
            ->select('*')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$checksheet) {
            return redirect()->to('/checksheet')->with('error', 'Data tidak ditemukan!');
        }

        // Ambil data master berdasarkan master_id di preuse_tb_checksheet
        $master = $db->table('preuse_tb_master')
            ->select('*')
            ->where('id', $checksheet['master_id'])
            ->get()
            ->getRowArray();

        // Ambil data dari preuse_tb_detail_master berdasarkan master_id
        $detailMasters = $db->table('preuse_tb_detail_master')
            ->select('*')
            ->where('master_id', $checksheet['master_id'])
            ->get()
            ->getResultArray();

        // Ambil data status dari preuse_tb_detail_checksheet berdasarkan tanggal
        $detailChecksheet = $db->table('preuse_tb_detail_checksheet')
            ->select('*')
            ->where('checksheet_id', $id)
            ->get()
            ->getResultArray();

        // Buat array status berdasarkan item_check dan tanggal
        $statusArray = [];
        $npkArray = [];
        $isSubmitted = false;

        // Pertama, cek apakah ada data yang submitted
        foreach ($detailChecksheet as $row) {
            if (!empty($row['is_submitted']) && $row['is_submitted'] == 1) {
                $isSubmitted = true;
                break;
            }
        }

        // Kemudian, muat semua data terlepas dari status submitted
        foreach ($detailChecksheet as $row) {
            // Simpan status dan npk ke array
            $statusArray[$row['item_check']][$row['kolom']] = $row['status'];
            if (!empty($row['npk'])) {
                $npkArray[$row['kolom']] = $row['npk'];
            }
        }

        $data = [
            'title' => 'Detail Checksheet',
            'checksheet' => $checksheet,
            'master' => $master,
            'detailMasters' => $detailMasters,
            'detailChecksheet' => $detailChecksheet,
            'statusArray' => $statusArray, // Kirim status ke view
            'npkArray' => $npkArray,
            'isSubmitted' => $isSubmitted
        ];

        return view('checksheet/tabel', $data);
    }

    public function edit($id)
    {
        $db = \Config\Database::connect();

        // Ambil data checksheet berdasarkan ID
        $checksheet = $db->table('preuse_tb_checksheet')
            ->select('*')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$checksheet) {
            return redirect()->to('/checksheet')->with('error', 'Data tidak ditemukan!');
        }

        // Ambil data master berdasarkan master_id di preuse_tb_checksheet
        $masters = $db->table('preuse_tb_master')
            ->select('*')
            ->where('id', $checksheet['master_id'])
            ->get()
            ->getRowArray();

        // Gabungkan data dari kedua tabel
        $data = [
            'title' => 'Edit Checksheet',
            'checksheet' => $checksheet,
            'masters' => $masters
        ];

        return view('checksheet/edit', $data);
    }

    public function update($id)
    {
        $data = [
            'mesin' => $this->request->getPost('mesin'),
            'bulan' => $this->request->getPost('bulan'),
            'departemen' => $this->request->getPost('departemen'),
            'seksi' => $this->request->getPost('seksi'),
        ];

        $this->checksheetModel->update($id, $data);
        return redirect()->to('/checksheet')->with('success', 'Checksheet berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $model = new Checksheet();
        $model->delete($id);
        return redirect()->to('/checksheet')->with('success', 'Data berhasil dihapus');
    }
}
