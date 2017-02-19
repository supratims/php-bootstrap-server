<?php

require_once 'vendor/autoload.php';

define('DIR', __DIR__);
define('TWIG_SRC', DIR . '/templates');
define('TWIG_CACHE', DIR . '/twig_cache');

spl_autoload_register(function ($classname){
	require("objects/" . $classname . ".php");
});

