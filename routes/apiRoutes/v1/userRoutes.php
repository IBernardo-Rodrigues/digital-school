<?php

use \App\Http\Response;
use \App\Controllers\Api\v1;

$router->get('/api/v1/user/{id}', [
  'middlewares' => [
    'api'
  ],
  function($id) {
    return new Response(200, v1\UserController::getUser($id), 'application/json');
  }
]);

$router->get('/api/v1/users', [
  'middlewares' => [
    'api'
  ],
  function() {
    return new Response(200, v1\UserController::getUsers(), 'application/json');
  }
]);

$router->post('/api/v1/users', [
  'middlewares' => [
    'api'
  ],
  function($request) {
    return new Response(200, v1\UserController::setUser($request), 'application/json');
  }
]);

$router->put('/api/v1/user/{id}', [
  'middlewares' => [
    'api'
  ],
  function($request, $id) {
    return new Response(200, v1\UserController::editUser($request, $id), 'application/json');
  }
]);

$router->delete('/api/v1/lesson/{id}', [
  'middlewares' => [
    'api'
  ],
  function($id) {
    return new Response(200, v1\UserController::deleteLesson($id), 'application/json');
  }
]);