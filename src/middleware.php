<?php
// Application middleware

use Slim\Middleware\TokenAuthentication;
use App\Auth;

$authenticator = function($request, TokenAuthentication $tokenAuth) use($app){
	$c = $app->getContainer();
    $token = $tokenAuth->findToken($request);

    $auth = new Auth();
	
	$res = $auth->getUserByToken($token);
	if($res === false) {
		$c->logger->error("token: $token", [
        	'error' => true,
        	'msg' => 'token invalido'
    	]);
	}

	return $res;
};

$app->add(new TokenAuthentication([
    'path' => ['/api'],
    'authenticator' => $authenticator,
    'secure' => false
]));