<?php

namespace App\Pai;

use Slim\Http\Request;
use Slim\Http\Response;

class Consultar {

	public static function xss($data, $problem='') {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		$data = strip_tags($data);
		if ($problem && strlen($data) == 0){ return ($problem); }
		return $data;
	}

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

		global $app;
		// Sample log message
		$c = $app->getContainer();

		$tokenx = self::xss(apache_request_headers()["Authorization"]);

		$c->logger->addInfo("consultaPai", [
				'token' => $tokenx,
                'error' => false,
        ]);

		$doc = self::xss($args['cpf']);

		if(!preg_match("#^([0-9]){3}([0-9]){3}([0-9]){3}([0-9]){2}$#i", $doc)) {
			$dataok = ['msg' => 'doc invalido.'];
		} else {

			$url   = "http://191.96.28.230/instint/api_pai.php?cpf={$doc}";
			$dados = self::curl($url, null, null, null, false);

			if(stristr($dados, '<pai>')) {

				$res  = simplexml_load_string($dados);
		
				$c->loggerPai->addInfo("pai", [
							'token' => $tokenx,
							'error' => false,
							'dados' => json_encode($res)
				]);

				$nome = trim(rtrim(ltrim($res->nome)));
				$nasc = trim(rtrim(ltrim($res->nascimento)));
				$mae  = trim(rtrim(ltrim($res->mae)));
				$pai  = trim(rtrim(ltrim($res->pai)));
				$cpf  = trim(rtrim(ltrim($res->cpf)));

				$dataok = array(
					'cpf'  => $cpf,
					'nome' => $nome,
					'nascimento' => $nasc,
					'mae' => $mae,
					'pai' => $pai
				);
			} else {

				$url   = "http://191.96.28.230/instint/api_pai.php?cpf={$doc}";
				$dados = self::curl($url, null, null, null, false);
	
				if(stristr($dados, '<pai>')) {
					$res  = simplexml_load_string($dados);
					$c->loggerPai->addInfo("pai", [
								'token' => $tokenx,
								'error' => false,
								'dados' => json_encode($res)
					]);

					$nome = trim(rtrim(ltrim($res->nome)));
					$nasc = trim(rtrim(ltrim($res->nascimento)));
					$mae  = trim(rtrim(ltrim($res->mae)));
					$pai  = trim(rtrim(ltrim($res->pai)));
					$cpf  = trim(rtrim(ltrim($res->cpf)));

					$dataok = array(
						'cpf'  => $cpf,
						'nome' => $nome,
						'nascimento' => $nasc,
						'mae' => $mae,
						'pai' => $pai
					);
				}else{
					$dataok = ['msg' => 'nada encontrado.'];
				}
			}
		}

		$data = $dataok;
		return $response->withJson($data);
	}
}
