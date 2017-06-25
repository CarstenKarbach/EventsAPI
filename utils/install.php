<?php

// Install this event receiver service

$rootFolder = __DIR__.'/../';

echo shell_exec('(cd '.$rootFolder.'; composer install)');
echo shell_exec('(cd '.$rootFolder.'; chown -R www-data:www-data .)');


?>