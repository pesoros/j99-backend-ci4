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

        $bodyRaw = $this->request->getRawInput();

        $identity = isset($bodyRaw['identity']) ? $bodyRaw['identity'] : '';
        $identityNumber = isset($bodyRaw['identityNumber']) ? $bodyRaw['identityNumber'] : '';

        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());
        $data = [
            'email'     => $bodyRaw['email'],
            'password'  => password_hash($bodyRaw['password'], PASSWORD_BCRYPT),
            'first_name'     => $bodyRaw['firstName'],
            'last_name'     => $bodyRaw['lastName'],
            'address'     => $bodyRaw['address'],
            'phone'     => $bodyRaw['phone'],
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
