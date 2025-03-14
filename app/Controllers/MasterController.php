<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Master;
use CodeIgniter\HTTP\ResponseInterface;

class MasterController extends BaseController
{
    public function index()
    {
        $data['title'] = 'Master Checksheet ';
        return view('checksheet/master', $data);
    }
    public function store()
    {
        $checksheetModel = new Master();

        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'mesin'        => 'required',
            'item_check'   => 'required',
            'inspeksi'     => 'required',
            'standar'      => 'required',
        ]);

        if (!$this->validate($validation->getRules())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Ambil data dari request
        $mesin = $this->request->getPost('mesin'); // Ambil sebagai string JSON
        $itemCheck = $this->request->getPost('item_check'); // Array
        $inspeksi = $this->request->getPost('inspeksi'); // Array
        $standar = $this->request->getPost('standar'); // Array

        $dataToInsert = [];
        foreach ($itemCheck as $key => $item) {
            $dataToInsert[] = [
                'mesin'        => json_encode($mesin), // Simpan sebagai JSON
                'item_check'   => $item,
                'inspeksi'     => $inspeksi[$key],
                'standar'      => $standar[$key],
                'created_at'   => date('Y-m-d H:i:s'),
            ];
        }
        // berikan dd data yang akan di kirim
        dd($dataToInsert);

        $checksheetModel->insertBatch($dataToInsert);
        return redirect()->to('/master-checksheet')->with('success', 'Data berhasil disimpan.');
    }
}
