<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\MasterModel;
use App\Models\TripModel;
use CodeIgniter\I18n\Time;

class MasterData extends ResourceController
{
    use ResponseTrait;
    protected $masterModel;
    public function __construct()
    {
        $this->masterModel = new MasterModel();
    }

    public function dataKota()
    {
        $bodyRaw = $this->request->getRawInput();
        $q = isset($bodyRaw['keyword']) ? $bodyRaw['keyword'] : '';

        $result = $this->masterModel->getCity($q)->getResult();
        $locationpool = [];

        foreach ($result as $key => $value) {
            $locat = $this->masterModel->getLocation($value->id)->getResult();
            foreach ($locat as $keylo => $valuelo) {
                $ded = [];
                $ded['id'] = $valuelo->id;
                $ded['namaKota'] = $value->namaKota.' - '.$valuelo->name;
                $locationpool[] = $ded;
            }
        }

        $result = array_merge($result, $locationpool);

        // foreach ($result as $key => $value) {
        //     $value->row = $key;
        // }
        return $this->respond($result, 200);
    }

    public function dataKelas()
    {
        $bodyRaw = $this->request->getRawInput();
        $q = isset($bodyRaw['keyword']) ? $bodyRaw['keyword'] : '';

        $result = $this->masterModel->getFleetType($q)->getResult();
        return $this->respond($result, 200);
    }

    public function dataUnit()
    {
        $result = $this->masterModel->getUnit()->getResult();
        return $this->respond($result, 200);
    }

    public function dataResto()
    {
        $result = $this->masterModel->getResto()->getResult();
        return $this->respond($result, 200);
    }

    public function dataMenu()
    {
        $bodyRaw = $this->request->getRawInput();
        $idResto = isset($bodyRaw['idResto']) ? $bodyRaw['idResto'] : '';
        $class = isset($bodyRaw['class']) ? $bodyRaw['class'] : '';

        $result = $this->masterModel->getRestoMenu($idResto,$class)->getResult();

        foreach ($result as $key => $value) {
            if ($value->image != null) {
                $value->image = getenv('ADMIN_ENDPOINT').$value->image;
            } else {
                $value->image = base_url('assets/default_food.jpeg');
            }
        }

        return $this->respond($result, 200);
    }

    public function dataCheckinStatus()
    {
        $result = $this->masterModel->getCheckinStatus()->getResult();
        return $this->respond($result, 200);
    }

    public function checkRegis()
    {
        $bodyRaw = $this->request->getRawInput();
        $email = isset($bodyRaw['email']) ? $bodyRaw['email'] : '';

        $getEmail = $this->masterModel->getregis($email)->getRow();

        if (!$getEmail) {
            $result['status'] = 200;
            $result['message'] = 'email not taken';
        } else {
            $result['status'] = 404;
            $result['message'] = 'email taken';
        }
        
        return $this->respond($result, 200);
    }

    public function clearticket()
    {
        $ticket = $this->masterModel->clearTicket()->getResult();
        $now = new Time('-62 minutes');

        foreach ($ticket as $key => $value) {
            if ($value->created_at < $now) {
                $value->delete = true;
                $this->masterModel->deleteTicket($value->booking_code,$value->id_no);
            }
        }

        $result['ticket'] = $ticket;
        $result['datetimenow'] = $now;
        return $this->respond($result, 200);
    }
}
