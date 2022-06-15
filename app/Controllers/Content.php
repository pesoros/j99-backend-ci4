<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\MasterModel;
use App\Models\ContentModel;

class Content extends ResourceController
{
    use ResponseTrait;
    protected $contentModel;
    public function __construct()
    {
        $this->contentModel = new ContentModel();
    }

    public function disclaimer()
    {
        $result = $this->contentModel->getDisclaimer()->getRow();
        return $this->respond($result, 200);
    }

}
