<?php

$router = new \Phalcon\Mvc\Router\Annotations(false);

// 404
$router->notFound([
    "controller" => "errors",
    "action" => "notFound"
]);

// Default Route
$router->add('/', 'Home::index')->setName('home');

// Standard Controllers
$router->addResource('Sessions', '/sessions');
$router->addGet('/login', 'Sessions::new')->setName('login');
$router->addGet('/logout', 'Sessions::destroy')->setName('logout');
$router->addResource('Buckets', '/buckets');

// API resources
$router->addResource('Api\Users', '/api/users');
$router->addResource('Api\Events', '/api/events');
$router->addResource('Api\Buckets', '/api/buckets');

// Return instance for dependency injection
return $router;
