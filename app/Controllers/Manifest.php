<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\ManifestModel;

class Manifest extends ResourceController
{
    use ResponseTrait;
    protected $manifestModel;
    public function __construct()
    {
        $this->manifestModel = new ManifestModel();
        $this->db = \Config\Database::connect();
    }

    public function tripDetail(Type $var = null)
    {
        $bodyRaw = $this->request->getRawInput();
        $tripIdNo = isset($bodyRaw['tripIdNo']) ? $bodyRaw['tripIdNo'] : '';
        $tripDate = isset($bodyRaw['tripDate']) ? $bodyRaw['tripDate'] : '';

        $result['status'] = 200;
        $result['messages'] = 'success';

        return $this->respond($result, 200);
    }
    
    public function checkinList()
    {
        $bodyRaw = $this->request->getRawInput();
        $tripIdNo = isset($bodyRaw['tripIdNo']) ? $bodyRaw['tripIdNo'] : '';
        $tripDate = isset($bodyRaw['tripDate']) ? $bodyRaw['tripDate'] : '';

        $checkinList = $this->manifestModel->getCheckinList($tripIdNo,$tripDate)->getResult();
        
        $result['status'] = 200;
        $result['messages'] = 'success';
        $result['data'] = $checkinList;

        return $this->respond($result, 200);
    }

    public function setStatusCheckin()
    {
        $bodyRaw = $this->request->getRawInput();
        $ticketNumber = isset($bodyRaw['ticketNumber']) ? $bodyRaw['ticketNumber'] : '';
        $status = isset($bodyRaw['status']) ? $bodyRaw['status'] : '';

        $checkin = $this->manifestModel->findCheckin($ticketNumber)->getRow();

        $data = [
            'ticket_number' => $ticketNumber,
            'status'    => $status,
        ];
        
        if ($checkin) {
            $checkinList = $this->manifestModel->updateCheckin($data);
        } else {
            $data['foodtake'] = 0;
            $checkinList = $this->manifestModel->createCheckin($data);
        }

        $result['status'] = 200;
        $result['messages'] = 'success';

        return $this->respond($result, 200);
    }

    public function expensesList()
    {
        $bodyRaw = $this->request->getRawInput();
        $tripIdNo = isset($bodyRaw['tripIdNo']) ? $bodyRaw['tripIdNo'] : '';
        $tripDate = isset($bodyRaw['tripDate']) ? $bodyRaw['tripDate'] : '';

        $expensesList = $this->manifestModel->getExpensesList($tripIdNo,$tripDate)->getResult();
        
        $result['status'] = 200;
        $result['messages'] = 'success';
        $result['data'] = $expensesList;

        return $this->respond($result, 200);
    }

    public function expensesSet()
    {
        $bodyRaw = $this->request->getRawInput();
        $tripIdNo = isset($bodyRaw['tripIdNo']) ? $bodyRaw['tripIdNo'] : '';
        $tripDate = isset($bodyRaw['tripDate']) ? $bodyRaw['tripDate'] : '';
        $description = isset($bodyRaw['description']) ? $bodyRaw['description'] : '';
        $nominal = isset($bodyRaw['nominal']) ? $bodyRaw['nominal'] : '';
        $dateNow = date("Y-m-d H:i:s");

        $data = [
            'trip_id_no' => $tripIdNo,
            'trip_date'    => $tripDate,
            'description'    => $description,
            'nominal'    => $nominal,
            'status'    => 1,
            'created_at'    => $dateNow,
        ];
        
        $createExpense = $this->manifestModel->createExpense($data);

        $result['status'] = 200;
        $result['messages'] = 'success';

        return $this->respond($result, 200);
    }
}
