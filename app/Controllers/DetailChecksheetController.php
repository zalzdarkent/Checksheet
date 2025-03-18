<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DetailChecksheet;
use CodeIgniter\HTTP\ResponseInterface;

class DetailChecksheetController extends BaseController
{
    public function saveStatus()
    {
        $model = new DetailChecksheet();

        // dd($this->request->getPost());
        $checksheetId = $this->request->getPost('checksheet_id');
        $statusData = $this->request->getPost('status');
        $npkData = $this->request->getPost('npk');
        $action = $this->request->getPost('action');
        $tanggalData = $this->request->getPost('tanggal'); // Ambil tanggal dalam bentuk array

        // Ambil data tambahan dari form
        $itemCheckData = $this->request->getPost('item_check');
        $inspeksiData = $this->request->getPost('inspeksi');
        $standarData = $this->request->getPost('standar');

        $today = date('j'); // Tanggal hari ini

        if (!$checksheetId || empty($statusData)) {
            return redirect()->back()->with('error', 'Data tidak lengkap!');
        }

        $hasAnyStatus = false;
        foreach ($statusData as $rowIndex => $statuses) {
            foreach ($statuses as $colIndex => $status) {
                if (!empty($status)) {
                    $hasAnyStatus = true;

                    // Cek apakah NPK diisi
                    if (empty($npkData[$colIndex])) {
                        return redirect()->back()->with('error', 'NPK harus diisi untuk tanggal yang memiliki OK/NG.');
                    }

                    // Cek apakah tanggal lebih dari hari ini
                    if ($colIndex > $today) {
                        return redirect()->back()->with('error', 'Tidak bisa mengisi data untuk tanggal yang belum lewat.');
                    }
                }
            }
        }

        if (!$hasAnyStatus) {
            return redirect()->back()->with('error', 'Minimal satu OK/NG harus dipilih.');
        }

        $filteredTanggal = [];
        foreach ($tanggalData as $key => $tanggal) {
            if (!empty($statusData[$key]) && !empty($npkData[$key])) {
                $filteredTanggal[] = $tanggal;
            }
        }

        // **Debugging untuk memastikan tanggal terbawa dengan benar**
        dd([
            'checksheet_id' => $checksheetId,
            'tanggalData'   => $tanggalData[$colIndex + 1] ?? $colIndex + 1, // Cek apakah input hidden ini terbawa
            'statusData'    => $statusData,
            'npkData'       => $npkData,
            'colIndex'      => array_keys($statusData[0] ?? []),
        ]);

        // Simpan data ke database
        foreach ($statusData as $rowIndex => $statuses) {
            foreach ($statuses as $colIndex => $status) {
                if (!empty($status)) {
                    // Pastikan indeks tanggal sesuai dengan colIndex
                    // $tanggalFix = $tanggalData[$colIndex - 1] ?? $colIndex;

                    $data = [
                        'checksheet_id' => $checksheetId,
                        'tanggal'       => $tanggalData, // Gunakan tanggal yang benar
                        'item_check'    => $itemCheckData[$rowIndex] ?? 'UNKNOWN',
                        'inspeksi'      => $inspeksiData[$rowIndex] ?? null,
                        'standar'       => $standarData[$rowIndex] ?? null,
                        'status'        => $status,
                        'npk'           => $npkData[$colIndex] ?? null,
                        'is_submitted'  => ($action == 'submit') ? 1 : 0,
                    ];
                    $model->insert($data);
                }
            }
        }

        return redirect()->back()->with('success', 'Data berhasil ' . ($action == 'submit' ? 'dikirim!' : 'disimpan!'));
    }
}
