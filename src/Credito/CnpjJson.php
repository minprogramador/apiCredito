<?php

namespace App\Credito;

use Slim\Http\Request;
use Slim\Http\Response;

class CnpjJson {


	public static function clean_string($value)
	{
		return str_replace(array("\n",'  ','	'), '', $value);
	}
	
	public static function clean($value) {
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
		
		$cnpj = self::corta($res,'CNPJ:','</tbody>');
		$cnpj = explode('<br/>', $cnpj);
		if(count($cnpj) > 1) {
			$cnpj = strip_tags(trim(rtrim($cnpj[1])));
			
			/* INICIO IDENTIFICACAO */
			$identificacao = self::corta($res, '<th colspan="5"><strong class="blue">IDENTIFICA', '</tbody');
			$identificacao = preg_split('/(<strong>|<br\/>|<br>)/', self::clean_string($identificacao));
			$razao = '-';
			$nire = '-';
			$fantasia = '-';
			$razao_anterior = '-';
			$data = '-';
			$fundacao = '-';
			$encerramento = '-';
			$inscr = '-';
			$situacao_cnpj = '-';
			$dt_cnpj = '-';
			$situacao_sintegra = '-';
			$dt_sintegra = '-';
			$natureza = '-';
			$faixa_func = '-';
			$filiais = '-';
			$cidades = '-';
			
			foreach($identificacao as $key => $item) {
				if(strpos($item, 'o Social')) {
					$razao = strip_tags(trim(rtrim(self::corta(self::clean_string($res), 'o Social ', '</td>'))));
				}
				if(strpos($item, 'Nire')) {
					$nire = strip_tags(trim(rtrim(self::corta(self::clean_string($res), 'Nire', '</td>'))));
				}
				if(strpos($item, 'Nome Fantasia')) {
					$fantasia = strip_tags(trim(rtrim(self::corta(self::clean_string($res), 'Nome Fantasia ', '</td>'))));
				}
				if(strpos($item, 'o Anterior ')) {
					$razao_anterior = strip_tags(trim(rtrim(self::corta(self::clean_string($res), 'Raz&atilde;o Anterior ', '</td>'))));
				}
				if(strpos($item, 'at&eacute;')) {
					$data = strip_tags(trim(rtrim(self::corta(self::clean_string($res), 'at&eacute;', '</td>'))));
					$data = 'at&eacute; '.$data;
				}
				if(strpos($item, 'Data de Funda')) {
					$fundacao = strip_tags(trim(rtrim(self::corta(self::clean_string($res), 'Data de Funda&ccedil;&atilde;o ', '</td>'))));
				}
				if(strpos($item, 'Data de Encerramento')) {
					$encerramento = strip_tags(trim(rtrim(self::corta(self::clean_string($res), 'Data de Encerramento', '</td>'))));
				}
				if(strpos($item, 'Inscr. Est.')) {
					$inscr = strip_tags(trim(rtrim(self::corta(self::clean_string($res), 'Inscr. Est.', '</td>'))));
				}
				if(strpos($item, 'o do CNPJ ')) {
					$situacao_cnpj = strip_tags(trim(rtrim(self::corta(self::clean_string($res), 'Situa&ccedil;&atilde;o do CNPJ ', '</tr>'))));
					$datas_cnpj = preg_split('/(Data|Consultado em)/', $situacao_cnpj);
					
					if(stristr($situacao_cnpj, 'Data') && stristr($situacao_cnpj, 'Consultado em')) {
						$dt_cnpj = $datas_cnpj[1];
						$consultado_cnpj = $datas_cnpj[2];
					} elseif (!stristr($situacao_cnpj, 'Data')) {
						$consultado_cnpj = $datas_cnpj[1];
					}
					
					$situacao_cnpj = $datas_cnpj[0];
				}
				if(strpos($item, 'o SINTEGRA ')) {
					$situacao_sintegra = strip_tags(trim(rtrim(self::corta(self::clean_string($res), 'Situa&ccedil;&atilde;o SINTEGRA ', '</tr>'))));
					$datas_sintegra = preg_split('/(Data|Consultado em)/', $situacao_sintegra);
					
					if(stristr($situacao_sintegra, 'Data') && stristr($situacao_sintegra, 'Consultado em')) {
						$dt_sintegra = $datas_sintegra[1];
						$consultado_sintegra = $datas_sintegra[2];
					} elseif (!stristr($situacao_sintegra, 'Data')) {
						$consultado_sintegra = $datas_sintegra[1];
					}
					
					$situacao_sintegra = $datas_sintegra[0];
				}
				if(strpos($item, 'Natureza Jur')) {
					$natureza = strip_tags(trim(rtrim(self::corta(self::clean_string($res), 'Natureza Jur&iacute;dica ', '</td>'))));
				}
				if(strpos($item, 'Faixa de Funcion')) {
					$faixa_func = strip_tags(trim(rtrim(self::corta(self::clean_string($res), 'Faixa de Funcion&aacute;rios ', '</td>'))));
				}
				if(strpos($item, 'Filiais')) {
					$filiais = strip_tags(trim(rtrim(self::corta(self::clean_string($res), 'Filiais', '</td>'))));
				}
				if(strpos($item, 'Ramo de Atividade Prim')) {
					$ativ_prim = strip_tags(trim(rtrim(self::corta(self::clean_string($res), 'Ramo de Atividade Prim&aacute;rio', '</td>'))));
					$ativ_prim = explode('CNAE', $ativ_prim);
					$ativ_prim = $ativ_prim[1];
				}
				if(strpos($item, 'Ramo de Atividade secund')) {
					$ativ_sec = strip_tags(trim(rtrim(self::corta(self::clean_string($res), 'Ramo de Atividade secund&aacute;rio', '</td>'))));
					if(stristr($ativ_sec, 'CNAE')) {
						$ativ_sec = explode('CNAE', $ativ_sec);
						$ativ_sec = $ativ_sec[1];
					} else {
						$ativ_sec = '-';
					}
				}
				if(strpos($item, 'Cidades')) {
					$cidades = strip_tags(trim(rtrim(self::corta(self::clean_string($res), 'Cidades', '</td>'))));
				}
			}
			
			/* FIM IDENTIFICACAO */
			
			/* INICIO - BLOCO PAINEL DE CONTROLE */
			$titulos = self::corta($res, '<a href="#titulos0">', '</tr>');
			$titulos = explode('<td>', $titulos);
			$titulosQtde = $titulos[1];
			$titulosData = $titulos[2];
			$titulosValor = strip_tags(trim(rtrim($titulos[3])));
			
			$comportamento = self::corta($res, '<a href="#pagamento0">', '</tr>');
			$comportamento = explode('<td>', $comportamento);
			$comportamentoQtde = $comportamento[1];
			$comportamentoData = $comportamento[2];
			$comportamentoValor = strip_tags(trim(rtrim($comportamento[3])));
			
			$pendencias = self::corta($res, '<a href="#pendencias0"> ', '</tr>');
			$pendencias = explode('<td>', $pendencias);
			$pendenciasQtde = $pendencias[1];
			$pendenciasData = $pendencias[2];
			$pendenciasValor = strip_tags(trim(rtrim($pendencias[3])));
			
			$sustados = self::corta($res, '<strong>Cheques Sustados - Motivo 21</strong>', '</tr>');
			$sustados = explode('<td>', $sustados);
			$sustadosQtde = $sustados[1];
			$sustadosData = $sustados[2];
			$sustadosValor = strip_tags(trim(rtrim($sustados[3])));
			
			$chequeSemFundo = self::corta($res, 'Cheques sem Fundo', '</tr>');
			$chequeSemFundo = explode('<td>', $chequeSemFundo);
			$chequeSemFundoQtde = strip_tags(trim(rtrim($chequeSemFundo[1])));
			$chequeSemFundoData = $chequeSemFundo[2];
			$chequeSemFundoValor = $chequeSemFundo[3];
			
			$protestos = self::corta($res, 'Protestos', '</tr>');
			$protestos = explode('<td>', $protestos);
			$protestosQtde = $protestos[1];
			$protestosData = $protestos[2];
			$protestosValor = strip_tags(trim(rtrim($protestos[3])));
			
			$recuperacao = self::corta($res, '<a href="#recuperacao0"> ', '</tr>');
			$recuperacao = explode('<td>', $recuperacao);
			$recuperacaoQtde = $recuperacao[1];
			$recuperacaoData = $recuperacao[2];
			$recuperacaoValor = strip_tags(trim(rtrim($recuperacao[3])));
			
			$acoes = self::corta($res, 'es Judiciais', '</tr>');
			$acoes = explode('<td>', $acoes);
			$acoesQtde = $acoes[1];
			$acoesData = $acoes[2];
			$acoesValor = strip_tags(trim(rtrim($acoes[3])));
			
			$socios = self::corta($res, '<a href="#socios0"', '</tr>');
			$socios = explode('<td>', $socios);
			$sociosQtde = $socios[1];
			$sociosData = $socios[2];
			$sociosValor = strip_tags(trim(rtrim($socios[3])));
			
			$admin = self::corta($res, '<strong>Administradores</strong>', '</tr>');
			$admin = explode('<td>', $admin);
			$adminQtde = $admin[1];
			$adminData = $admin[2];
			$adminValor = strip_tags(trim(rtrim($admin[3])));
			
			$participacoes = self::corta($res, 'es em Empresas</strong>', '</tr>');
			$participacoes = explode('<td>', $participacoes);
			$participacoesQtde = $participacoes[1];
			$participacoesData = $participacoes[2];
			$participacoesValor = $participacoes[3];
			/* FIM - BLOCO PAINEL DE CONTROLE */
			
			/* INICIO LOCALIZACAO */
			$localizacao = self::corta($res, 'LOCALIZA', '</tbody>');
			$localizacao = preg_split('/(<strong>|<br\/>)/', self::clean_string($localizacao));
			$localizacao = self::clean_string($localizacao);
			if(count($localizacao) > 1) {
				$endereco = strip_tags(trim(rtrim($localizacao[10])));
				$bairro = strip_tags(trim(rtrim($localizacao[12])));
				$cidade = strip_tags(trim(rtrim($localizacao[14])));
				$cep = strip_tags(trim(rtrim($localizacao[16])));
				$tel = '-';
				if(isset($localizacao[17]) && stristr($localizacao[17], 'Telefone')) {
					$tel = strip_tags(trim(rtrim($localizacao[18])));
				}
			} else {
				$localizacao = 'Nada Consta';
			}
			/* FIM LOCALIZACAO */
			
			/* INICIO SCORE EMPRESARIAL */
			if(isset($_POST['scoreEmp'])) {
				$score_emp = self::corta($res, '<!-- ## SCORE EMPRESARIAL ## -->', '<!-- ## END: SCORE EMPRESARIAL ## -->');
				$score_emp = explode('</table>', self::clean_string($score_emp));
				array_walk_recursive($score_emp, function(&$value, $key){ $value = strip_tags(trim(rtrim($value))); });
				$num_scoreEmp = preg_split('/(&nbsp;)/', $score_emp[0]);
				$classe_scoreEmp = preg_split('/(Score|Probabilidade)/', $score_emp[2]);
				$prob_scoreEmp = $classe_scoreEmp[3];
				$classe_scoreEmp = $classe_scoreEmp[1];
				
				if(isset($score_emp[4]) && stristr($score_emp[4], 'De cada 100')) {
					$texto_scoreEmp = $score_emp[4];
				} else {
					$texto_scoreEmp = $score_emp[3];
				}
			}
			/* FIM SCORE EMPRESARIAL */
			
			/* INICIO SCORE EMPRESARIAL */
			if(isset($_POST['scoreAta'])) {
				$score_ata = self::corta($res, '<!-- ## SCORE ATACADISTA ## -->', '<!-- ## END: SCORE ATACADISTA ## -->');
				$score_ata = explode('</table>', self::clean_string($score_ata));
				array_walk_recursive($score_ata,  function(&$value, $key){ $value = strip_tags(trim(rtrim($value))); });
				$num_scoreAta = preg_split('/(&nbsp;)/', $score_ata[0]);
				$classe_scoreAta = preg_split('/(Score|Probabilidade)/', $score_ata[2]);
				$prob_scoreAta = $classe_scoreAta[3];
				$classe_scoreAta = $classe_scoreAta[1];
				
				if(isset($score_ata[4]) && stristr($score_ata[4], 'De cada 100')) {
					$texto_scoreAta = $score_ata[4];
				} else {
					$texto_scoreAta = $score_ata[3];
				}
			}
			/* FIM SCORE ATACADISTA */
			
			/* INICIO FATURAMENTO */
			if(isset($_POST['faturamento'])) {
				$blocoFaturamento = self::corta($res, '<th colspan="2"><strong class="blue">FATURAMENTO PRESUMIDO</strong></th>', '</tbody>');
				$blocoFaturamento = explode('</td>', self::clean_string($blocoFaturamento));
				array_walk_recursive($blocoFaturamento,  function(&$value, $key){ $value = strip_tags(trim(rtrim($value))); });
				
				if(stristr($blocoFaturamento[0], 'calculado')) {
					$texto_faturamento = $blocoFaturamento[0];
					$faturamento_nao_calculado = true;
				} else {
					$faixaFaturamento = explode('Faixa', $blocoFaturamento[0]);
					$faixaFaturamento = $faixaFaturamento[1];
					$faturamentoAnual = explode('(Em Reais)', $blocoFaturamento[1]);
					$faturamentoAnual = $faturamentoAnual[1];
					$texto_faturamento = $blocoFaturamento[3];
					$faturamento_nao_calculado = false;
				}
			}
			/* FIM FATURAMENTO */
			
			/* INICIO TITULOS */
			$blocoTitulos = self::corta($res, 'TULOS A VENCER</strong></th>', '</tbody>');
			$blocoTitulos = explode('</tr>', self::clean_string($blocoTitulos));
			$arrTitulos = Array();
			for($i = 1; $i < count($blocoTitulos); $i++)
			{
				$arrTitulos[] = $blocoTitulos[$i];
				$arrTitulos[$i] = explode('</td>', $blocoTitulos[$i]);
			}
			array_walk_recursive($arrTitulos,  function(&$value, $key){ $value = strip_tags(trim(rtrim($value))); });
			/* FIM TITULOS */
			
			/* INICIO COMPORTAMENTO */
			$blocoComportamento = self::corta($res, '<th colspan="11"><strong class="blue">COMPORTAMENTO DE PAGAMENTOS</strong></th>', '</tbody>');
			$blocoComportamento = explode('</tr>', self::clean_string($blocoComportamento));
			$arrComportamento = Array();
			for($i = 1; $i < count($blocoComportamento); $i++)
			{
				$arrComportamento[] = $blocoComportamento[$i];
				$arrComportamento[$i] = explode('</td>', $blocoComportamento[$i]);
			}
			array_walk_recursive($arrComportamento,  function(&$value, $key){ $value = strip_tags(trim(rtrim($value))); });
			/* FIM COMPORTAMENTO */
			
			/* INICIO PENDENCIAS */
			$blocoPendencias = self::corta($res, '<!-- ## PENDENCIAS E RESTRIÇÕES ## -->', '<table width="100%" height="3"  border="0" cellpadding="0" cellspacing="0">');
			$blocoPendencias = explode('<tr', self::clean_string($blocoPendencias));
			$arrPendencias = Array();
			for($i = 1; $i < count($blocoPendencias); $i++)
			{
				$arrPendencias[] = $blocoPendencias[$i];
				$arrPendencias[$i] = preg_split('/(<td class="">|<td>|<td class="blue">)/', $blocoPendencias[$i]);
			}
			if(count($arrPendencias) == 1) {
				$arrPendencias = 'Nada Consta';
			}
			$totalPendencias = self::corta($arrPendencias[2][0], 'ncias:', ' </strong>');
			$totalCredores = self::corta($arrPendencias[2][0], 'Total de Credores:', '</strong>');
			$valorPendencias = self::corta($arrPendencias[2][0], '(R$):', '</strong>');
			/* FIM PENDENCIAS */
			
			/* INICIO SUSTADOS */
			$blocoSustados = self::corta($res, '<th colspan="5"><strong class="blue">CHEQUES SUSTADOS MOTIVO 21</strong></th>', '</tbody>');
			$blocoSustados = explode('<tr', self::clean_string($blocoSustados));
			$arrSustados = Array();
			for($i = 1; $i < count($blocoSustados); $i++)
			{
				$arrSustados[] = $blocoSustados[$i];
				$arrSustados[$i] = explode('</td>', $blocoSustados[$i]);
			}
			array_walk_recursive($arrSustados,  function(&$value, $key){ $value = strip_tags(trim(rtrim($value))); });
			if(!stristr($arrSustados[0], 'Nada Consta.')) {
				$totalCheques = explode(':', $arrSustados[0]);
				$totalCheques = $totalCheques[1];
			}
			/* FIM SUSTADOS */
			
			/* INICIO SEM FUNDO */
			$blocoSemFundo = self::corta($res, '<strong class="blue">CHEQUES SEM FUNDO</strong>', '</tbody>');
			$blocoSemFundo = explode('</tr>', self::clean_string($blocoSemFundo));
			$arrSemFundo = Array();
			for($i = 1; $i < count($blocoSemFundo); $i++)
			{
				$arrSemFundo[] = $blocoSemFundo[$i];
				$arrSemFundo[$i] = explode('</td>', $blocoSemFundo[$i]);
			}
			array_walk_recursive($arrSemFundo,  function(&$value, $key){ $value = strip_tags(trim(rtrim($value))); });
			if(!stristr($arrSemFundo[0], 'Nada Consta.')) {
				$totalCheques = explode(':', $arrSemFundo[0]);
				$totalCheques = $totalCheques[1];
			}
			/* FIM SEM FUNDO */
			
			/* INICIO PROTESTOS */
			$blocoProtestos = self::corta($res, '<th colspan="6"><strong class="blue">PROTESTOS</strong></th>', '</tbody>');
			$blocoProtestos = explode('<tr', self::clean_string($blocoProtestos));
			$arrProtestos = Array();
			$valorProtestos = 0;
			for($i = 1; $i < count($blocoProtestos); $i++)
			{
				$arrProtestos[] = $blocoProtestos[$i];
				$arrProtestos[$i] = preg_split('/(<div align="center">|<\/td>)/', $blocoProtestos[$i]);
				unset($arrProtestos[$i][0]);
				if(isset($arrProtestos[$i][5]))
					$valorProtestos += $arrProtestos[$i][5];
			}
			
			unset($arrProtestos[0]);
			if(count($arrProtestos) > 1) {
				array_walk_recursive($arrProtestos,  function(&$value, $key){ $value = strip_tags(trim(rtrim($value))); });
			}
			/* FIM PROTESTOS */
			
			/* INICIO RECUPERACOES, FALENCIAS E ACOES JUDICIAIS */
			$blocoRFAJ = self::corta($res, '<th colspan="4"><strong class="blue">RECUPERA', '</tbody>');
			$blocoRFAJ = explode('<tr ', self::clean_string($blocoRFAJ));
			$arrBlocoRFAJ = Array();
			
			for($i = 1; $i < count($blocoRFAJ); $i++) {
				$arrBlocoRFAJ[] = $blocoRFAJ[$i];
				$arrBlocoRFAJ[$i] = preg_split('/(<td class="">|<td class="blue">)/', $blocoRFAJ[$i]);
			}
			array_walk_recursive($arrBlocoRFAJ,  function(&$value, $key){ $value = strip_tags(trim(rtrim($value))); });
			/* FIM RECUPERACOES, FALENCIAS E ACOES JUDICIAIS */
			
			/* INICIO SOCIOS */
			$blocoSocios = self::corta($res, '<th colspan="5"><strong class="blue">S&Oacute;CIOS</strong></th>', '</tbody>');
			$blocoSocios = explode('<tr ', self::clean_string($blocoSocios));
			$arrBlocoSocios = Array();
			
			for($i = 1; $i < count($blocoSocios); $i++) {
				$arrBlocoSocios[] = $blocoSocios[$i];
				$arrBlocoSocios[$i] = preg_split('/(<td class="">|<td class="blue">)/', $blocoSocios[$i]);
			}
			array_walk_recursive($arrBlocoSocios,  function(&$value, $key){ $value = strip_tags(trim(rtrim($value))); });
			if(count($arrBlocoSocios) > 1) {
				$socioCapital = preg_split('/(l:|\|)/', $arrBlocoSocios[1][0]);
				$socioJunta = explode(':', $socioCapital[2]);
			}
			/* FIM SOCIOS */
			
			/* INICIO ADMINISTRADORES */
			$blocoAdm = self::corta($res, '<th colspan="8"><strong class="blue">ADMINISTRADORES</strong></th>', '</tbody>');
			$blocoAdm = explode('<tr ', self::clean_string($blocoAdm));
			$arrAdm = Array();
			
			for($i = 1; $i < count($blocoAdm); $i++) {
				$arrAdm[] = $blocoAdm[$i];
				$arrAdm[$i] = preg_split('/(<td class="">|<td class="blue">)/', $blocoAdm[$i]);
			}
			if(!stristr($arrAdm[0], 'Nada Consta.')) {
				array_walk_recursive($arrAdm,  function(&$value, $key){ $value = strip_tags(trim(rtrim($value))); });
				$admCapital = '';
				$admJunta = '';
				if(stristr($arrAdm[1][0], 'Capital Social')) {
					$inicio = 3;
					$hide = '';
					$admCapital = preg_split('/(l:|\|)/', $arrAdm[1][0]);
					$admJunta = explode(':', $admCapital[2]);
					$admCapital = $admCapital[1];
					$admJunta = $admJunta[1];
				} else {
					$inicio = 2;
					$hide = 'none';
				}
			}
			/* FIM ADMINISTRADORES */
			
			/* INICIO PARTICIPACAO EM EMPRESAS */
			$blocoParticipacao = self::corta($res, '<th colspan="6"><strong class="blue">PARTICIPA', '<td colspan="6" style="text-align:right;" class="rodape">');
			$blocoParticipacao = explode('<tr ', self::clean_string($blocoParticipacao));
			$arrParticipacao = Array();
			
			for($i = 1; $i < count($blocoParticipacao); $i++) {
				$arrParticipacao[] = $blocoParticipacao[$i];
				$arrParticipacao[$i] = preg_split('/(<td class="">|<td class="blue">)/', $blocoParticipacao[$i]);
			}
			array_walk_recursive($arrParticipacao,  function(&$value, $key){ $value = strip_tags(trim(rtrim($value))); });
			/* FIM PARTICIPACAO EM EMPRESAS */
			
			/* INICIO REFERENCIAS */
			$blocoRef = self::corta($res, '<th colspan="4"><strong class="blue">REFERENCIAIS DE NEG', '</tbody>');
			$blocoRef = explode('</tr>', self::clean_string($blocoRef));
			$arrRef = Array();
			
			for($i = 1; $i < count($blocoRef); $i++) {
				$arrRef[] = $blocoRef[$i];
				$arrRef[$i] = explode('<td>', $blocoRef[$i]);
			}
			array_walk_recursive($arrRef,  function(&$value, $key){ $value = strip_tags(trim(rtrim($value))); });
			/* FIM REFERENCIAS */
			
			/* INICIO FORNECEDORES */
			$blocoFornecedores = self::corta($res, 'PRINCIPAIS FORNECEDORES - Atualiza', '</tbody>');
			$blocoFornecedores = explode('</tr>', self::clean_string($blocoFornecedores));
			$arrFornecedores = Array();
			$ultimaAtualizacao = '-';
			if(count($blocoFornecedores) > 1) {
				$ultimaAtualizacao = explode(':', strip_tags($blocoFornecedores[0]));
				$ultimaAtualizacao = $ultimaAtualizacao[1];
				
				for($i = 1; $i < count($blocoFornecedores); $i++) {
					$arrFornecedores[] = $blocoFornecedores[$i];
					$arrFornecedores[$i] = preg_split('/(<td class="">|<td class="blue">)/', $blocoFornecedores[$i]);
				}
				array_walk_recursive($arrFornecedores,  function(&$value, $key){ $value = strip_tags(trim(rtrim($value))); });
			}
			/* FIM REFERENCIAS */
			
			/* INICIO INCONSISTENCIAS DOS PARTICIPANTES */
			$blocoInconsistencia = self::corta($res, '<th colspan="4"><strong class="blue">VEJA DETALHES DE INCONSIST', '</tbody>');
			$blocoInconsistencia = explode('</tr>', self::clean_string($blocoInconsistencia));
			$arrInconsistencia = Array();
			
			for($i = 1; $i < count($blocoInconsistencia); $i++) {
				$arrInconsistencia[] = $blocoInconsistencia[$i];
				$arrInconsistencia[$i] = preg_split('/(<td class="">|<td class="blue">)/', $blocoInconsistencia[$i]);
			}
			array_walk_recursive($arrInconsistencia,  function(&$value, $key){ $value = strip_tags(trim(rtrim($value))); });
			/* FIM INCONSISTENCIAS DOS PARTICIPANTES  */
			
			/* INICIO INADIMPLENCIA DOS PARTICIPANTES */
			$blocoInadimplencia = self::corta($res, '<th colspan="4"><strong class="blue">VEJA DETALHES DE INADIMPL', '</tbody>');
			$blocoInadimplencia = explode('</tr>', self::clean_string($blocoInadimplencia));
			$arrInadimplencia = Array();
			
			for($i = 1; $i < count($blocoInadimplencia); $i++) {
				$arrInadimplencia[] = $blocoInadimplencia[$i];
				$arrInadimplencia[$i] = explode('</td>', $blocoInadimplencia[$i]);
			}
			array_walk_recursive($arrInadimplencia,  function(&$value, $key){ $value = strip_tags(trim(rtrim($value))); });
			/* FIM INADIMPLENCIA DOS PARTICIPANTES */
			
			/* INICIO INFORMACOES RECENTES */
			$blocoInfo = self::corta($res, 'ES MAIS RECENTES</strong></th>', '</tbody>');
			$blocoInfo = explode('</tr>', self::clean_string($blocoInfo));
			$arrInfo = Array();
			
			for($i = 1; $i < count($blocoInfo); $i++) {
				$arrInfo[] = $blocoInfo[$i];
				$arrInfo[$i] = explode('<td>', $blocoInfo[$i]);
			}
			array_walk_recursive($arrInfo, function(&$value, $key){ $value = strip_tags(trim(rtrim($value))); });
			/* FIM INFORMACOES RECENTES */
			
			/* INICIO RELACIONAMENTO FORNECEDORES */
			$blocoRelFor = self::corta($res, 'TEMPO DE RELACIONAMENTO COM FORNECEDORES', '</tbody>');
			$blocoRelFor = strip_tags($blocoRelFor);
			$blocoRelFor = preg_split('/(:|D|M|A)/', self::clean_string($blocoRelFor));
			/* FIM RELACIONAMENTO FORNECEDORES */
			
			/* INICIO COMPORTAMENTO COM SUA EMPRESA */
			$blocoComportamentoEmp = self::corta($res, 'COMPORTAMENTO COM SUA EMPRESA', '</tbody>');
			$blocoComportamentoEmp = explode('</tr>', self::clean_string($blocoComportamentoEmp));
			$arrCompEmp = Array();
			
			for($i = 1; $i < count($blocoComportamentoEmp); $i++) {
				$arrCompEmp[] = $blocoComportamentoEmp[$i];
				$arrCompEmp[$i] = explode('</td>', $blocoComportamentoEmp[$i]);
			}
			array_walk_recursive($arrCompEmp,  function(&$value, $key){ $value = strip_tags(trim(rtrim($value))); });
			/* FIM COMPORTAMENTO COM SUA EMPRESA */
			
		}
		
		
			// if(strlen($razao) > 3)
			// {
			// 	$control->saveConsulta();
			// }
		
		
		$identificacaoOk = [
		'razao' => self::clean($razao),
		'cnpj' => self::clean($cnpj),
		'nire' => self::clean($nire),
		'fantasia' => self::clean($fantasia),
		'razao_anterior' => self::clean($razao_anterior),
		'data' => self::clean($data),
		'fundacao' => self::clean($fundacao),
		'encerramento' => self::clean($encerramento),
		'inscr' => self::clean($inscr),
		'situacao_cnpj' => self::clean($situacao_cnpj),
		'dt_cnpj' => self::clean($dt_cnpj),
		'consultado_cnpj' => self::clean($consultado_cnpj),
		'situacao_sintegra' => self::clean($situacao_sintegra),
		'dt_sintegra' => self::clean($dt_sintegra),
		'consultado_sintegra' => self::clean($consultado_sintegra),
		'natureza' => self::clean($natureza),
		'faixa_func' => self::clean($faixa_func),
		'filiais' => self::clean($filiais),
		'ativ_prim' => self::clean($ativ_prim),
		'ativ_sec' => self::clean($ativ_sec),
		'cidades' => self::clean($cidades)
		
		];
		
		if(count($localizacao) > 1) {
			$localizacaoOk = [
			'endereco' => self::clean($endereco),
			'bairro' => self::clean($bairro),
			'cidade' => self::clean($cidade),
			'cep' => self::clean($cep),
			'tel' => self::clean($tel)
			];
		} else {
			$localizacaoOk = [];
		}
		
		$scoreOk = [
		'score' => self::clean($num_scoreEmp[2]),
		'legenda' => self::clean($texto_scoreEmp)
		];
		
		$scoreAtacOk = [
		'score' => self::clean($num_scoreAta[2]),
		'legenda' => self::clean($texto_scoreAta)
		];
		
		if(!$faturamento_nao_calculado) {
			$faturamentoPresumidoOk = [
			'faixaFaturamento' => self::clean($faixaFaturamento),
			'faturamentoAnual' => self::clean($faturamentoAnual),
			'legenda' => self::clean($texto_faturamento)
			];
		}else{
			$faturamentoPresumidoOk = [];
		}
		
		$painelOk = [
		'titulos_a_vencer' => [
		'qnt' => self::clean($titulosQtde),
		'data' => self::clean($titulosData),
		'valor' => self::clean($titulosValor)
		],
		'comportamento_de_pagamentos' => [
		'qnt' => self::clean($comportamentoQtde),
		'data' => self::clean($comportamentoData),
		'valor' => self::clean($comportamentoValor)
		],
		'pendencias_restricoes_financeiras' => [
		'qnt' => self::clean($pendenciasQtde),
		'data' => self::clean($pendenciasData),
		'valor' => self::clean($pendenciasValor)
		],
		'cheques_sustados_motivo21' => [
		'qnt' => self::clean($sustadosQtde),
		'data' => self::clean($sustadosData),
		'valor' => self::clean($sustadosValor)
		],
		'cheques_sem_fundo' => [
		'qnt' => self::clean($chequeSemFundoQtde),
		'data' => self::clean($chequeSemFundoData),
		'valor' => self::clean($chequeSemFundoValor)
		],
		'protestos' => [
		'qnt' => self::clean($protestosQtde),
		'data' => self::clean($protestosData),
		'valor' => self::clean($protestosValor)
		],
		'recuperacoes_e_falencias' => [
		'qnt' => self::clean($recuperacaoQtde),
		'data' => self::clean($recuperacaoData),
		'valor' => self::clean($recuperacaoValor)
		],
		'acoes_judiciais' => [
		'qnt' => self::clean($acoesQtde),
		'data' => self::clean($acoesData),
		'valor' => self::clean($acoesValor)
		],
		'socios' => [
		'qnt' => self::clean($sociosQtde),
		'data' => self::clean($sociosData),
		'valor' => self::clean($sociosValor)
		],
		'administradores' => [
		'qnt' => self::clean($adminQtde),
		'data' => self::clean($adminData),
		'valor' => self::clean($adminValor)
		],
		'particopacoes_em_empresas' => [
		'qnt' => self::clean($participacoesQtde),
		'data' => self::clean($participacoesData),
		'valor' => self::clean($participacoesValor)
		]
		];
		
		$titulosOk = [];
		
		if(count($arrTitulos)>1) {
			for($i = 2; $i < count($arrTitulos)-2; $i++) {
				$titulosOk['titulos_a_vencer'] = [
				'periodo' => self::clean($arrTitulos[$i][0]),
				'fornecedores' => self::clean($arrTitulos[$i][1]),
				'titulos' => self::clean($arrTitulos[$i][2]),
				'valor' => self::clean($arrTitulos[$i][3])
				];
			}
			
			$penult = count($arrTitulos)-2;
			$titulosOk['total'] = [
			'periodo' => 'Total',
			'fornecedores' => self::clean($arrTitulos[$penult][1]),
			'titulos' => self::clean($arrTitulos[$penult][2]),
			'valor' => self::clean($arrTitulos[$penult][3])
			];
			
		} else {
			$titulosOk['titulos_a_vencer'] = [];
		}
		
		$compoartamento_de_pagamentoOk = [];
		
		if(!stristr($arrComportamento[0], 'Nada Consta.')) {
			
			for($i = 4; $i < count($arrComportamento)-1; $i++) {
				$compoartamento_de_pagamentoOk[] = [
				'periodo' => self::clean($arrComportamento[$i][0]),
				'fornecedores' => self::clean($arrComportamento[$i][1]),
				'titulos' => self::clean($arrComportamento[$i][2]),
				'valor_total' => self::clean($arrComportamento[$i][3]),
				'a_vista' => self::clean($arrComportamento[$i][4]),
				'pontual' => self::clean($arrComportamento[$i][5]),
				'dias_de_atraso' => [
				'6_a_15' => self::clean($arrComportamento[$i][6]),
				'16_a_30' => self::clean($arrComportamento[$i][7]),
				'31_a_60' => self::clean($arrComportamento[$i][8]),
				'mais_de_60' => self::clean($arrComportamento[$i][9]),
				'atraso_medio_dias' => self::clean($arrComportamento[$i][10])
				]
				];
			}
			
			$compoartamento_de_pagamentoOk['total'] = [
				'periodo' => self::clean($arrComportamento[3][1]),
				'fornecedores' => self::clean($arrComportamento[3][2]),
				'titulos' => self::clean($arrComportamento[3][3]),
				'valor_total' => self::clean($arrComportamento[3][4]),
				'a_vista' => self::clean($arrComportamento[3][5]),
				'pontual' => self::clean($arrComportamento[3][6]),
				'dias_de_atraso' => [
					'6_a_15' => self::clean($arrComportamento[3][7]),
					'16_a_30' => self::clean($arrComportamento[3][8]),
					'31_a_60' => self::clean($arrComportamento[3][9]),
					'mais_de_60' => self::clean($arrComportamento[3][10])
				]
			];
		}
		
		$pendenias_restricoesOk = [];
		if(stristr($arrPendencias[2][0], 'Total')) {
			$pendenias_restricoesOk['total'] = [
			'total_pendencias' => self::clean($totalPendencias),
			'total_credores' => self::clean($totalCredores),
			'valor' => self::clean($valorPendencias)
			];
			
			for($i = 5; $i <= count($arrPendencias)-1; $i++) {
				$pendenias_restricoesOk[] = [
				'data' => self::clean(strip_tags($arrPendencias[$i][1])),
				'credor' => self::clean(strip_tags($arrPendencias[$i][2])),
				'valor' => self::clean(strip_tags($arrPendencias[$i][3]))
				];
			}
		}
		
		$cheques_sem_fundoOk = [];
		if(!stristr($arrSemFundo[0], 'Nada Consta.')) {
			
			for($i = 4; $i < count($arrSemFundo)-1; $i++) {
				$cheques_sem_fundoOk[] = [
				'qnt' => self::clean($arrSemFundo[$i][0]),
				'data_ultimo' => self::clean($arrSemFundo[$i][1]),
				'banco' => self::clean($arrSemFundo[$i][2]),
				'agencia' => self::clean($arrSemFundo[$i][3]),
				'motivo' => self::clean($arrSemFundo[$i][4])
				];
			}
			
			$cheques_sem_fundoOk['total'] = self::clean($totalCheques);
		}
		
		$protestosOk = [];
		if(!stristr($protestosQtde, '-')) {
			$protestosOk['total'] = [
			'qnt' => self::clean($protestosQtde),
			'valor' => self::clean($protestosValor)
			];
			
			for($i = 7; $i <= count($arrProtestos); $i++) {
				$protestosOk[] = [
				'data' => self::clean($arrProtestos[$i][1]),
				'vencimento' => self::clean($arrProtestos[$i][3]),
				'cartorio' => self::clean($arrProtestos[$i][4]),
				'cidade' => self::clean($arrProtestos[$i][5]),
				'uf' => self::clean($arrProtestos[$i][6]),
				'valor' => self::clean($arrProtestos[$i][7])
				];
			}
		}
		
		$rec_falencia_aocesOk = [];
		if(!stristr($arrBlocoRFAJ[0], 'Nada Consta.')) {
			for($i = 2; $i < count($arrBlocoRFAJ); $i++) {
				$rec_falencia_aocesOk[] = [
				'qnt' => self::clean($arrBlocoRFAJ[$i][1]),
				'tipo' => self::clean($arrBlocoRFAJ[$i][2]),
				'data' => self::clean($arrBlocoRFAJ[$i][3]),
				'vara' => self::clean($arrBlocoRFAJ[$i][4]),
				'uf' => self::clean($arrBlocoRFAJ[$i][5]),
				'cidade' => self::clean($arrBlocoRFAJ[$i][6])
				];
			}
		}
		
		$sociosOk = [];
		if(count($arrBlocoSocios) > 1) {
			$sociosOk['total'] = [
			'capital_social' => self::clean($socioCapital[1]),
			'atualizacao_na_junta' => self::clean($socioJunta[1])
			];
			
			for($i = 3; $i < count($arrBlocoSocios); $i++) {
				$sociosOk[] = [
				'cpf_cnpj' => self::clean($arrBlocoSocios[$i][2]),
				'socios_acionistas' => self::clean($arrBlocoSocios[$i][3]),
				'entrada' => self::clean($arrBlocoSocios[$i][4]),
				'participacao' => self::clean($arrBlocoSocios[$i][5])
				];
			}
		}
		
		$administradoresOk = [];
		if(!stristr($arrAdm[0], 'Nada Consta.')) {
			$administradoresOk['total'] = [
			'capital_social' => self::clean($admCapital),
			'atualizacao_na_junta' => self::clean($admJunta)
			];
			
			for($i = $inicio; $i < count($arrAdm); $i++) {
				$administradoresOk[] = [
				'cpf_cnpj' => self::clean($arrAdm[$i][2]),
				'administracao' => self::clean($arrAdm[$i][3]),
				'cargo' => self::clean($arrAdm[$i][4]),
				'nacionalidade' => self::clean($arrAdm[$i][5]),
				'estado_civil' => self::clean($arrAdm[$i][6]),
				'entrada' => self::clean($arrAdm[$i][7]),
				'mandato' => self::clean($arrAdm[$i][8])
				];
			}
		}
		
		$participacoesEmpresasOk = [];
		if(!stristr($arrParticipacao[0], 'Nada Consta.')) {
			
			for($i = 2; $i < count($arrParticipacao); $i++) {
				$participacoesEmpresasOk[] = [
				'cnpj_participada' => self::clean($arrParticipacao[$i][2]),
				'razao_social_participada' => self::clean($arrParticipacao[$i][3]),
				'entrada' => self::clean($arrParticipacao[$i][4]),
				'capital' => self::clean($arrParticipacao[$i][5]),
				'cnpj_cpf_participante' => self::clean($arrParticipacao[$i][6])
				];
			}
		}
		
		$referenciasDeNegociosOk = [];
		if(count($arrRef)>1) {
			$referenciasDeNegociosOk['ultima_compra'] = [
			'data' => self::clean($arrRef[2][2]),
			'valor' => self::clean($arrRef[2][3]),
			'media' => self::clean($arrRef[2][4])
			];
			
			$referenciasDeNegociosOk['maior_fatura'] = [
			'data' => self::clean($arrRef[3][2]),
			'valor' => self::clean($arrRef[3][3]),
			'media' => self::clean($arrRef[3][4])
			];
			
			$referenciasDeNegociosOk['maior_credito'] = [
			'data' => self::clean($arrRef[4][2]),
			'valor' => self::clean($arrRef[4][3]),
			'media' => self::clean($arrRef[4][4])
			];
		}
		
		$principaisFornecedoresOk = ['ultima_atualizacao' => $ultimaAtualizacao];
		
		if(count($arrFornecedores)>1) {
			for($i = 2; $i < count($arrFornecedores)-1; $i++) {
				$principaisFornecedoresOk[] = [
				'cnpj' => self::clean($arrFornecedores[$i][1]),
				'razao_social' => self::clean($arrFornecedores[$i][2]),
				'cnae' => self::clean($arrFornecedores[$i][3])
				];
			}
		}
		
		$tempoRelacionamento = [];
		if(count($blocoRelFor)>1) {
			$tempoRelacionamento[] = [
			'ate_6_meses' => self::clean($blocoRelFor[4]),
			'de_7_ate_12_meses' => self::clean($blocoRelFor[6]),
			'de_1_ate_2_anos' => self::clean($blocoRelFor[8]),
			'de_3_ate_5_anos' => self::clean($blocoRelFor[11]),
			'de_6_ate_10_anos' => self::clean($blocoRelFor[13]),
			'mais_de_10_anos' => self::clean($blocoRelFor[15]),
			'fontes_consultas' => self::clean($blocoRelFor[1])
			];
		} 
		
		$detalhesInadinplenciasParcipOk = [];
		
		if(!stristr($arrInadimplencia[0], 'Nada Consta.')) {
			for($i = 2; $i < count($arrInadimplencia)-1; $i++) {
				$detalhesInadinplenciasParcipOk[] = [
				'nome_razao' => self::clean($arrInadimplencia[$i][0]),
				'cpf_cnpj' => self::clean($arrInadimplencia[$i][1]),
				'tipo' => self::clean($arrInadimplencia[$i][2])
				];
			}
		}
		
		$informacoesRecentesOk = [];
		if(count($arrInfo)>1) {
			for($i = 2; $i < count($arrInfo)-1; $i++) {
				$informacoesRecentesOk[] = [
				'data' => self::clean($arrInfo[$i][1]),
				'tipo' => self::clean($arrInfo[$i][2])
				];
			}
		}
		
		
		
		$dadosOk = [
		'identificacao' => $identificacaoOk,
		'localizacao' => $localizacaoOk,
		'score_empresarial' => $scoreOk,
		'score_atacadista' => $scoreAtacOk,
		'faturamento_resumo' => $faturamentoPresumidoOk,
		'painel_de_controle' => $painelOk,
		'titulos_a_vencer' => $titulosOk,
		'comportamento_de_pagamentos' => $compoartamento_de_pagamentoOk,
		'pendencias_e_restricoes_financeiras' => $pendenias_restricoesOk,
		'cheques_sem_fundo' => $cheques_sem_fundoOk,
		'protestos' => $protestosOk,
		'recuperacoes_falencias_acoes_judiciais' => $rec_falencia_aocesOk,
		'socios' => $sociosOk,
		'administradores' => $administradoresOk,
		'participacoes_em_empresas' => $participacoesEmpresasOk,
		'referencias_de_negocios' => $referenciasDeNegociosOk,
		'principais_fornecedores' => $principaisFornecedoresOk,
		'tempo_de_relacionamento_com_fornecedores' => $tempoRelacionamento,
		'detalhes_de_inadimplencia_participantes' => $detalhesInadinplenciasParcipOk,
		'informacoes_mais_recentes' => $informacoesRecentesOk
		];

		return $dadosOk;

	}
}
