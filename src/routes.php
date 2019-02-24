<?php


$app->group('/api', function () use ($app) {

	$app->get('/pai/{cpf}',    'App\Pai\Consultar::cpf');

	$app->get('/credito/consumo',     'App\Credito\Consultar::consumo');
	$app->get('/credito/cpf/{cpf}',   'App\Credito\Consultar::cpf');
	$app->get('/credito/cnpj/{cnpj}', 'App\Credito\Consultar::cnpj');

});
