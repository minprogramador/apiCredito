<?php

namespace App\Credito;

use Slim\Http\Request;
use Slim\Http\Response;

class CnpjJson {


	public static function clean_string($value)
	{
		return str_replace(array("\n",'  ','	'), '', $value);
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
		'razao' => $razao,
		'cnpj' => $cnpj,
		'nire' => $nire,
		'fantasia' => $fantasia,
		'razao_anterior' => $razao_anterior,
		'data' => $data,
		'fundacao' => $fundacao,
		'encerramento' => $encerramento,
		'inscr' => $inscr,
		'situacao_cnpj' => $situacao_cnpj,
		'dt_cnpj' => $dt_cnpj,
		'consultado_cnpj' => $consultado_cnpj,
		'situacao_sintegra' => $situacao_sintegra,
		'dt_sintegra' => $dt_sintegra,
		'consultado_sintegra' => $consultado_sintegra,
		'natureza' => $natureza,
		'faixa_func' => $faixa_func,
		'filiais' => $filiais,
		'ativ_prim' => $ativ_prim,
		'ativ_sec' => $ativ_sec,
		'cidades' => $cidades
		
		];
		
		if(count($localizacao) > 1) {
			$localizacaoOk = [
			'endereco' => $endereco,
			'bairro' => $bairro,
			'cidade' => $cidade,
			'cep' => $cep,
			'tel' => $tel
			];
		} else {
			$localizacaoOk = [];
		}
		
		$scoreOk = [
		'score' => $num_scoreEmp[2],
		'legenda' => $texto_scoreEmp
		];
		
		$scoreAtacOk = [
		'score' => $num_scoreAta[2],
		'legenda' => $texto_scoreAta
		];
		
		if(!$faturamento_nao_calculado) {
			$faturamentoPresumidoOk = [
			'faixaFaturamento' => $faixaFaturamento,
			'faturamentoAnual' => $faturamentoAnual,
			'legenda' => $texto_faturamento
			];
		}else{
			$faturamentoPresumidoOk = [];
		}
		
		$painelOk = [
		'titulos_a_vencer' => [
		'qnt' => $titulosQtde,
		'data' => $titulosData,
		'valor' => $titulosValor
		],
		'comportamento_de_pagamentos' => [
		'qnt' => $comportamentoQtde,
		'data' => $comportamentoData,
		'valor' => $comportamentoValor
		],
		'pendencias_restricoes_financeiras' => [
		'qnt' => $pendenciasQtde,
		'data' => $pendenciasData,
		'valor' => $pendenciasValor
		],
		'cheques_sustados_motivo21' => [
		'qnt' => $sustadosQtde,
		'data' => $sustadosData,
		'valor' => $sustadosValor
		],
		'cheques_sem_fundo' => [
		'qnt' => $chequeSemFundoQtde,
		'data' => $chequeSemFundoData,
		'valor' => $chequeSemFundoValor
		],
		'protestos' => [
		'qnt' => $protestosQtde,
		'data' => $protestosData,
		'valor' => $protestosValor
		],
		'recuperacoes_e_falencias' => [
		'qnt' => $recuperacaoQtde,
		'data' => $recuperacaoData,
		'valor' => $recuperacaoValor
		],
		'acoes_judiciais' => [
		'qnt' => $acoesQtde,
		'data' => $acoesData,
		'valor' => $acoesValor
		],
		'socios' => [
		'qnt' => $sociosQtde,
		'data' => $sociosData,
		'valor' => $sociosValor
		],
		'administradores' => [
		'qnt' => $adminQtde,
		'data' => $adminData,
		'valor' => $adminValor
		],
		'particopacoes_em_empresas' => [
		'qnt' => $participacoesQtde,
		'data' => $participacoesData,
		'valor' => $participacoesValor
		]
		];
		
		$titulosOk = [];
		
		if(count($arrTitulos)>1) {
			for($i = 2; $i < count($arrTitulos)-2; $i++) {
				$titulosOk['titulos_a_vencer'] = [
				'periodo' => $arrTitulos[$i][0],
				'fornecedores' => $arrTitulos[$i][1],
				'titulos' => $arrTitulos[$i][2],
				'valor' => $arrTitulos[$i][3]
				];
			}
			
			$penult = count($arrTitulos)-2;
			$titulosOk['total'] = [
			'periodo' => 'Total',
			'fornecedores' => $arrTitulos[$penult][1],
			'titulos' => $arrTitulos[$penult][2],
			'valor' => $arrTitulos[$penult][3]
			];
			
		} else {
			$titulosOk['titulos_a_vencer'] = [];
		}
		
