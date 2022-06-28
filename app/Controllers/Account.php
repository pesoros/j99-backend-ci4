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
        $this->email = \Config\Services::email();
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

    public function forgotPassword()
    {
        $bodyRaw = $this->request->getRawInput();
        $email = isset($bodyRaw['email']) ? $bodyRaw['email'] : '';
        $token = md5($email.'+'.date('Y-m-d H:i:s'));

        $getEmail = $this->accountModel->getregis($email)->getRow();

        if (!$getEmail) {
            $result['status'] = 404;
            $result['message'] = 'email not found';
        } else {
            $data['email'] = $email;
            $data['token'] = $token;
            $data['status'] = 0;

            $createReset = $this->accountModel->createReset($data); 

            $data['link'] = getenv('FE_ENDPOINT').'passwordreset/'.$token;
            $bodyMail = view('mail/passwordreset',$data);
            $sendMail = $this->sendMail($email,'Password Reset',$bodyMail);

            $result['status'] = 200;
            $result['message'] = 'forgot password send';
        }

        return $this->respond($result, 200);
    }

    public function checkResetToken()
    {
        $bodyRaw = $this->request->getRawInput();
        $token = isset($bodyRaw['token']) ? $bodyRaw['token'] : '';

        $getToken = $this->accountModel->getResetToken($token)->getRow();

        if (!$getToken) {
            $result['status'] = 404;
            $result['message'] = 'token not found';
        } else {
            if ($getToken->status == 1) {
                $result['status'] = 404;
                $result['message'] = 'token not found';
            } else {
                $result['status'] = 200;
                $result['message'] = 'token valid';
                $result['email'] = $getToken->email;
                $result['token'] = $getToken->token;
            }
        }
        
        return $this->respond($result, 200);
    }

    public function resetPassword()
    {
        $bodyRaw = $this->request->getRawInput();
        $token = isset($bodyRaw['token']) ? $bodyRaw['token'] : '';
        $password = isset($bodyRaw['password']) ? $bodyRaw['password'] : '';
        $password_second = isset($bodyRaw['password_second']) ? $bodyRaw['password_second'] : '';

        if ($password != $password_second) {
            $result['status'] = 400;
            $result['message'] = 'password not match';

            return $this->respond($result, 200);
        }

        $getToken = $this->accountModel->getResetToken($token)->getRow();

        if (!$getToken) {
            $result['status'] = 404;
            $result['message'] = 'token not found';
        } else {
            $email = $getToken->email;
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);

            $reset = $this->accountModel->updatePassword($email,$data);
            $reset = $this->accountModel->statusReset($email,['status' => 1]);

            $result['status'] = 200;
            $result['message'] = 'password reset succeed';
        }
        
        return $this->respond($result, 200);
    }

    private function sendMail($mailTo,$subject,$message)
	{
        $nickname = getenv('EMAIL_CONFIG_SENDERNAME');
        $mailFrom = getenv('EMAIL_CONFIG_SENDERMAIL');

		// $config['protocol'] = getenv('EMAIL_CONFIG_PROTOCOL');
		// $config['SMTPHost'] = getenv('EMAIL_CONFIG_HOST');
		// $config['SMTPPort'] = getenv('EMAIL_CONFIG_PORT');
		// $config['SMTPUser'] = getenv('EMAIL_CONFIG_USER');
		// $config['SMTPPass'] = getenv('EMAIL_CONFIG_PASS');
        // $config['SMTPCrypto'] = getenv('CRYPTO');

        $this->email->clear();
        // $this->email->initialize($config);
        $this->email->setTo($mailTo);
        $this->email->setFrom($mailFrom, $nickname);
        
        $this->email->setSubject($subject);
        $this->email->setMessage($message);
        $send = $this->email->send();
		
		return $send;
	}
}
