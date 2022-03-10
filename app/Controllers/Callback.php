<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
// use App\Models\CallbackModel;

class Callback extends ResourceController
{
    use ResponseTrait;
    protected $callbackModel;
    public function __construct()
    {
        // $this->callbackModel = new CallbackModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $bodyRaw = $this->request->getVar();

        return $this->respond($bodyRaw, 200);
    }
}
