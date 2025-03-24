<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DetailChecksheet;
use App\Models\Checksheet;
use App\Models\DetailMaster;
use App\Models\Npk;
use CodeIgniter\HTTP\ResponseInterface;

class DetailChecksheetController extends BaseController
{
    public function saveStatus()
    {
        $model = new DetailChecksheet();
        $checksheetModel = new Checksheet();

        $checksheetId = $this->request->getPost('checksheet_id');
        $statusData = $this->request->getPost('status');
        $npkData = $this->request->getPost('npk');
        $action = $this->request->getPost('action');
        $itemCheckData = $this->request->getPost('item_check');
        $inspeksiData = $this->request->getPost('inspeksi');
        $standarData = $this->request->getPost('standar');

        $checksheet = $checksheetModel->find($checksheetId);
        if (!$checksheetId || !$checksheet) {
            return redirect()->back()->with('error', 'Data checksheet tidak ditemukan!');
        }

        $today = date('j');
        $hasChanges = false;

        // Process NPK updates first
        foreach ($npkData as $colIndex => $npk) {
            if (!empty($npk)) {
                if (!ctype_digit($npk)) {
                    return redirect()->back()->with('error', 'NPK hanya boleh berisi angka!');
                }

                $existingData = $model->where([
                    'checksheet_id' => $checksheetId,
                    'kolom' => intval($colIndex),
                    'is_submitted' => 1
                ])->first();

                if ($existingData) {
                    continue; // Skip if already submitted
                }

                $existing = $model->where([
                    'checksheet_id' => $checksheetId,
                    'kolom' => intval($colIndex),
                ])->first();

                if ($existing) {
                    $model->update($existing['id'], ['npk' => $npk]);
                    $hasChanges = true;
                }
            }
        }

        // Process status updates
        if (!empty($statusData)) {
            foreach ($statusData as $rowIndex => $statuses) {
                foreach ($statuses as $colIndex => $status) {
                    if (empty($status)) continue;

                    if ($colIndex > $today) {
                        return redirect()->back()->with('error', 'Tidak bisa mengisi data untuk tanggal yang belum lewat.');
                    }

                    if (empty($npkData[$colIndex])) {
                        return redirect()->back()->with('error', 'NPK harus diisi untuk tanggal yang memiliki OK/NG.');
                    }

                    $existingData = $model->where([
                        'checksheet_id' => $checksheetId,
                        'kolom' => intval($colIndex),
                        'is_submitted' => 1
                    ])->first();

                    if ($existingData) {
                        return redirect()->back()->with('error', 'Data untuk tanggal ' . $colIndex . ' sudah disubmit dan tidak bisa diubah!');
                    }

                    $existing = $model->where([
                        'checksheet_id' => $checksheetId,
                        'item_check' => $itemCheckData[$rowIndex] ?? 'UNKNOWN',
                        'kolom' => intval($colIndex),
                    ])->first();

                    $isSubmitted = ($action == 'submit') ? 1 : 0;
                    $fullDate = date('Y-m-d', strtotime($checksheet['bulan'] . '-' . $colIndex));

                    if (!$existing) {
                        $model->insert([
                            'checksheet_id' => $checksheetId,
                            'tanggal' => $fullDate,
                            'kolom' => intval($colIndex),
                            'item_check' => $itemCheckData[$rowIndex] ?? 'UNKNOWN',
                            'inspeksi' => $inspeksiData[$rowIndex] ?? null,
                            'standar' => $standarData[$rowIndex] ?? null,
                            'status' => $status,
                            'npk' => $npkData[$colIndex],
                            'is_submitted' => $isSubmitted,
                        ]);
                    } else {
                        $model->update($existing['id'], [
                            'status' => $status,
                            'npk' => $npkData[$colIndex],
                            'is_submitted' => $isSubmitted
                        ]);
                    }
                    $hasChanges = true;
                }
            }
        }

        if (!$hasChanges) {
            return redirect()->back()->with('error', 'Tidak ada perubahan yang dilakukan.');
        }

        return redirect()->back()->with('success', 'Data berhasil ' . ($action == 'submit' ? 'dikirim!' : 'disimpan!'));
    }
}
