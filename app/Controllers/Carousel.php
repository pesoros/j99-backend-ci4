<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\carouselModel;

class Carousel extends ResourceController
{ 
    use ResponseTrait;
    protected $carouselModel;
    public function __construct()
    {
        $this->carouselModel = new CarouselModel();
    }

    public function phone()
    {
        $result['url'] = base_url().'/';
        $result['image'] = $this->carouselModel->getCarousel()->getResult();

        return $this->respond($result, 200);
    }
}