		$compoartamento_de_pagamentoOk = [];
		
		if(!stristr($arrComportamento[0], 'Nada Consta.')) {
			
			for($i = 4; $i < count($arrComportamento)-1; $i++) {
				$compoartamento_de_pagamentoOk[] = [
				'periodo' => $arrComportamento[$i][0],
				'fornecedores' => $arrComportamento[$i][1],
				'titulos' => $arrComportamento[$i][2],
				'valor_total' => $arrComportamento[$i][3],
				'a_vista' => $arrComportamento[$i][4],
				'pontual' => $$arrComportamento[$i][5],
				'dias_de_atraso' => [
				'6_a_15' => $arrComportamento[$i][6],
				'16_a_30' => $arrComportamento[$i][7],
				'31_a_60' => $arrComportamento[$i][8],
				'mais_de_60' => $arrComportamento[$i][9],
				'atraso_medio_dias' => $arrComportamento[$i][10]
				]
				];
			}
			
			$compoartamento_de_pagamentoOk['total'] = [
				'periodo' => $arrComportamento[3][1],
				'fornecedores' => $arrComportamento[3][2],
				'titulos' => $arrComportamento[3][3],
				'valor_total' => $arrComportamento[3][4],
				'a_vista' => $arrComportamento[3][5],
				'pontual' => $arrComportamento[3][6],
				'dias_de_atraso' => [
					'6_a_15' => $arrComportamento[3][7],
					'16_a_30' => $arrComportamento[3][8],
					'31_a_60' => $arrComportamento[3][9],
					'mais_de_60' => $arrComportamento[3][10]
				]
			];
		}
		
		$pendenias_restricoesOk = [];
		if(stristr($arrPendencias[2][0], 'Total')) {
			$pendenias_restricoesOk['total'] = [
			'total_pendencias' => $totalPendencias,
			'total_credores' => $totalCredores,
			'valor' => $valorPendencias
			];
			
			for($i = 5; $i <= count($arrPendencias)-1; $i++) {
				$pendenias_restricoesOk[] = [
				'data' => strip_tags($arrPendencias[$i][1]),
				'credor' => strip_tags($arrPendencias[$i][2]),
				'valor' => strip_tags($arrPendencias[$i][3])
				];
			}
		}
		
		$cheques_sem_fundoOk = [];
		if(!stristr($arrSemFundo[0], 'Nada Consta.')) {
			
			for($i = 4; $i < count($arrSemFundo)-1; $i++) {
				$cheques_sem_fundoOk[] = [
				'qnt' => $arrSemFundo[$i][0],
				'data_ultimo' => $arrSemFundo[$i][1],
				'banco' => $arrSemFundo[$i][2],
				'agencia' => $arrSemFundo[$i][3],
				'motivo' => $arrSemFundo[$i][4]
				];
			}
			
			$cheques_sem_fundoOk['total'] = $totalCheques;
		}
		
		$protestosOk = [];
		if(!stristr($protestosQtde, '-')) {
			$protestosOk['total'] = [
			'qnt' => $protestosQtde,
			'valor' => $protestosValor
			];
			
			for($i = 7; $i <= count($arrProtestos); $i++) {
				$protestosOk[] = [
				'data' => $arrProtestos[$i][1],
				'vencimento' => $arrProtestos[$i][3],
				'cartorio' => $arrProtestos[$i][4],
				'cidade' => $arrProtestos[$i][5],
				'uf' => $arrProtestos[$i][6],
				'valor' => $arrProtestos[$i][7]
				];
			}
		}
		
		$rec_falencia_aocesOk = [];
		if(!stristr($arrBlocoRFAJ[0], 'Nada Consta.')) {
			for($i = 2; $i < count($arrBlocoRFAJ); $i++) {
				$rec_falencia_aocesOk[] = [
				'qnt' => $arrBlocoRFAJ[$i][1],
				'tipo' => $arrBlocoRFAJ[$i][2],
				'data' => $arrBlocoRFAJ[$i][3],
				'vara' => $arrBlocoRFAJ[$i][4],
				'uf' => $arrBlocoRFAJ[$i][5],
				'cidade' => $arrBlocoRFAJ[$i][6]
				];
			}
		}
		
