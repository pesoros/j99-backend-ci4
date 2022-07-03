<?php

namespace App\Models;

use CodeIgniter\Model;

class CallbackModel extends Model
{
    public function savePayment($data)
    {
        $save = $this->db->table('payment_receive')
            ->insert($data);

        return $save;
    }

    public function updateStatusPayment($booking_code,$status)
    {
        $data['payment_status'] = $status;
        $update = $this->db->table('tkt_booking_head')
            ->where('booking_code',$booking_code)
            ->update($data);

        return $update;
    }

    public function getBooking($booking_code)
    {
        $booking = $this->db->table('tkt_booking_head')
            ->where('booking_code',$booking_code)
            ->get();

        return $booking;
    }
}
