<?php

class Observation extends Model {
	public static $_table = 'observations';
	public static $_id_column = 'id';
	
	public function player() {
		return $this->belongs_to('Player', 'player_id', 'id')->find_one();
    }
}

class ObservationController {
	private $logger;
	private $view;
	
	public function __construct($view, $logger) {
		$this->view = $view;
		$this->logger = $logger;
	}
	
	public function add_observation($request, $response) {
		
		$requestBody = $request->getParsedBody();
		
		// Bad Request if not correct parameters
		if (empty($requestBody['text']) || empty($requestBody['user_id'])) {
			$this->logger->error("ObservationController::add_observation - POST /observation Bad Request (check parameters in file " . __FILE__ . ":" . __LINE__ . ")");
			return $response->withStatus(400);
		}
		
		// Save new session in DB
		$observation = Model::factory('Observation')->create();
		$observation->text = $requestBody['text'];
		$observation->player_id = $requestBody['user_id'];
		$observation->save();
        $this->logger->info("ObservationController::add_observation - POST /observation " . json_encode($requestBody));

		$response->getBody()->write("OK");
		return $response->withStatus(200);
	}
}

?>