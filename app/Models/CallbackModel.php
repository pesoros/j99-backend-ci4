<?php

namespace App\Models;

use CodeIgniter\Model;

class CallbackModel extends Model
{
    public function savePayment($data)
    {
        $save = $this->db->table('payment_receive')
            ->insert($data);

        $updatPaymentstatus = $this->updateStatusPayment($data['external_id'],1);

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
}
