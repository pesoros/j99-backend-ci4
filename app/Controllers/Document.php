<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
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


class Document extends ResourceController
{
    use ResponseTrait;
    public function __construct()
    {

    }

    public function thermalTicket()
    {
        $kodeTicket = "T-K98NMD";
        $data['qrcode'] = $this->qrcodeGenerate($kodeTicket);
        $filename = date('y-m-d-H-i-s'). 'ticket';
        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('documents/thermalTicket', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream($filename, array("Attachment" => false));
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
