<?php
// DIC configuration

$container = $app->getContainer();


$container['notFoundHandler'] = function ($c) {
	return function ($request, $response) use ($c) {
	$c->logger->alert("acesso.incomum");

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
