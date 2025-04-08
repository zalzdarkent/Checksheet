<?php

namespace App\Controllers;

use App\Models\DetailChecksheet;

class ApiController extends BaseController
{
    public function getNGCount()
    {
        $detailChecksheet = new DetailChecksheet();
        $count = $detailChecksheet->getUnresolvedNGCount();
        
        return $this->response->setJSON([
            'count' => $count
        ]);
    }
}
