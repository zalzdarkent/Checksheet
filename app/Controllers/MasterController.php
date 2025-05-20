<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DetailMaster;
use App\Models\Master;
use App\Models\DetailChecksheet;
use App\Models\MasterMesin;
use CodeIgniter\HTTP\ResponseInterface;

class MasterController extends BaseController
{
    protected $masterModel;
    protected $detailMasterModel;
    protected $detailChecksheetModel;

    public function __construct()
    {
        $this->masterModel = new Master();
        $this->detailMasterModel = new DetailMaster();
        $this->detailChecksheetModel = new DetailChecksheet();
    }
    public function index()
    {
        $model = new Master();
        $pager = \Config\Services::pager();

        // Set jumlah item per halaman
        $perPage = 10;

        // Hitung total records untuk pagination
        $totalRecords = $model->countAllResults();

        // Ambil nomor halaman dari URL, default ke halaman 1
        $page = $this->request->getGet('page') ?? 1;

        // Query dengan pagination
        $data['items'] = $model->findAll($perPage, ($page - 1) * $perPage);
        $data['title'] = 'Master Checksheet';
        $data['pager'] = $pager->makeLinks($page, $perPage, $totalRecords, 'bootstrap_pager');
        $data['currentPage'] = $page;

        return view('checksheet/master', $data);
    }
    public function create()
    {
        $mesinModel = new MasterMesin();
        $data['title'] = 'Form Master';

        // Ambil semua data mesin dari DB
        $data['mesinList'] = $mesinModel->findAll();

        return view('checksheet/master-form', $data);
    }

