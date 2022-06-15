<?php

namespace App\Models;

use CodeIgniter\Model;

class ContentModel extends Model
{
    public function getDisclaimer()
    {
        $query = $this->db->table('web_disclaimer')
            ->select('*')
            ->get();
        return $query;
    }
}
