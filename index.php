<?php

use M1ke\Sql\ExtendedPdo;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__.'/bootstrap.php';

$config = [
	'settings' => [
		'displayErrorDetails' => true,

		'logger' => [
			'name' => 'slim-app',
			'level' => 'debug',
			'path' => __DIR__ . '/logs/app.log',
		],

		'db' => [
			'host' => "localhost",
			'user' => 'root',
			'pass' => '',
			'dbname' => 'test',
		],
	],
];


$app = new \Slim\App($config);

$container = $app->getContainer();
$container['logger'] = function ($c){
	$logger = new \Monolog\Logger('my_logger');
	$file_handler = new \Monolog\Handler\StreamHandler("/logs/app.log");
	$logger->pushHandler($file_handler);

	return $logger;
};
$container['db'] = function ($c){
	$db = $c['settings']['db'];
	$pdo = new ExtendedPdo($db['dbname'], $db['user'], $db['pass']);

	return $pdo;
};
$container['twig'] = function ($c){
	$loader = new Twig_Loader_Filesystem(TWIG_SRC);
	$twig = new Twig_Environment($loader, [
		'cache' => TWIG_CACHE,
		'auto_reload' => true,
	]);

	return $twig;
};

// Bind generic page routes here

$app->get('/', function (Request $request, Response $response, $args = []){
	$response = $this->twig->render('home.twig');

	return $response;
});

$app->get('/login', function (Request $request, Response $response, $args = []){

	$response = $this->twig->render('login.twig');

	return $response;
});

$app->post('/login', function (Request $request, Response $response, $args = []){

	$login = new Login($this->db);
	$auth = $login->authenticate($request->getAttribute('login'), $request->getAttribute('password'));

	if ($auth){
		$this->get('cookies')->set('pass', [
			'value' => $args['name'],
			'expires' => '7 days'
		]);
		$response->getBody()->write('Success');
	}
	else {
		$response->getBody()->write('Failed');
	}

	// Redirect
	//return $res->withStatus(302)->withHeader('Location', 'your-new-uri');
});

// Bind REST routes here

$app->any('/books/[{id}]', function (Request $request, Response $response, $args = []) {
	$response->getBody()->write('Hey !'.print_r($args, true));
	// Apply changes to books or book identified by $args['id'] if specified.
	// To check which method is used: $request->getMethod();
});

$app->run();
