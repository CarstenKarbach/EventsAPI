<?php

namespace jards\eventsapi;

require_once __DIR__.'/../vendor/autoload.php';

/**
 * Implement to react on incoming events. 
 *
 */
interface EventListener{
	
	/**
	 * This is called for every new received event.
	 * 
	 * @param object $event encoded object from JSON
	 */
	public function handleEvent($event);
}

?>