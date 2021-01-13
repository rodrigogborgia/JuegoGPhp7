<?php

class Session extends Model {
	public static $_table = 'sessions';
	public static $_id_column = 'id';
	
	public function player() {
        return $this->belongs_to('Player', 'player_id', 'id')->find_one();
    }
	
	public function events() {
		return $this->has_many('Event', 'session_id', 'id')->order_by_asc('session_time')->find_many();
	}

	public function last_event() {
		return $this->has_many('Event', 'session_id', 'id')->order_by_desc('session_time')->find_one();
	}
}

class SessionController {
	private $logger;
	private $view;
	
	public function __construct($view, $logger) {
		$this->view = $view;
		$this->logger = $logger;
	}
	
	public function add_session($request, $response) {
		
		$requestBody = $request->getParsedBody();
		
		// Bad Request if not correct parameters
		if (empty($requestBody['level']) || empty($requestBody['user_id'])) {
			$this->logger->error("SessionController::add_session - POST /session Bad Request (check parameters in file " . __FILE__ . ":" . __LINE__ . ")");
			return $response->withStatus(400);
		}
		
		// return Not Found if user_id not found
		if (Model::factory('Player')->where('id', $requestBody['user_id'])->count() < 1) {
			$this->logger->error("SessionController::add_session - POST /session player_id:" . $requestBody['user_id'] . " Not Found");
			return $response->withStatus(404);
		}
		
		// Save new session in DB
		$session = Model::factory('Session')->create();
		$session->level = $requestBody['level'];
		$session->player_id = $requestBody['user_id'];
		$session->save();
        $this->logger->info("SessionController::add_session - POST /session " . json_encode($requestBody));

		$response->getBody()->write($session->id());
		return $response->withStatus(200);
	}
	
	public function session_details($request, $response, $args) {
		
		// return Not Found if session_id not found
		if (Model::factory('Session')->where('id', $args['session_id'])->count() < 1) {
			$this->logger->error("SessionController::session_details - GET /session/id:" . $args['session_id'] . " Not Found");
			return $response->withStatus(404);
		}

		$session = Model::factory('Session')->find_one($args['session_id']);
		$events = $session->events();

		// return No Content if there are no events associated with session_id
		if (count($events) < 1) {
			$this->logger->error("SessionController::session_graph - GET /session/id:" . $args['session_id'] . " No Session Data");
			return $response->withStatus(402);
		}
		
		$session->actions = array(
			"MOVE" => $this->getEventActionCount($events, "MOVE"),
			"WRONG" => $this->getEventActionCount($events, "WRONG")
		);

		$mins = floor($session->last_event()->session_time / 60 % 60);
		$secs = floor($session->last_event()->session_time % 60);
		$session->stats = array(
			"Tiempo total de juego" => sprintf('%02d:%02d', $mins, $secs),
			"Promedio de tiempo entre errores" => sprintf("%01.2f", $this->getAverageBetweenActionTime($events, "WRONG")),
			"Promedio de movimiento correctos consecutivos" => sprintf("%01.2f", $this->getAverageConsecutiveActionCount($events, "MOVE"))
		);
		
		$this->logger->info("SessionController::session_details - GET /session/id:" . $args['session_id']);
		return $this->view->render('session.html', ['session' => $session]);
	}
	
	public function session_stats($request, $response, $args) {
		
		// return Not Found if session_id not found
		if (Model::factory('Session')->where('id', $args['session_id'])->count() < 1) {
			$this->logger->error("SessionController::session_details - GET /session/id:" . $args['session_id'] . " Not Found");
			return $response->withStatus(404);
		}

		$session = Model::factory('Session')->find_one($args['session_id']);

		// return No Content if there are no events associated with session_id
		if (count($session->events()) < 1) {
			$this->logger->error("SessionController::session_graph - GET /session/id:" . $args['session_id'] . " No Session Data");
			return $response->withStatus(402);
		}

		$session_data = array();
		$events = $session->events();
		$prev_event = current($events);
		$events_data = array(1, $prev_event->action, 0);
		while ($event = next($events)) {
			if ($prev_event->action == $event->action) {
				$events_data[0] += 1;
				$events_data[2] += $event->session_time - $prev_event->session_time;
			} else {
				array_push($session_data, $events_data);
				$events_data = array(1, $event->action, 0);
			}
			$prev_event = $event;
		}
		array_push($session_data, $events_data);

		$response->getBody()->write(json_encode($session_data));
		return $response->withStatus(200);
	}
	
