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
        return $this->respond($result, 200);
    }
}
