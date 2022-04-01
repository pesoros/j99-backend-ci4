<?php

namespace App\Models;

use CodeIgniter\Model;

class ManifestModel extends Model
{
    public function getTripDetail($trip_id_no)
    {
        $query = $this->db->table('trip_assign AS ta')
            ->select("
                trt.name as route,
                flr.reg_no,
                flr.model_no as nopol,
                flr.company as brand,
                ftp.type as class,
                rs.resto_name,
                CONCAT(empdriver.first_name,' ',empdriver.second_name) as driver,
                CONCAT(empassist1.first_name,' ',empassist1.second_name) as assistant_1,
                CONCAT(empassist2.first_name,' ',empassist2.second_name) as assistant_2,
                CONCAT(empassist3.first_name,' ',empassist3.second_name) as assistant_3,
            ")
            ->join('fleet_registration AS flr', 'ta.fleet_registration_id = flr.id')
            ->join('fleet_type AS ftp', 'flr.fleet_type_id = ftp.id')
            ->join('resto AS rs', 'ta.resto_id = rs.id')
            ->join('trip AS tr', 'ta.trip = tr.trip_id')
            ->join('trip_route AS trt', 'tr.route = trt.id')
            ->join('employee_history AS empdriver', 'ta.driver_id = empdriver.id','left')
            ->join('employee_history AS empassist1', 'ta.assistant_1 = empassist1.id','left')
            ->join('employee_history AS empassist2', 'ta.assistant_2 = empassist2.id','left')
            ->join('employee_history AS empassist3', 'ta.assistant_3 = empassist3.id','left')
            ->where('ta.trip', $trip_id_no)
            ->get();

        return $query;
    }

    public function getCheckinList($trip_id_no,$booking_date)
    {
        $query = $this->db->table('tkt_passenger_pcs AS tps')
            ->select("
                tps.name,
                tps.ticket_number,
                tps.seat_number,
                rmen.food_name,
                IF(tps.baggage = 1, 'Bawa', 'Tidak Bawa') as baggage,
                IF(cst.status_name IS NULL, 'Menunggu', cst.status_name) as checkin_status,
            ")
            ->join('tkt_booking AS tb', 'tps.booking_id = tb.id_no')
            ->join('checkin AS cn', 'tps.ticket_number = cn.ticket_number','left')
            ->join('resto_menu AS rmen', 'tps.food = rmen.id','left')
            ->join('checkin_status AS cst', 'cn.status = cst.id','left')
            ->where('tb.trip_id_no', $trip_id_no)
            ->where('DATE(tb.booking_date)', $booking_date)
            ->get();

        return $query;
    }

    public function findCheckin($ticketNumber)
    {
        $query = $this->db->table('checkin')
            ->where('ticket_number', $ticketNumber)
            ->get();

        return $query;
    }

    public function createCheckin($data)
    {
        $save = $this->db->table('checkin')
            ->insert($data);

        return $save;
    }

    public function updateCheckin($data)
    {
        $save = $this->db->table('checkin')
            ->where('ticket_number',$data['ticket_number'])
            ->update($data);

        return $save;
    }

    public function getAllowance($trip_id_no)
    {
        $query = $this->db->table('trip_assign')
            ->select('allowance')
            ->where('trip', $trip_id_no)
            ->get();

        return $query;
    }

    public function getExpensesList($trip_id_no,$booking_date)
    {
        $query = $this->db->table('trip_expenses')
            ->where('trip_id_no', $trip_id_no)
            ->where('DATE(trip_date)', $booking_date)
            ->get();

        return $query;
    }

    public function createExpense($data)
    {
        $save = $this->db->table('trip_expenses')
            ->insert($data);

        return $save;
    }

    public function getTypeFrom()
    {
        $query = $this->db->table('trip_baggage_type_from')
            ->get();

        return $query;
    }

    public function getBaggageList($trip_id_no,$booking_date)
    {
        $query = $this->db->table('trip_baggage')
            ->where('trip_id_no', $trip_id_no)
            ->where('DATE(trip_date)', $booking_date)
            ->get();

        return $query;
    }

    public function createBaggage($data)
    {
        $save = $this->db->table('trip_baggage')
            ->insert($data);

        return $save;
    }
}
