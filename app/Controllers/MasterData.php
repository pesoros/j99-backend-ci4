<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\MasterModel;
use App\Models\TripModel;

class MasterData extends ResourceController
{
    use ResponseTrait;
    protected $masterModel;
    public function __construct()
    {
        $this->masterModel = new MasterModel();
    }

    public function dataKota()
    {
        $bodyRaw = $this->request->getRawInput();
        $q = isset($bodyRaw['keyword']) ? $bodyRaw['keyword'] : '';

        $result = $this->masterModel->getLocation($q)->getResult();

        foreach ($result as $key => $value) {
            $value->row = $key;
        }
        return $this->respond($result, 200);
    }

    public function dataKelas()
    {
        $bodyRaw = $this->request->getRawInput();
        $q = isset($bodyRaw['keyword']) ? $bodyRaw['keyword'] : '';

        $result = $this->masterModel->getFleetType($q)->getResult();
        return $this->respond($result, 200);
    }

    public function dataUnit()
    {
        $result = $this->masterModel->getUnit();
        return $this->respond($result, 200);
    }

    public function dataResto()
    {
        $result = $this->masterModel->getResto()->getResult();
        return $this->respond($result, 200);
    }

    public function dataMenu()
    {
        $bodyRaw = $this->request->getRawInput();
        $idResto = isset($bodyRaw['idResto']) ? $bodyRaw['idResto'] : '';

        $result = $this->masterModel->getRestoMenu($idResto)->getResult();
        return $this->respond($result, 200);
    }
}
