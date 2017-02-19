<?php

use M1ke\Sql\ExtendedPdo;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__.'bootstrap.php';

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

$app->get('/', function (Request $request, Response $response, $args = []){
	$response->getBody()->write("Home");
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

$app->get('/quiz', function (Request $request, Response $response){
	$topics = new Topics($this->db);
	$list = $topics->getList();

	$response->getBody()->write(json_encode($list));

	$response->withJSON();

	return $response;

});

$app->get('/quiz/start', function (Request $request, Response $response){
	$response->getBody()->write("Start ...");
});

$app->get('/users', function (Request $request, Response $response){
	$admin = new Admin($this->db);
	$list = $admin->users();

	$response->getBody()->write(json_encode($list));

	return $response;
});

$app->run();
