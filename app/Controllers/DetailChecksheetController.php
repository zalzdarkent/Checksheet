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

        // Process NPK updates first
        foreach ($npkData as $colIndex => $karyawanId) {
            if (!empty($karyawanId)) {
                // Get karyawan data
                $karyawan = $this->karyawanModel->find($karyawanId);
                if (!$karyawan) {
                    return redirect()->back()->with('error', 'Data karyawan tidak ditemukan!');
                }

                $existingData = $model->where([
                    'checksheet_id' => $checksheetId,
                    'kolom' => intval($colIndex),
                    'is_submitted' => 1
                ])->first();

                if ($existingData) {
                    continue; // Skip if already submitted
                }

                // Update all rows for this column
                $model->where([
                    'checksheet_id' => $checksheetId,
                    'kolom' => intval($colIndex)
                ])->set([
                    'npk' => $karyawan['npk'],
                    'id_karyawan' => $karyawan['id'],
                    'is_submitted' => $isSubmitted
                ])->update();

                $hasChanges = true;
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
                    ])->select('id, checksheet_id, kolom, item_check, status, npk, id_karyawan, is_submitted')
                      ->first();

                    $fullDate = date('Y-m-d', strtotime($checksheet['bulan'] . '-' . $colIndex));

                    // Get karyawan data for this column
                    $karyawan = $this->karyawanModel->find($npkData[$colIndex]);
                    if (!$karyawan) {
                        return redirect()->back()->with('error', 'Data karyawan tidak ditemukan!');
                    }

                    if (!$existing) {
                        $model->insert([
                            'checksheet_id' => $checksheetId,
                            'tanggal' => $fullDate,
                            'kolom' => intval($colIndex),
                            'item_check' => $itemCheckData[$rowIndex] ?? 'UNKNOWN',
                            'inspeksi' => $inspeksiData[$rowIndex] ?? '',
                            'standar' => $standarData[$rowIndex] ?? '',
                            'status' => $status,
                            'npk' => $karyawan['npk'],
                            'id_karyawan' => $karyawan['id'],
                            'is_submitted' => $isSubmitted
                        ]);
                        $detailId = $model->getInsertID();
                        if ($status === 'NG') {
                            // Cek apakah item yang sama masih NG di kolom sebelumnya
                            $previousNG = $model->where([
                                'checksheet_id' => $checksheetId,
                                'item_check' => $itemCheckData[$rowIndex] ?? 'UNKNOWN',
                                'kolom' => intval($colIndex) - 1,
                                'status' => 'NG'
                            ])->first();

                            if (!$previousNG) {
                                $statusLogModel = new StatusChangeLog();
                                $statusLogModel->insert([
                                    'detail_checksheet_id' => $detailId,
                                    'previous_status' => 'NG',
                                ]);
                            }
                        }
                    } else {
                        $model->update($existing['id'], [
                            'status' => $status,
                            'npk' => $karyawan['npk'],
                            'id_karyawan' => $karyawan['id'],
                            'is_submitted' => $isSubmitted
                        ]);
                        if ($status === 'NG') {
                            // Cek apakah item yang sama masih NG di kolom sebelumnya
                            $previousNG = $model->where([
                                'checksheet_id' => $checksheetId,
                                'item_check' => $itemCheckData[$rowIndex] ?? 'UNKNOWN',
                                'kolom' => intval($colIndex) - 1,
                                'status' => 'NG'
                            ])->first();

                            if (!$previousNG) {
                                $statusLogModel = new StatusChangeLog();
                                $statusLogModel->insert([
                                    'detail_checksheet_id' => $existing['id'],
                                    'previous_status' => 'NG',
                                ]);
                            }
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

    public function index($id)
    {
        $model = new DetailChecksheet();
        $checksheetModel = new Checksheet();
        $detailMasterModel = new DetailMaster();

        $checksheet = $checksheetModel->find($id);
        if (!$checksheet) {
            return redirect()->back()->with('error', 'Data checksheet tidak ditemukan!');
        }

        $master = $detailMasterModel->where('id', $checksheet['master_id'])->first();

        // Get all detail masters for this checksheet
        $detailMasters = $detailMasterModel->where('master_id', $checksheet['master_id'])->findAll();

        // Get all details for this checksheet
        $details = $model->where('checksheet_id', $id)->findAll();

        // Initialize status array
        $statusArray = [];
        $npkArray = [];
        $isSubmitted = false;

        // Process details into status array
        foreach ($details as $detail) {
            if ($detail['is_submitted']) {
                $isSubmitted = true;
            }

            // Store status and resolved state
            $statusArray[$detail['item_check']][$detail['kolom']] = $detail['status'];
            $statusArray[$detail['item_check']]['is_resolved'] = $detail['is_resolved'];

            if (!empty($detail['npk'])) {
                $npkArray[$detail['kolom']] = $detail['npk'];
            }
        }

        // Get list of deleted item checks
        $deletedItemChecks = [];
        foreach ($detailMasters as $master) {
            if ($master['deleted_at']) {
                $deletedItemChecks[] = $master['item_check'];
            }
        }

        $data = [
            'title' => 'Detail Checksheet',
            'checksheet' => $checksheet,
            'master' => $master,
            'detailMasters' => $detailMasters,
            'statusArray' => $statusArray,
            'npkArray' => $npkArray,
            'isSubmitted' => $isSubmitted,
            'deletedItemChecks' => $deletedItemChecks
        ];

        return view('checksheet/tabel', $data);
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
        $log = $statusLogModel
            ->select('preuse_tb_status_change_log.*, preuse_tb_detail_checksheet.item_check')
            ->join('preuse_tb_detail_checksheet', 'preuse_tb_detail_checksheet.id = preuse_tb_status_change_log.detail_checksheet_id')
            ->where('preuse_tb_status_change_log.id', $logId)
            ->first();

        if (!$log) {
            return redirect()->back()->with('error', 'Log tidak ditemukan');
        }

        $data = [
            'title' => 'Change Ticket',
            'log' => $log
        ];

        return view('detail_checksheet/change_status_form', $data);
    }

    public function updateStatus($logId)
    {
        date_default_timezone_set('Asia/Jakarta');
        $statusLogModel = new StatusChangeLog();
        $detailChecksheetModel = new DetailChecksheet();

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

        // Update status di log
        $statusLogModel->update($logId, [
            'new_status' => $newStatus,
            'reason' => $reason,
            'changed_by' => $npk,
            'changed_at' => date('Y-m-d H:i:s')
        ]);

        // Jika status diubah menjadi OK, tandai semua detail checksheet yang terkait sebagai resolved
        if ($newStatus === 'OK') {
            // Ambil detail checksheet yang terkait dengan log ini
            $detailChecksheet = $detailChecksheetModel->find($log['detail_checksheet_id']);
            
            if ($detailChecksheet) {
                // Update semua detail checksheet dengan item_check yang sama dan status NG
                $detailChecksheetModel->where([
                    'checksheet_id' => $detailChecksheet['checksheet_id'],
                    'item_check' => $detailChecksheet['item_check'],
                    'status' => 'NG'
                ])->set(['is_resolved' => 1])->update();
            }
        }

        return redirect()->to('open-ticket')->with('success', 'Status berhasil diupdate');
    }
}
