<?php

namespace App\Credito;

use Slim\Http\Request;
use Slim\Http\Response;

class Consultar {
	
	public static function cpf(Request $request, Response $response, $args) {
		print_r($args);
		die;
		$data = array('pagina' => 'consulta cpf');
		return $response->withJson($data);
	}
	
	public static function cnpj(Request $request, Response $response, $args) {
		print_r($args);
		die;
		$data = array('pagina' => 'consulta cpf');
		return $response->withJson($data);
	}

	public static function consumo(Request $request, Response $response, $args) {
		$r = $request->getHeader('Authorization')[0];
		print_r($r);
		die;
		$headers = $request->getHeaders();
		print_r($headers);
		die;
		$data = array('pagina' => 'consumo');
		return $response->withJson($data);
	}
}
