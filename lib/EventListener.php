<?php

namespace jards\eventsapi;

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