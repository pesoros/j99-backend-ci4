<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;

class Register extends ResourceController
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
            'email' => 'required|valid_email|is_unique[users_client.email]',
            'password' => 'required|min_length[6]',
            'firstName' => 'required',
            'lastName' => 'required',
            'address' => 'required',
            'phone' => 'required',
        ];

        $identity = $this->request->getRawInput()->identity ? $this->request->getRawInput()->identity : '';
        $identityNumber = $this->request->getRawInput()->identityNumber ? $this->request->getRawInput()->identityNumber : '';

        // if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());
        $data = [
            'email'     => $this->request->getRawInput()->email,
            'password'  => password_hash($this->request->getRawInput()->password, PASSWORD_BCRYPT),
            'first_name'     => $this->request->getRawInput()->firstName,
            'last_name'     => $this->request->getRawInput()->lastName,
            'address'     => $this->request->getRawInput()->address,
            'phone'     => $this->request->getRawInput()->phone,
            'identity'     => $identity,
            'identity_number'     => $identityNumber,
        ];
        $model = new UserModel();
        $registered = $model->save($data);
        $created = $this->respondCreated($registered);
        if ($created == true) {
            $result['messages'] = 'registration succeed';
        } else {
            $result['messages'] = 'registration failed';
        }
 
        return $this->respond($result);
    }
}