	public function distance_to_goal($request, $response, $args) {
		
		// return Not Found if session_id not found
		if (Model::factory('Session')->where('id', $args['session_id'])->count() < 1) {
			$this->logger->error("SessionController::session_details - GET /session/id:" . $args['session_id'] . " Not Found");
			return $response->withStatus(404);
		}

		$session = Model::factory('Session')->find_one($args['session_id']);

		// return No Content if there are no events associated with session_id
		if (count($session->events()) < 1) {
			$this->logger->error("SessionController::session_graph - GET /session/id:" . $args['session_id'] . " No Session Data");
			return $response->withStatus(402);
		}

		$session_data = array();
		foreach ($session->events() as $event) {
			$metadata = json_decode($event->metadata);
			array_push($session_data, array($metadata->remaining, $event->session_time));
		}

		$response->getBody()->write(json_encode($session_data));
		return $response->withStatus(200);
	}
	
	public function session_graph($request, $response, $args) {
		
		// return Not Found if session_id not found
		if (Model::factory('Session')->where('id', $args['session_id'])->count() < 1) {
			$this->logger->error("SessionController::session_graph - GET /session/id:" . $args['session_id'] . " Not Found");
			return $response->withStatus(404);
		}

		$session = Model::factory('Session')->find_one($args['session_id']);

		// return No Content if there are no events associated with session_id
		if (count($session->events()) < 1) {
			$this->logger->error("SessionController::session_graph - GET /session/id:" . $args['session_id'] . " No Session Data");
			return $response->withStatus(402);
		}

		// process events in session
		$events = $session->events();
		$last_event_time = end($session->events())->session_time;
		$labels = ["0"];
		$move_data = ["0"];
		$ok_data = ["0"];
		$wrong_data = ["0"];

		if (!isset($_GET['group']) || $_GET['group'] == 0) {
			foreach ($events as $event) {
				array_push($labels, $event->session_time);
				array_push($move_data, 1);
				if ($event->action == "WRONG") {
					array_push($ok_data, "0");
					array_push($wrong_data, "1");
				} else {
					array_push($ok_data, "1");
					array_push($wrong_data, "0");
				}
			}
		} else {
			$min_filter = 0;
			for ($i = $_GET['group']; $i < $last_event_time; $i += $_GET['group']) {
				$ok = $this->getEventDataCount($events, "MOVE", $min_filter, $i);
				$wrong = $this->getEventDataCount($events, "WRONG", $min_filter, $i);

				array_push($labels, strval($i));
				array_push($ok_data, strval($ok));
				array_push($wrong_data, strval($wrong));
				array_push($move_data, strval($ok + $wrong));
				$min_filter = $i;
			}

			// get last stats (add last WIN to MOVE event)
			$ok = $this->getEventDataCount($events, "MOVE", $min_filter, $last_event_time) + (end($session->events())->action == "WIN" ? 1 : 0);
			$wrong = $this->getEventDataCount($events, "WRONG", $min_filter, $last_event_time);

			array_push($labels, $last_event_time);
			array_push($ok_data, strval($ok));
			array_push($wrong_data, strval($wrong));
			array_push($move_data, strval($ok + $wrong));
		}

		// return result
		$this->logger->info("SessionController::session_graph - GET /session/graph/id:" . $args['session_id']);
		
		$session_data = [
			"labels" => $labels,
			"datasets" => [
				array("data" => $move_data),
				array("data" => $ok_data),
				array("data" => $wrong_data)
			]
		];
		$response->getBody()->write(json_encode($session_data));
		return $response->withStatus(200);
	}

	private function getEventDataCount($events, $action, $min, $max) {
		assert($min != $max && $min < $max);
		$total_events = 0;
		foreach ($events as $event) {
			$session_time = floatval($event->session_time);
			if ( $event->action == $action
			&&   $session_time > $min
			&&   $session_time <= $max ) {
				$total_events += 1;
			}
		}

		return $total_events;
	}

	private function getEventActionCount($events, $action) {
		$total_actions = 0;
		foreach ($events as $event) {
			if ( $event->action == $action ) {
				$total_actions += 1;
			}
		}
		
		return $total_actions;
	}

	private function getAverageConsecutiveActionCount($events, $action) {
		$consecutive_actions = array();
		$is_counting = false;
		$current_count = 0;

		foreach ($events as $event) {
			if ( $event->action == $action ) {
				$is_counting = true;
				$current_count += 1;
			}
			else {
				if ($is_counting) {
					if ($current_count > 0)
						array_push($consecutive_actions, $current_count);
					
					$is_counting = false;
					$current_count = 0;
				}
			}
		}

		return array_sum($consecutive_actions) / count($consecutive_actions);
	}

	private function getAverageBetweenActionTime($events, $action) {
		$between_actions = array();
		$is_counting = false;
		$current_time = 0;

		foreach ($events as $event) {
			if ( $event->action == $action ) {
				$session_time = floatval($event->session_time);
				if ($current_time > 0) {
					array_push($between_actions, $session_time - $current_time);
				}
				$current_time = $session_time;
			}
		}
		
		return (count($between_actions) > 1) ?
			array_sum($between_actions) / count($between_actions) :
			0;
	}
}

?>