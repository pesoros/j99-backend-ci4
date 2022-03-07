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
        Xendit::setApiKey('xnd_development_ahcjWS67zdnSCcqTnbBpZ8k9E4RHqZtGu3LpfM10mLBTU1BTjTZsbJ8zT4hKGJ');
    }

    public function index()
    {
        $params = ['external_id' => 'demo_147580196270',
            'payer_email' => 'sample_email@xendit.co',
            'description' => 'Trip to Bali',
            'amount' => 32000,
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

        return $this->respond($createInvoice);
    }

    public function paymentMethodList()
    {
        $getPaymentChannels = \Xendit\PaymentChannels::list();

        return $this->respond($getPaymentChannels, 200);
    }
}
