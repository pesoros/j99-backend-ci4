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

        $result = $this->paketModel->getPacket($packetCode)->getRow();

        if (empty($result)) {
            return $this->failNotFound('Data Not Found');
        } 

        $result->trace = $this->paketModel->getTrace($result->id)->getResult();
        $result->pool_sender_id = $this->paketModel->getPool($result->pool_sender_id)->getRow()->name;
        $result->pool_receiver_id = $this->paketModel->getPool($result->pool_receiver_id)->getRow()->name;

        return $this->respond($result, 200);
    }
}
