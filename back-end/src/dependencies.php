<?php

use Slim\App;

require __DIR__ . '/../src/controllers/event.php';
require __DIR__ . '/../src/controllers/observation.php';
require __DIR__ . '/../src/controllers/player.php';
require __DIR__ . '/../src/controllers/session.php';

return function (App $app) {
    $container = $app->getContainer();

    // monolog
    $container['logger'] = function ($c) {
        $settings = $c->get('settings')['logger'];
        $logger = new \Monolog\Logger($settings['name']);
        $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
        return $logger;
    };
	
	// Twig
	$container['twig'] = function ($c) {
		$template_path = $c->get('settings')['renderer']['template_path'];
		$loader = new \Twig\Loader\FilesystemLoader($template_path);
		$twig = new \Twig\Environment($loader, [
			'cache' => false,
			'cache_path' => $template_path . '/cache',
			'debug' => true,
			'auto_reload' => true
		]);
		
		$twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) {
			return '/' . ltrim($asset, '/');
		}));
		
		return $twig;
	};
	
	// Controllers
	$container['PlayerController'] = function ($c) {
		return new PlayerController($c['twig'], $c['logger']);
	};
	$container['EventController'] = function ($c) {
		return new EventController($c['twig'], $c['logger']);
	};
	$container['SessionController'] = function ($c) {
		return new SessionController($c['twig'], $c['logger']);
	};
	$container['ObservationController'] = function ($c) {
		return new ObservationController($c['twig'], $c['logger']);
	};
};
