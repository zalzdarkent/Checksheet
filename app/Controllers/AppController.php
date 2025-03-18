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
        $checksheets = $this->db->table('tb_checksheet')
            ->select('tb_checksheet.*, tb_master.mesin as master_mesin, tb_master.id as master_id')
            ->join('tb_master', 'tb_checksheet.master_id = tb_master.id', 'left')
            ->get()
            ->getResultArray();

        foreach ($checksheets as &$checksheet) {
            $checksheet['mesin'] = $checksheet['mesin'] ?? 'Unknown'; // Langsung ambil dari tb_checksheet
        }

        $masters = $this->db->table('tb_master')->get()->getResultArray(); // Ambil data tb_master

        return view('checksheet/index', [
            'checksheets' => $checksheets,
            'masters' => $masters,
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
        list($master_id, $mesin_index) = explode('|', $mesinValue); // Pisahkan ID Master dan Index Mesin

        // Ambil nama mesin berdasarkan index di tb_master
        $master = $this->db->table('tb_master')->where('id', $master_id)->get()->getRowArray();
        $mesinList = json_decode($master['mesin'], true);
        $mesinName = $mesinList[$mesin_index] ?? 'Unknown'; // Ambil nama mesin berdasarkan index

        // Data yang akan disimpan
        $data = [
            'bulan'      => $this->request->getPost('bulan'),
            'departemen' => $this->request->getPost('departemen'),
            'seksi'      => $this->request->getPost('seksi'),
            'master_id'  => $master_id, // Simpan ID dari tb_master
            'mesin'      => $mesinName, // Simpan nama mesin
        ];

        // Simpan ke database
        $this->db->table('tb_checksheet')->insert($data);

        return redirect()->to('/list-checksheet')->with('success', 'Data berhasil disimpan!');
    }

    public function detail($id)
    {
        $db = \Config\Database::connect();

        // Ambil data checksheet berdasarkan ID
        $checksheet = $db->table('tb_checksheet')
            ->select('*')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$checksheet) {
            return redirect()->to('/table-checksheet')->with('error', 'Data tidak ditemukan');
        }

        // Ambil data master berdasarkan master_id di tb_checksheet
        $master = $db->table('tb_master')
            ->select('*')
            ->where('id', $checksheet['master_id'])
            ->get()
            ->getRowArray();

        // Ambil data dari tb_detail_master berdasarkan master_id
        $detailMasters = $db->table('tb_detail_master')
            ->select('*')
            ->where('master_id', $checksheet['master_id'])
            ->get()
            ->getResultArray();

        // Ambil data status dari tb_detail_checksheet berdasarkan tanggal
        $detailChecksheet = $db->table('tb_detail_checksheet')
            ->select('*')
            ->where('checksheet_id', $id)
            ->get()
            ->getResultArray();

        // Buat array status berdasarkan item_check dan tanggal
        $statusArray = [];
        $npkArray = [];
        $isSubmitted = false;
        foreach ($detailChecksheet as $row) {
            if (!empty($row['is_submitted']) && $row['is_submitted'] == 1) {
                $isSubmitted = true;
                break; // Stop loop jika sudah menemukan satu yang submitted
            }

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
        $model = new Checksheet();
        $data['checksheet'] = $model->find($id);

        if (!$data['checksheet']) {
            return redirect()->to('/checksheet')->with('error', 'Data tidak ditemukan!');
        }

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
        return redirect()->to('/list-checksheet')->with('success', 'Checksheet berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $model = new Checksheet();
        $model->delete($id);
        return redirect()->to('/list-checksheet')->with('success', 'Data berhasil dihapus');
    }
}
