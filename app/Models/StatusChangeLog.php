<?php

namespace App\Models;

use CodeIgniter\Model;

class StatusChangeLog extends Model
{
    protected $table            = 'preuse_tb_status_change_log';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'detail_checksheet_id',
        'previous_status',
        'new_status',
        'reason',
        'changed_by',
        'changed_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'changed_at';
    protected $updatedField  = '';
}
