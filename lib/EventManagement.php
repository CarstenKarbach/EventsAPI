<?php

namespace jards\eventsapi;

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Swagger\Client\Api\EventsApi;
use Swagger\Client\Model\Event;

/**
 * Listen for events.
 * Allow listing for all posted events. 
 *
 */
class EventManagement{
	
	/**
	 * Folder where to store events
	 * @var string
	 */
	private $dataFolder = __DIR__.'/../data';
	
	/**
	 * List of EventListener instances
	 * @var array of EventListener
	 */
	private $handlers = array();
	
	/**
	 * Add a new handler, which is called on a new incoming event.
	 * 
	 * @param EventHandler $handler
	 */
	public function registerHandler($handler){
		$this->handlers[] = $handler;
	}
	
	/**
	 * Get a list of event ids
	 * 
	 * @return array of event ids
	 */
	protected function getEventIDs(){
		$result = array();
		
		$objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->dataFolder));
		foreach($objects as $name => $object){
			if(!is_file($name)){
				continue;
			}
		
			$name = realpath($name);
			$objectFileName = basename($name);
		
			if(preg_match("/^(\d+)\.obj$/", $objectFileName, $matches)){
				$id = intval($matches[1]);
		
				$result[] = $id;
			}
		}
		
		return $result;
	}
	
	/**
	 * Get the maximum event ID
	 * 
	 * @return integer maximum event ID, 0 if none was found
	 */
	protected function getMaxEventID(){
		$allIDs = $this->getEventIDs();
		
		if(empty($allIDs)){
			return 0;
		}
		
		return max($allIDs);
	}
	
	/**
	 * Get a list of all events stored
	 * 
	 * @return JsonResponse with array of event objects
	 */
	public function getEvents(){
		$result = array();
		
		$ids = array();
		$idToObject = array();
		
		$objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->dataFolder));
		foreach($objects as $name => $object){
			if(!is_file($name)){
				continue;
			}
				
			$name = realpath($name);
			$objectFileName = basename($name);
				
			if(preg_match("/^(\d+)\.obj$/", $objectFileName, $matches)){
				$id = intval($matches[1]);
				$objectJSON = file_get_contents($name);
				$eventObject = json_decode($objectJSON);
				$eventObject->id=$id;
				
				$ids[] = $id;
				$idToObject[$id] = $eventObject;
			}
		}
		
		sort($ids);
		$idToObjectFunction = function($id) use($idToObject){
			return $idToObject[$id];
		};
		$result = array_map($idToObjectFunction, $ids);
		
		return new JsonResponse( $result );
	}
	
	/**
	 * Call this function to let the receiver receive a new event.
	 * 
	 * @param Request $request POST request object
	 * 
	 * @return Response the response to the receive event request
	 */
	public function receiveEvent(Request $request){
		$eventJSON = $request->getContent();
		
		if(empty($eventJSON)){
			return new Response("New event sent.", 400);
		}
		
		$event = json_decode($eventJSON);
		
		if(!empty($event)){
			foreach($this->handlers as $handler){
				if($handler instanceof EventListener){
					$handler->handleEvent($event);
				}
			}
		}
		
		$name = $event->name;
		
		$newID = $this->getMaxEventID()+1;
		$fileName = $this->dataFolder.'/'.$newID.'.obj';
		$result = file_put_contents($fileName, $eventJSON);
		
		if($result === false){
			return new Response("Could not store event.", 400);
		}
		
		return new Response("Successfully handled event $newID: $name.");
	}
	
	/**
	 * Send an event to a event receiver.
	 * @param string $name name of the event
	 * @param string $description description of the event
	 * @param string $baseURL the root URL of the event receiver REST API
	 * 
	 * @return boolean true on success, false on error
	 */
	public function sendEvent($name, $description, $baseURL='http://localhost/myapps/EventsAPI/rest/events'){
		
		date_default_timezone_set ( 'Europe/Amsterdam' );
		$apiClient = new \Swagger\Client\ApiClient ();
		$apiClient->getConfig ()->setHost ( $baseURL );
		
		$eventsApi = new EventsApi($apiClient);
		
		$event = new Event();
		$event->setName($name);
		$event->setDescription($description);
		$event->setDate(new \DateTime());
		
		try{
			$eventsApi->eventsPost($event);
		}
		catch(\Swagger\Client\ApiException $e){
			return false;
		}
		
		return true;
	}
	
}

?>