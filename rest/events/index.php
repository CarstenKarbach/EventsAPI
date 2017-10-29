<?php
require_once __DIR__.'/../../vendor/autoload.php';

use Silex\Application;
use jards\eventsapi\EventListener;

$app = new Application();

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app['debug'] = true;

$app["EventListener"] = function($app){
	$app["EventListener"] = new EventListener();
	return $app["EventListener"];
};

$app->POST('/events', 'EventListener:receiveEvent');
$app->GET('/events', 'EventListener:getEvents');

$app->run();

?>