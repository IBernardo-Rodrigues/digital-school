<?php

use \App\Http\Response;
use \App\Controllers\Admin;

$router->get("/admin/lessons", [
  "middlewares" => [
    "required-login",
    "required-admin"
  ],
  function($request) {
    return new Response(200, Admin\LessonController::getLessons($request));
  }
]);

$router->get("/admin/lessons/new", [
  "middlewares" => [
    "required-login",
    "required-admin"
  ],
  function($request) {
    return new Response(200, Admin\LessonController::getNewLesson($request));
  }
]);

$router->post("/admin/lessons/new", [
  "middlewares" => [
    "required-login",
    "required-admin"
  ],
  function($request) {
    return new Response(200, Admin\LessonController::setNewLesson($request));
  }
]);

$router->get("/admin/lessons/{id}/edit", [
  "middlewares" => [
    "required-login",
    "required-admin"
  ],
  function($request, $id) {
    return new Response(200, Admin\LessonController::getEditLesson($request, $id));
  }
]);

$router->post("/admin/lessons/{id}/edit", [
  "middlewares" => [
    "required-login",
    "required-admin"
  ],
  function($request, $id) {
    return new Response(200, Admin\LessonController::setEditLesson($request, $id));
  }
]);

$router->get("/admin/lessons/{id}/delete", [
  "middlewares" => [
    "required-login",
    "required-admin"
  ],
  function($request, $id) {
    return new Response(200, Admin\LessonController::setDeleteLesson($request, $id));
  }
]);