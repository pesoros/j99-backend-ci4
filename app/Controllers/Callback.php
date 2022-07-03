<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\CallbackModel;

class Callback extends ResourceController
{
    use ResponseTrait;
    protected $callbackModel;
    public function __construct()
    {
        $this->callbackModel = new CallbackModel();
        $this->db = \Config\Database::connect();
    }

    public function virtualAccountCreate()
    {
        // == virtual Account Create ==
        $bodyRaw = $this->request->getVar();
        $id = isset($bodyRaw->id) ? $bodyRaw->id : '-';
        $owner_id = isset($bodyRaw->owner_id) ? $bodyRaw->owner_id : '-';
        $setPay['external_id'] = isset($bodyRaw->external_id) ? $bodyRaw->external_id : '-';
        $expiration_date = isset($bodyRaw->expiration_date) ? $bodyRaw->expiration_date : '-';
        $account_number = isset($bodyRaw->account_number) ? $bodyRaw->account_number : '-';
        $bank_code = isset($bodyRaw->bank_code) ? $bodyRaw->bank_code : '-';
        $name = isset($bodyRaw->name) ? $bodyRaw->name : '-';
        $status = isset($bodyRaw->status) ? $bodyRaw->status : '-';
        $created = isset($bodyRaw->created) ? $bodyRaw->created : '-';
        $is_closed = isset($bodyRaw->is_closed) ? $bodyRaw->is_closed : '-';

        return $this->respond($bodyRaw, 200);
    }

    public function virtualAccountPay()
    {
        // == virtual Account ==
        $bodyRaw = $this->request->getVar();
        $setPay['payment_id'] = isset($bodyRaw->id) ? $bodyRaw->id : '-';
        $setPay['external_id'] = isset($bodyRaw->external_id) ? $bodyRaw->external_id : '-';
        $setPay['amount'] = isset($bodyRaw->amount) ? $bodyRaw->amount : '-';
        $setPay['transaction_timestamp'] = isset($bodyRaw->transaction_timestamp) ? $bodyRaw->transaction_timestamp : '-';
        $setPay['channel_name'] = isset($bodyRaw->bank_code) ? $bodyRaw->bank_code : '-';
        $setPay['code'] = isset($bodyRaw->account_number) ? $bodyRaw->account_number : '-';

        $getBooking = $this->callbackModel->getBooking($setPay['external_id'])->getRow();

        if (!$getBooking) {
            return $this->respond(['respond'=>'booking not found'], 200);
        }

        $setBookingCode = $this->callbackModel->savePayment($setPay);

        if (intval($setPay['amount']) >= intval($getBooking->total_price)) {
            $status = 1;
        } else {
            $status = 0;
        }

        $updatPaymentstatus = $this->callbackModel->updateStatusPayment($setPay['external_id'],$status);

        return $this->respond($bodyRaw, 200);
    }

    public function retailOutletPay(Type $var = null)
    {
        $bodyRaw = $this->request->getVar();
        $setPay['payment_id'] = isset($bodyRaw->id) ? $bodyRaw->id : '-';
        $setPay['external_id'] = isset($bodyRaw->external_id) ? $bodyRaw->external_id : '-';
        $setPay['amount'] = isset($bodyRaw->amount) ? $bodyRaw->amount : '-';
        $setPay['transaction_timestamp'] = isset($bodyRaw->transaction_timestamp) ? $bodyRaw->transaction_timestamp : '-';
        $setPay['channel_name'] = isset($bodyRaw->retail_outlet_name) ? $bodyRaw->retail_outlet_name : '-';
        $setPay['code'] = isset($bodyRaw->payment_code) ? $bodyRaw->payment_code : '-';

        $setBookingCode = $this->callbackModel->savePayment($setPay);

        return $this->respond($bodyRaw, 200);
    }

    public function ewalletPay(Type $var = null)
    {
        $bodyRaw = $this->request->getVar();
        $bodyRaw = isset($bodyRaw->data) ? $bodyRaw->data : [];
        $setPay['payment_id'] = isset($bodyRaw->id) ? $bodyRaw->id : '-';
        $setPay['external_id'] = isset($bodyRaw->reference_id) ? $bodyRaw->reference_id : '-';
        $setPay['amount'] = isset($bodyRaw->charge_amount) ? $bodyRaw->charge_amount : '-';
        $setPay['transaction_timestamp'] = isset($bodyRaw->created) ? $bodyRaw->created : '-';
        $setPay['channel_name'] = isset($bodyRaw->channel_code) ? $bodyRaw->channel_code : '-';

        $status = isset($bodyRaw->status) ? $bodyRaw->status : '-';
        if ($status == 'SUCCEEDED') {
            $setBookingCode = $this->callbackModel->savePayment($setPay);
        }

        return $this->respond($bodyRaw, 200);
    }
}
