<?php

namespace App\Controllers;

use App\Models\MasterModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use Xendit\Xendit;

class XenditResource extends ResourceController
{
    use ResponseTrait;
    protected $masterModel;
    public function __construct()
    {
        $this->masterModel = new MasterModel();
        Xendit::setApiKey(getenv('XENDIT_TOKEN'));
    }

    public function index()
    {
        $paymentID = 'pesoros_2021zbcde';

        $params = ['external_id' => $paymentID,
            'payer_email' => 'sample_email@xendit.co',
            'description' => 'Trip to Bali',
            'amount' => 1000,
        ];

        $createInvoice = \Xendit\Invoice::create($params);
        // var_dump($createInvoice);

        // $id = $createInvoice['id'];

        // $getInvoice = \Xendit\Invoice::retrieve($id);
        // // var_dump($getInvoice);

        // $params = [
        // ];
        // $expireInvoice = \Xendit\Invoice::expireInvoice($id, $params);
        // // var_dump($expireInvoice);

        // $retrieveAll = [
        // ];
        // $getAllInvoice = \Xendit\Invoice::retrieveAll($retrieveAll);
        // var_dump(($getAllInvoice));

        $params = ["external_id" => $paymentID,
            "bank_code" => "BNI",
            "name" => "Steve Wozniak",
        ];

        $createVA = \Xendit\VirtualAccounts::create($params);

        // $updateParams = ["suggested_amount" => 150000];
      
        // $updateVA = \Xendit\VirtualAccounts::update($paymentID, $updateParams);
      

        // $getFVAPayment = \Xendit\VirtualAccounts::getFVAPayment($paymentID);

        return $this->respond(['a' => $createVA]);
    }

    public function paymentMethodList()
    {
        $getPaymentChannels = \Xendit\PaymentChannels::list();

        return $this->respond($getPaymentChannels, 200);
    }
}
