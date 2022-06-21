<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketModel extends Model
{
    public function getBook($packetCode)
    {
        $query = $this->db->table('tkt_booking_head')
            ->where('booking_code',$packetCode)
            ->get();
        return $query;
    }

    public function detailBook($code)
    {
        $query = $this->db->table('tkt_booking')
            ->where('booking_code',$code)
            ->orderBy('id','DESC')
            ->get();
        return $query;
    }

    public function getTicket($code, $type = null)
    {
        if ($type == 'book') {
            $wherefield = 'tbook.booking_code';
        } else {
            $wherefield = 'tps.ticket_number';
        }
        $query = $this->db->table('tkt_passenger_pcs AS tps')
            ->select("
                tbook.booking_code,
                tps.name,
                tps.phone,
                tps.ticket_number,
                ft.type,
                tps.seat_number,
                tbook.pickup_trip_location,
                tbook.drop_trip_location,
                tbook.booking_date,
                tps.baggage,
                IF(tps.baggage = 1, 'Bawa', 'Tidak Bawa') as baggage,
                resto.food_name,
                tbook.price,
                trip.trip_id,
            ")
            ->join('tkt_booking AS tbook', 'tps.booking_id = tbook.id_no')
            ->join('trip', 'tbook.trip_id_no = trip.trip_id')
            ->join('fleet_type AS ft', 'tps.fleet_type = ft.id')
            ->join('resto_menu AS resto', 'tps.food = resto.id')
            ->where($wherefield,$code)
            ->get();
        return $query;
    }

    public function getHour($trip,$pickup,$drop)
    {
        $query = $this->db->table('trip_point')
            ->select("
                trip_point.dep_time,
                trip_point.arr_time
            ")
            ->join('trip_assign', 'trip_assign.id = trip_point.trip_assign_id')
            ->join('trip', 'trip.trip_id = trip_assign.trip')
            ->where('trip_point.dep_point',$pickup)
            ->where('trip_point.arr_point',$drop)
            ->where('trip.trip_id',$trip)
            ->get();
        return $query;
    }


    public function getPaymentRegis($code)
    {
        $query = $this->db->table('payment_registration')
            ->select('
                payment_method,
                payment_channel_code,
                va_number,
                dekstop_link,
                mobile_link
            ')
            ->where('booking_code',$code)
            ->get();
        return $query;
    }
}
