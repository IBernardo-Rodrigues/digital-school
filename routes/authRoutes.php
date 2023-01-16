<?php

use \App\Http\Response;
use \App\Controllers\Auth;

$router->get("/signup", [
  function($request) {
    return new Response(200, Auth\SignUpController::getSignUp($request));
  }
]);

$router->post("/signup", [
  function($request) {
    return new Response(200, Auth\SignUpController::setSignUp($request));
  }
]);

$router->get("/login", [
  function($request) {
    return new Response(200, Auth\LoginController::getLogin($request));
  }
]);

$router->post("/login", [
  function($request) {
    return new Response(200, Auth\LoginController::setLogin($request));
  }
]);


$router->get("/logout", [
  function($request) {
    return new Response(200, Auth\LogoutController::setLogout($request));
  }
]);