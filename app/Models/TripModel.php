<?php

namespace App\Models;

use CodeIgniter\Model;

class TripModel extends Model
{
    public function getTripList($data = array())
    {
        $kelas = $data['fleet_type'];
        $unit_type = $data['unit_type'];
        $start = $data['start_point'];
        $end = $data['end_point'];
        $date = $data['date'];
        $whereext = '';

        if ($kelas !== "") {
            $whereext .= " AND ta.type = ".$kelas;
        }

        if ($unit_type !== "") {
            $whereext .= " AND fr.unit_id = ".$unit_type;
        }

        $whereext .= " AND tpoint.dep_point = '".$start."'";
        $whereext .= " AND tpoint.arr_point = '".$end."'";

        $query = $this->db->query("SELECT
            ta.trip_id AS trip_id_no,
            ta.route AS trip_route_id,
            ta.shedule_id,
            tr.name AS trip_route_name,
            ta.type,
            tp.total_seat AS fleet_seats,
            tp.type AS class,
            fr.reg_no AS fleet_registration_id,
            fr.unit_id AS unit_type,
            tr.approximate_time AS duration,
            tr.stoppage_points,
            tr.distance,
            tr.pickup_points,
            tr.dropoff_points,
            tras.closed_by_id,
            tras.resto_id,
            tl1.name AS pickup_trip_location,
            tl2.name AS drop_trip_location,
            tpoint.dep_time as start,
            tpoint.arr_time as end,
            tpoint.price as price
            FROM trip AS ta
            LEFT JOIN shedule ON shedule.shedule_id = ta.shedule_id
            LEFT JOIN trip_route AS tr ON tr.id = ta.route
            LEFT JOIN trip_assign AS tras ON tras.trip = ta.trip_id
            LEFT JOIN fleet_type AS tp ON tp.id = ta.type
            LEFT JOIN fleet_registration AS fr ON fr.fleet_type_id = tp.id
            LEFT JOIN trip_location AS tl1 ON tl1.name = '$start' 
            LEFT JOIN trip_location AS tl2 ON tl2.name = '$end' 
            LEFT JOIN trip_location_pool AS tpool1 ON tl1.id = tpool1.location_id 
            LEFT JOIN trip_location_pool AS tpool2 ON tl2.id = tpool2.location_id 
            LEFT JOIN trip_point AS tpoint ON tras.id = tpoint.trip_assign_id
            WHERE (FIND_IN_SET('$start',tr.pickup_points))
            AND (FIND_IN_SET('$end',tr.dropoff_points))
            AND (!FIND_IN_SET(DAYOFWEEK('$date'),ta.weekend)) 
            $whereext 
            GROUP BY ta.trip_id
        ");

        return $query;
    }

    public function checkSeatAvail($trip_id_no, $date)
    {
        $bookingResult = $this->db->table("tkt_booking AS tb")
            ->select("SUM(tb.total_seat) AS picked")
            ->join('trip AS ta', "ta.trip_id = tb.trip_id_no")
            ->where('tb.trip_id_no', $trip_id_no)
            ->like('tb.booking_date', $date, 'after')
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

    public function getPrice($trip_route_id, $fleet_type_id)
    {
        $query = $this->db->table('pri_price')
            ->select('*')
            ->where('route_id', $trip_route_id)
            ->where(' vehicle_type_id', $fleet_type_id)
            ->get();

        return $query;
    }

    public function getBankInfo()
    {
        $query = $this->db->table('bank_info')->select('*')->get();

        return $query;
    }

    public function getBookedSeats($trip_id_no, $booking_date)
    {
        $query = $this->db->table('tkt_booking AS tb')
            ->select("
                tb.trip_id_no,
                SUM(tb.total_seat) AS booked_seats,
                GROUP_CONCAT(tb.seat_numbers SEPARATOR ', ') AS booked_serial
            ")
            ->where('tb.trip_id_no', $trip_id_no)
            ->like('tb.booking_date', $booking_date, 'after')
            ->groupStart()
            ->where("tb.tkt_refund_id IS NULL", null, false)
            ->orWhere("tb.tkt_refund_id", 0)
            ->orWhere("tb.tkt_refund_id", null)
            ->groupEnd()
            ->get();

        return $query;
    }

    public function getfleetseats($fleet_type_id)
    {
        $query = $this->db->table("fleet_type")
            ->select("
                total_seat,
                seat_numbers,
                fleet_facilities
            ")
            ->where('id', $fleet_type_id)
            ->get();

        return $query;
    }

    public function layoutSet($fleet_type_id)
    {
        $query = $this->db->table("fleet_type")
            ->select("*")
            ->where('id', $fleet_type_id)
            ->get();

        return $query;
    }
}
