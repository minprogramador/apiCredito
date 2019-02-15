<?php

namespace App;

use Slim\Http\Request;
use Slim\Http\Response;

class App {
	
	public static function index(Request $request, Response $response) {
		$data = array('pagina' => 'index');
		return $response->withJson($data);
	}

	
}
