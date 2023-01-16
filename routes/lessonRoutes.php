<?php

use \App\Http\Response;
use \App\Controllers\Pages;

$router->get("/module/{moduleName}/{lessonName}", [
  "middlewares" => [
    "required-login",
    'cache'
  ],
  function($request, $moduleName, $lessonName) {
    return new Response(200, Pages\LessonController::getLesson($request, $moduleName, $lessonName));
  }
]);

// o controller dessa rota vai ver as variaveis do router(ou vai usar elas por meio do parametro)
// para achar o caminho do video
// obs: verificar se o video realmente exise para evitar conflito 
// ( verificar antes de redireiconar, ou veriicar e redirevionar )