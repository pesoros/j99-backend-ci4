<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\AccountModel;

class Mail extends ResourceController
{
    use ResponseTrait;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->email = \Config\Services::email();
    }
    
    public function index()
    {
        $bodyRaw = $this->request->getRawInput();
        $mailTo = isset($bodyRaw['mailTo']) ? $bodyRaw['mailTo'] : '';
        $subject = isset($bodyRaw['subject']) ? $bodyRaw['subject'] : '';
        $message = isset($bodyRaw['message']) ? $bodyRaw['message'] : '';

        $sendMail = $this->sendMail($mailTo,$subject,$message);

        $result['status'] = 200;
        $result['message'] = 'email Successfully sent';
        // $result['mailinfo'] = $this->email;

        return $this->respond($result);
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
