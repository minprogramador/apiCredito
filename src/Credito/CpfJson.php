<?php

namespace App\Credito;

use Slim\Http\Request;
use Slim\Http\Response;

class CpfJson {


	public static function clean_string($value) {
		if(is_array($value)){
			return $value;
		}
		$clear = str_replace(["\n",'  ','	', "\t", "\r"], '', $value);
		$clear = trim(rtrim($clear));
		$clear = str_replace('</td>', '', $clear);
		return $clear;
	}

	public static function corta($str, $left, $right) {
		$str = substr ( stristr ( $str, $left ), strlen ( $left ) );
		$leftLen = strlen ( stristr ( $str, $right ) );
		$leftLen = $leftLen ? - ($leftLen) : strlen ( $str );
		$str = substr ( $str, 0, $leftLen );
		return $str;
	}


	public static function run($res) {

		$cpf = self::corta($res,'CPF:','</tbody>');
		$cpf = explode('<br/>', $cpf);
		$cpf = strip_tags(trim(rtrim($cpf[1])));
		$cpf = self::clean_string($cpf);
		
		/* INICIO - BLOCO PAINEL DE CONTROLE */
		$protestos = self::corta($res, 'Protestos', '</tr>');
		$protestos = explode('<td>', $protestos);
		$protestosQtde = $protestos[1];
		$protestosData = $protestos[2];
		$protestosValor = strip_tags(trim(rtrim($protestos[3])));

		
		$registroDebito = self::corta($res, '<a href="#09r" class="link_gold_p">', '</tr>');
		$registroDebito = explode('<td>', $registroDebito);
		$registroDebitoQtde = $registroDebito[1];
		$registroDebitoData = $registroDebito[2];
		$registroDebitoValor = strip_tags(trim(rtrim($registroDebito[3])));

		
		$chequeSemFundo = self::corta($res, 'Cheques sem Fundo', '</tr>');
		$chequeSemFundo = explode('<td>', $chequeSemFundo);
		$chequeSemFundoQtde = strip_tags(trim(rtrim($chequeSemFundo[1])));
		$chequeSemFundoData = $chequeSemFundo[2];
		$chequeSemFundoValor = $chequeSemFundo[3];

		
		$rfaj = self::corta($res, '<a href="#14" class="link_gold_p">', '</tr>');
		$rfaj = explode('<td>', $rfaj);
		$rfajQtde = $rfaj[1];
		$rfajData = $rfaj[2];
		$rfajValor = $rfaj[3];

		
		$acoes = self::corta($res, '<a href="#15" class="link_gold_p">', '</tr>');
		$acoes = explode('<td>', $acoes);
		$acoesQtde = $acoes[1];
		$acoesData = $acoes[2];
		$acoesValor = strip_tags(trim(rtrim($acoes[3])));

		
		$participacoes = self::corta($res, '<a href="#05" class="link_gold_p">', '</tr>');
		$participacoes = explode('<td>', $participacoes);
		$participacoesQtde = $participacoes[1];
		$participacoesData = $participacoes[2];
		$participacoesValor = $participacoes[3];
		/* FIM - BLOCO PAINEL DE CONTROLE */

		
		/* INICIO SCORE */
		$score_credito = self::corta($res, '<div align="center" class="subtitlexlarge" style="color:#CC0000; font-family:Arial,Verdana,Helvetica,sans-serif; font-size:28px; font-weight:bold;">', '</div>');
		$score_credito = strip_tags(trim(rtrim(self::clean_string($score_credito))));
		$score = self::corta($res, '<table width="100%" height="66" border="0" cellpadding="3" cellspacing="1" class="tbScore">', '<!-- ## END: SCORE CREDITO ## -->');
		$score = explode('<strong>', self::clean_string($score));

		if(count($score) > 1) {
			$classe_score = strip_tags($score[2]);
			$probabilidade = $score[4];
			$probabilidadeLegenda = explode('%', $probabilidade);
			$probabilidadeLegenda = $probabilidadeLegenda[0];
		}
		/* FIM SCORE */

		/* INICIO IDENTIFICACAO */
		$identificacao = self::corta($res, '<th colspan="4"><strong class="blue">IDENTIFICA', '</tbody');
		$identificacao = preg_split('/(<strong>|<br\/>|<br>)/', self::clean_string($identificacao));
		if(preg_replace('/\s/', '', strip_tags($identificacao[3])) != 'NomedaM&atilde;e') {
			array_splice($identificacao, 3, 0, "Nome da Mãe");
			array_splice($identificacao, 4, 0, "-");
		}
		$nome = strip_tags(trim(rtrim($identificacao[2])));
		$nome_mae = strip_tags(trim(rtrim($identificacao[4])));
		$situacao_cpf = strip_tags(trim(rtrim($identificacao[8])));
		$data_atualizacao = strip_tags(trim(rtrim($identificacao[10])));
		$origem = strip_tags(trim(rtrim($identificacao[12])));
		$data_nascimento = "-";
		$nacionalidade = "-";
		$sexo = "-";
		$civil = "-";
		$dependentes = "-";
		$escolaridade = "-";

		if(!isset($identificacao[13])) {
			array_splice($identificacao, 13, 0, "Data de Nascimento");
			array_splice($identificacao, 14, 0, "-");
		} else {
			$data_nascimento = strip_tags(trim(rtrim($identificacao[14])));
		}
		if(count($identificacao) > 15) {
			switch (preg_replace('/\s/', '', strip_tags($identificacao[15]))) {
				case 'Nacionalidade':
					$nacionalidade = strip_tags(trim(rtrim($identificacao[16])));
					break;
				case 'Sexo':
					$sexo = strip_tags(trim(rtrim($identificacao[16])));
					break;
				case 'EstadoCivil':
					$civil = strip_tags(trim(rtrim($identificacao[16])));
					break;
				case 'Dependentes':
					$dependentes = strip_tags(trim(rtrim($identificacao[16])));
					break;
				case 'GraudeInstru&ccedil;&atilde;o':
					$escolaridade = strip_tags(trim(rtrim($identificacao[16])));
					break;
			}
		}
		if(count($identificacao) > 17) {
			switch (preg_replace('/\s/', '', strip_tags($identificacao[17]))) {
				case 'Sexo':
					$sexo = strip_tags(trim(rtrim($identificacao[18])));
					break;
				case 'EstadoCivil':
					$civil = strip_tags(trim(rtrim($identificacao[18])));
					break;
				case 'Dependentes':
					$dependentes = strip_tags(trim(rtrim($identificacao[18])));
					break;
				case 'GraudeInstru&ccedil;&atilde;o':
					$escolaridade = strip_tags(trim(rtrim($identificacao[18])));
					break;
			}
		}
		if(count($identificacao) > 19) {
			switch (preg_replace('/\s/', '', strip_tags($identificacao[19]))) {
				case 'EstadoCivil':
					$civil = strip_tags(trim(rtrim($identificacao[20])));
					break;
				case 'Dependentes':
					$dependentes = strip_tags(trim(rtrim($identificacao[20])));
					break;
				case 'GraudeInstru&ccedil;&atilde;o':
					$escolaridade = strip_tags(trim(rtrim($identificacao[20])));
					break;
			}
		}
		if(count($identificacao) > 21) {
			switch (preg_replace('/\s/', '', strip_tags($identificacao[21]))) {
				case 'Dependentes':
					$dependentes = strip_tags(trim(rtrim($identificacao[22])));
					break;
				case 'GraudeInstru&ccedil;&atilde;o':
					$escolaridade = strip_tags(trim(rtrim($identificacao[22])));
					break;
			}
		}
		/* FIM IDENTIFICACAO */

		/* INICIO LOCALIZACAO */
		$localizacao = self::corta($res, '<th colspan="3"><strong class="blue">LOCALIZA', '</tbody>');
		$localizacao = preg_split('/(<strong>|<br\/>)/', self::clean_string($localizacao));
		$localizacao = self::clean_string($localizacao);
		if(count($localizacao) > 1) {
			$endereco = strip_tags(trim(rtrim($localizacao[2])));
			$bairro = strip_tags(trim(rtrim($localizacao[4])));
			$cidade = strip_tags(trim(rtrim($localizacao[6])));
			$uf = strip_tags(trim(rtrim($localizacao[8])));
			$cep = strip_tags(trim(rtrim($localizacao[10])));
			$tel = Array('-', '-', '-');

			if(isset($localizacao[11]))
			{
				$j = 12;
				$i = 0;
				while ($j < count($localizacao))
				{
					$tel[$i] = strip_tags(trim(rtrim($localizacao[$j])));
					$j+=2;
					$i++;
				}
			}
		} else {
			$localizacao = 'Nada Consta';
		}
		/* FIM LOCALIZACAO */

		/* INICIO OUTRAS GRAFIAS */
		$grafias = self::corta($res, '<th colspan="5"><strong class="blue">OUTRAS GRAFIAS</strong></th>', '</tbody>');
		$grafias =  explode('<strong>Nome:</strong>', self::clean_string($grafias));
		if(count($grafias) > 1) {
			$arrGrafias = Array();
			for($i = 0; $i < count($grafias); $i++)
			{
				$arrGrafias[] = $grafias[$i];
				$arrGrafias[$i] = preg_split("/(<td|:)/", $grafias[$i]);
				if(isset($arrGrafias[$i][8]) && preg_replace('/\s+/', '', $arrGrafias[$i][8]) == 'colspan="3"width="60%"><strong>Endere&ccedil;o') {
					array_splice($arrGrafias[$i], 8, 0, "-");
				}
				if(isset($arrGrafias[$i][11]) && preg_replace('/\s+/', '', $arrGrafias[$i][11]) != 'colspan="2"width="40%"><strong>Bairro') {
					array_splice($arrGrafias[$i], 12, 0, "-");
				}
				if(isset($arrGrafias[$i][17]) && preg_replace('/\s+/', '', $arrGrafias[$i][17]) != '><strong>CEP') {
					array_splice($arrGrafias[$i], 18, 0, "-");
				}
				if(isset($arrGrafias[$i][19]) && preg_replace('/\s+/', '', $arrGrafias[$i][19]) == 'colspan="2"style="width') {
					$arrGrafias[$i][20] = '-';
					$arrGrafias[$i][21] = '-';
					$arrGrafias[$i][22] = '-';
				}
				if(!isset($arrGrafias[$i][20])) {
					$arrGrafias[$i][20] = '-';
					$arrGrafias[$i][21] = '-';
					$arrGrafias[$i][22] = '-';
				}
				if(preg_replace('/\s+/', '', $arrGrafias[$i][21]) == 'colspan="2">&nbsp;</td>') {
					$arrGrafias[$i][21] = '-';
				}
				if(preg_replace('/\s+/', '', $arrGrafias[$i][22]) == ">&nbsp;</td></tr><trclass='white'>") {
					$arrGrafias[$i][22] = '-';
				}
			}
		}
		/* FIM OUTRAS GRAFIAS */

		/* INICIO PARTICIPACAO EM EMPRESAS */
		$blocoParticipacao = self::corta($res, '<th colspan="2"><strong class="blue">PARTICIPA', '</tbody>');
		$blocoParticipacao = explode('CNPJ', self::clean_string($blocoParticipacao));
		$resultParticipacao = Array();
		if(isset($blocoParticipacao[1]) && preg_replace('/\s/', '', strip_tags($blocoParticipacao[1])) == 'NadaConsta') {
			$resultParticipacao = 'Nada Consta';
		} else {
			for($i = 0; $i < count($blocoParticipacao); $i++) {
				$resultParticipacao[] = $blocoParticipacao[$i];
				$resultParticipacao[$i] = preg_split('/(<strong>|:|<\/td><td style="width|50%;">|<\/td><\/tr>|<\/strong>)/', preg_replace('#<a.*?>(.*?)</a>#i', '\1', $blocoParticipacao[$i]));
			}
		}
		/* FIM PARTICIPACAO EM EMPRESAS */

		/* INICIO DEBITOS */
		$debitos = self::corta($res, 'Dt. Ocorr', '</tbody>');
		$debitos = preg_split('/(<tr class="white" id="trRegDebito">|<tr class="blue" id="trRegDebito">|<tr class=\'white\' id=\'trRegDebito\' >|<tr class=\'blue\' id=\'trRegDebito\' >)/', self::clean_string($debitos));
		$valorTotalC = 0;
		$valorTotalA = 0;
		$totalRegistrosComprador = 0;
		$totalRegistrosAvalista = 0;
		$arrDebitos = Array();
		for($i = 0; $i < count($debitos); $i++)
		{
			$arrDebitos[] = $debitos[$i];
			$arrDebitos[$i] = preg_split('/(<td>|<strong>|<div align="left">|<div align="center">|<td align="center">|<div align=\'left\'>|<div align=\'center\'>|<td align=\'center\')/', $debitos[$i]);

			unset($arrDebitos[$i][0]);
			if(isset($arrDebitos[$i][6])) {
				switch (preg_replace('/\s/', '', strip_tags($arrDebitos[$i][6]))) {
					case 'C':
						$valorTotalC += $arrDebitos[$i][7];
						$totalRegistrosComprador++;
						break;
					case 'A':
						$valorTotalA += $arrDebitos[$i][7];
						$totalRegistrosAvalista++;
						break;
				}
			}
		}
		unset($arrDebitos[0]);
		if(count($arrDebitos) == 0) {
			$arrDebitos = 'Nada Consta';
		}
		/* FIM DEBITOS */

		/* INICIO CHEQUES SEM FUNDO */
		$semFundo = self::corta($res, 'Motivo', '</tbody>');
		$semFundo = explode('<tr', self::clean_string($semFundo));
		$arrSemFundo = Array();
		$totalCheques = 0;

		for($i = 1; $i < count($semFundo); $i++)
		{
			$arrSemFundo[] = $semFundo[$i];
			$arrSemFundo[$i] = explode('<td>', $semFundo[$i]);
			unset($arrSemFundo[$i][0]);
			$totalCheques += $arrSemFundo[$i][1];
		}
		unset($arrSemFundo[0]);
		if(count($arrSemFundo) == 0) {
			$arrSemFundo = 'Nada Consta';
		}
		/* FIM CHEQUES SEM FUNDO */

		/* INICIO PROTESTOS */
		$blocoProtestos = self::corta($res, '<th colspan="4"><strong class="blue">PROTESTOS</strong></th>', '</tbody>');
		$blocoProtestos = explode('<tr', self::clean_string($blocoProtestos));
		$arrProtestos = Array();
		$valorProtestos = 0;
		for($i = 1; $i < count($blocoProtestos); $i++)
		{
			$arrProtestos[] = $blocoProtestos[$i];
			$arrProtestos[$i] = preg_split('/(<td align="right">|<td>)/', $blocoProtestos[$i]);
			unset($arrProtestos[$i][0]);
			if(isset($arrProtestos[$i][5]))
				$valorProtestos += $arrProtestos[$i][5];
		}
		unset($arrProtestos[0]);
		if(count($arrProtestos) == 1) {
			$arrProtestos = 'Nada Consta';
		}
		// $totalProtestos = count($arrProtestos);
		/* FIM PROTESTOS */

		/* INICIO RECUPERACOES, FALENCIAS E ACOES JUDICIAIS */
		$blocoRFAJ = self::corta($res, '<th colspan="4"><strong class="blue">RECUPERA', '</tbody>');
		$blocoRFAJ = explode('<td colspan="4" align="center">', self::clean_string($blocoRFAJ));
		/* FIM RECUPERACOES, FALENCIAS E ACOES JUDICIAIS */

		/* ACOES CIVEIS */
		$blocoAC = self::corta($res, 'VEIS</strong></th>', '</tbody>');
		$blocoAC = preg_split('/(<tr)/', self::clean_string($blocoAC));
		$arrAcoes = Array();
		$valorAcoes = 0;
		$qtdAcoes = 0;
		for($i = 1; $i < count($blocoAC); $i++)
		{
			$arrAcoes[] = $blocoAC[$i];
			$arrAcoes[$i] = preg_split('/(<td colspan="3">|<td colspan="2">)/', $blocoAC[$i]);
			// unset($arrAcoes[$i][0]);
			// if(isset($arrAcoes[$i][5]))
			// 	$valorAcoes += $arrAcoes[$i][5];
		}
		unset($arrAcoes[0]);
		if(isset($arrAcoes[5])) {
			if(strip_tags($arrAcoes[5][2]) == 'Quantidade') {
				for($i = 6; $i < count($blocoAC); $i++) {
					$qtdAcoes += $arrAcoes[$i][2];
				}
			} else {
				for($i = 5; $i < count($blocoAC); $i++) {
					$qtdAcoes += $arrAcoes[$i][2];
				}
			}
			$valorAcoes = explode('(R$):', $arrAcoes[1][0]);
			$valorAcoes = strip_tags($valorAcoes[1]);
		}
		if(count($arrAcoes) == 1) {
			$arrAcoes = 'Nada Consta';
		}
		// $totalAcoes = count($arrAcoes);
		/* FIM ACOES CIVEIS */

		/* INICIO OUTRAS INFORMACOES */
		$blocoOI = self::corta($res, '<th><strong class="blue">OUTRAS INFORMA', '</tbody>');
		$blocoOI = explode('<td align="center">', self::clean_string($blocoOI));
		/* FIM OUTRAS INFORMACOES */

		if(strlen($nome) > 3)
		{
			$identificacao = [
				'nome' => $nome,
				'mae' => $nome_mae,
				'nascimento' => $data_nascimento,
				'dependentes' => $dependentes,
				'nacionalidade' => $nacionalidade,
				'sexo' => $sexo,
				'civil' => $civil,
				'escolaridade' => $escolaridade,
				'cpf' => $cpf,
				'situacao_cpf' => $situacao_cpf,
				'data_atualizacao' => $data_atualizacao,
				'origem' => $origem
			];

			$localizacao = [
				'logradouro' => $endereco,
				'bairro' => $bairro,
				'cidade' => $cidade,
				'uf' => $uf,
				'cep' => $cep
			];

			$tel0 = explode('&nbsp;', $tel[0]);

			$telefones = [];
			$telefones[] = [
				'ddd' => $tel0[0],
				'numero' => $tel0[1]
			];
			$tel1 = explode('&nbsp;', $tel[1]);
			$telefones[] = [
				'ddd' => $tel1[0],
				'numero' => $tel1[1]
			];
			$tel2 = explode('&nbsp;', $tel[2]);
			$telefones[] = [
				'ddd' => $tel2[0],
				'numero' => $tel2[1]
			];

			$grafiasok = [];

			if(count($grafias) > 1) {
				for($i = 1; $i < count($arrGrafias); $i++) {


					if($arrGrafias[$i][20] == '-' || $arrGrafias[$i][20] != '-') {
						$telgr = strip_tags($arrGrafias[$i][20]);
					} elseif($arrGrafias[$i][20] != '-' && $arrGrafias[$i][21] != '-') {
						$telgr = strip_tags($arrGrafias[$i][20]).'&nbsp;'.strip_tags($arrGrafias[$i][21]);
					} else {
						$telgr = strip_tags($arrGrafias[$i][20]).'&nbsp;'.strip_tags($arrGrafias[$i][21]).'&nbsp;'.strip_tags($arrGrafias[$i][22]);
					}

					$grafiasok[] = [
						'nome' => strip_tags($arrGrafias[$i][0]),
						'cpf' => strip_tags($arrGrafias[$i][3]),
						'nascimento' => strip_tags($arrGrafias[$i][8]),
						'telefone' => $telgr,
						'enderecos' => [
							'logradouro' => strip_tags($arrGrafias[$i][10]),
							'bairro' => strip_tags($arrGrafias[$i][12]),
							'cidade' => strip_tags($arrGrafias[$i][14]),
							'uf' => strip_tags($arrGrafias[$i][16]),
							'cep' => strip_tags($arrGrafias[$i][18])

						]
					];
				}

			}



			$painelControle = [
				'protestos' => [
					'qnt' => self::clean_string($protestosQtde),
					'data'  => self::clean_string($protestosData),
					'valor' => self::clean_string($protestosValor)
				],
				'debitos' => [
					'qnt' => self::clean_string($registroDebitoQtde),
					'data' => self::clean_string($registroDebitoData),
					'valor' => self::clean_string($registroDebitoValor)
				],
				'chequeSemFundo' => [
					'qnt' => self::clean_string($chequeSemFundoQtde),
					'data' => self::clean_string($chequeSemFundoData),
					'valor' => self::clean_string($chequeSemFundoValor)
				],
				'recFalenciaAcoesJud' => [
					'qnt' => self::clean_string($rfajQtde),
					'data' => self::clean_string($rfajData),
					'valor' => self::clean_string($rfajValor)
				],
				'acoesJud' => [
					'qnt' => self::clean_string($acoesQtde),
					'data' => self::clean_string($acoesData),
					'valor' => self::clean_string($acoesValor)
				],
				'particEmpresas' => [
					'qnt' => self::clean_string($participacoesQtde),
					'data' => self::clean_string($participacoesData),
					'valor' => self::clean_string($participacoesValor)
				]
			];

			if(count($score) > 1) {
				$scoreok = [];

				if(preg_replace('/\s/', '', strip_tags($score[5])) == 'Scorenãodisponível') {
					$scoreok['score'] = strip_tags($score[5]);
					$scoreok['legenda'] = '';
				} else {
					$scoreok['score'] = $score_credito;
					$scoreok['legenda'] = 'De cada 100 pessoas classificadas nesta classe de score, é provável que <b>'.$probabilidadeLegenda.'</b> apresentem débitos no mercado nos próximos 6 meses.';
				}
			}else{
				$scoreok = [];
			}

			if(is_array($arrProtestos)) {
				$protestos = [
					'total' => $protestosQtde,
					'valorTotal' => $valorProtestos
				];

				for($i = 4; $i <= count($arrProtestos); $i++) {
					$protestos['registros'] = [
						'data' => $arrProtestos[$i][1],
						'cartorio' => $arrProtestos[$i][2],
						'cidade' => $arrProtestos[$i][3],
						'uf' => $arrProtestos[$i][4],
						'valor' => $arrProtestos[$i][5],
					];
				}
			} else {
				$protestos = [];
			}

			if(is_array($arrDebitos)) {
				$debios = [
					'totalComprador' => $totalRegistrosComprador,
					'valorTotalComprador' => $valorTotalC,
					'totalAvalista' => $totalRegistrosAvalista,
					'valorTotalAvalista' => $valorTotalA
				];

				for($i = 1; $i <= count($arrDebitos); $i++) {
					$debios['registros'] = [
						'dataOcorrencia' => $arrDebitos[$i][1],
						'informante' => $arrDebitos[$i][2],
						'contrato' => $arrDebitos[$i][3],
						'cidade' => $arrDebitos[$i][4],
						'uf' => $arrDebitos[$i][5],
						'situacao' => $arrDebitos[$i][6],
						'valor' => $arrDebitos[$i][7]
					];
						            		
				}
			} else {
				$debios = [];
			}


			$chequeSemFund = [
				'total' => $totalCheques
			];

			if(is_array($arrSemFundo)) {

				for($i = 1; $i <= count($arrSemFundo); $i++) {
					$chequeSemFund['registros'] = [
						'qnt' => $arrSemFundo[$i][1],
						'dataUltimo' => $arrSemFundo[$i][2],
						'banco' => $arrSemFundo[$i][3],
						'agencia' => $arrSemFundo[$i][4],
						'motivo' => $arrSemFundo[$i][5]
					];
						            	
				}
						            
			} else {
				$chequeSemFund['registros'] = [];
			}

			$recFalencia = $blocoRFAJ[1];



			if(count($arrAcoes) > 2) {
				$acoesCivies = [
					'total5Anos' => $qtdAcoes,
					'valorTotal' => $valorAcoes
				];

				for($i = 5; $i < count($blocoAC); $i++) {
					$acoesCivies['registros'] = [
						'tipo' => $arrAcoes[$i][1],
						'qnt' => $arrAcoes[$i][2]
					];
						            		
				}
			} else {
				$acoesCivies = [];
			}
					         
			$partcipEmpresas = [];
			if(count($resultParticipacao)>1) {

				for($i = 1; $i < count($resultParticipacao); $i++) {
					$partcipEmpresas['registros'] = [
						'cnpj' => strip_tags($resultParticipacao[$i][2]),
						'razao' => strip_tags($resultParticipacao[$i][7]),
						'tipo' => strip_tags($resultParticipacao[$i][12]),
						'participacao' => strip_tags($resultParticipacao[$i][17]),
						'entrada' => strip_tags($resultParticipacao[$i][22])
					];
						            
				}
			} else {
				$partcipEmpresas['registros'] = [];
			}

			$outrasInfos = $blocoOI[1];



			$retorno = [
				'identificacao' => $identificacao,
				'localizacao' => $localizacao,
				'telefones' => $telefones,
				'grafias' => $grafiasok,
				'painelControle' => $painelControle,
				'score' => $scoreok,
				'protestos' => $protestos,
				'debios' => $debios,
				'chequeSemFund' => $chequeSemFund,
				'recFalencia' => $recFalencia,
				'acoesCivies' => $acoesCivies,
				'partcipEmpresas' => $partcipEmpresas,
				'outrasInfos' => $outrasInfos
			];


			//erro em recFalencia, html puro ?
			//erro em outras infos...
			return $retorno;

		}

		return false;

	}


}
