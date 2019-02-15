<?php

namespace App;

class Auth
{
    public function getUserByToken($token) {

        if ($token != '4739b3c713dc50a48f6911180a669969') {
            return false;
        }

        $user = [
            'name' => 'api demo',
            'id' => 1,
            'permisssion' => 'credito'
        ];

        return $user;
    }
}
