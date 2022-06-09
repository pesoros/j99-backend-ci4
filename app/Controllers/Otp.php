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
        $this->otpModel = new OtpModel();
        $this->db = \Config\Database::connect();
        $this->email = \Config\Services::email();
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
        $sendBy = isset($bodyRaw['sendBy']) ? $bodyRaw['sendBy'] : '';
        $otpNumber= mt_rand(1000,9999);
        $data['otpNumber'] = $otpNumber;

        $bodyOtp = view('mail/otp',$data);

        if ($sendBy == 'email') {
            $sendMail = $this->sendMail($email,'OTP',$bodyOtp);
        } else if ($sendBy == 'wa') {
            $sendWa = $this->sendWa($phone,$otpNumber);
        }

        $untilTime = date("Y-m-d H:i:s", strtotime('+15 minutes'));

        $data = [
            'email' => $email,
            'phone' => $phone,
            'otp' => $otpNumber,
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

    public function sendWa($waTo,$otpNumber)
    {
        $reqData = '{
            "channel_id": '.getenv('QSC_channel_id').',
            "template_name": "sample_shipping_confirmation",
            "namespace": "e24690f7_bbde_44d8_b7d4_9e25808b52dc",
            "language": "id",
            "variables": ["'.$otpNumber.'"],
            "phone_numbers": ["'.$waTo.'"],
            "hide_variables": false
        }';

        return $this->httpPostXformQsc($reqData);
    }

    public function httpPostXformQsc($reqData)
    {
        $QSC_BaseUrl = getenv('QSC_BaseUrl').'/api/v2/admin/broadcast/client_broadcast';
        $QSC_AdminToken = getenv('QSC_AdminToken');
        $QSC_AppCode = getenv('QSC_AppCode');

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://multichannel.qiscus.com/api/v2/admin/broadcast/client_broadcast',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $reqData,
        CURLOPT_HTTPHEADER => array(
                'Authorization: '.$QSC_AdminToken,
                'Content-Type: application/json',
                'Qiscus-App-Id: '.$QSC_AppCode
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}
