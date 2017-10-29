<?php

namespace jards\eventsapi;

require_once __DIR__.'/../vendor/autoload.php';

/**
 * Handler, which sends a mail for every event
 *
 */
class MailOnEventListener implements EventListener{
	
	/**
	 * 
	 * @param unknown $event
	 */
	public function handleEvent($event){
		mail('carstenkarbach@gmx.de', 'New event received', json_encode($event));
	}
}

?>