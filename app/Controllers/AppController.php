<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Checksheet;
use CodeIgniter\HTTP\ResponseInterface;

class AppController extends BaseController
{
    protected $checksheetModel;

    public function __construct()
    {
        $this->checksheetModel = new Checksheet();
    }
    public function checksheet()
    {
        $checksheets = new Checksheet();
        $data['checksheets'] = $checksheets->findAll();
        $data['title'] = 'List Checksheet ';
        return view('checksheet/index', $data);
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
        $model = new Checksheet();
        $request = $this->request->getPost();

        // Aturan validasi
        $validationRules = [
            'mesin' => 'required',
            'bulan' => 'required|regex_match[/^\d{4}-(0[1-9]|1[0-2])$/]', // Format YYYY-MM
            'departemen' => 'required',
            'seksi' => 'required',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Ambil data yang sudah divalidasi
        $mesin = $request['mesin'];
        $bulanRaw = $request['bulan'];
        $departemen = $request['departemen'];
        $seksi = $request['seksi'];

        // Ubah format bulan (YYYY-MM) menjadi (Nama Bulan YYYY)
        $dateObj = \DateTime::createFromFormat('Y-m', $bulanRaw);
        $bulan = strftime('%B %Y', $dateObj->getTimestamp()); // Contoh: Februari 2025

        // Simpan ke database
        $model->insert([
            'mesin' => $mesin,
            'bulan' => $bulan,
            'departemen' => $departemen, // Data dari API nantinya
            'seksi' => $seksi // Data dari API nantinya
        ]);

        return redirect()->to('/list-checksheet')->with('success', 'Data berhasil disimpan');
    }

    public function detail($id)
    {
        $checksheet = $this->checksheetModel->find($id);

        if (!$checksheet) {
            return redirect()->to('/table-checksheet')->with('error', 'Data tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Checksheet',
            'checksheet' => $checksheet
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
