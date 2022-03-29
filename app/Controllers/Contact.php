<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\ContactModel;

class Contact extends ResourceController
{
    use ResponseTrait;
    protected $contactModel;
    public function __construct()
    {
        $this->contactModel = new ContactModel();
        $this->db = \Config\Database::connect();
    }
    
    public function pariwisata()
    {
        $bodyRaw = $this->request->getRawInput();
        $name = isset($bodyRaw['name']) ? $bodyRaw['name'] : '';
        $handphone = isset($bodyRaw['handphone']) ? $bodyRaw['handphone'] : '';
        $email = isset($bodyRaw['email']) ? $bodyRaw['email'] : '';
        $busType = isset($bodyRaw['busType']) ? $bodyRaw['busType'] : '';
        $description = isset($bodyRaw['description']) ? $bodyRaw['description'] : '';

        $result['status'] = 200;
        $result['messages'] = 'success';
        $result['body'] = $bodyRaw;

        return $this->respond($result, 200);
    }
}
