<?php
require_once __DIR__.'/../../vendor/autoload.php';

use Silex\Application;
use jards\eventsapi\EventListener;
use jards\eventsapi\MailOnEventHandler;
use jards\eventsapi\MailOnEventListener;
use jards\eventsapi\EventManagement;

$app = new Application();

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app['debug'] = true;

$app["MailOnEventHandler"] = function($app){
	return new MailOnEventListener();
};

$app["EventManagement"] = function($app){
	$result = new EventManagement();
	$result->registerHandler($app["MailOnEventHandler"]);
	return $result;
};

$app->POST('/events', 'EventManagement:receiveEvent');
$app->GET('/events', 'EventManagement:getEvents');

$app->run();

?>