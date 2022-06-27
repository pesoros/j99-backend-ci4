<?php

namespace App\Models;

use CodeIgniter\Model;

class TripModel extends Model
{
    // public function getCity()
    // {
    //     $query = $this->db->table('wil_city')->select('*')->get();

    //     return $query;
    // }

    public function getTripList($data = array())
    {
        $kelas = $data['fleet_type'];
        $unit_type = $data['unit_type'];
        $start = $data['start_point'];
        $end = $data['end_point'];
        $date = $data['date'];
        $whereext = '';

        if ($kelas !== "") {
            $whereext .= " AND tp.id = ".$kelas;
        }

        if ($unit_type !== "") {
            $whereext .= " AND fr.unit_id = ".$unit_type;
        }

        $whereext .= " AND citydep.name = '".$start."'";
        $whereext .= " AND cityarr.name = '".$end."'";

        $query = $this->db->query("SELECT
            ta.trip_id AS trip_id_no,
            ta.route AS trip_route_id,
            ta.shedule_id,
            tr.name AS trip_route_name,
            tp.id as type,
            tp.total_seat AS fleet_seats,
            tp.type AS class,
            tp.image,
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
            tprs.price as normal_price,
            tprs.price as price,
            tprs.sp_price as sp_price,
            citydep.name as citydep,
            cityarr.name as cityarr,
            trext.price as price_ext,
            tras.sp_day
            FROM trip_point_price AS tprs
            INNER JOIN trip_point AS tpoint ON tpoint.id = tprs.point_id
            INNER JOIN trip_assign AS tras ON tras.id = tpoint.trip_assign_id
            INNER JOIN trip AS ta ON tras.trip = ta.trip_id
            LEFT JOIN shedule ON shedule.shedule_id = ta.shedule_id
            LEFT JOIN trip_route AS tr ON tr.id = ta.route
            LEFT JOIN fleet_type AS tp ON tp.id = tprs.type
            LEFT JOIN trip_price_ext AS trext ON trext.assign_id = tras.id AND trext.date = '$date' AND trext.type = tprs.type 
            LEFT JOIN fleet_registration AS fr ON fr.id = tras.fleet_registration_id
            LEFT JOIN trip_location AS tl1 ON tl1.name = tpoint.dep_point
            LEFT JOIN trip_location AS tl2 ON tl2.name = tpoint.arr_point
            LEFT JOIN wil_city AS citydep ON tl1.city = citydep.id 
            LEFT JOIN wil_city AS cityarr ON tl2.city = cityarr.id 
            WHERE tras.status == 1
            $whereext 
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

    public function getBookedSeats($trip_id_no, $booking_date, $fleet_type_id)
    {
        $query = $this->db->table('tkt_booking AS tb')
            ->select("
                tb.trip_id_no,
                SUM(tb.total_seat) AS booked_seats,
                GROUP_CONCAT(tb.seat_numbers SEPARATOR ',') AS booked_serial
            ")
            ->where('tb.trip_id_no', $trip_id_no)
            ->where('tb.fleet_type', $fleet_type_id)
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
