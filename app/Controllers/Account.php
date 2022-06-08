<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\AccountModel;
use App\Models\UserModel;

class Account extends ResourceController
{
    use ResponseTrait;
    protected $accountModel;
    public function __construct()
    {
        $this->accountModel = new AccountModel();
        $this->db = \Config\Database::connect();
    }
    
    public function getProfile()
    {
        $bodyRaw = $this->request->getRawInput();
        $email = isset($bodyRaw['email']) ? $bodyRaw['email'] : '';

        $result = $this->accountModel->getProfile($email)->getRow();

        if (empty($result)) {
            return $this->failNotFound('Data Not Found');
        } 

        return $this->respond($result, 200);
    }

    public function updateProfile()
    {
        $bodyRaw = $this->request->getRawInput();

        $email = isset($bodyRaw['email']) ? $bodyRaw['email'] : '';
        $first_name = isset($bodyRaw['first_name']) ? $bodyRaw['first_name'] : '';
        $last_name = isset($bodyRaw['last_name']) ? $bodyRaw['last_name'] : '';
        $address = isset($bodyRaw['address']) ? $bodyRaw['address'] : '';
        $phone = isset($bodyRaw['phone']) ? $bodyRaw['phone'] : '';
        $identity = isset($bodyRaw['identity']) ? $bodyRaw['identity'] : '';
        $identity_number = isset($bodyRaw['identity_number']) ? $bodyRaw['identity_number'] : '';

        // $model = new UserModel();
        // $checkNik = $model->where("identity_number", $identity_number)->first();
        // if ($checkNik) {
        //     return $this->fail('identity number was exist');
        // }

        unset($bodyRaw['email']);

        $result = $this->accountModel->updateProfile($email,$bodyRaw);

        return $this->respond($result, 200);
    }

    public function historyTicket()
    {
        $bodyRaw = $this->request->getRawInput();
        $email = isset($bodyRaw['email']) ? $bodyRaw['email'] : '';
        if ($email == '') {
            return $this->failNotFound('Email empty');
        }
        $getHistory = $this->accountModel->historyTicket($email)->getResult();
        foreach ($getHistory as $key => $value) {
            $getDetailBook = $this->accountModel->detailBook($value->booking_code)->getRow();
            if (isset($getDetailBook)) {
                $value->from = $getDetailBook->pickup_trip_location;
                $value->to = $getDetailBook->drop_trip_location;
            }
        }

        if (empty($getHistory)) {
            return $this->failNotFound('Data Not Found');
        } 

        $result['status'] = 200;
        $result['data'] = $getHistory;

        return $this->respond($result, 200);
    }

    public function changePassword()
    {
        $bodyRaw = $this->request->getRawInput();
        $email = isset($bodyRaw['email']) ? $bodyRaw['email'] : '';
        $newPassword = isset($bodyRaw['newPassword']) ? $bodyRaw['newPassword'] : '';
        $confNewPassword = isset($bodyRaw['confNewPassword']) ? $bodyRaw['confNewPassword'] : '';

        if ($newPassword !== $confNewPassword) {
            return $this->failNotFound('Passord not match');
        }

        $data['password'] = password_hash($newPassword, PASSWORD_BCRYPT);

        $result = $this->accountModel->updatePassword($email,$data);

        return $this->respond($result);
    }
}
