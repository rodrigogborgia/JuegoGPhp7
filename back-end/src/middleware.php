<?php



use Slim\App;

return function (App $app) {
	
	$app->add(function ($request, $response, $next) {
		
		// Connect to DB
		// TODO add settings to settings.php
		// ORM::configure('mysql:host=localhost;dbname=motivarnos_mof');
		// ORM::configure('username', 'root');
		// ORM::configure('password', '');
		ORM::configure('mysql:host=localhost;dbname=motivarnos');
		ORM::configure('username', 'root');
		ORM::configure('password', 'root');
		
		$response = $next($request, $response);
		return $response;
	});
	
};
