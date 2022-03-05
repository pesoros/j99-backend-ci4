<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterModel extends Model
{
    public function getLocation($keyword = '')
    {
        $query = $this->db->table('trip_location')
            ->select('id, name as namaKota')
            ->like('name', $keyword, 'both')
            ->get();
        return $query;
    }
}
