<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Master;
use CodeIgniter\HTTP\ResponseInterface;

class MasterController extends BaseController
{
    public function index()
    {
        $model = new Master();
        $data['items'] = $model->findAll();
        $data['title'] = 'Master Checksheet ';
        return view('checksheet/master', $data);
    }
    public function store()
    {
        $checksheetModel = new Master();

        // dd($this->request->getPost('mesin'));
        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'mesin'        => 'required',
            'item_check'   => 'required',
            'inspeksi'     => 'required',
            'standar'      => 'required',
        ]);

        // if (!$this->validate($validation->getRules())) {
        //     return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        // }

        // Ambil data dari request
        $mesin = json_decode($this->request->getPost('mesin'), true); // Ubah JSON ke array
        $itemCheck = $this->request->getPost('item_check'); // Array
        $inspeksi = $this->request->getPost('inspeksi'); // Array
        $standar = $this->request->getPost('standar'); // Array

        $dataToInsert = [];
        foreach ($itemCheck as $key => $item) {
            $dataToInsert[] = [
                'mesin'        => json_encode($mesin), // Ambil mesin sesuai indeksnya
                'item_check'   => $item,
                'inspeksi'     => $inspeksi[$key],
                'standar'      => $standar[$key],
                'created_at'   => date('Y-m-d H:i:s'),
            ];
        }
        // berikan dd data yang akan di kirim
        // dd($dataToInsert);

        $checksheetModel->insertBatch($dataToInsert);
        return redirect()->to('/master-checksheet/index')->with('success', 'Data berhasil disimpan.');
    }

    public function edit($id)
    {
        $model = new Master();
        $data['item'] = $model->find($id);

        if (!$data['item']) {
            return redirect()->to('/master')->with('error', 'Data tidak ditemukan.');
        }

        $data['title'] = 'Edit Data';
        return view('checksheet/master-edit', $data);
    }

    public function update($id)
    {
        $model = new Master();
        $request = $this->request->getPost();

        // Validasi input
        $validationRules = [
            'mesin' => 'required',
            'item_check' => 'required',
            'inspeksi' => 'required',
            'standar' => 'required',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Data yang akan diperbarui
        $data = [
            'mesin' => is_string($request['mesin']) ? $request['mesin'] : json_encode($request['mesin']), // Cek dulu
            'item_check' => $request['item_check'],
            'inspeksi' => $request['inspeksi'],
            'standar' => $request['standar'],
        ];        

        $model->update($id, $data);
        return redirect()->to('/master-checksheet/index')->with('success', 'Data berhasil diperbarui.');
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
