<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class TestingController extends BaseController
{
    public function index()
    {
        try {
            $db = \Config\Database::connect('prodControlv2');

            $query = $db->query("SELECT 1 AS test");

            $result = $query->getRow();

            echo "Koneksi berhasil! Test result: " . $result->test;
        } catch (\Exception $e) {
            echo "Koneksi gagal: " . $e->getMessage();
        }
    }
}
