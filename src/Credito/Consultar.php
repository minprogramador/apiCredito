<?php

namespace App\Credito;

use Slim\Http\Request;
use Slim\Http\Response;

class Consultar {
	
	public static function curl($url, $cookies, $post, $referer=null, $header=true, $proxy=null) {
		$user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:54.0) Gecko/20100101 Firefox/54.0';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, $header);
		if(strlen($cookies) > 5) {
			curl_setopt($ch, CURLOPT_COOKIE, $cookies);
		}
		curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);

		if(isset($referer)){ curl_setopt($ch, CURLOPT_REFERER,$referer); }
		else{ curl_setopt($ch, CURLOPT_REFERER,$url); }
		if ($post){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
		}
		
		if($proxy != null) {
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
		}

		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
		$res = curl_exec( $ch);
		curl_close($ch); 
		return $res;
	}

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

		$url = 'http://181.215.238.197/getInfo.php?token=bfc4abb1449d4d2d50e691f46a0aa916';

		$dados = self::curl($url, null, null, null, false);
		
		if(stristr($dados, '{"info":')) {
			$dados = json_decode($dados, true);
		}else{
			$dados = ['msg' => 'indisponivel no momento.'];
		}
		
		$data = $dados;
		return $response->withJson($data);
	}
}
