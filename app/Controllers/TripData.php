<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\MasterModel;
use App\Models\TripModel;

class TripData extends ResourceController
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
        $bodyRaw = $this->request->getRawInput();
        $unitType = isset($bodyRaw['unitType']) ? $bodyRaw['unitType'] : '';
        $kelas = isset($bodyRaw['kelas']) ? $bodyRaw['kelas'] : '';
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
			'fleet_type'  => $kelas
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
        $bodyRaw = $this->request->getRawInput();
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
        $pricess = $this->db->table('pri_price')->select('*')->where('route_id', $trip_route_id)->where(' vehicle_type_id', $fleet_type_id)->get()->getResult();
        if (empty($pricess)) {
            return $this->failNotFound('Data Not Found');
        } 
        // $data['child_pric'] = 'Children - ' . $pricess[0]['children_price'] . $currency[0]['currency'] . ', Adult -' . $pricess[0]['price'] . $currency[0]['currency'] . ', Special-' . $pricess[0]['special_price'] . $currency[0]['currency'];
        // return $this->respond($data);
        $data['bankinfo'] = $this->db->table('bank_info')->select('*')->get()->getResult();

        #---------BOOKED SEAT(S)-----------#
        $bookedSeats = $this->db->table('tkt_booking AS tb')
        ->select("
                tb.trip_id_no,
                SUM(tb.total_seat) AS booked_seats,
                GROUP_CONCAT(tb.seat_numbers SEPARATOR ', ') AS booked_serial
            ")
            ->where('tb.trip_id_no', $trip_id_no)
            ->like('tb.booking_date', $booking_date, 'after')
            ->groupStart()
            ->where("tb.tkt_refund_id IS NULL", null, false)
            ->orWhere("tb.tkt_refund_id", 0)
            ->orWhere("tb.tkt_refund_id", null)
            ->groupEnd()
            ->get()
            ->getResult();
        
        $bookArray = explode(',', $bookedSeats[0]->booked_serial);

        #---------FLEET SEAT(S)-----------#
        $fleetSeats = $this->db->table("fleet_type")
            ->select("
                total_seat, seat_numbers,fleet_facilities
            ")
            ->where('id', $fleet_type_id)
            ->get()
            ->getResult();
        $seatArray = explode(',', $fleetSeats[0]->seat_numbers);

        $layoutset = $this->db->table("fleet_type")
            ->select("*")
            ->where('id', $fleet_type_id)
            ->get()
            ->getResult();
            
        $result['seatsInfo'] = $layoutset[0];
        
        if ($layoutset[0]->layout == "2-2") {
            $separate = [2,6,10,14,18];
        } else {
            $separate = [];
        }

        foreach ($seatArray as $key => $value) {
            $result['seats'][] = [
                'row' => $key,
                'id' => $key+1,
                'name' => 'A'.$key+1,
                'isAvailable' => true,
                'isSeat' => true,
            ];

            if (in_array($key+1, $separate)) {
                $result['seats'][] = [
                    'row' => 00,
                    'id' => 00,
                    'name' => '-',
                    'isAvailable' => false,
                    'isSeat' => false,
                ];
            }
        }

        // $rowSeat = 1;
        // $totalSeats = 1;
        // $lastSeats = ((sizeof($seatArray) >= 3) ? (sizeof($seatArray) - 5) : sizeof($seatArray));

        return $this->respond($result, 200);
    }
}
