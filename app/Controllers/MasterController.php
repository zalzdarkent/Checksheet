<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DetailMaster;
use App\Models\Master;
use CodeIgniter\HTTP\ResponseInterface;

class MasterController extends BaseController
{
    protected $masterModel;
    protected $detailMasterModel;

    public function __construct()
    {
        $this->masterModel = new Master();
        $this->detailMasterModel = new DetailMaster();
    }
    public function index()
    {
        $model = new Master();
        $data['items'] = $model->findAll();
        $data['title'] = 'Master Checksheet ';
        return view('checksheet/master', $data);
    }
    public function create()
    {
        $data['title'] = 'Form Master ';
        return view('checksheet/master-form', $data);
    }
    public function store()
    {
        $masterModel = new Master(); // Model untuk tb_master
        $detailMasterModel = new DetailMaster(); // Model untuk tb_detail_master

        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'judul_checksheet' => 'required',
            'mesin'            => 'required',
            'item_check'       => 'required',
            'inspeksi'         => 'required',
            'standar'          => 'required',
        ]);

        if (!$this->validate($validation->getRules())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Ambil data dari request
        $judulChecksheet = $this->request->getPost('judul_checksheet');
        $mesin = json_decode($this->request->getPost('mesin'), true); // Ubah JSON ke array
        $itemCheck = $this->request->getPost('item_check'); // Array
        $inspeksi = $this->request->getPost('inspeksi'); // Array
        $standar = $this->request->getPost('standar'); // Array

        // Simpan ke tb_master (hanya 1 kali)
        $masterData = [
            'judul_checksheet' => $judulChecksheet,
            'mesin'            => json_encode($mesin), // Simpan dalam bentuk JSON
            'created_at'       => date('Y-m-d H:i:s'),
        ];

        $masterModel->insert($masterData);
        $masterId = $masterModel->insertID(); // Ambil ID master yang baru disimpan

        // Simpan ke tb_detail_master (hanya simpan item_check, inspeksi, standar)
        $dataToInsert = [];
        foreach ($itemCheck as $key => $item) {
            $dataToInsert[] = [
                'master_id'   => $masterId, // Hubungkan dengan master
                'item_check'  => $item,
                'inspeksi'    => $inspeksi[$key],
                'standar'     => $standar[$key],
                'created_at'  => date('Y-m-d H:i:s'),
            ];
        }

        // dd($dataToInsert); // Debugging untuk cek data sebelum insert

        $detailMasterModel->insertBatch($dataToInsert);

        return redirect()->to('/master-checksheet/index')->with('success', 'Data berhasil disimpan.');
    }

    public function edit($id)
    {
        $model = new Master();
        $detailModel = new DetailMaster(); // Model untuk tabel kedua

        // Ambil data dari tabel master
        $data['item'] = $model->find($id);

        if (!$data['item']) {
            return redirect()->to('/master')->with('error', 'Data tidak ditemukan.');
        }

        // Ambil data dari tabel detail berdasarkan master_id
        $details = $detailModel->where('master_id', $id)->findAll();

        // Ubah hasil query ke array untuk digunakan di view
        $data['itemChecks'] = array_column($details, 'item_check');
        $data['inspeksiList'] = array_column($details, 'inspeksi');
        $data['standarList'] = array_column($details, 'standar');

        $data['title'] = 'Edit Data';
        return view('checksheet/master-edit', $data);
    }

    public function update($id)
    {
        $validation = \Config\Services::validation();
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // Ambil data lama dari database
            $existingData = $this->masterModel->find($id);

            // Validasi data yang dikirim dari form
            $inputData = $this->request->getPost();

            if (!$this->validate([
                'judul'   => 'permit_empty',
                'mesin'   => 'permit_empty',
                'item_check' => 'permit_empty',
                'inspeksi' => 'permit_empty',
                'standar'  => 'permit_empty',
            ])) {
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }

            // Siapkan data yang akan diupdate (hanya yang dikirim dari form)
            $masterData = [
                'judul_checksheet' => $inputData['judul'] ?? $existingData['judul_checksheet'], // Jika tidak dikirim, pakai data lama
                'mesin' => isset($inputData['mesin'])
                    ? (is_array($inputData['mesin']) ? json_encode($inputData['mesin']) : $inputData['mesin'])
                    : $existingData['mesin'], // Hindari JSON nested
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Update tb_master
            $this->masterModel->update($id, $masterData);

            // Update tb_detail_master hanya jika ada perubahan
            if (isset($inputData['item_check']) && count($inputData['item_check']) > 0) {
                // Hapus detail lama hanya jika ada data baru
                $this->detailMasterModel->where('master_id', $id)->delete();

                // Masukkan data detail master baru
                $detailData = [];
                foreach ($inputData['item_check'] as $index => $itemCheck) {
                    $detailData[] = [
                        'item_check' => $itemCheck,
                        'inspeksi'   => $inputData['inspeksi'][$index] ?? null,
                        'standar'    => $inputData['standar'][$index] ?? null,
                        'master_id'  => $id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                }

                $this->detailMasterModel->insertBatch($detailData);
            }

            $db->transCommit();
            return redirect()->to('/master-checksheet/index')->with('success', 'Data berhasil diperbarui');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $model = new Master(); // Gunakan model yang sesuai
        $data = $model->find($id);

        if ($data) {
            $model->delete($id);
            return redirect()->to('/master-checksheet/index')->with('success', 'Data berhasil dihapus.');
        } else {
            return redirect()->to('/master-checksheet/index')->with('error', 'Data tidak ditemukan.');
        }
    }
}
