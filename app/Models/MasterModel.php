<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterModel extends Model
{
    public function getLocation($keyword = '')
    {
        $query = $this->db->table('trip_location as tl')
            ->select('
                city.id, city.name as namaKota
            ')
            ->join('wil_city as city','tl.city = city.id')
            ->like('city.name', $keyword, 'both')
            ->groupBy('city.name')
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

    public function getRestoMenu($idResto,$class)
    {
        if ($class != '') {
            $query = $this->db->table('resto_menu')
            ->where('id_resto', $idResto)
            ->where('class', $class)
            ->where('status',1)
            ->get();
        } else {
            $query = $this->db->table('resto_menu')
            ->where('id_resto', $idResto)
            ->where('status',1)
            ->get();
        }
        return $query;
    }

    public function getCheckinStatus()
    {
        $query = $this->db->table('checkin_status')
            ->get();
        return $query;
    }

    public function getregis($email)
    {
        $query = $this->db->table('users_client')
            ->where('email',$email)
            ->get();
        return $query;
    }
}
