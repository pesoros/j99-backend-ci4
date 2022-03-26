<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketModel extends Model
{
    public function getBook($packetCode)
    {
        $query = $this->db->table('tkt_booking_head')
            ->where('booking_code',$packetCode)
            ->get();
        return $query;
    }

    public function getTicket($packetCode)
    {
        $query = $this->db->table('tkt_passenger_pcs')
            ->where('ticket_number',$packetCode)
            ->get();
        return $query;
    }
}
