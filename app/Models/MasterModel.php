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

    public function getFleetType($keyword = '')
    {
        $query = $this->db->table('fleet_type')
            ->select('id, type as kelas')
            ->like('type', $keyword, 'both')
            ->get();
        return $query;
    }

    public function getUnit()
    {
        $query = $this->db->table('unit')
            ->where('status',1)
            ->get();
        return $query;
    }
    
    public function getResto()
    {
        $query = $this->db->table('resto')
            ->where('status',1)
            ->get();
        return $query;
    }

    public function getRestoMenu($idResto)
    {
        $query = $this->db->table('resto_menu')
            ->where('id_resto', $idResto)
            ->where('status',1)
            ->get();
        return $query;
    }

    public function getCheckinStatus()
    {
        $query = $this->db->table('checkin_status')
            ->get();
        return $query;
    }
}
