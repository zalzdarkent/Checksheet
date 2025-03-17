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
            ->select('tb_checksheet.*, tb_master.mesin')
            ->join('tb_master', 'tb_checksheet.master_id = tb_master.id', 'left')
            ->get()
            ->getResultArray();

        foreach ($checksheets as &$checksheet) {
            $mesinList = json_decode($checksheet['mesin'], true); // Decode JSON ke array
            $index = (int) $checksheet['mesin_index']; // Ambil index yang disimpan
            $checksheet['mesin_name'] = $mesinList[$index] ?? 'Unknown'; // Ambil nama mesin dari array
        }

        $masters = $this->db->table('tb_master')->get()->getResultArray(); // Ambil master mesin

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
        $request = $this->request->getPost();

        // Cek data yang diterima
        dd($request);

        list($masterId, $mesinIndex) = explode('|', $request['master_id']);

        // Cek apakah explode berhasil
        // dd([
        //     'master_id'  => $masterId,
        //     'mesin_index' => $index
        // ]);
        // Debug sebelum insert
        // dd($insertData);
        $masterId = (int) $masterId;
        $mesinIndex = (int) $mesinIndex;

        try {
            // Gunakan raw query untuk insert
            $this->db->query("INSERT INTO tb_checksheet (master_id, mesin_index, bulan, departemen, seksi) 
                              VALUES (?, ?, ?, ?, ?)", [
                $masterId,
                $mesinIndex,
                $request['bulan'],
                $request['departemen'],
                $request['seksi']
            ]);

            return redirect()->to('/list-checksheet')->with('success', 'Data berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function detail($id, $mesin_index = null)
    {
        $db = \Config\Database::connect();

        // Query untuk mendapatkan data checksheet
        $queryChecksheet = $db->table('tb_checksheet')
            ->select('*')
            ->where('id', $id)
            ->get();
        $checksheet = $queryChecksheet->getRowArray(); // Ambil hanya satu baris karena ini checksheet utama

        if (!$checksheet) {
            return redirect()->to('/table-checksheet')->with('error', 'Data tidak ditemukan');
        }

        // Query untuk mendapatkan semua data dari tb_master berdasarkan master_id dari checksheet
        $queryMaster = $db->table('tb_master')
            ->select('*')
            ->where('id', $checksheet['master_id']) // Ambil semua data yang sesuai dengan master_id
            ->get();
        $masterData = $queryMaster->getResultArray(); // Ambil semua data dalam bentuk array

        $data = [
            'title' => 'Detail Checksheet',
            'checksheet' => $checksheet, // Data utama checksheet (hanya satu baris)
            'masterData' => $masterData, // Semua data dari tb_master
            'mesin_index' => $mesin_index ?? $checksheet['mesin_index']
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
