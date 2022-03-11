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
        $external_id = isset($bodyRaw->external_id) ? $bodyRaw->external_id : '-';
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
        $payment_id = isset($bodyRaw->payment_id) ? $bodyRaw->payment_id : '-';
        $external_id = isset($bodyRaw->external_id) ? $bodyRaw->external_id : '-';
        $amount = isset($bodyRaw->amount) ? $bodyRaw->amount : '-';
        $transaction_timestamp = isset($bodyRaw->transaction_timestamp) ? $bodyRaw->transaction_timestamp : '-';
        $id = isset($bodyRaw->id) ? $bodyRaw->id : '-';
        $owner_id = isset($bodyRaw->owner_id) ? $bodyRaw->owner_id : '-';
        $callback_virtual_account_id = isset($bodyRaw->callback_virtual_account_id) ? $bodyRaw->callback_virtual_account_id : '-';
        $account_number = isset($bodyRaw->account_number) ? $bodyRaw->account_number : '-';
        $bank_code = isset($bodyRaw->bank_code) ? $bodyRaw->bank_code : '-';


        return $this->respond($bodyRaw, 200);
    }

    public function retailOutletPay(Type $var = null)
    {
        $bodyRaw = $this->request->getVar();
        $payment_id = isset($bodyRaw->payment_id) ? $bodyRaw->payment_id : '-';
        $external_id = isset($bodyRaw->external_id) ? $bodyRaw->external_id : '-';
        $amount = isset($bodyRaw->amount) ? $bodyRaw->amount : '-';
        $transaction_timestamp = isset($bodyRaw->transaction_timestamp) ? $bodyRaw->transaction_timestamp : '-';
        $id = isset($bodyRaw->id) ? $bodyRaw->id : '-';
        $owner_id = isset($bodyRaw->owner_id) ? $bodyRaw->owner_id : '-';
        $payment_code = isset($bodyRaw->payment_code) ? $bodyRaw->payment_code : '-';
        $retail_outlet_name = isset($bodyRaw->retail_outlet_name) ? $bodyRaw->retail_outlet_name : '-';
        $name = isset($bodyRaw->name) ? $bodyRaw->name : '-';

        return $this->respond($bodyRaw, 200);
    }

    public function ewalletPay(Type $var = null)
    {
        $bodyRaw = $this->request->getVar();
        $bodyRaw = isset($bodyRaw->data) ? $bodyRaw->data : [];
        $id = isset($bodyRaw->id) ? $bodyRaw->id : '-';
        $status = isset($bodyRaw->status) ? $bodyRaw->status : '-';
        $created = isset($bodyRaw->created) ? $bodyRaw->created : '-';
        $customer_id = isset($bodyRaw->customer_id) ? $bodyRaw->customer_id : '-';
        $channel_code = isset($bodyRaw->channel_code) ? $bodyRaw->channel_code : '-';
        $reference_id = isset($bodyRaw->reference_id) ? $bodyRaw->reference_id : '-';
        $charge_amount = isset($bodyRaw->charge_amount) ? $bodyRaw->charge_amount : '-';
        $capture_amount = isset($bodyRaw->capture_amount) ? $bodyRaw->capture_amount : '-';
        $checkout_method = isset($bodyRaw->checkout_method) ? $bodyRaw->checkout_method : '-';
        $payment_method_id = isset($bodyRaw->payment_method_id) ? $bodyRaw->payment_method_id : '-';

        return $this->respond($bodyRaw, 200);
    }
}
