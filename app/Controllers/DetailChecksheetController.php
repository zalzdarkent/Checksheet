<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DetailChecksheet;
use App\Models\Checksheet;
use App\Models\DetailMaster;
use App\Models\Karyawan;
use App\Models\StatusChangeLog;
use CodeIgniter\HTTP\ResponseInterface;

class DetailChecksheetController extends BaseController
{
    protected $karyawanModel;

    public function __construct()
    {
        $this->karyawanModel = new Karyawan();
    }

    public function saveStatus()
    {
        $model = new DetailChecksheet();
        $checksheetModel = new Checksheet();
        $statusLogModel = new StatusChangeLog();
        // dd($this->request->getPost('run_hour'));

        $checksheetId = $this->request->getPost('checksheet_id');
        $statusData = $this->request->getPost('status');
        $npkData = $this->request->getPost('npk');
        $action = $this->request->getPost('action');
        $itemCheckData = $this->request->getPost('item_check');
        $inspeksiData = $this->request->getPost('inspeksi');
        $standarData = $this->request->getPost('standar');
        $runHourData = $this->request->getPost('run_hour'); // Ambil data run_hour dari input
        $temperaturData = $this->request->getPost('temperature'); // Ambil data run_hour dari input
        $runLoadData = $this->request->getPost('run_load'); // Ambil data run_load dari input

        $checksheet = $checksheetModel->find($checksheetId);
        if (!$checksheetId || !$checksheet) {
            return redirect()->back()->with('error', 'Data checksheet tidak ditemukan!');
        }

        // Cek apakah data sudah disubmit sebelumnya
        $existingSubmittedData = $model->where([
            'checksheet_id' => $checksheetId,
            'is_submitted' => 1
        ])->first();

        if ($existingSubmittedData && $action == 'submit') {
            return redirect()->back()->with('error', 'Data sudah disubmit sebelumnya dan tidak bisa diubah!');
        }

        $today = date('j');
        $hasChanges = false;
        $isSubmitted = ($action == 'submit') ? 1 : 0;

        foreach ($npkData as $colIndex => $karyawanId) {
            if (!empty($karyawanId)) {
                $karyawan = $this->karyawanModel->find($karyawanId);
                if (!$karyawan) {
                    return redirect()->back()->with('error', 'Data karyawan tidak ditemukan!');
                }

                $updateData = [
                    'npk' => $karyawan['npk'],
                    'id_karyawan' => $karyawan['id'],
                    'is_submitted' => $isSubmitted,
                ];

                if (!empty($runHourData[$colIndex])) {
                    $updateData['run_hour'] = $runHourData[$colIndex];
                }

                if (!empty($temperaturData[$colIndex])) {
                    $updateData['temperature'] = $temperaturData[$colIndex];
                }
                
                if (!empty($runLoadData[$colIndex])) {
                    $updateData['run_load'] = $runLoadData[$colIndex];
                }

                $model->where([
                    'checksheet_id' => $checksheetId,
                    'kolom' => intval($colIndex)
                ])->set($updateData)->update();

                $hasChanges = true;
            }
        }

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

                    $fullDate = date('Y-m-d', strtotime($checksheet['bulan'] . '-' . $colIndex));

                    // Get karyawan data for this column
                    $karyawan = $this->karyawanModel->find($npkData[$colIndex]);
                    if (!$karyawan) {
                        return redirect()->back()->with('error', 'Data karyawan tidak ditemukan!');
                    }

                    // Check if record exists
                    $existing = $model->where([
                        'checksheet_id' => $checksheetId,
                        'item_check' => $itemCheckData[$rowIndex],
                        'kolom' => intval($colIndex)
                    ])->first();

                    // Get run_hour value if applicable
                    $itemCheckId = $itemCheckData[$rowIndex];
                    $runHourValue = $runHourData[$colIndex] ?? null;
                    $temperatureValue = $temperaturData[$colIndex] ?? null;
                    $runLoadValue = $runLoadData[$colIndex] ?? null;

                    if (!$existing) {
                        // Insert new record
                        $model->insert([
                            'checksheet_id' => $checksheetId,
                            'tanggal' => $fullDate,
                            'kolom' => intval($colIndex),
                            'item_check' => $itemCheckId,
                            'inspeksi' => $inspeksiData[$rowIndex],
                            'standar' => $standarData[$rowIndex],
                            'status' => $status,
                            'npk' => $karyawan['npk'],
                            'id_karyawan' => $karyawan['id'],
                            'is_submitted' => $isSubmitted,
                            'run_hour' => $runHourValue,
                            'temperature' => $temperatureValue,
                            'run_load' => $runLoadValue,
                        ]);

                        // Hanya tambahkan log jika status baru adalah NG
                        if ($status === 'NG') {
                            $detailId = $model->getInsertID();
                            $statusLogModel->insert([
                                'detail_checksheet_id' => $detailId,
                                'previous_status' => 'NG',
                                'created_at' => date('Y-m-d H:i:s')
                            ]);
                        }
                    } else {
                        // Update existing record
                        $model->update($existing['id'], [
                            'status' => $status,
                            'npk' => $karyawan['npk'],
                            'id_karyawan' => $karyawan['id'],
                            'is_submitted' => $isSubmitted
                        ]);

                        // Jika status baru adalah NG
                        if ($status === 'NG') {
                            // Hapus log sebelumnya jika ada
                            $statusLogModel->where('detail_checksheet_id', $existing['id'])->delete();

                            // Buat log baru dengan previous_status NG
                            $statusLogModel->insert([
                                'detail_checksheet_id' => $existing['id'],
                                'previous_status' => 'NG',
                                'created_at' => date('Y-m-d H:i:s')
                            ]);
                        }
                        // Jika status berubah menjadi OK, hapus semua log
                        else if ($status === 'OK') {
                            $statusLogModel->where('detail_checksheet_id', $existing['id'])->delete();
                        }
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

    public function ngList()
    {
        $model = new DetailChecksheet();
        $statusLogModel = new StatusChangeLog();

        // Get all NG items that have logs with null new_status (unresolved)
        $ngItems = $statusLogModel
            ->select('preuse_tb_status_change_log.id, 
                     preuse_tb_status_change_log.previous_status,
                     preuse_tb_status_change_log.new_status,
                     preuse_tb_detail_checksheet.id as detail_id,
                     preuse_tb_detail_checksheet.item_check, 
                     preuse_tb_detail_checksheet.tanggal, 
                     preuse_tb_detail_checksheet.inspeksi, 
                     preuse_tb_detail_checksheet.standar,
                     preuse_tb_detail_checksheet.is_resolved,
                     c.mesin')
            ->join('preuse_tb_detail_checksheet', 'preuse_tb_detail_checksheet.id = preuse_tb_status_change_log.detail_checksheet_id')
            ->join('preuse_tb_checksheet c', 'preuse_tb_detail_checksheet.checksheet_id = c.id')
            ->findAll();

        $data = [
            'title' => 'Open Ticket',
            'ngItems' => $ngItems
        ];

        return view('detail_checksheet/ng_list', $data);
    }

    public function notifNG()
    {
        $statusLogModel = new StatusChangeLog();

        // Hitung total log status
        $totalLogs = $statusLogModel->countAll();

        // Kirim data ke view
        return view('layouts/app', ['totalLogs' => $totalLogs]);
    }

    public function detailChangeLog($id)
    {
        $statusLogModel = new StatusChangeLog();

        // Ambil data berdasarkan ID
        $log = $statusLogModel
            ->select('preuse_tb_status_change_log.*, d.item_check, d.tanggal, d.inspeksi, d.standar, c.mesin')
            ->join('preuse_tb_detail_checksheet d', 'd.id = preuse_tb_status_change_log.detail_checksheet_id')
            ->join('preuse_tb_checksheet c', 'd.checksheet_id = c.id')
            ->find($id);

        // Jika tidak ditemukan
        if (!$log) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Change log not found.');
        }

        $data = [
            'title' => 'Detail Ticket',
            'log' => $log
        ];

        return view('detail_checksheet/detail', $data);
    }


    public function changeStatusForm($logId)
    {
        $statusLogModel = new StatusChangeLog();
        $checksheetModel = new Checksheet();
        $karyawanModel = new Karyawan();

        $log = $statusLogModel
            ->select('preuse_tb_status_change_log.*, preuse_tb_detail_checksheet.item_check, preuse_tb_detail_checksheet.inspeksi, preuse_tb_detail_checksheet.standar, preuse_tb_detail_checksheet.tanggal, preuse_tb_detail_checksheet.checksheet_id')
            ->join('preuse_tb_detail_checksheet', 'preuse_tb_detail_checksheet.id = preuse_tb_status_change_log.detail_checksheet_id')
            ->where('preuse_tb_status_change_log.id', $logId)
            ->first();

        if (!$log) {
            return redirect()->back()->with('error', 'Log tidak ditemukan');
        }

        $mesin = $checksheetModel
            ->select('mesin')
            ->where('id', $log['checksheet_id'])
            ->first();

        $karyawanList = $karyawanModel->findAll();

        $data = [
            'title' => 'Change Ticket',
            'log' => $log,
            'mesin' => $mesin['mesin'] ?? 'Tidak ditemukan',
            'karyawanList' => $karyawanList
        ];

        return view('detail_checksheet/change_status_form', $data);
    }

    public function updateStatus($logId)
    {
        date_default_timezone_set('Asia/Jakarta');
        $statusLogModel = new StatusChangeLog();
        $detailChecksheetModel = new DetailChecksheet();
        $karyawanModel = new Karyawan(); // Tambahkan model Karyawan

        $log = $statusLogModel->find($logId);

        if (!$log) {
            return redirect()->back()->with('error', 'Log tidak ditemukan');
        }

        $newStatus = $this->request->getPost('new_status');
        $reason = $this->request->getPost('reason');
        $npk = $this->request->getPost('npk');

        if (!$newStatus || !$reason || !$npk) {
            return redirect()->back()->with('error', 'Semua field harus diisi');
        }

        // Cari karyawan berdasarkan NPK
        $karyawan = $karyawanModel->where('npk', $npk)->first();
        if (!$karyawan) {
            return redirect()->back()->with('error', 'Karyawan dengan NPK tersebut tidak ditemukan');
        }

        // Update status di log
        $statusLogModel->update($logId, [
            'new_status' => $newStatus,
            'reason' => $reason,
            'changed_by' => $npk, // Simpan NPK
            'id_karyawan' => $karyawan['id'], // Simpan ID Karyawan
            'changed_at' => date('Y-m-d H:i:s')
        ]);

        if ($newStatus === 'OK') {
            $detailChecksheet = $detailChecksheetModel->find($log['detail_checksheet_id']);

            if ($detailChecksheet) {
                // Ambil semua tiket NG untuk item_check yang sama dan checksheet_id yang sama
                $ngTickets = $detailChecksheetModel->where([
                    'checksheet_id' => $detailChecksheet['checksheet_id'],
                    'item_check' => $detailChecksheet['item_check'],
                    'status' => 'NG'
                ])->orderBy('tanggal', 'ASC')->findAll();

                // Ubah tiket pada tanggal yang sama atau sebelumnya menjadi resolved
                foreach ($ngTickets as $ticket) {
                    if ($ticket['tanggal'] <= $detailChecksheet['tanggal']) {
                        // Update is_resolved pada detail_checksheet
                        $detailChecksheetModel->update($ticket['id'], ['is_resolved' => 1]);

                        // Perbarui log status menjadi resolved dan tambahkan reason, changed_by, dan changed_at
                        $statusLogModel->where('detail_checksheet_id', $ticket['id'])
                            ->set([
                                'new_status' => 'OK',
                                'reason' => $reason,
                                'changed_by' => $npk,
                                'id_karyawan' => $karyawan['id'], // Tambahkan ID Karyawan
                                'changed_at' => date('Y-m-d H:i:s')
                            ])
                            ->update();
                    }
                }
            }
        }

        return redirect()->to('open-ticket')->with('success', 'Status berhasil diupdate');
    }
}
