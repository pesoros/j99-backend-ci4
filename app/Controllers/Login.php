<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Login extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    use ResponseTrait;
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

        $model = new UserModel();
        $user = $model->where("email", $this->request->getVar('email'))->first();
        if (!$user) {
            return $this->failNotFound('Email Not Found');
        }

        $verify = password_verify($this->request->getVar('password'), $user['password']);
        if (!$verify) {
            return $this->fail('Wrong Password');
        }

        $key = getenv('TOKEN_SECRET');
        $payload = array(
            "uid" => $user['id'],
            "email" => $user['email'],
            "firstName" => $user['first_name'],
            "lastName" => $user['last_name'],
            "address" => $user['address'],
            "phone" => $user['phone'],
            "active" => $user['active'],
            "iat" => 1356999524,
            "nbf" => 1357000000,
        );

        $token = JWT::encode($payload, $key, 'HS256');
        $decoded = JWT::decode($token, new Key($key, 'HS256'));

        $result['status'] = 'login succeed';
        $result['token'] = $token;
        $result['decoded'] = $decoded;

        return $this->respond($result);
    }
}
