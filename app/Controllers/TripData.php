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

    public function seatList()
    {
        $separate = [2,6,10,14,18];
        for ($i=0; $i < 20; $i++) { 
            $result[] = [
                'row' => $i,
                'id' => $i+1,
                'name' => 'A'.$i+1,
                'isAvailable' => true,
                'isSeat' => true,
            ];

            if (in_array($i+1, $separate)) {
                $result[] = [
                    'row' => 00,
                    'id' => 00,
                    'name' => '-',
                    'isAvailable' => false,
                    'isSeat' => false,
                ];
            }
        }
        
        return $this->respond($result, 200);
    }
}
