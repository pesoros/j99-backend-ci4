<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\OtpModel;

class Otp extends ResourceController
{
    use ResponseTrait;
    protected $otpModel;
    public function __construct()
    {
        $this->otpModel = new   OtpModel();
        $this->db = \Config\Database::connect();
    }
    
    public function checkOtp()
    {
        $bodyRaw = $this->request->getRawInput();
        $phone = isset($bodyRaw['phone']) ? $bodyRaw['phone'] : '';
        $email = isset($bodyRaw['email']) ? $bodyRaw['email'] : '';
        $otp = isset($bodyRaw['otp']) ? $bodyRaw['otp'] : '';
        $dateNow = date("Y-m-d H:i:s");

        if ($phone !== '') {
            # code...
            $getOtp = $this->otpModel->getOtpPhone($phone,$dateNow)->getRow();
        } else if ($email !== '') {
            $getOtp = $this->otpModel->getOtpMail($email,$dateNow)->getRow();
        } else {
            return $this->failNotFound('email or phone must be filled');
        }

        if (empty($getOtp)) {
            return $this->failNotFound('Data Not Found');
        } 

        if ($getOtp->otp == $otp) {
            if ($getOtp->valid_until < $dateNow) {
                $result['status'] = 500;
                $result['message'] = 'expired';
            } else {
                $result['status'] = 200;
                $result['message'] = 'valid';
            }
        } else {
            $result['status'] = 500;
            $result['message'] = 'not valid';
        }

        return $this->respond($result, 200);
    }

    public function createOtp()
    {
        $bodyRaw = $this->request->getRawInput();
        $phone = isset($bodyRaw['phone']) ? $bodyRaw['phone'] : '';
        $email = isset($bodyRaw['email']) ? $bodyRaw['email'] : '';

        $untilTime = date("Y-m-d H:i:s", strtotime('+15 minutes'));

        $data = [
            'email' => $email,
            'phone' => $phone,
            'otp' => mt_rand(1000,9999),
            'status' => 1,
            'valid_until' => $untilTime
        ];

        $createOtp = $this->otpModel->createOtp($data);

        if ($createOtp !== true) {
            return $this->failNotFound('Failed');
        } 

        $result['status'] = 200;
        $result['message'] = 'success';

        return $this->respond($result, 200);
    }
}
