<?php

namespace App;

class Auth
{
    public function getUsers() {
        $users = [];
        $users[] = [
            'id' => 1,
            'token' => '4739b3c713dc50a48f6911180a669969',
            'name' => 'api demo',
            'permisssion' => ['credito'],
            'status' => true
        ];
        $users[] = [
            'id' => 2,
            'token' => 'db783c1f1834f5b1d28d540fef1f9712',
            'name' => 'mister',
            'permisssion' => ['pai'],
            'status' => true
        ];

        return $users;
    }

    public function getUserByToken($token) {
        $check = false;
        $user = null;
        if(strlen($token) == 32) {
            $users = self::getUsers();
            foreach($users as $u){
                if($u['token'] == $token) {
                    $check = true;
                    $user = $u;
                    break;
                }
            }
        }

        if($check == true) {
            return $user;
        }else{
            return false;
        }
    }
}
