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

    public function detailBook($code)
    {
        $query = $this->db->table('tkt_booking')
            ->where('booking_code',$code)
            ->orderBy('id','DESC')
            ->get();
        return $query;
    }

    public function updatePassword($email,$data)
    {
        $update = $this->db->table('users_client')
            ->where('email',$email)
            ->update($data);

        return $update;
    }

    public function getregis($email)
    {
        $query = $this->db->table('users_client')
            ->where('email',$email)
            ->get();
        return $query;
    }

    public function createReset($data)
    {
        $save = $this->db->table('password_reset')
            ->insert($data);

        return $save;
    }

    public function getResetToken($token)
    {
        $query = $this->db->table('password_reset')
            ->where('token',$token)
            ->orderBy('id','DESC')
            ->get();
        return $query;
    }

    public function statusReset($email,$data)
    {
        $update = $this->db->table('password_reset')
            ->where('email',$email)
            ->update($data);

        return $update;
    }

    public function deleteAccount($email)
    {
        $delete = $this->db->table('users_client')
            ->where('email',$email)
            ->delete();

        return $delete;
    }
}
