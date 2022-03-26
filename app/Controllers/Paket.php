<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\PaketModel;

class Paket extends ResourceController
{
    use ResponseTrait;
    protected $paketModel;
    public function __construct()
    {
        $this->paketModel = new PaketModel();
        $this->db = \Config\Database::connect();
    }
    
    public function cekPaket()
    {
        $bodyRaw = $this->request->getRawInput();
        $packetCode = isset($bodyRaw['code']) ? $bodyRaw['code'] : '';

        $result = $this->paketModel->getPacket($packetCode)->getResult();

        if (empty($result)) {
            return $this->failNotFound('Data Not Found');
        } 

        $result[0]->trace = $this->paketModel->getTrace($result[0]->id)->getResult();

        return $this->respond($result, 200);
    }
}
