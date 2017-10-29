<?php

require_once __DIR__.'/../vendor/autoload.php';
use Swagger\Client\Api\EventsApi;

/**
 * Configure the rest api client for the OIDC server access.
 *
 * @return \Swagger\Client\ApiClient the configured api client with access to the REST api
 */
function getDefaultApiClient() {
	date_default_timezone_set ( 'Europe/Amsterdam' );
	$apiClient = new \Swagger\Client\ApiClient ();
	$apiClient->getConfig ()->setHost ( 'http://localhost/myapps/EventsAPI/rest/events' );
	return $apiClient;
}

$eventsApi = new EventsApi(getDefaultApiClient());
$events = $eventsApi->eventsGet();

foreach($events as $event){
	echo $event->getId().": ".$event->getName()."<br/>\n";
}