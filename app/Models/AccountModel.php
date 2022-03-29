<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountModel extends Model
{
    public function getProfile($email)
    {
        $query = $this->db->table('users_client')
            ->select('
                email,
                first_name,
                last_name,
                address,
                phone,
                identity,
                identity_number
            ')
            ->where('email',$email)
            ->where('active',1)
            ->get();
        return $query;
    }

    public function updateProfile($email,$data)
    {
        $update = $this->db->table('users_client')
            ->where('email',$email)
            ->update($data);

        return $update;
    }

    public function historyTicket($email)
    {
        $query = $this->db->table('tkt_booking_head')
            ->where('booker',$email)
            ->get();
        return $query;
    }
}