<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\MasterModel;
use App\Models\TripModel;

class TripData extends ResourceController
{
    use ResponseTrait;
    protected $tripModel;
    public function __construct()
    {
        $this->tripModel = new TripModel();
    }

    public function tripList()
    {
        $bodyRaw = $this->request->getRawInput();
        $unitType = isset($bodyRaw['unitType']) ? $bodyRaw['unitType'] : '';
        $kelas = isset($bodyRaw['kelas']) ? $bodyRaw['kelas'] : '';
        $jumlahPenumpang = isset($bodyRaw['jumlahPenumpang']) ? $bodyRaw['jumlahPenumpang'] : '';
        $tanggalBerangkat = isset($bodyRaw['tanggal']) ? $bodyRaw['tanggal'] : '';
        $kotaBerangkat = isset($bodyRaw['berangkat']) ? $bodyRaw['berangkat'] : '';
        $kotaTujuan = isset($bodyRaw['tujuan']) ? $bodyRaw['tujuan'] : '';

        $filterData = [
			'start_point' => $kotaBerangkat,
			'end_point'   => $kotaTujuan,
			'date'        => date("Y-m-d", strtotime(!empty($tanggalBerangkat)?$tanggalBerangkat:date('Y-m-d'))),
			'fleet_type'  => $kelas
        ];
        $result = $this->tripModel->getTripList($filterData)->getResult();

        return $this->respond($result, 200);;
    }
}
