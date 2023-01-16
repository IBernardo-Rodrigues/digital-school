<?php

use \App\Http\Response;
use \App\Controllers\Admin;

$router->get("/admin/users", [
  "middlewares" => [
    "required-login",
    "required-admin"
  ],
  function($request) {
    return new Response(200, Admin\UserController::getUsers($request));
  }
]);

$router->get("/admin/users/new", [
  "middlewares" => [
    "required-login",
    "required-admin"
  ],
  function($request) {
    return new Response(200, Admin\UserController::getNewUser($request));
  }
]);

$router->post("/admin/users/new", [
  "middlewares" => [
    "required-login",
    "required-admin"
  ],
  function($request) {
    return new Response(200, Admin\UserController::setNewUser($request));
  }
]);

$router->get("/admin/users/{id}/edit", [
  "middlewares" => [
    "required-login",
    "required-admin"
  ],
  function($request, $id) {
    return new Response(200, Admin\UserController::getEditUser($request, $id));
  }
]);

$router->post("/admin/users/{id}/edit", [
  "middlewares" => [
    "required-login",
    "required-admin"
  ],
  function($request, $id) {
    return new Response(200, Admin\UserController::setEditUser($request, $id));
  }
]);

$router->get("/admin/users/{id}/delete", [
  "middlewares" => [
    "required-login",
    "required-admin"
  ],
  function($request, $id) {
    return new Response(200, Admin\UserController::setDeleteUser($request, $id));
  }
]);

