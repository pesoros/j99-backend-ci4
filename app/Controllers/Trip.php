<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\TripModel;

class Trip extends ResourceController
{
    use ResponseTrait;
    protected $tripModel;
    public function __construct()
    {
        $this->tripModel = new TripModel();
        $this->db = \Config\Database::connect();
    }

    public function tripList()
    {
        $bodyRaw = $this->request->getVar();
        $unitType = isset($bodyRaw['unitType']) ? $bodyRaw['unitType'] : '';
        $kelas = isset($bodyRaw['kelas']) ? $bodyRaw['kelas'] : '';
        $unit_type = isset($bodyRaw['unit_type']) ? $bodyRaw['unit_type'] : '';
        $jumlahPenumpang = isset($bodyRaw['jumlahPenumpang']) ? $bodyRaw['jumlahPenumpang'] : '';
        $dateawal = isset($bodyRaw['tanggal']) ? $bodyRaw['tanggal'] : '';
        $kotaBerangkat = isset($bodyRaw['berangkat']) ? $bodyRaw['berangkat'] : '';
        $kotaTujuan = isset($bodyRaw['tujuan']) ? $bodyRaw['tujuan'] : '';
        $dayforday = date("l", strtotime(!empty($dateawal)?$dateawal:date('Y-m-d')));

        $dayArray = [
            'Sunday' => '1'
            ,'Monday' => '2'
            ,'Tuesday' => '3'
            ,'Wednesday' => '4'
            ,'Thursday' => '5'
            ,'Friday' => '6'
            ,'Saturday' => '7'
        ];

        if (empty($dateawal)) {
            return $this->failNotFound('Data Not Found');
        } 

        $tanggalBerangkat = date("Y-m-d", strtotime(!empty($dateawal)?$dateawal:date('Y-m-d')));

        $filterData = [
			'start_point' => $kotaBerangkat,
			'end_point'   => $kotaTujuan,
			'dateawal'     => $dateawal,
			'date'        => $tanggalBerangkat,
			'fleet_type'  => $kelas,
			'unit_type'  => $unit_type,
        ];
        
        $result = $this->tripModel->getTripList($filterData)->getResult();

        if (empty($result)) {
            return $this->failNotFound('Data Not Found');
        } 

        foreach ($result as $key => $value) {
            $checkSeat = $this->tripModel->checkSeatAvail($value->trip_id_no, $tanggalBerangkat, $value->type)->getResult();
            $value->seatPicked = strval(COUNT($checkSeat)); 
            $value->seatAvail = intval($value->fleet_seats) - intval(COUNT($checkSeat)); 
            if ($tanggalBerangkat < date("Y-m-d", strtotime('2022-07-29'))) {
                if ((intval($value->trip_id_no) == 28 || intval($value->trip_id_no) == 29) && $dateawal > date("Y-m-d", strtotime('2022-07-27'))) {
                } else {
                    $value->seatAvail = 0; 
                }
            }
            $spday = explode(',', $value->sp_day);
            for ($i=0; $i < count($spday); $i++) { 
                if ($spday[$i] == $dayArray[$dayforday]) {
                    $value->price = strval($value->sp_price);
                    $i = count($spday);
                }
            }
            if ($value->price_ext !== null) {
                $priceextnom = intval($value->price) * (intval($value->price_ext) / 100);
                $value->price = strval($value->price - $priceextnom);

            }

            if ($value->image != null) {
                $value->image = getenv('ADMIN_ENDPOINT').$value->image;
            } else {
                $value->image = base_url('assets/default_bus.jpeg');
            }
        }
        
        return $this->respond($result, 200);
    }

