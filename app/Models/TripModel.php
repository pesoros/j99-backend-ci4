<?php

namespace App\Models;

use CodeIgniter\Model;

class TripModel extends Model
{
    public function getTripList($data = array())
    {
        $f = $data['fleet_type'];
        $start = $data['start_point'];
        $end = $data['end_point'];
        $date = $data['date'];

        $query = $this->db->query("SELECT
            ta.trip_id AS trip_id_no,
            ta.route AS trip_route_id,
            ta.shedule_id,
            tr.name AS trip_route_name,
            tl1.name AS pickup_trip_location,
            tl2.name AS drop_trip_location,
            ta.type,
            tp.total_seat AS fleet_seats,
            pp.price AS price,
            pp.children_price,
            pp.special_price,
            tr.approximate_time AS duration,
            tr.stoppage_points,
            tr.distance,
            shedule.start,
            shedule.end,
            tras.closed_by_id
            FROM trip AS ta
            LEFT JOIN shedule ON shedule.shedule_id = ta.shedule_id
            LEFT JOIN trip_route AS tr ON tr.id = ta.route
            LEFT JOIN trip_assign AS tras ON tras.trip = ta.trip_id
            LEFT JOIN fleet_type AS tp ON tp.id = ta.type
            LEFT JOIN pri_price AS pp ON pp.route_id = ta.route AND pp.vehicle_type_id= ta.type
            LEFT JOIN trip_location AS tl1 ON tl1.id = tr.start_point
            LEFT JOIN trip_location AS tl2 ON tl2.id = tr.end_point
            WHERE (FIND_IN_SET('$start',tr.stoppage_points))
            AND (FIND_IN_SET('$end',tr.stoppage_points))
            AND ta.type = $f
            AND (!FIND_IN_SET(DAYOFWEEK('$date'),ta.weekend))
            GROUP BY ta.trip_id
        ");

        return $query;
    }

    public function checkSeatAvail($trip_id_no,$date)
    {
        $bookingResult = $this->db->table("tkt_booking AS tb")
            ->select("SUM(tb.total_seat) AS picked")
            ->join('trip AS ta', "ta.trip_id = tb.trip_id_no")
            ->where('tb.trip_id_no', $trip_id_no)
            ->like('tb.booking_date',$date,'after')
            ->groupStart()
                ->where("tb.tkt_refund_id IS NULL", null, false)
                ->orWhere("tb.tkt_refund_id", 0)
                ->orWhere("tb.tkt_refund_id", null)
            ->groupEnd()
            ->get();
            
        return $bookingResult;
    }

    public function retrieve_currency()
    {
        $query = $this->db->query('SELECT * FROM ws_setting');
        
        if ($query->getNumRows() > 0) {
            return $query;  
        }
        return false;
    }
}
