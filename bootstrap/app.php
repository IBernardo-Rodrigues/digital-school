<?php

require_once __DIR__.'/../vendor/autoload.php';

use \App\Utils\View;
use \App\Http\Middleware\Queue;
use \App\Http\Middleware;

if (session_status() != PHP_SESSION_ACTIVE) {
  session_start();
}

View::init([
  'URL' => URL
]);

Queue::setMap([
  'maintenance' => Middleware\Maintenance::class,
  'required-login' => Middleware\RequiredLogin::class,
  'required-admin' => Middleware\RequiredAdmin::class,
  'cache' => Middleware\Cache::class,
  'api' => Middleware\Api::class,
]);

Queue::setDefault([
  'maintenance'
]);

