<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\ManifestModel;

class Resto extends ResourceController
{
    use ResponseTrait;
    protected $manifestModel;
    protected $paketModel;
    public function __construct()
    {
        $this->manifestModel = new ManifestModel();
        $this->db = \Config\Database::connect();
    }
    
    public function foodList($generate)
    {
        $getData = $this->manifestModel->getOrders($generate)->getRow();
        if (empty($getData)) {
            return $this->failNotFound('Data Not Found');
        }
        $tripIdNo = isset($getData->trip_id_no) ? $getData->trip_id_no : '';
        $tripDate = isset($getData->trip_date) ? $getData->trip_date : '';

        $checkinList = $this->manifestModel->getCheckinList($tripIdNo,$tripDate)->getResult();

        foreach ($checkinList as $key => $value) {
            $value->url_print = base_url('print/ticket/thermal?code='.$value->ticket_number);
        }
        
        $data['status'] = 200;
        $data['messages'] = 'success';
        $data['data'] = $checkinList;

        return view('documents/restoOrders', $data);
    }
}
