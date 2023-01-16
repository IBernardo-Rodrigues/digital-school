<?php

use \App\Http\Response;
use \App\Controllers\Admin;

$router->get("/admin", [
  "middlewares" => [
    "required-login",
    "required-admin"
  ],
  function() {
    return new Response(200, Admin\HomeController::getHome());
  }
]);

require_once __DIR__.'/adminRoutes/lessonRoutes.php';
require_once __DIR__.'/adminRoutes/moduleRoutes.php';
require_once __DIR__.'/adminRoutes/userRoutes.php';