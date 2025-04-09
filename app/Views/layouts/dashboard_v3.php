<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= isset($title) ? $title . '| CBI' : 'CBI' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard v3</h1>
    </div>

    <form class="mb-3">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="filterBulan" class="form-label">Filter Bulan</label>
                <select id="filterBulan" class="form-select">
                    <option selected disabled>Pilih Bulan</option>
                    <option value="1">Januari</option>
                    <option value="2">Februari</option>
                    <option value="3">Maret</option>
                    <option value="4">April</option>
                    <option value="5">Mei</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">Agustus</option>
                    <option value="9">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="filterMesin" class="form-label">ID Mesin</label>
                <input type="text" id="filterMesin" class="form-control" placeholder="Contoh: 001">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>Mesin</th>
                    <th>ID Mesin</th>
                    <!-- Tanggal 1 - 31 -->
                    <!-- Bisa generate otomatis nanti, tapi ini dummy -->
                    <!-- Kamu juga bisa pakai loop di backend -->
                    <!-- Di sini manual -->
                    <!-- atau pakai JS nanti -->
                    <!-- Untuk slicing, tulis manual dulu aja -->
                    <!-- Lebih cepat -->
                    <!-- Bisa pakai Emmet juga: `th*31{$}` -->
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                    <th>5</th>
                    <th>6</th>
                    <th>7</th>
                    <th>8</th>
                    <th>9</th>
                    <th>10</th>
                    <th>11</th>
                    <th>12</th>
                    <th>13</th>
                    <th>14</th>
                    <th>15</th>
                    <th>16</th>
                    <th>17</th>
                    <th>18</th>
                    <th>19</th>
                    <th>20</th>
                    <th>21</th>
                    <th>22</th>
                    <th>23</th>
                    <th>24</th>
                    <th>25</th>
                    <th>26</th>
                    <th>27</th>
                    <th>28</th>
                    <th>29</th>
                    <th>30</th>
                    <th>31</th>
                </tr>
            </thead>
            <tbody>
                <!-- Dummy data mesin -->
                <tr>
                    <td>Mesin A</td>
                    <td>001</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td></td>
                    <td>✓</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>✓</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>✓</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>✓</td>
                    <td></td>
                    <td></td>
                    <td>✓</td>
                    <td></td>
                    <td></td>
                    <td>✓</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>✓</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Mesin B</td>
                    <td>002</td>
                    <td></td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>✓</td>
                    <td></td>
                    <td></td>
                    <td>✓</td>
                    <td></td>
                    <td></td>
                    <td>✓</td>
                    <td></td>
                    <td>✓</td>
                    <td></td>
                    <td>✓</td>
                    <td></td>
                    <td></td>
                    <td>✓</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>✓</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>✓</td>
                </tr>
                <tr>
                    <td>Mesin C</td>
                    <td>003</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                    <td>✓</td>
                </tr>
            </tbody>
        </table>
    </div>
</main>
<?= $this->endSection() ?>