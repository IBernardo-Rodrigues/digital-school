<?php

use \App\Http\Response;
use \App\Controllers\Admin;

$router->get("/admin/modules", [
  "middlewares" => [
    "required-login",
    "required-admin"
  ],
  function($request) {
    return new Response(200, Admin\ModuleController::getModules($request));
  }
]);

$router->get("/admin/modules/new", [
  "middlewares" => [
    "required-login",
    "required-admin"
  ],
  function($request) {
    return new Response(200, Admin\ModuleController::getNewModule($request));
  }
]);

$router->post("/admin/modules/new", [
  "middlewares" => [
    "required-login",
    "required-admin"
  ],
  function($request) {
    return new Response(200, Admin\ModuleController::setNewModule($request));
  }
]);

$router->get("/admin/modules/{id}/edit", [
  "middlewares" => [
    "required-login",
    "required-admin"
  ],
  function($request, $id) {
    return new Response(200, Admin\ModuleController::getEditModule($request, $id));
  }
]);

$router->post("/admin/modules/{id}/edit", [
  "middlewares" => [
    "required-login",
    "required-admin"
  ],
  function($request, $id) {
    return new Response(200, Admin\ModuleController::setEditModule($request, $id));
  }
]);

$router->get("/admin/modules/{id}/delete", [
  "middlewares" => [
    "required-login",
    "required-admin"
  ],
  function($request, $id) {
    return new Response(200, Admin\ModuleController::setDeleteModule($request, $id));
  }
]);