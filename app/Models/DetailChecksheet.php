<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailChecksheet extends Model
{
    protected $table            = 'preuse_tb_detail_checksheet';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;  
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'checksheet_id',
        'tanggal',
        'kolom',
        'item_check',
        'inspeksi',
        'standar',
        'status',
        'npk',
        'is_submitted',
        'is_resolved',  // Menambahkan field untuk menandai status yang sudah resolved
        'created_at', 
        'deleted_at'
    ];

    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Fungsi untuk mendapatkan data termasuk yang sudah dihapus
    public function getAllIncludingDeleted()
    {
        return $this->withDeleted()->findAll();
    }

    // Fungsi untuk mendapatkan data yang sudah dihapus
    public function getDeletedOnly()
    {
        return $this->onlyDeleted()->findAll();
    }
}
