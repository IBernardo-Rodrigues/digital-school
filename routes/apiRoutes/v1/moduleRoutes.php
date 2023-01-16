<?php

use \App\Http\Response;
use \App\Controllers\Api\v1;

$router->get('/api/v1/module/{id}', [
  'middlewares' => [
    'api'
  ],
  function($id) {
    return new Response(200, v1\ModuleController::getModule($id), 'application/json');
  }
]);

$router->get('/api/v1/modules', [
  'middlewares' => [
    'api'
  ],
  function() {
    return new Response(200, v1\ModuleController::getModules(), 'application/json');
  }
]);

$router->post('/api/v1/modules', [
  'middlewares' => [
    'api'
  ],
  function($request) {
    return new Response(200, v1\ModuleController::setModule($request), 'application/json');
  }
]);

$router->put('/api/v1/module/{id}', [
  'middlewares' => [
    'api'
  ],
  function($request, $id) {
    return new Response(200, v1\ModuleController::editModule($request, $id), 'application/json');
  }
]);

$router->delete('/api/v1/module/{id}', [
  'middlewares' => [
    'api'
  ],
  function($id) {
    return new Response(200, v1\ModuleController::deleteModule($id), 'application/json');
  }
]);