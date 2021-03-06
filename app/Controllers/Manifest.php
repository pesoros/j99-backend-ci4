<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\ManifestModel;
use App\Models\PaketModel;

class Manifest extends ResourceController
{
    use ResponseTrait;
    protected $manifestModel;
    protected $paketModel;
    public function __construct()
    {
        $this->manifestModel = new ManifestModel();
        $this->paketModel = new PaketModel();
        $this->db = \Config\Database::connect();
    }

    public function tripDetail()
    {
        $bodyRaw = $this->request->getRawInput();
        $tripIdNo = isset($bodyRaw['tripIdNo']) ? $bodyRaw['tripIdNo'] : '';
        $tripDate = isset($bodyRaw['tripDate']) ? $bodyRaw['tripDate'] : '';

        $penumpangAkum = 0;
        $menungguAkum = 0;
        $baggageAkum = 0;
        $foodAkum = [];
        $foodAkumResult = [];
        $manifest = $this->manifestModel->getManifestByTrip($tripIdNo,$tripDate)->getRow();
        $checkinList = $this->manifestModel->getCheckinList($tripIdNo,$tripDate,)->getResult();
        foreach ($checkinList as $key => $value) {
            if (!isset($foodAkum[$value->food_name])) {
                $foodAkum[$value->food_name] = 1;
            } else {
                $foodAkum[$value->food_name] += 1;
            }
            if ($value->baggage == 'Bawa') {
                $baggageAkum += 1;
            }
            if ($value->checkin_status == 'Menunggu') {
                $menungguAkum += 1;
            }
            $penumpangAkum += 1;
        }

        foreach ($foodAkum as $key => $value) {
            $des = [];
            $des['nama_makanan'] = $key;
            $des['qty'] = $value;
            $foodAkumResult[] = $des;
        }

        $tripDetail = $this->manifestModel->getTripDetail($tripIdNo, $manifest->id)->getRow();
        $class = $this->manifestModel->getTripType($tripDetail->reg_no)->getResult();
        $className = '';
        foreach ($class as $key => $value) {
            if ($key > 0) {
                $className .= ',';
            }
            $className .= $value->type;
        }
        $tripDetail->class = $className;
        $tripDetail->totalpenumpang = $penumpangAkum;
        $tripDetail->penumpang_belum_checkin = $menungguAkum;
        $tripDetail->totalbagasi = $baggageAkum;
        $tripDetail->akumulasi_makanan = $foodAkumResult;

        $result['status'] = 200;
        $result['messages'] = 'success';
        $result['data'] = $tripDetail;

        return $this->respond($result, 200);
    }
    
    public function checkinList()
    {
        $bodyRaw = $this->request->getRawInput();
        $tripIdNo = isset($bodyRaw['tripIdNo']) ? $bodyRaw['tripIdNo'] : '';
        $tripDate = isset($bodyRaw['tripDate']) ? $bodyRaw['tripDate'] : '';

        $checkinList = $this->manifestModel->getCheckinList($tripIdNo,$tripDate)->getResult();

        foreach ($checkinList as $key => $value) {
            $value->url_print = base_url('print/ticket/thermal?code='.$value->ticket_number);
        }
        
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
        $result['url_print'] = base_url('print/ticket/thermal?code='.$ticketNumber);

        return $this->respond($result, 200);
    }

