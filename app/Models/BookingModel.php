<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    public function createBooking($data)
    {
        $save = $this->db->table('tkt_booking_head')
            ->insert($data);

        return $save;
    }

    public function paymentRegistration($data)
    {
        $save = $this->db->table('payment_registration')
            ->insert($data);

        return $save;
    }

    public function createGroup($data)
    {
        $save = $this->db->table('ws_booking_history')
            ->insert($data);

        return $save;
    }

    public function createTktBooking($data)
    {
        $save = $this->db->table('tkt_booking')
            ->insert($data);

        return $save;
    }

    public function createTicket($data)
    {
        $save = $this->db->table('tkt_passenger_pcs')
            ->insert($data);

        return $save;
    }

    public function getTripRoute($trip_route_id)
    {
        $query = $this->db->table('trip_route')->select('*')->where('id', $trip_route_id)->get();

        return $query;
    }

    public function getTicketBooking($trip_id_no, $booking_date)
    {
        $query = $this->db->table('tkt_booking AS tb')
            ->select("
                count(tb.child) AS tchild,
                count(tb.special) AS tspecial
            ")
            ->where('tb.trip_id_no', $trip_id_no)
            ->like('tb.booking_date', $booking_date, 'after')
            ->get();

        return $query;
    }

    public function getBookingHistory($id_no)
    {
        $query = $this->db->table('ws_booking_history AS bh')
            ->select("
                bh.*,
                tr.name AS route_name,
                DATE_FORMAT(bh.booking_date, '%m/%d/%Y %h:%i %p') AS booking_date
            ")
            ->join('trip_route AS tr', 'tr.id = bh.trip_route_id')
            ->where('id_no', $id_no)
            ->get();

        return $query;
    }

    public function checkBooking($fleetId)
    {
        $query = $this->db->table("fleet_type")
            ->select("
                total_seat, seat_numbers,fleet_facilities
            ")
            ->where('id', $fleetId)
            ->get();

        return $query;
    }

    public function getBookedSeat($tripIdNo, $booking_date)
    {
        $query = $this->db->table('tkt_booking AS tb')
            ->select("
                tb.trip_id_no,
                SUM(tb.total_seat) AS booked_seats,
                GROUP_CONCAT(tb.seat_numbers SEPARATOR ', ') AS booked_serial
            ")
            ->where('tb.trip_id_no', $tripIdNo)
            ->like('tb.booking_date', $booking_date, 'after')
            ->groupStart()
            ->where("tb.tkt_refund_id IS NULL", null, false)
            ->orWhere("tb.tkt_refund_id", 0)
            ->orWhere("tb.tkt_refund_id", null)
            ->groupEnd()
            ->get();

        return $query;
    }

    public function getWsSetting($id = null)
    {
        if ($id) {
            $query = $this->db->table('ws_setting')->select('*')->where('id', $id)->get();
        } else {
            $query = $this->db->table('ws_setting')->select('*')->get();
        }

        return $query;
    }

    public function getPriPrice($trip_route_id)
    {
        $query = $this->db->table('pri_price')->select('*')->where('route_id', $trip_route_id)->get();

        return $query;
    }
}
