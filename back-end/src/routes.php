<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
return function (App $app) {
    $container = $app->getContainer();
	
	$app->get('/players[/{sort_field}[/{sort_order}]]', 'PlayerController:list_players');
	$app->get('/player/{player_id}', 'PlayerController:player_details');
	
	$app->get('/session/{session_id}', 'SessionController:session_details');
	$app->get('/session/stats/{session_id}', 'SessionController:session_stats');
	$app->get('/session/distance/{session_id}', 'SessionController:distance_to_goal');
	$app->get('/session/graph/{session_id}', 'SessionController:session_graph');
	
	$app->post('/player', 'PlayerController:add_player');
	$app->post('/event', 'EventController:add_event');
	$app->post('/session', 'SessionController:add_session');
	$app->post('/observation', 'ObservationController:add_observation');
	
	$app->redirect('/', './players');
};


?>