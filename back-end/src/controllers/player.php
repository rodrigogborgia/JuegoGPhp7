<?php

class Player extends Model {
	public static $_table = 'players';
	public static $_id_column = 'id';
	
	public function observation() {
        return $this->has_one('Observation', 'player_id', 'id')->find_one();
    }
	
	public function sessions() {
        return $this->has_many('Session', 'player_id', 'id')->find_many();
    }
}

class PlayerController {
	private $logger;
	private $view;
	
	public function __construct($view, $logger) {
		$this->view = $view;
		$this->logger = $logger;
	}
	
	public function list_players($request, $response, $args) {
		
        $this->logger->info("PlayerController::list_players - GET /");
		
		$players = Model::factory('Player');
		
		if (isset($args['sort_field'])) {
			switch ($args['sort_order']) {
				case 'desc':
					$players = $players->order_by_desc($args['sort_field']);
					break;
				case 'asc':
				default:
					$players = $players->order_by_asc($args['sort_field']);
			}
		}
		
		return $this->view->render('players.html', ['players' => $players->find_array()]);
	}
	
	public function player_details($request, $response, $args) {
		
		// return Not Found if user_id not found
		if (Model::factory('Player')->where('id', $args['player_id'])->count() < 1) {
			$this->logger->error("PlayerController::player_details - GET /player_details player_id:" . $args['player_id'] . " Not Found");
			return $response->withStatus(404);
		}
		
		$this->logger->info("PlayerController::player_details - GET /player player_id:" . $args['player_id']);
		return $this->view->render('player.html', ['player' => Model::factory('Player')->find_one($args['player_id'])]);
	}
	
	public function add_player($request, $response) {
		
		$requestBody = $request->getParsedBody();
		
		// Bad Request if not correct parameters
		if (empty($requestBody['name'])) {
			$this->logger->error("PlayerController::add_player - POST /player Bad Request (check parameters in file " . __FILE__ . ":" . __LINE__ . ")");
			return $response->withStatus(400);
		}
		
		// Save new player in DB
		$user = Model::factory('Player')->create();
		$user->name = $requestBody['name'];
		$user->save();
        $this->logger->info("PlayerController::add_player - POST /player " . json_encode($requestBody));

		$response->getBody()->write($user->id());
		return $response->withStatus(200);
	}
}

?>