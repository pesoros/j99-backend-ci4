<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\TicketModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode;
use Spatie;

class Ticket extends ResourceController
{
    use ResponseTrait;
    protected $ticketModel;
    public function __construct()
    {
        $this->ticketModel = new TicketModel();
        $this->db = \Config\Database::connect();
    }
    
    public function cekTicket()
    {
        $bodyRaw = $this->request->getRawInput();
        $code = isset($bodyRaw['code']) ? $bodyRaw['code'] : '';
        $alpha = explode("-",$code);
        $alpha = $alpha[0];

        if ($alpha == "B") {
            $result = $this->ticketModel->getBook($code)->getRow();
            $result->payment_registration = $this->ticketModel->getPaymentRegis($code)->getRow();
            if (empty($result)) {
                return $this->failNotFound('Data Not Found');
            } 
            $result->code_type = 'booking';
            $result->ticket = $this->ticketModel->getTicket($code,'book')->getResult();
        } else if ($alpha == "T") {
            $result = $this->ticketModel->getTicket($code)->getRow();

            $qrcode = $this->qrcodeGenerate($code);
            if (empty($result)) {
                return $this->failNotFound('Data Not Found');
            } 
            $result->code_type = 'ticket';
            $result->qrcode = $qrcode;
            // $result->print_url = base_url('print/ticket/thermal?code='.$code);
        } else {
            return $this->failNotFound('wrong code number');
        }

        if (empty($result)) {
            return $this->failNotFound('Data Not Found');
        } 

        return $this->respond($result, 200);
    }

    public function thermalTicket()
    {
        $code = $this->request->getVar("code");

        $data['tickedData'] = $this->ticketModel->getTicket($code)->getRow();
        $data['qrcode'] = $this->qrcodeGenerate($code);

        $filename = $code. '-ticket.php';
        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('documents/thermalTicket', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream($filename, array("Attachment" => false));
        exit();
    }

    public function thermalTicketHtml()
    {
        $code = $this->request->getVar("code");

        $data['tickedData'] = $this->ticketModel->getTicket($code)->getRow();
        $data['qrcode'] = $this->qrcodeGenerate($code);

        return view('documents/thermalTicket', $data);
    }

    public function thermalTicketImage()
    {
        $code = $this->request->getVar("code");

        // $data['tickedData'] = $this->ticketModel->getTicket($code)->getRow();
        // $data['qrcode'] = $this->qrcodeGenerate($code);

        $filename = $code. '-ticket';
        // $options = new Options();
        // $options->set('isRemoteEnabled', TRUE);
        // $dompdf = new Dompdf($options);
        // $dompdf->loadHtml(view('documents/thermalTicket', $data));
        // $dompdf->setPaper('A4', 'landscape');
        // $dompdf->render();
        // $output = $dompdf->output();
        // file_put_contents('ticket/pdf/'.$filename.'.pdf', $output);

        $pdf = new Spatie\PdfToImage\Pdf('ticket/pdf/T-Y1BJLXBQ-ticket.pdf');
        $pdf->saveImage('/ticket/png/'.$filename.'.png');

        return;
    }

    public function qrcodeGenerate($kodeTicket)
    {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($kodeTicket)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(170)
            ->margin(0)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            // ->logoPath($_SERVER['DOCUMENT_ROOT'].'/assets/logojuragan99.png')
            // ->labelText($kodeTicket)
            // ->labelFont(new NotoSans(6))
            // ->labelAlignment(new LabelAlignmentCenter())
            ->build();

            header('Content-Type: '.$result->getMimeType());
            $res = $result->getString();

            // Save it to a file
            $result->saveToFile($_SERVER['DOCUMENT_ROOT'].'/assets/qrcode/qrcodefile.png');

            // Generate a data URI to include image data inline (i.e. inside an <img> tag)
            $dataUri = $result->getDataUri();

            return $dataUri;
    }
}
