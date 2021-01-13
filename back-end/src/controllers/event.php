<?php

class Event extends Model {
	public static $_table = 'events';
	public static $_id_column = 'id';
	
	public function session() {
        return $this->belongs_to('Session', 'session_id', 'id')->find_one();
    }
	
}

class EventController {
	private $logger;
	private $view;
	
	public function __construct($view, $logger) {
		$this->view = $view;
		$this->logger = $logger;
	}
	
	public function add_event($request, $response) {
		
		$requestBody = $request->getParsedBody();
		
		// return Bad Request if incorrect parameters
		if (empty($requestBody['action']) || empty($requestBody['session_id']) || empty($requestBody['time'])) {
			$this->logger->error("EventController::add_event - POST /event Bad Request (check parameters in file " . __FILE__ . ":" . __LINE__ . ")");
			return $response->withStatus(400);
		}
		
		// return Not Found if user_id not found
		if (Model::factory('Session')->where('id', $requestBody['session_id'])->count() < 1) {
			$this->logger->error("EventController::add_event - POST /event session_id:" . $requestBody['session_id'] . " Not Found");
			return $response->withStatus(404);
		}
		
		// Save new event in DB
		$event = Model::factory('Event')->create();
		$event->action = $requestBody['action'];
		$event->session_id = $requestBody['session_id'];
		$event->session_time = $requestBody['time'];
		$event->metadata = json_encode($requestBody['metadata']);
		$event->save();
        $this->logger->info("EventController::add_event - POST /event " . json_encode($requestBody));
        
		$response->getBody()->write("OK");
		return $response->withStatus(200);
	}
}

?>