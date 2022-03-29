<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\EnquiryModel;

class Enquiry extends ResourceController
{
    use ResponseTrait;
    protected $enquiryModel;
    public function __construct()
    {
        $this->enquiryModel = new EnquiryModel();
        $this->db = \Config\Database::connect();
    }
    
    public function pariwisata()
    {
        $bodyRaw = $this->request->getRawInput();
        $data = [
            'name' => isset($bodyRaw['name']) ? $bodyRaw['name'] : '',
            'phone' => isset($bodyRaw['handphone']) ? $bodyRaw['handphone'] : '',
            'email' => isset($bodyRaw['email']) ? $bodyRaw['email'] : '',
            'bus_type' => isset($bodyRaw['busType']) ? $bodyRaw['busType'] : '',
            'enquiry' => isset($bodyRaw['description']) ? $bodyRaw['description'] : '',
            'wich' => 'pariwisata',
            'created_date' => date('Y-m-d H:i:s')
        ];

        $this->enquiryModel->saveEnquiry($data);

        $result['status'] = 200;
        $result['messages'] = 'success';
        $result['body'] = $bodyRaw;

        return $this->respond($result, 200);
    }
}
