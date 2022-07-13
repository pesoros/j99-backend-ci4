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
use DateTime;
use DateInterval;

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
            if (empty($result)) {
                return $this->failNotFound('Data Not Found');
            } 
            $getDetailBook = $this->ticketModel->detailBook($result->booking_code)->getRow();
            if (isset($getDetailBook)) {

                $minutes_to_add = 60;
                $time = new DateTime($getDetailBook->date);
                $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
                $expired = $time->format('Y-m-d H:i:s');

                $result->expired = $expired;
                $result->from = $getDetailBook->pickup_trip_location;
                $result->to = $getDetailBook->drop_trip_location;
            }
            
            $result->payment_registration = $this->ticketModel->getPaymentRegis($code)->getRow();
            $result->payment_tutorial = $this->ticketModel->getPaymentTutor($result->payment_registration->payment_channel_code)->getResult();
            $result->code_type = 'booking';
            $result->ticket = $this->ticketModel->getTicket($code,'book')->getResult();
            foreach ($result->ticket as $key => $value) {
                $timebook = new DateTime($value->booking_date);
                $timebook = $timebook->format('Y-m-d');

                $hour = $this->ticketModel->getHour($value->trip_id,$value->pickup_trip_location,$value->drop_trip_location)->getRow();
                $value->booking_date = $timebook.' '.$hour->dep_time;
            }
        } else if ($alpha == "T") {
            $result = $this->ticketModel->getTicket($code)->getRow();

            $qrcode = $this->qrcodeGenerate($code);
            if (empty($result)) {
                return $this->failNotFound('Data Not Found ..');
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
