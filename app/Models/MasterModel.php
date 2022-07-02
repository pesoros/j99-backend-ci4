<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterModel extends Model
{
    public function getCity($keyword = '')
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
    public function getLocation($cityid)
    {
        $query = $this->db->table('trip_location')
            ->select('
                id,name
            ')
            ->where('city', $cityid)
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

    public function clearTicket()
    {
        $query = $this->db->table('tkt_booking_head as a')
            ->select('
                a.booking_code
                ,b.id_no
                ,a.created_at
            ')
            ->join('tkt_booking as b','b.booking_code = a.booking_code')
            ->where('a.payment_status',0)
            ->orderBy('a.id','ASC')
            ->get();
        return $query;
    }

    public function deleteTicket($bookingCode,$id_no)
    {
        $query1 = $this->db->table('tkt_booking_head')
            ->where('booking_code', $bookingCode)
            ->delete();

        $query2 = $this->db->table('tkt_booking')
            ->where('booking_code', $bookingCode)
            ->delete();

        $query3 = $this->db->table('tkt_passenger_pcs')
            ->where('booking_id', $id_no)
            ->delete();
    }
}
