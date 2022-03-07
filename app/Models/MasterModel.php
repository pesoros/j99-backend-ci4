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
        $unit[] = [
            'id' => '1',
            'unit' => 'bus'
        ];
        $unit[] = [
            'id' => '2',
            'unit' => 'shuttle'
        ];

        return $unit;
    }

    public function getPaymentMethod()
    {
        $unit[] = [
            'bankCode' => 'mandiri',
            'bankName' => 'Mandiri'
        ];
        $unit[] = [
            'bankCode' => 'bca',
            'bankName' => 'BCA'
        ];
        $unit[] = [
            'bankCode' => 'bri',
            'bankName' => 'BRI'
        ];
        $unit[] = [
            'bankCode' => 'bni',
            'bankName' => 'BNI'
        ];

        return $unit;
    }
}
