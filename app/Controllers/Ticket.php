<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\TicketModel;

class Ticket extends ResourceController
{
    use ResponseTrait;
    protected $ticketModel;
    public function __construct()
    {
        $this->ticketModel = new TicketModel();
        $this->db = \Config\Database::connect();
    }
    
    public function cekTicket()
    {
        $bodyRaw = $this->request->getRawInput();
        $ticketcode = isset($bodyRaw['code']) ? $bodyRaw['code'] : '';
        $alpha = explode("-",$ticketcode);
        $alpha = $alpha[0];

        if ($alpha == "B") {
            $result = $this->ticketModel->getBook($ticketcode)->getResult();
        } else if ($alpha == "T") {
            $result = $this->ticketModel->getTicket($ticketcode)->getResult();
        } else {
            return $this->failNotFound('wrong code number');
        }

        if (empty($result)) {
            return $this->failNotFound('Data Not Found');
        } 

        return $this->respond($result, 200);
    }
}
