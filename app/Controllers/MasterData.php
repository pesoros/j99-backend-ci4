<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class MasterData extends ResourceController
{
    use ResponseTrait;

    public function dataKota()
    {
        $q = $this->request->getVar("q");

        $data[] = [
            'id' => '1',
            'namaKota' => 'Jakarta',
        ];
        $data[] = [
            'id' => '2',
            'namaKota' => 'Surabaya',
        ];
        $data[] = [
            'id' => '3',
            'namaKota' => 'Malang',
        ];
        $data[] = [
            'id' => '4',
            'namaKota' => 'DI Yogyakarta',
        ];
        $data[] = [
            'id' => '5',
            'namaKota' => 'Bandung',
        ];
        $data[] = [
            'id' => '6',
            'namaKota' => 'Tangerang',
        ];
        $data[] = [
            'id' => '7',
            'namaKota' => 'Bali',
        ];

        if ($q) {
            $data = $this->searchArray($data, 'namaKota', $q);
        }

        return $this->respond($data);
    }

    public function searchArray($array, $field, $q)
    {
        $result = [];
        foreach ($array as $key => $val) {
            $leftstr = strtolower($val[$field]);
            $rightstr = strtolower($q);
            if (strpos($leftstr, $rightstr) !== FALSE) {
                $result[] = $val;
            }
        }
        return $result;
    }
}
