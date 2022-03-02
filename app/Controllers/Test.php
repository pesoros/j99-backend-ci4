<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class Test extends ResourceController
{
    use ResponseTrait;

    public function test()
    {
        $data['one'] = '1';
        $data['two'] = '2';
        $data['three'] = '3';
        return $this->respond($data);
    }
}
