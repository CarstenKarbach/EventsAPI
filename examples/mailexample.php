<?php

$headers =  'MIME-Version: 1.0' . "\r\n";
$headers .= 'From: Events API <no-reply@eventsapi.jards.fz-juelich.de>' . "\r\n";
$headers .= 'Content-type: text/html; charset=utf8' . "\r\n";


mail('carstenkarbach@gmx.de', 'New event received', "Hello Carsten", $headers);

?>