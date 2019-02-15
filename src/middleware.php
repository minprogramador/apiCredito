<?php
// Application middleware

use Slim\Middleware\TokenAuthentication;
use App\Auth;

$authenticator = function($request, TokenAuthentication $tokenAuth){

    $token = $tokenAuth->findToken($request);

    $auth = new Auth();
	
	return $auth->getUserByToken($token);
};

$app->add(new TokenAuthentication([
    'path' => ['/api'],
    'authenticator' => $authenticator
]));