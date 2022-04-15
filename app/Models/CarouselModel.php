<?php

namespace App\Models;

use CodeIgniter\Model;

class CarouselModel extends Model
{
    public function getCarousel($keyword = '')
    {
        $query = $this->db->table('web_carousel')
            ->select('
                image
            ')
            ->where('place','PHONE_CAROUSEL')
            ->where('status',1)
            ->orderBy('sequence','ASC')
            ->get();
        return $query;
    }
}