    public function store()
    {
        $masterModel = new Master();
        $detailMasterModel = new DetailMaster();

        $runHour = $this->request->getPost('run_hour') === '1' ? true : false;
        $temperature = $this->request->getPost('temperature') === '1' ? true : false;

        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'judul_checksheet' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Judul checksheet harus diisi'
                ]
            ],
            'mesin' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Minimal satu mesin harus dipilih'
                ]
            ],
            'id_machine' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'ID mesin tidak valid'
                ]
            ],
            'item_check.*' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Item check harus diisi'
                ]
            ],
            'inspeksi.*' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Inspeksi harus diisi'
                ]
            ],
            'standar.*' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Standar harus diisi'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        // Ambil data dari request
        $judulChecksheet = $this->request->getPost('judul_checksheet');
        $mesin = json_decode($this->request->getPost('mesin'), true); // Ubah JSON ke array
        $idMachine       = json_decode($this->request->getPost('id_machine'), true);
        $itemCheck = $this->request->getPost('item_check'); // Array
        $inspeksi = $this->request->getPost('inspeksi'); // Array
        $standar = $this->request->getPost('standar'); // Array

        // Simpan ke tb_master (hanya 1 kali)
        $masterData = [
            'judul_checksheet' => $judulChecksheet,
            'run_hour' => $runHour,
            'temperature' => $temperature,
            'mesin'            => json_encode($mesin), // Simpan dalam bentuk JSON
            'id_machine'       => json_encode($idMachine),
            'created_at'       => date('Y-m-d H:i:s'),
        ];

        // Debugging: Lihat semua data yang akan disimpan
        // dd([
        //     'Raw Request' => $this->request->getPost(),
        //     'Processed Data' => [
        //         'masterData' => $masterData,
        //         'run_hour (processed)' => $runHour,
        //         'temperature (processed)' => $temperature,
        //         'mesin (decoded)' => $mesin,
        //         'id_machine (decoded)' => $idMachine
        //     ]
        // ]);

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

        return redirect()->to('/master')->with('success', 'Data berhasil disimpan.');
    }

    public function edit($id)
    {
        // Ambil data dari tabel master
        $data['item'] = $this->masterModel->find($id);

        if (!$data['item']) {
            return redirect()->to('/master')->with('error', 'Data tidak ditemukan.');
        }

        // Decode JSON untuk mesin dan id_machine
        $data['mesin'] = json_decode($data['item']['mesin'], true);
        $data['id_machine'] = json_decode($data['item']['id_machine'], true);
        $data['run_hour'] = (bool)$data['item']['run_hour'];
        $data['temperature'] = (bool)$data['item']['temperature'];

        // Ambil semua data mesin dari MasterMesin
        $mesinModel = new MasterMesin();
        $data['mesinList'] = $mesinModel->findAll();

        // Ambil data dari tabel detail berdasarkan master_id
        $details = $this->detailMasterModel->getDetailsByMasterId($id);

        if (empty($details)) {
            return redirect()->to('/master')->with('error', 'Detail data tidak ditemukan.');
        }

        // Ubah hasil query ke array untuk digunakan di view
        $data['itemChecks']   = array_column($details, 'item_check');
        $data['inspeksiList'] = array_column($details, 'inspeksi');
        $data['standarList']  = array_column($details, 'standar');

        $data['title'] = 'Edit Checksheet ';
        return view('checksheet/master-edit', $data);
    }

    public function update($id)
    {
        $validation = \Config\Services::validation();
        $db = \Config\Database::connect();
        $detailModel = new \App\Models\DetailMaster();
        $detailChecksheetModel = new \App\Models\DetailChecksheet();
        $db->transBegin();

        try {
            // Ambil data lama dari database
            $existingData = $this->masterModel->find($id);

            // Validasi data yang dikirim dari form
            $inputData = $this->request->getPost();

            // Set rules validasi
            $validation->setRules([
                'judul' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Judul checksheet harus diisi'
                    ]
                ],
                'mesin' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Minimal satu mesin harus dipilih'
                    ]
                ],
                'mesin_id' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'ID mesin tidak valid'
                    ]
                ],
                'item_check.*' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Item check harus diisi'
                    ]
                ],
                'inspeksi.*' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Inspeksi harus diisi'
                    ]
                ],
                'standar.*' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Standar harus diisi'
                    ]
                ]
            ]);

            // Jalankan validasi
            if (!$validation->withRequest($this->request)->run()) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $validation->getErrors());
            }

            // Validasi tambahan untuk jumlah item
            if (empty($inputData['item_check']) || count($inputData['item_check']) === 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Minimal harus ada satu item check');
            }

            // Update data master
            $masterData = [
                'judul_checksheet' => $inputData['judul'],
                'mesin' => $inputData['mesin'],
                'id_machine' => $inputData['mesin_id'],
                'run_hour' => ($this->request->getPost('run_hour') === '1'),
                'temperature' => ($this->request->getPost('temperature') === '1'),
            ];
            $this->masterModel->update($id, $masterData);

            // Dapatkan item_check yang ada sebelumnya
            $existingDetails = $detailModel->where('master_id', $id)->findAll();
            $existingItemChecks = array_column($existingDetails, 'item_check');

            // Hapus detail lama dari tb_detail_master
            $detailModel->where('master_id', $id)->delete();

            // Insert detail baru ke tb_detail_master
            $itemChecks = $inputData['item_check'] ?? [];
            $inspeksiList = $inputData['inspeksi'] ?? [];
            $standarList = $inputData['standar'] ?? [];

            foreach ($itemChecks as $index => $itemCheck) {
                // Skip jika semua field dalam row kosong
                if (empty($itemCheck) && empty($inspeksiList[$index]) && empty($standarList[$index])) {
                    continue;
                }

                $detailData = [
                    'master_id' => $id,
                    'item_check' => $itemCheck,
                    'inspeksi' => $inspeksiList[$index],
                    'standar' => $standarList[$index]
                ];
                $detailModel->insert($detailData);
            }

            // Cek item_check yang dihapus
            $deletedItemChecks = array_diff($existingItemChecks, $itemChecks);

            // Soft delete data di tb_detail_checksheet untuk item_check yang dihapus
            if (!empty($deletedItemChecks)) {
                foreach ($deletedItemChecks as $deletedItemCheck) {
                    $detailChecksheetModel
                        ->where('item_check', $deletedItemCheck)
                        ->set(['deleted_at' => date('Y-m-d H:i:s')])
                        ->update();
                }
            }

            $db->transCommit();
            return redirect()->to('/master')->with('success', 'Data berhasil diupdate!');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $model = new Master(); // Gunakan model yang sesuai
        $data = $model->find($id);

        if ($data) {
            $model->delete($id);
            return redirect()->to('/master')->with('success', 'Data berhasil dihapus.');
        } else {
            return redirect()->to('/master')->with('error', 'Data tidak ditemukan.');
        }
    }
}