    public function seatList()
    {
        $bodyRaw = $this->request->getVar();
        $trip_route_id = isset($bodyRaw['trip_route_id']) ? $bodyRaw['trip_route_id'] : '';
        $trip_id_no = isset($bodyRaw['trip_id_no']) ? $bodyRaw['trip_id_no'] : '';
        $fleet_registration_id = isset($bodyRaw['fleet_registration_id']) ? $bodyRaw['fleet_registration_id'] : '';
        $fleet_type_id = isset($bodyRaw['fleet_type_id']) ? $bodyRaw['fleet_type_id'] : '';
        $booking_date = isset($bodyRaw['booking_date']) ? $bodyRaw['booking_date'] : '';

        #--------------------------------------------------------
        $currency = $this->tripModel->retrieve_currency()->getResult();
        $data['trip_id_no'] = $trip_id_no;
        $data['trip_route_id'] = $trip_route_id;
        $data['fleet_registration_id'] = $fleet_registration_id;
        $data['fleet_type_id'] = $fleet_type_id;
        $data['booking_date'] = $booking_date;
        // $pricess = $this->tripModel->getPrice($trip_route_id, $fleet_type_id)->getResult();
        // if (empty($pricess)) {
        //     return tripModelfailNotFound('Data Not Found');
        // } 
        $data['bankinfo'] = $this->tripModel->getBankinfo()->getResult();

        #---------BOOKED SEAT(S)-----------#
        $bookedSeats = $this->tripModel->getBookedSeats($trip_id_no, $booking_date, $fleet_type_id)->getResult();

        // return $this->respond($bookedSeats);
        
        if ($bookedSeats[0]->booked_serial != null) {
            $bookArray = explode(',', $bookedSeats[0]->booked_serial);
        } else {
            $bookArray = [];
        }

        #---------FLEET SEAT(S)-----------#
        $fleetSeats = $this->tripModel->getfleetseats($fleet_type_id)->getResult();
        if (empty($fleetSeats)) {
            return $this->failNotFound('Data Not Found');
        }
        if ($fleetSeats[0]->seat_numbers != null) {
            $seatArray = explode(',', $fleetSeats[0]->seat_numbers);
        } else {
            $seatArray = [];
        }

        $layoutset = $this->tripModel->layoutSet($fleet_type_id)->getResult();
        
        $result['seatsInfo'] = $layoutset[0];
        $result['seatsInfo']->picked = $bookArray;
        
        if ($layoutset[0]->layout == "2-2") {
            $separate = [2,6,10,14,18,22];

            foreach ($seatArray as $key => $value) {
                if (in_array(trim($value), $bookArray)) {
                    $avail = false;
                } else {
                    $avail = true;
                    if ($booking_date < date("Y-m-d", strtotime('2022-07-29'))) {
                        $avail = false;
                        if ((intval($trip_id_no) == 28 || intval($trip_id_no) == 29) && $booking_date > date("Y-m-d", strtotime('2022-07-27'))) {
                            $avail = true;
                        }
                    }
                }

                if (trim($value) == 'X') {
                    $avail = false;
                    $value = '-';
                }

                $result['seats'][] = [
                    'id' => $key+1,
                    'name' => trim($value),
                    'isAvailable' => $avail,
                    'isSeat' => true,
                ];
    
                if (in_array($key+1, $separate)) {
                    $result['seats'][] = [
                        'id' => 00,
                        'name' => '-',
                        'isAvailable' => $avail,
                        'isSeat' => false,
                    ];
                }
            }

        } elseif ($layoutset[0]->layout == "1-1") {
            $separate = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20];
            $separate_2 = [1,3,5,7,9,11,13,14,15,17,19,21];

            foreach ($seatArray as $key => $value) {
                if (trim($value) != "") {
                    if (in_array(trim($value), $bookArray)) {
                        $avail = false;
                    } else {
                        $avail = true;
                        if ($booking_date < date("Y-m-d", strtotime('2022-07-29'))) {
                            $avail = false;
                            if ((intval($trip_id_no) == 28 || intval($trip_id_no) == 29) && $booking_date > date("Y-m-d", strtotime('2022-07-27'))) {
                                $avail = true;
                            }
                        }
                    }

                    if (trim($value) == 'X') {
                        $result['seats'][] = [
                            'id' => 00,
                            'name' => '-',
                            'isAvailable' => $avail,
                            'isSeat' => false,
                        ];
                    } else {
                        $result['seats'][] = [
                            'id' => $key+1,
                            'name' => trim($value),
                            'isAvailable' => $avail,
                            'isSeat' => true,
                        ];
                    }
        
                    if (in_array($key+1, $separate)) {
                        $result['seats'][] = [
                            'id' => 00,
                            'name' => '-',
                            'isAvailable' => $avail,
                            'isSeat' => false,
                        ];
                    }
                    if (in_array($key+1, $separate_2)) {
                        $result['seats'][] = [
                            'id' => 00,
                            'name' => '-',
                            'isAvailable' => $avail,
                            'isSeat' => false,
                        ];
                    }
                }
            }
        } elseif ($layoutset[0]->layout == "1-1-1") {
            $separate = [1,2,4,5,7,8,10,11,13,14,16,17,19,20];
            $separate_2 = [3,6,12,15,18,21];

            foreach ($seatArray as $key => $value) {
                if (trim($value) != "") {
                    if (in_array(trim($value), $bookArray)) {
                        $avail = false;
                    } else {
                        $avail = true;
                        if ($booking_date < date("Y-m-d", strtotime('2022-07-29'))) {
                            $avail = false;
                            if ((intval($trip_id_no) == 28 || intval($trip_id_no) == 29) && $booking_date > date("Y-m-d", strtotime('2022-07-27'))) {
                                $avail = true;
                            }
                        }
                    }

                    if (trim($value) == 'X') {
                        $result['seats'][] = [
                            'id' => 00,
                            'name' => '-',
                            'isAvailable' => $avail,
                            'isSeat' => false,
                        ];
                    } else {
                        $result['seats'][] = [
                            'id' => $key+1,
                            'name' => trim($value),
                            'isAvailable' => $avail,
                            'isSeat' => true,
                        ];
                    }
        
                    if (in_array($key+1, $separate)) {
                        $result['seats'][] = [
                            'id' => 00,
                            'name' => '-',
                            'isAvailable' => $avail,
                            'isSeat' => false,
                        ];
                    }
                }
            }
        } else {
            $separate = [];
        }

        return $this->respond($result, 200);
    }
}
