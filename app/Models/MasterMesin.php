<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterMesin extends Model
{
    // protected $table            = 'data_master_mesin_MTN';
    // protected $primaryKey       = 'id';
    // protected $useAutoIncrement = true;
    // protected $returnType       = 'array';
    // protected $useSoftDeletes   = false;
    // protected $protectFields    = true;
    // protected $allowedFields    = [
    //     'id_machine',
    //     'name_machine',
    //     'spec_unit',
    //     'category',
    //     'line',
    //     'production',
    //     'area',
    //     'description',
    //     'sub_assy_mc',
    //     'id_sub_assy_mc',
    //     'created_at',
    //     'data_version'
    // ];

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

    public function __construct()
    {
        $this->db = \Config\Database::connect('prodControlv2');
    }

    public function getAll()
    {
        return $this->db->table('data_master_mesin_MTN')->get()->getResultArray();
    }
}
