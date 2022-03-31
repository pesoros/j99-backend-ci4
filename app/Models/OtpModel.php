<?php

namespace App\Models;

use CodeIgniter\Model;

class OtpModel extends Model
{
    public function getOtpPhone($phone,$dateNow)
    {
        $query = $this->db->table('otp')
            ->where('phone',$phone)
            ->where('status',1)
            ->orderBy('id','DESC')
            ->get();
        return $query;
    }

    public function getOtpMail($email,$dateNow)
    {
        $query = $this->db->table('otp')
            ->where('email',$email)
            ->where('status',1)
            ->orderBy('id','DESC')
            ->get();
        return $query;
    }

    public function createOtp($data)
    {
        $save = $this->db->table('otp')
            ->insert($data);

        return $save;
    }
}
