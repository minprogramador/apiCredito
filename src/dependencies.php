<?php
// DIC configuration

$container = $app->getContainer();


$container['notFoundHandler'] = function ($c) {
	return function ($request, $response) use ($c) {
	$tokenx = apache_request_headers()["Authorization"];
	$c->logger->error("token: $tokenx", [
        'error' => true,
        'msg' => 'pagina invalida.'
    ]);

	$response = new \Slim\Http\Response(404);
		return $response->write("Page not found");
	};
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
	$logger->pushProcessor(new Monolog\Processor\WebProcessor());
	$logger->pushProcessor(new Monolog\Processor\UidProcessor());
	$handler = new Monolog\Handler\StreamHandler($settings['path'], $settings['level']);
    $logger->pushHandler($handler);
    return $logger;
};

$container['loggerPai'] = function ($c) {
    $settings = $c->get('settings')['loggerPai'];
    $logger = new Monolog\Logger($settings['name']);
	$logger->pushProcessor(new Monolog\Processor\WebProcessor());
	$logger->pushProcessor(new Monolog\Processor\UidProcessor());
	$handler = new Monolog\Handler\StreamHandler($settings['path'], $settings['level']);
    $logger->pushHandler($handler);
    return $logger;
};