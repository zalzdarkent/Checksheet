<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Checksheet;
use App\Models\Master;
use App\Models\Karyawan;
use CodeIgniter\HTTP\ResponseInterface;

class ChecksheetController extends BaseController
{
    protected $checksheetModel;
    protected $db;
    protected $karyawanModel;

    public function __construct()
    {
        $this->checksheetModel = new Checksheet();
        $this->db = \Config\Database::connect();
        $this->karyawanModel = new Karyawan();
    }
    public function checksheet()
    {
        $db = \Config\Database::connect();
        $pager = \Config\Services::pager();

        // Set jumlah item per halaman
        $perPage = 10;

        // Ambil parameter line dari URL, kalau tidak ada default-nya null
        $line = $this->request->getGet('line');

        // Hitung total records untuk pagination, sesuaikan dengan filter line
        $builder = $db->table('preuse_tb_checksheet')
            ->select('preuse_tb_checksheet.*, preuse_tb_master.mesin as master_mesin, preuse_tb_master.id_machine as master_id_machine, preuse_tb_master.id as master_id')
            ->join('preuse_tb_master', 'preuse_tb_checksheet.master_id = preuse_tb_master.id', 'left');

        // Filter berdasarkan parameter line jika ada
        if ($line) {
            $builder->where('preuse_tb_checksheet.line', $line);
        }

        // Hitung total records dengan filter line
        $totalRecords = $builder->countAllResults(false);

        // Ambil nomor halaman dari URL, default ke halaman 1
        $page = $this->request->getGet('page') ?? 1;

        // Query dengan pagination dan filter line
        $checksheets = $builder
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        foreach ($checksheets as &$checksheet) {
            $checksheet['mesin'] = $checksheet['mesin'] ?? 'Unknown';
        }

        $masters = $db->table('preuse_tb_master')->get()->getResultArray();

        $data = [
            'title' => 'Checksheet Pre-Use',
            'checksheets' => $checksheets,
            'masters' => $masters,
            'pager' => $pager->makeLinks($page, $perPage, $totalRecords, 'bootstrap_pager'),
            'currentPage' => $page
        ];

        return view('checksheet/index', $data);
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
        // dd($this->request->getPost());
        $validation = \Config\Services::validation();

        // Aturan validasi
        $rules = [
            'bulan'      => 'required',
            'departemen' => 'required',
            'seksi'      => 'required',
            'mesin'      => 'required',
            'id_machine' => 'required',
            'line'       => 'required|numeric|greater_than[0]|less_than[8]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Ambil data dari form
        $mesinValue = $this->request->getPost('mesin'); // Format: "master_id|index"
        $bulan = $this->request->getPost('bulan');
        $line = $this->request->getPost('line');
        $idMachine = $this->request->getPost('id_machine');

        list($master_id, $mesin_index) = explode('|', $mesinValue); // Pisahkan ID Master dan Index Mesin

        // Ambil nama mesin berdasarkan index di preuse_tb_master
        $master = $this->db->table('preuse_tb_master')->where('id', $master_id)->get()->getRowArray();
        if (!$master) {
            return redirect()->back()->withInput()->with('error', 'Data master tidak ditemukan!');
        }

        $mesinList = json_decode($master['mesin'], true);
        $mesinName = $mesinList[$mesin_index] ?? 'Unknown';

        // Cek apakah kombinasi mesin, line, dan bulan sudah ada
        $existingChecksheet = $this->db->table('preuse_tb_checksheet')
            ->where('master_id', $master_id)
            ->where('mesin', $mesinName)
            ->where('line', $line)
            ->where('bulan', $bulan)
            ->get()
            ->getRowArray();

        if ($existingChecksheet) {
            $bulanFormatted = date('F Y', strtotime($bulan));
            return redirect()->back()->withInput()
                ->with('error', "Checksheet untuk mesin '{$mesinName}' Line {$line} pada bulan {$bulanFormatted} sudah ada!");
        }

        // Data yang akan disimpan
        $data = [
            'bulan'      => $bulan,
            'departemen' => $this->request->getPost('departemen'),
            'seksi'      => $this->request->getPost('seksi'),
            'master_id'  => $master_id,
            'mesin'      => $mesinName,
            'id_machine' => $idMachine,
            'line'       => $line,
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
            ->select('id, checksheet_id, tanggal, kolom, item_check, inspeksi, standar, status, npk, id_karyawan, is_submitted, is_resolved, deleted_at')
            ->where('checksheet_id', $id)
            ->get()
            ->getResultArray();

        // Debug data detailChecksheet
        // dd($detailChecksheet);

        // Ambil data karyawan untuk dropdown NPK
        $karyawanList = $this->karyawanModel->findAll();

        // Buat array status berdasarkan item_check dan tanggal
        $statusArray = [];
        $npkArray = [];
        $isSubmitted = false;
        $deletedItemChecks = []; // Array untuk menyimpan item_check yang dihapus

        // Pertama, cek apakah ada data yang submitted dan kumpulkan item_check yang dihapus
        foreach ($detailChecksheet as $row) {
            if (!empty($row['is_submitted']) && $row['is_submitted'] == 1) {
                $isSubmitted = true;
            }

            // Tambahkan item_check ke deletedItemChecks jika memiliki deleted_at
            if (!empty($row['deleted_at'])) {
                $deletedItemChecks[] = $row['item_check'];
            }
        }

        // Debug deletedItemChecks
        // dd($deletedItemChecks);

        // Kemudian, muat semua data terlepas dari status submitted
        foreach ($detailChecksheet as $row) {
            // Simpan status dan id_karyawan ke array
            $statusArray[$row['item_check']][$row['kolom']] = [
                'status' => $row['status'],
                'is_resolved' => $row['is_resolved'],
                'deleted_at' => $row['deleted_at'] // Tambahkan deleted_at ke statusArray
            ];
            if (!empty($row['id_karyawan'])) {
                $npkArray[$row['kolom']] = $row['id_karyawan'];
            }
        }

        $data = [
            'title' => 'Detail Checksheet',
            'checksheet' => $checksheet,
            'master' => $master,
            'detailMasters' => $detailMasters,
            'detailChecksheet' => $detailChecksheet,
            'statusArray' => $statusArray,
            'npkArray' => $npkArray,
            'isSubmitted' => $isSubmitted,
            'karyawanList' => $karyawanList,
            'deletedItemChecks' => $deletedItemChecks // Tambahkan data yang dihapus ke view
        ];

        // Debug data yang dikirim ke view
        // dd($data);

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

        // Ambil semua data mesin dari preuse_tb_master
        $masters = $db->table('preuse_tb_master')
            ->select('*')
            ->get()
            ->getResultArray(); // Mengambil semua data

        // Gabungkan data dari kedua tabel
        $data = [
            'title' => 'Edit Checksheet',
            'checksheet' => $checksheet,
            'masters' => $masters // Ubah dari getRowArray() ke getResultArray()
        ];

        return view('checksheet/edit', $data);
    }

    public function update($id)
    {
        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'mesin' => 'required',
            'id_machine' => 'required',
            'line' => 'required|numeric',
            'bulan' => 'required',
            'departemen' => 'required',
            'seksi' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Ambil data dari form
        $mesinValue = $this->request->getPost('mesin'); // Format: "master_id|index"
        $id_machine = $this->request->getPost('id_machine');
        $line = $this->request->getPost('line');
        $bulan = $this->request->getPost('bulan');
        $departemen = $this->request->getPost('departemen');
        $seksi = $this->request->getPost('seksi');

        list($master_id, $mesin_index) = explode('|', $mesinValue); // Pisahkan ID Master dan Index Mesin

        // Ambil nama mesin berdasarkan index di preuse_tb_master
        $master = $this->db->table('preuse_tb_master')->where('id', $master_id)->get()->getRowArray();
        if (!$master) {
            return redirect()->back()->withInput()->with('error', 'Data master tidak ditemukan!');
        }

        $mesinList = json_decode($master['mesin'], true);
        $mesinName = $mesinList[$mesin_index] ?? 'Unknown';

        // Cek apakah kombinasi mesin, line, dan bulan sudah ada (kecuali untuk data yang sedang diupdate)
        $existingChecksheet = $this->db->table('preuse_tb_checksheet')
            ->where('master_id', $master_id)
            ->where('mesin', $mesinName)
            ->where('line', $line)
            ->where('bulan', $bulan)
            ->where('id !=', $id) // Exclude current record
            ->get()
            ->getRowArray();

        if ($existingChecksheet) {
            $bulanFormatted = date('F Y', strtotime($bulan));
            return redirect()->back()->withInput()
                ->with('error', "Checksheet untuk mesin '{$mesinName}' Line {$line} pada bulan {$bulanFormatted} sudah ada!");
        }

        // Update data ke database
        $data = [
            'master_id' => $master_id,
            'mesin' => $mesinName,
            'id_machine' => $id_machine,
            'line' => $line,
            'bulan' => $bulan,
            'departemen' => $departemen,
            'seksi' => $seksi
        ];

        $this->checksheetModel->update($id, $data);

        return redirect()->to('/checksheet')->with('success', 'Data checksheet berhasil diperbarui');
    }

    public function destroy($id)
    {
        $model = new Checksheet();
        $model->delete($id);
        return redirect()->to('/checksheet')->with('success', 'Data berhasil dihapus');
    }
}
