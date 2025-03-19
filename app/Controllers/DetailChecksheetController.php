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

        // Ambil data dari request
        $checksheetId = $this->request->getPost('checksheet_id');
        $statusData = $this->request->getPost('status');
        $npkData = $this->request->getPost('npk');
        $action = $this->request->getPost('action');

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

        // dd($filledColumnsArray);

        // Simpan data ke database
        foreach ($statusData as $rowIndex => $statuses) {
            foreach ($statuses as $colIndex => $status) {
                if (!empty($npk) && !ctype_digit($npk)) {
                    return redirect()->back()->with('error', 'NPK hanya boleh berisi angka!');
                }
                if (!empty($status)) {
                    // Cek apakah data sudah ada untuk mencegah duplikasi
                    $existing = $model->where([
                        'checksheet_id' => $checksheetId,
                        'item_check'    => $itemCheckData[$rowIndex] ?? 'UNKNOWN',
                        'kolom'         => $colIndex, // Pastikan hanya menyimpan 1 kolom, bukan array
                    ])->first();

                    // Tentukan status is_submitted berdasarkan tombol yang diklik
                    $isSubmitted = ($action == 'submit') ? 1 : 0;

                    if (!$existing) {
                        // Jika data belum ada, tambahkan data baru
                        $data = [
                            'checksheet_id' => $checksheetId,
                            'tanggal'       => $colIndex, // Simpan sebagai tanggal
                            'kolom'         => $filledColumnsArray, // Simpan kolom sebagai angka
                            'item_check'    => $itemCheckData[$rowIndex] ?? 'UNKNOWN',
                            'inspeksi'      => $inspeksiData[$rowIndex] ?? null,
                            'standar'       => $standarData[$rowIndex] ?? null,
                            'status'        => $status,
                            'npk'           => $npkData[$colIndex] ?? null,
                            'is_submitted'  => $isSubmitted, // Set berdasarkan tombol yang diklik
                        ];
                        $model->insert($data);
                    } else {
                        // Jika sudah ada, update status dan is_submitted jika perlu
                        $model->where('id', $existing['id'])->set([
                            'status'       => $status,
                            'npk'          => $npkData[$colIndex] ?? $existing['npk'],
                            'is_submitted' => $isSubmitted, // Perbarui status jika diklik "Kirim"
                        ])->update();
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Data berhasil ' . ($action == 'submit' ? 'dikirim!' : 'disimpan!'));
    }
}
