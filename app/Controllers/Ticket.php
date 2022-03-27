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
        $code = isset($bodyRaw['code']) ? $bodyRaw['code'] : '';
        $alpha = explode("-",$code);
        $alpha = $alpha[0];

        if ($alpha == "B") {
            $result = $this->ticketModel->getBook($code)->getResult()[0];
            $result->code_type = 'booking';
            $result->ticket = $this->ticketModel->getTicket($code,'book')->getResult();
        } else if ($alpha == "T") {
            $result = $this->ticketModel->getTicket($code)->getResult()[0];
            $result->code_type = 'ticket';
        } else {
            return $this->failNotFound('wrong code number');
        }

        if (empty($result)) {
            return $this->failNotFound('Data Not Found');
        } 

        return $this->respond($result, 200);
    }
}
