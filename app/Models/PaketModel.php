<?php

namespace App\Models;

use CodeIgniter\Model;

class PaketModel extends Model
{
    public function getPacket($packetCode)
    {
        $query = $this->db->table('packet')
            ->where('packet_code',$packetCode)
            ->get();
        return $query;
    }

    public function getPool($id)
    {
        $query = $this->db->table('trip_location')
            ->where('id',$id)
            ->get();
        return $query;
    }

    public function getTrace($id)
    {
        $query = $this->db->table('packet_trace')
            ->where('packet_id',$id)
            ->get();
        return $query;
    }
}
