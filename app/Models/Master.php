<?php

namespace App\Models;

use CodeIgniter\Model;

class Master extends Model
{
    protected $table            = 'preuse_tb_master';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'mesin',
        'judul_checksheet',
        'created_at'
    ];
    protected $useTimestamps = false;
    public function getMasterWithDetails($id)
    {
        return $this->select('tb_master.*, tb_detail_master.item_check, tb_detail_master.inspeksi, tb_detail_master.standar')
            ->join('tb_detail_master', 'tb_detail_master.master_id = tb_master.id')
            ->where('tb_master.id', $id)
            ->findAll();
    }


    // protected bool $allowEmptyInserts = false;
    // protected bool $updateOnlyChanged = true;

    // protected array $casts = [];
    // protected array $castHandlers = [];

    // // Dates
    // protected $useTimestamps = false;
    // protected $dateFormat    = 'datetime';
    // protected $createdField  = 'created_at';
    // protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at';

    // // Validation
    // protected $validationRules      = [];
    // protected $validationMessages   = [];
    // protected $skipValidation       = false;
    // protected $cleanValidationRules = true;

    // // Callbacks
    // protected $allowCallbacks = true;
    // protected $beforeInsert   = [];
    // protected $afterInsert    = [];
    // protected $beforeUpdate   = [];
    // protected $afterUpdate    = [];
    // protected $beforeFind     = [];
    // protected $afterFind      = [];
    // protected $beforeDelete   = [];
    // protected $afterDelete    = [];
}
