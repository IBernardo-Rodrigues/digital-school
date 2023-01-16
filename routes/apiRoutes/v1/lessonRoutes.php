<?php

use \App\Http\Response;
use \App\Controllers\Api\v1;

$router->get('/api/v1/lesson/{id}', [
  'middlewares' => [
    'api'
  ],
  function($id) {
    return new Response(200, v1\LessonController::getLesson($id), 'application/json');
  }
]);

$router->get('/api/v1/lessons', [
  'middlewares' => [
    'api'
  ],
  function() {
    return new Response(200, v1\LessonController::getLessons(), 'application/json');
  }
]);

$router->post('/api/v1/lessons', [
  'middlewares' => [
    'api'
  ],
  function($request) {
    return new Response(200, v1\LessonController::setLesson($request), 'application/json');
  }
]);

$router->put('/api/v1/lesson/{id}', [
  'middlewares' => [
    'api'
  ],
  function($request, $id) {
    return new Response(200, v1\LessonController::editLesson($request, $id), 'application/json');
  }
]);

$router->delete('/api/v1/lesson/{id}', [
  'middlewares' => [
    'api'
  ],
  function($id) {
    return new Response(200, v1\LessonController::deleteLesson($id), 'application/json');
  }
]);