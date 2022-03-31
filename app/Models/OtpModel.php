<?php

namespace App\Models;

use CodeIgniter\Model;

class OtpModel extends Model
{
    public function getOtp($phone,$dateNow)
    {
        $query = $this->db->table('otp')
            ->where('phone',$phone)
            ->where('status',1)
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
