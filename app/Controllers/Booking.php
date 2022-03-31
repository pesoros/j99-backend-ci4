<?php

namespace App\Controllers;

use App\Models\BookingModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\I18n\Time;
use CodeIgniter\RESTful\ResourceController;
use Xendit\Xendit;

class Booking extends ResourceController
{
    use ResponseTrait;
    protected $bookingModel;
    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->db = \Config\Database::connect();
        Xendit::setApiKey(getenv('XENDIT_TOKEN'));
    }

    public function storeBook()
    {
        $timezone = $this->bookingModel->getWsSetting(1)->getResult();
        date_default_timezone_set($timezone[0]->timezone);

        $bodyRaw = $this->request->getVar();
        $booker_id = isset($bodyRaw['booker_id']) ? $bodyRaw['booker_id'] : '';
        $booker_name = isset($bodyRaw['booker_name']) ? $bodyRaw['booker_name'] : '';
        $booker_email = isset($bodyRaw['booker_email']) ? $bodyRaw['booker_email'] : '';
        $booker_phone = isset($bodyRaw['booker_phone']) ? $bodyRaw['booker_phone'] : '';
        $offer_code = isset($bodyRaw['offer_code']) ? $bodyRaw['offer_code'] : '';
        $pergi = isset($bodyRaw['pergi']) ? $bodyRaw['pergi'] : '';
        $pulang = isset($bodyRaw['pulang']) ? $bodyRaw['pulang'] : '';
        $dateNow = date('Y-m-d H:i:s');
        $bookingCode = $this->codeGenerate("B", 8);

        $total_price = 0;
        $total_seat = 0;
        $roundTrip = 0;

        $data['pergi'] = $this->setTicket($pergi, $bookingCode, $offer_code);
        $total_price = $data['pergi']['price'];
        unset($data['pergi']['price']);
        unset($data['pergi']['message']);
        $total_seat = count($pergi['seatPicked']);

        if ($pulang) {
            $roundTrip = 1;
            $total_seat = $total_seat + count($pulang['seatPicked']);
            $data['pulang'] = $this->setTicket($pulang, $bookingCode, $offer_code);
            $total_price = $total_price + $data['pulang']['price'];
            unset($data['pulang']['price']);
            unset($data['pulang']['message']);
        } else {
            $data['pulang'] = '-';
        }

        $setBookingCode = $this->createBooking([
            'booker' => $booker_email,
            'booking_code' => $bookingCode,
            'round_trip' => $roundTrip,
            'total_price' => $total_price,
            'total_seat' => $total_seat,
            'payment_status' => '0',
            'offer_code' => $offer_code,
            'created_at' => $dateNow,
        ]);

        $payment_method = isset($bodyRaw['payment_method']) ? $bodyRaw['payment_method'] : '';
        $payment_channel_code = isset($bodyRaw['payment_channel_code']) ? $bodyRaw['payment_channel_code'] : '';

        $setPayment = $this->paymentGateway($payment_method, $bookingCode, $booker_email, $booker_name, 'j99 ticket', $total_price, $payment_channel_code);
        $data['payment'] = $setPayment;
        $data['payment']['total_price'] = $total_price;

        return $this->respond($data, 200);
    }

    public function createBooking($bookData)
    {
        $createTicket = $this->bookingModel->createBooking($bookData);
    }

    public function setTicket($datas, $bookingCode, $offer_code)
    {
        $trip_id_no = isset($datas['trip_id_no']) ? $datas['trip_id_no'] : '';
        $trip_route_id = isset($datas['trip_route_id']) ? $datas['trip_route_id'] : '';
        $pickup_location = isset($datas['pickup_location']) ? $datas['pickup_location'] : '';
        $drop_location = isset($datas['drop_location']) ? $datas['drop_location'] : '';
        $pricePerSeat = isset($datas['pricePerSeat']) ? $datas['pricePerSeat'] : '';
        $booking_date = isset($datas['booking_date']) ? $datas['booking_date'] : '';
        $fleet_type = isset($datas['fleet_type_id']) ? $datas['fleet_type_id'] : '';
        $facilities = null;

        $seatPicked = $datas['seatPicked'];
        $seatCount = count($seatPicked);
        $seat_number = '';
        foreach ($seatPicked as $key => $value) {
            if ($key > 0) {
                $seat_number .= ',';
            }
            $seat_number .= $value['seat'];
        }

        $adult_sts = $seatCount;
        $child_sts = 0;
        $special_sts = 0;
        $totl_inpt = intval($child_sts) + intval($adult_sts) + intval($special_sts);
        $price = intval($pricePerSeat) * intval($seatCount);

        /// Every Route Children and special seats info
        $rout_chsp_seat = $this->bookingModel->getTripRoute($trip_route_id)->getResult();

        if ($seatCount == $totl_inpt) {
            #--------------------------------------
            $booking_date = $booking_date . ' ' . date('H:i:s');

            if ($offer_code != '') {
                $discount = $this->checkOffer(
                    $offer_code,
                    $trip_route_id,
                    date('Y-m-d', strtotime($booking_date))
                );
            } else {
                $discount = 0;
            }
            $passengerId = $this->codeGenerate("P", 12);
            $bookId = $this->codeGenerate("G", 12);

            #--------------------------------------

            $postData = [
                'booking_code' => $bookingCode,
                'id_no' => $bookId,
                'trip_id_no' => $trip_id_no,
                'tkt_passenger_id_no' => $passengerId,
                'trip_route_id' => $trip_route_id,
                'pickup_trip_location' => $pickup_location,
                'drop_trip_location' => $drop_location,
                'request_facilities' => $facilities,
                'price' => $price,
                'discount' => $discount,
                'adult' => $adult_sts,
                'child' => $child_sts,
                'special' => $special_sts,
                'total_seat' => $seatCount,
                'seat_numbers' => $seat_number,
                'offer_code' => $offer_code,
                'tkt_refund_id' => null,
                'agent_id' => null,
                'booking_date' => $booking_date,
                'date' => date('Y-m-d H:i:s'),
                'status' => '0',
            ];

            $cs = $this->bookingModel->getTicketBooking($trip_id_no, $booking_date)->getResult();
            $tcs = $cs[0]->tchild + 0;
            $tspecialck = $cs[0]->tspecial + 0;
            $req_children_seat = (!empty($rout_chsp_seat[0]->children_seat) ? $rout_chsp_seat[0]->children_seat : 20);
            $req_special_seat = (!empty($rout_chsp_seat[0]->special_seat) ? $rout_chsp_seat[0]->special_seat : 20);
            if ($tcs <= $req_children_seat) {
                if ($tspecialck <= $rout_chsp_seat[0]->special_seat) {
                    #---------check seats--------
                    $bookCheck = $this->checkBooking($trip_id_no, $fleet_type, $seat_number, $booking_date);
                    // return $this->respond($bookCheck);
                    if ($bookCheck) {

                        if ($this->bookingModel->createGroup($postData)) {

                            foreach ($seatPicked as $key => $value) {
                                $ticketNumber = $this->codeGenerate("T", 8);
                                $ticketdata = [
                                    'booking_id' => $bookId,
                                    'ticket_number' => $ticketNumber,
                                    'name' => $value['name'],
                                    'fleet_type' => $fleet_type,
                                    'seat_number' => $value['seat'],
                                    'food' => $value['food'],
                                    'baggage' => $value['baggage'],
                                    'phone' => $value['phone'],
                                ];
                                $createTicket = $this->bookingModel->createTicket($ticketdata);
                            }

                            $binfo = $this->bookingModel->getBookingHistory($postData['id_no'])->getResult();
                            $total_amnt = $binfo[0]->price;
                            $comission = $this->bookingModel->getWsSetting()->getResult();
                            $obj['b_commission'] = ($binfo[0]->price * $comission[0]->bank_commission) / 100;
                            $obj['commission_per'] = $comission[0]->bank_commission;
                            $priprice = $this->bookingModel->getPriPrice($trip_route_id)->getResult();
                            $obj['routePrice'] = $priprice[0];
                            $data['status'] = true;
                            $data['message'] = 'save_successfully';
                            $obj['booking'] = $binfo[0];

                            $postData['booking_type'] = 'Cash';
                            $postData['payment_status'] = 2;
                            unset($postData['offer_code']);
                            unset($postData['tkt_refund_id']);
                            unset($postData['status']);

                            $data['price'] = $price;

                            $insertdata = $this->bookingModel->createTktBooking($postData);
                        } else {
                            $data['status'] = false;
                            $data['exception'] = 'please_try_again';
                        }
                    } else {
                        $data['status'] = false;
                        $data['exception'] = 'something_went_worng';
                    }
                } else {
                    $data['status'] = false;
                    $data['exception'] = 'Special Seats Are not Available';
                }
            } else {
                $data['status'] = false;
                $data['exception'] = 'Children Seats Are not Available';
            }
        } else {
            $data['status'] = false;
            $data['exception'] = 'Please Check your seat';
        }

        return $data;
    }

    private function checkBooking($tripIdNo = null, $fleetId = null, $newSeats = null, $booking_date = null)
    {
        if ($tripIdNo == null || $fleetId == null || $newSeats == null) {
            return $tripIdNo . '-' . $fleetId . '-' . $newSeats . '-' . $booking_date;
        }

        //---------------fleet seats----------------
        $fleetSeats = $this->bookingModel->checkBooking($fleetId)->getResult();

        $seatArray = array();
        $seatArray = array_map('trim', explode(',', $fleetSeats[0]->seat_numbers));
        //-----------------booked seats-------------------
        $bookedSeats = $this->bookingModel->getBookedSeat($tripIdNo, $booking_date)->getResult();

        $bookArray = array();
        if ($bookedSeats[0]->booked_serial !== null) {
            $bookArray = array_map('trim', explode(',', $bookedSeats[0]->booked_serial));
        }

        //-----------------booked seats-------------------
        $newSeatArray = array();
        $newSeatArray = array_map('trim', explode(',', $newSeats));

        if (sizeof($newSeatArray) > 0) {

            foreach ($newSeatArray as $seat) {
                if (!empty($seat)) {
                    if (in_array($seat, $bookArray)) {
                        return false;
                    } else if (!in_array($seat, $seatArray)) {
                        return true;
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public function paymentGateway($payment_method, $bookingId, $email, $name, $description, $amount, $payment_channel_code)
    {
        // $createInvoice = $this->createInvoice($bookingId, $email, $name, $description, $amount, $payment_channel_code);
        $dateExpired = new Time('+1 day');
        $dateExpired = date("Y-m-d", strtotime($dateExpired)) . 'T' . date("h:i:s", strtotime($dateExpired)) . '.000Z';

        if ($payment_method == 'VIRTUAL_ACCOUNT') {
            $result = $this->generateVirtualAccountPay($bookingId, $email, $name, $description, $amount, $payment_channel_code, $dateExpired);
        } elseif ($payment_method == 'EWALLET') {
            $result = $this->generateEwalletPay($bookingId, $email, $name, $description, $amount, $payment_channel_code, $dateExpired);
        } elseif ($payment_method == 'RETAIL_OUTLET') {
            $result = $this->generateRetailOutletPay($bookingId, $email, $name, $description, $amount, $payment_channel_code, $dateExpired);
        } else {
            $result = false;
        }

        return $result;
    }

    public function createInvoice($bookingId, $email, $name, $description, $amount, $payment_channel_code)
    {
        $params = ['external_id' => $bookingId,
            'payer_email' => $email,
            'description' => $description,
            'amount' => $amount,
        ];

        $createInvoice = \Xendit\Invoice::create($params);

        return $createInvoice;
    }

    public function generateVirtualAccountPay($bookingId, $email, $name, $description, $amount, $payment_channel_code, $dateExpired)
    {
        $params = ["external_id" => $bookingId,
            "bank_code" => strval($payment_channel_code),
            "name" => $name,
            "expiration_date" => $dateExpired,
        ];

        $createVA = \Xendit\VirtualAccounts::create($params);

        return $createVA;
    }

    public function generateEwalletPay($bookingId, $email, $name, $description, $amount, $payment_channel_code, $dateExpired)
    {
        $params = [
            'reference_id' => $bookingId,
            'currency' => 'IDR',
            'amount' => $amount,
            'checkout_method' => 'ONE_TIME_PAYMENT',
            'channel_code' => $payment_channel_code,
            'channel_properties' => [
                'success_redirect_url' => 'https://dashboard.xendit.co/register/1',
                'mobile_number' => "+6287822102761",
            ],
            'metadata' => [
                'branch_code' => 'tree_branch',
            ],
        ];

        $createEWalletCharge = \Xendit\EWallets::createEWalletCharge($params);

        return $createEWalletCharge;
    }

    public function generateRetailOutletPay($bookingId, $email, $name, $description, $amount, $payment_channel_code, $dateExpired)
    {
        $params = [
            'external_id' => $bookingId,
            'retail_outlet_name' => $payment_channel_code,
            'name' => $name,
            'expected_amount' => $amount,
            "expiration_date" => $dateExpired,
        ];

        $createFPC = \Xendit\Retail::create($params);

        return $createFPC;
    }

    public function codeGenerate($head = 'J99', $length = 12)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $result = $head . '-' . $randomString;

        return $result;
    }

}