		$sociosOk = [];
		if(count($arrBlocoSocios) > 1) {
			$sociosOk['total'] = [
			'capital_social' => $socioCapital[1],
			'atualizacao_na_junta' => $socioJunta[1]
			];
			
			for($i = 3; $i < count($arrBlocoSocios); $i++) {
				$sociosOk[] = [
				'cpf_cnpj' => $arrBlocoSocios[$i][2],
				'socios_acionistas' => $arrBlocoSocios[$i][3], 
				'entrada' => $arrBlocoSocios[$i][4],
				'participacao' => $arrBlocoSocios[$i][5]
				];
			}
		}
		
		$administradoresOk = [];
		if(!stristr($arrAdm[0], 'Nada Consta.')) {
			$administradoresOk['total'] = [
			'capital_social' => $admCapital,
			'atualizacao_na_junta' => $admJunta
			];
			
			for($i = $inicio; $i < count($arrAdm); $i++) {
				$administradoresOk[] = [
				'cpf_cnpj' => $arrAdm[$i][2],
				'administracao' => $arrAdm[$i][3],
				'cargo' => $arrAdm[$i][4],
				'nacionalidade' => $arrAdm[$i][5],
				'estado_civil' => $arrAdm[$i][6],
				'entrada' => $arrAdm[$i][7],
				'mandato' => $arrAdm[$i][8]
				];
			}
		}
		
		$participacoesEmpresasOk = [];
		if(!stristr($arrParticipacao[0], 'Nada Consta.')) {
			
			for($i = 2; $i < count($arrParticipacao); $i++) {
				$participacoesEmpresasOk[] = [
				'cnpj_participada' => $arrParticipacao[$i][2],
				'razao_social_participada' => $arrParticipacao[$i][3],
				'entrada' => $arrParticipacao[$i][4],
				'capital' => $arrParticipacao[$i][5],
				'cnpj_cpf_participante' => $arrParticipacao[$i][6]
				];
			}
		}
		
		$referenciasDeNegociosOk = [];
		if(count($arrRef)>1) {
			$referenciasDeNegociosOk['ultima_compra'] = [
			'data' => $arrRef[2][2],
			'valor' => $arrRef[2][3],
			'media' => $arrRef[2][4]
			];
			
			$referenciasDeNegociosOk['maior_fatura'] = [
			'data' => $arrRef[3][2],
			'valor' => $arrRef[3][3],
			'media' => $arrRef[3][4]
			];
			
			$referenciasDeNegociosOk['maior_credito'] = [
			'data' => $arrRef[4][2],
			'valor' => $arrRef[4][3],
			'media' => $arrRef[4][4]
			];
		}
		
		$principaisFornecedoresOk = ['ultima_atualizacao' => $ultimaAtualizacao];
		
		if(count($arrFornecedores)>1) {
			for($i = 2; $i < count($arrFornecedores)-1; $i++) {
				$principaisFornecedoresOk[] = [
				'cnpj' => $arrFornecedores[$i][1],
				'razao_social' => $arrFornecedores[$i][2],
				'cnae' => $arrFornecedores[$i][3]
				];
			}
		}
		
		$tempoRelacionamento = [];
		if(count($blocoRelFor)>1) {
			$tempoRelacionamento[] = [
			'ate_6_meses' => $blocoRelFor[4],
			'de_7_ate_12_meses' => $blocoRelFor[6],
			'de_1_ate_2_anos' => $blocoRelFor[8],
			'de_3_ate_5_anos' => $blocoRelFor[11],
			'de_6_ate_10_anos' => $blocoRelFor[13],
			'mais_de_10_anos' => $blocoRelFor[15],
			'fontes_consultas' => $blocoRelFor[1]
			];
		} 
		
		$detalhesInadinplenciasParcipOk = [];
		
		if(!stristr($arrInadimplencia[0], 'Nada Consta.')) {
			for($i = 2; $i < count($arrInadimplencia)-1; $i++) {
				$detalhesInadinplenciasParcipOk[] = [
				'nome_razao' => $arrInadimplencia[$i][0],
				'cpf_cnpj' => $arrInadimplencia[$i][1],
				'tipo' => $arrInadimplencia[$i][2]
				];
			}
		}
		
		$informacoesRecentesOk = [];
		if(count($arrInfo)>1) {
			for($i = 2; $i < count($arrInfo)-1; $i++) {
				$informacoesRecentesOk[] = [
				'data' => $arrInfo[$i][1],
				'tipo' => $arrInfo[$i][2]
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
		'socios' => sociosOk,
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
