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

        // Ambil data dari request
        $checksheetId = $this->request->getPost('checksheet_id');
        $statusData = $this->request->getPost('status');
        $npkData = $this->request->getPost('npk');
        $action = $this->request->getPost('action');

        // Dapatkan data checksheet untuk mendapatkan bulan
        $checksheet = $checksheetModel->find($checksheetId);
        if (!$checksheet) {
            return redirect()->back()->with('error', 'Data checksheet tidak ditemukan!');
        }

        // Dapatkan kolom yang telah diisi dari JS
        $filledColumns = $this->request->getPost('filled_columns');
        $filledColumnsArray = !empty($filledColumns) ? explode(',', $filledColumns) : [];

        // Ambil data tambahan
        $itemCheckData = $this->request->getPost('item_check');
        $inspeksiData = $this->request->getPost('inspeksi');
        $standarData = $this->request->getPost('standar');

        // Tanggal hari ini
        $today = date('j');

        // Validasi awal
        if (!$checksheetId || empty($statusData)) {
            return redirect()->back()->with('error', 'Data tidak lengkap!');
        }

        // Cek apakah ada status yang diisi
        $hasAnyStatus = false;
        $filledColumns = [];

        // Pertama, kumpulkan semua kolom yang diisi
        foreach ($statusData as $rowIndex => $statuses) {
            foreach ($statuses as $colIndex => $status) {
                if (!empty($status)) {
                    $filledColumns[] = $colIndex;
                    
                    // Cek apakah data sudah pernah disubmit
                    $existingData = $model->where([
                        'checksheet_id' => $checksheetId,
                        'kolom' => intval($colIndex),
                        'is_submitted' => 1
                    ])->first();

                    if ($existingData) {
                        return redirect()->back()->with('error', 'Data untuk tanggal ' . $colIndex . ' sudah disubmit dan tidak bisa diubah!');
                    }
                }
            }
        }

        // Validasi hanya satu kolom yang diisi
        $uniqueFilledColumns = array_unique($filledColumns);
        if (count($uniqueFilledColumns) > 1) {
            return redirect()->back()->with('error', 'Hanya boleh mengisi satu kolom tanggal dalam satu waktu!');
        }

        // Lanjutkan dengan validasi status
        foreach ($statusData as $rowIndex => $statuses) {
            foreach ($statuses as $colIndex => $status) {
                if (!empty($status)) {
                    $hasAnyStatus = true;

                    // Pastikan NPK diisi jika status ada
                    if (empty($npkData[$colIndex])) {
                        return redirect()->back()->with('error', 'NPK harus diisi untuk tanggal yang memiliki OK/NG.');
                    }

                    // Pastikan NPK diisi jika status ada
                    if (empty($npkData[$colIndex])) {
                        return redirect()->back()->with('error', 'NPK harus diisi untuk tanggal yang memiliki OK/NG.');
                    }

                    // Cegah pengisian tanggal masa depan
                    if ($colIndex > $today) {
                        return redirect()->back()->with('error', 'Tidak bisa mengisi data untuk tanggal yang belum lewat.');
                    }
                }
            }
        }

        if (!$hasAnyStatus) {
            return redirect()->back()->with('error', 'Minimal satu OK/NG harus dipilih.');
        }

        // Simpan data ke database
        foreach ($statusData as $rowIndex => $statuses) {
            foreach ($statuses as $colIndex => $status) {
                if (!empty($npkData[$colIndex]) && !ctype_digit($npkData[$colIndex])) {
                    return redirect()->back()->with('error', 'NPK hanya boleh berisi angka!');
                }
                if (!empty($status)) {
                    // Double check sekali lagi untuk memastikan data belum disubmit
                    $existingData = $model->where([
                        'checksheet_id' => $checksheetId,
                        'kolom' => intval($colIndex),
                        'is_submitted' => 1
                    ])->first();

                    if ($existingData) {
                        return redirect()->back()->with('error', 'Data untuk tanggal ' . $colIndex . ' sudah disubmit dan tidak bisa diubah!');
                    }

                    // Cek apakah data sudah ada untuk mencegah duplikasi
                    $existing = $model->where([
                        'checksheet_id' => $checksheetId,
                        'item_check'    => $itemCheckData[$rowIndex] ?? 'UNKNOWN',
                        'kolom'         => intval($colIndex),
                    ])->first();

                    // Tentukan status is_submitted berdasarkan tombol yang diklik
                    $isSubmitted = ($action == 'submit') ? 1 : 0;

                    // Jika action adalah submit, pastikan semua item untuk kolom tersebut diisi
                    if ($action == 'submit') {
                        $allItemsFilled = true;
                        foreach ($statusData as $checkRow => $checkStatuses) {
                            if (empty($checkStatuses[$colIndex])) {
                                $allItemsFilled = false;
                                break;
                            }
                        }
                        
                        if (!$allItemsFilled) {
                            return redirect()->back()->with('error', 'Semua item harus diisi sebelum melakukan submit!');
                        }
                    }

                    // Buat tanggal lengkap dari kombinasi hari dan bulan
                    $fullDate = date('Y-m-d', strtotime($checksheet['bulan'] . '-' . $colIndex));
                    
                    if (!$existing) {
                        // Jika data belum ada, tambahkan data baru
                        $data = [
                            'checksheet_id' => $checksheetId,
                            'tanggal'       => $fullDate, // Menggunakan format tanggal lengkap
                            'kolom'         => intval($colIndex),
                            'item_check'    => $itemCheckData[$rowIndex] ?? 'UNKNOWN',
                            'inspeksi'      => $inspeksiData[$rowIndex] ?? null,
                            'standar'       => $standarData[$rowIndex] ?? null,
                            'status'        => $status,
                            'npk'           => $npkData[$colIndex] ?? null,
                            'is_submitted'  => $isSubmitted,
                        ];
                        $model->insert($data);
                    } else {
                        // Jika sudah ada, update data yang ada
                        $model->update($existing['id'], [
                            'status'       => $status,
                            'npk'          => $npkData[$colIndex] ?? $existing['npk'],
                            'is_submitted' => $isSubmitted
                        ]);
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Data berhasil ' . ($action == 'submit' ? 'dikirim!' : 'disimpan!'));
    }

    public function index($id)
    {
        $checksheetModel = new Checksheet();
        $detailChecksheetModel = new DetailChecksheet();
        $detailMasterModel = new DetailMaster();
        $npkModel = new Npk();

        // Ambil data checksheet
        $data['checksheet'] = $checksheetModel->find($id);
        
        if (!$data['checksheet']) {
            return redirect()->back()->with('error', 'Data checksheet tidak ditemukan!');
        }

        // Ambil data detail checksheet yang sudah ada, termasuk yang soft deleted
        $existingData = $detailChecksheetModel->getAllIncludingDeleted();
        
        // Format data untuk tampilan
        $statusArray = [];
        $uniqueItemChecks = [];
        foreach ($existingData as $item) {
            $statusArray[$item['item_check']][$item['kolom']] = $item['status'];
            $uniqueItemChecks[] = $item['item_check'];
        }
        $uniqueItemChecks = array_unique($uniqueItemChecks);
        
        // Ambil semua data master (termasuk yang aktif dan tidak)
        $allMasterItems = $detailMasterModel->where('master_id', $data['checksheet']['master_id'])->findAll();
        $activeMasterItems = [];
        $deletedMasterItems = [];

        // Pisahkan item yang masih aktif dan yang sudah dihapus
        foreach ($allMasterItems as $item) {
            if (in_array($item['item_check'], $uniqueItemChecks)) {
                $activeMasterItems[] = $item;
            } else {
                $deletedMasterItems[] = $item;
            }
        }

        // Gabungkan data active dan deleted dengan urutan yang benar
        $data['detailMasters'] = array_merge($activeMasterItems, $deletedMasterItems);
        
        // Set item yang dihapus
        $data['deletedItemChecks'] = array_column($deletedMasterItems, 'item_check');
        
        $data['statusArray'] = $statusArray;
        $data['isSubmitted'] = false;

        // Ambil data NPK
        $data['npkList'] = $npkModel->getAllNpk();

        return view('checksheet/tabel', $data);
    }
}
