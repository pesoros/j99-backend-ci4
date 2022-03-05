<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class Home extends ResourceController
{
    use ResponseTrait;

    public function index(Type $var = null)
    {
        $bodyRaw = $this->request->getRawInput();
        $data = [
            'messages' => 'Juragan 99 API =PSR='
        ];

        return $this->respond($data,200);
    }
}
