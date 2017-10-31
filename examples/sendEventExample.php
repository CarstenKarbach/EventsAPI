<?php

use jards\eventsapi\EventManagement;
require_once __DIR__.'/../vendor/autoload.php';

$eventManagement = new EventManagement();
$result = $eventManagement->sendEvent('Submit', 'Application 1234 was submitted');

echo $result;

?>