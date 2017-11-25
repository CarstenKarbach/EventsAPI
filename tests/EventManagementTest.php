<?php

namespace jards\eventsapi\tests;

use PHPUnit\Framework\TestCase;
use jards\eventsapi\EventManagement;

class EventManagementTest extends TestCase{

	public function testGetEvents()
	{
		echo "testGetEvents\n";
		
		$eventManagement = new EventManagement();
		$events = $eventManagement->getEvents();
		
		$content = $events->getContent();
		
		var_dump($content);
		
		$eventsArray = json_decode($content);
		$this->assertTrue(is_array($eventsArray) || is_empty($eventsArray), "Result of event management is not an array." );
	}
	
}