<?php

namespace App\Models;

use CodeIgniter\Model;

class EnquiryModel extends Model
{
    public function saveEnquiry($data)
    {
        $save = $this->db->table('enquiry')
            ->insert($data);

        return $save;
    }
}
