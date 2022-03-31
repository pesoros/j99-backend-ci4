<?php

namespace App\Models;

use CodeIgniter\Model;

class ManifestModel extends Model
{
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
}
