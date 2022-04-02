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
        $tanggalBerangkat = isset($bodyRaw['tanggal']) ? $bodyRaw['tanggal'] : '';
        $kotaBerangkat = isset($bodyRaw['berangkat']) ? $bodyRaw['berangkat'] : '';
        $kotaTujuan = isset($bodyRaw['tujuan']) ? $bodyRaw['tujuan'] : '';

        if (empty($tanggalBerangkat)) {
            return $this->failNotFound('Data Not Found');
        } 

        $tanggalBerangkat = date("Y-m-d", strtotime(!empty($tanggalBerangkat)?$tanggalBerangkat:date('Y-m-d')));

        $filterData = [
			'start_point' => $kotaBerangkat,
			'end_point'   => $kotaTujuan,
			'date'        => $tanggalBerangkat,
			'fleet_type'  => $kelas,
			'unit_type'  => $unit_type,
        ];
        $result = $this->tripModel->getTripList($filterData)->getResult();

        if (empty($result)) {
            return $this->failNotFound('Data Not Found');
        } 

        foreach ($result as $key => $value) {
            $checkSeat = $this->tripModel->checkSeatAvail($value->trip_id_no, $tanggalBerangkat)->getResult();
            $value->seatPicked = $checkSeat[0]->picked; 
            $value->seatAvail = intval($value->fleet_seats) - intval($checkSeat[0]->picked); 
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
        $pricess = $this->tripModel->getPrice($trip_route_id, $fleet_type_id)->getResult();
        if (empty($pricess)) {
            return tripModelfailNotFound('Data Not Found');
        } 
        $data['bankinfo'] = $this->tripModel->getBankinfo()->getResult();

        #---------BOOKED SEAT(S)-----------#
        $bookedSeats = $this->tripModel->getBookedSeats($trip_id_no, $booking_date)->getResult();

        // return $this->respond($bookedSeats);
        
        if ($bookedSeats[0]->booked_serial != null) {
            $bookArray = explode(',', $bookedSeats[0]->booked_serial);
        } else {
            $bookArray = [];
        }

        #---------FLEET SEAT(S)-----------#
        $fleetSeats = $this->tripModel->getfleetseats($fleet_type_id)->getResult();

        if ($fleetSeats[0]->seat_numbers != null) {
            $seatArray = explode(',', $fleetSeats[0]->seat_numbers);
        } else {
            $seatArray = [];
        }

        $layoutset = $this->tripModel->layoutSet($fleet_type_id)->getResult();
        
        $result['seatsInfo'] = $layoutset[0];
        $result['seatsInfo']->picked = $bookArray;
        
        if ($layoutset[0]->layout == "2-2") {
            $separate = [2,6,10,14,18];
        } else {
            $separate = [];
        }

        foreach ($seatArray as $key => $value) {
            if (in_array(trim($value), $bookArray)) {
                $avail = false;
            } else {
                $avail = true;
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

        return $this->respond($result, 200);
    }
}
