<?php
// Application middleware

use Slim\Middleware\TokenAuthentication;
use App\Auth;

$authenticator = function($request, TokenAuthentication $tokenAuth) use($app){

    $c = $app->getContainer();
    $token = $tokenAuth->findToken($request);

    $auth = new Auth();
    
    $res = $auth->getUserByToken($token);


    $uri = $request->getUri();//this works
    $url = $uri->getPath();//this works

    if(stristr($url, '/api/pai/')) {
        $servico = 'pai';
    }elseif(stristr($url, '/api/credito/')) {
        $servico = 'credito';
    }

    if (!in_array($servico, $res['permisssion'])) { 
        $res = false;
    }

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