    public function expensesList()
    {
        $bodyRaw = $this->request->getRawInput();
        $tripIdNo = isset($bodyRaw['tripIdNo']) ? $bodyRaw['tripIdNo'] : '';
        $tripDate = isset($bodyRaw['tripDate']) ? $bodyRaw['tripDate'] : '';

        $expensesList = $this->manifestModel->getExpensesList($tripIdNo,$tripDate)->getResult();
        $getAllowance = $this->manifestModel->getAllowance($tripIdNo)->getRow();

        $spend = 0;
        $income = 0;
        $summary = 0;
        
        foreach ($expensesList as $key => $value) {
            if ($value->action == 'spend') {
                $spend = $spend + $value->nominal;
            } else {
                $income = $income + $value->nominal;
            }
        }

        if ($getAllowance) {
            $allowance = intval($getAllowance->allowance);
        } else {
            $allowance = 0;
        }
        $summary = $allowance + $income;
        $summary = $summary - $spend;
        
        $result['status'] = 200;
        $result['messages'] = 'success';
        $result['total_spend'] = $spend;
        $result['total_income'] = $income;
        $result['allowance'] = $allowance;
        $result['summary'] = $summary;
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
        $action = isset($bodyRaw['action']) ? $bodyRaw['action'] : '';
        $dateNow = date("Y-m-d H:i:s");

        $data = [
            'trip_id_no' => $tripIdNo,
            'trip_date'    => $tripDate,
            'description'    => $description,
            'nominal'    => $nominal,
            'action'    => $action,
            'status'    => 1,
            'created_at'    => $dateNow,
        ];
        
        $createExpense = $this->manifestModel->createExpense($data);

        $result['status'] = 200;
        $result['messages'] = 'success';

        return $this->respond($result, 200);
    }

    public function typeFrom()
    {
        $typeFrom = $this->manifestModel->getTypeFrom()->getResult();
        
        $result['status'] = 200;
        $result['messages'] = 'success';
        $result['data'] = $typeFrom;

        return $this->respond($result, 200);
    }

    public function baggageList()
    {
        $bodyRaw = $this->request->getRawInput();
        $tripIdNo = isset($bodyRaw['tripIdNo']) ? $bodyRaw['tripIdNo'] : '';
        $tripDate = isset($bodyRaw['tripDate']) ? $bodyRaw['tripDate'] : '';

        $baggageList = $this->manifestModel->getBaggageList($tripIdNo,$tripDate)->getResult();

        foreach ($baggageList as $key => $value) {
            if ($value->type_from == 'Package') {
                $getDetail = $this->getPaket($value->code);
                $value->from = $getDetail->pool_sender_id;
                $value->to = $getDetail->pool_receiver_id;
            } else {
                $getDetail = $this->manifestModel->getTicket($value->code)->getRow();
                if ($getDetail) {
                    $value->from = $getDetail->pickup_trip_location;
                    $value->to = $getDetail->drop_trip_location;
                }
            }
        }
        
        $result['status'] = 200;
        $result['messages'] = 'success';
        $result['data'] = $baggageList;

        return $this->respond($result, 200);
    }

    public function getPaket($packetCode)
    {
        $result = $this->paketModel->getPacket($packetCode)->getRow();

        if (empty($result)) {
            return $this->failNotFound('Data Not Found');
        } 

        $result->pool_sender_id = $this->paketModel->getPool($result->pool_sender_id)->getRow()->name;
        $result->pool_receiver_id = $this->paketModel->getPool($result->pool_receiver_id)->getRow()->name;

        return $result;
    }

    public function baggageSet()
    {
        $bodyRaw = $this->request->getRawInput();
        $tripIdNo = isset($bodyRaw['tripIdNo']) ? $bodyRaw['tripIdNo'] : '';
        $tripDate = isset($bodyRaw['tripDate']) ? $bodyRaw['tripDate'] : '';
        $typeFrom = isset($bodyRaw['typeFrom']) ? $bodyRaw['typeFrom'] : '';
        $code = isset($bodyRaw['code']) ? $bodyRaw['code'] : '';
        $dateNow = date("Y-m-d H:i:s");

        $data = [
            'trip_id_no' => $tripIdNo,
            'trip_date'    => $tripDate,
            'type_from'    => $typeFrom,
            'code'    => $code,
            'created_at'    => $dateNow,
        ];
        
        $createBaggage = $this->manifestModel->createBaggage($data);

        $result['status'] = 200;
        $result['messages'] = 'success';

        return $this->respond($result, 200);
    }
}
