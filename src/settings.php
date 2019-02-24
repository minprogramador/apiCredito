<?php

return [
	'settings' => [
		'displayErrorDetails'   => true,
		'addContentLengthHeader' => false,
		'logger' => [
			'name' => 'base',
			'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app_'. date("Y-m-d").'.log',
			'level' => \Monolog\Logger::DEBUG,
		],
		'loggerPai' => [
			'name' => 'pai',
			'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/pai_'. date("Y-m-d").'.log',
			'level' => \Monolog\Logger::DEBUG,
		],
	]
];
