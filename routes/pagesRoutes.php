<?php

use \App\Http\Response;
use \App\Controllers\Pages;

$router->get("/", [
  "middlewares" => [
    "required-login",
  ],
  function() {
    return new Response(200, Pages\HomeController::getHome());
  }
]);

$router->get("/profile", [
  "middlewares" => [
    "required-login"
  ],
  function($request) {
    return new Response(200, Pages\ProfileController::getProfile($request));
  }
]);

$router->post("/profile", [
  "middlewares" => [
    "required-login"
  ],
  function($request) {
    return new Response(200, Pages\ProfileController::setEditProfile($request));
  }
]);