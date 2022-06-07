<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserManifestModel;
use App\Models\ManifestModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class LoginManifest extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    use ResponseTrait;
    protected $manifestModel;
    public function __construct()
    {
        $this->manifestModel = new ManifestModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        helper(['form']);
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $model = new UserManifestModel();
        $user = $model->where("email", $this->request->getVar('email'))->first();
        if (!$user) {
            return $this->failNotFound('Email Not Found');
        }

        $verify = password_verify($this->request->getVar('password'), $user['password']);
        // $verify = md5($this->request->getVar('password')) == $user['password'];
        if (!$verify) {
            return $this->fail('Wrong Password');
        }

        $manifest = $this->manifestModel->getManifest($this->request->getVar('email'))->getRow();

        $key = getenv('TOKEN_SECRET');
        $payload = array(
            "uid" => $user['id'],
            "email" => $user['email'],
            "firstName" => $user['firstname'],
            "lastName" => $user['lastname'],
            "iat" => 1356999524,
            "nbf" => 1357000000,
        );

        $token = JWT::encode($payload, $key, 'HS256');
        $decoded = JWT::decode($token, new Key($key, 'HS256'));

        $result['status'] = 'login succeed';
        $result['token'] = $token;
        $result['uid'] = $decoded->uid;
        $result['email'] = $decoded->email;
        $result['firstName'] = $decoded->firstName;
        $result['lastName'] = $decoded->lastName;
        $result['iat'] = $decoded->iat;
        $result['nbf'] = $decoded->nbf;
        $result['manifest'] = $manifest;

        return $this->respond($result);
    }
}
