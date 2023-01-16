<?php
namespace App\Http\Middleware;

class Queue {
  public static $map = [];
  public static $default = [];
  public $middlewares;
  public $controller;
  public $args;

  public function __construct($middlewares, $controller, $args) {
    $this->middlewares = array_merge(self::$default, $middlewares);
    $this->controller = $controller;
    $this->args = $args;
  }

  public static function setMap($map) {
    self::$map = $map;
  }

  public static function setDefault($default) {
    self::$default = $default;
  }

  public function next($request) {
    if (empty($this->middlewares)) return call_user_func_array($this->controller, $this->args);

    $middleware = array_shift($this->middlewares);

    if (!isset(self::$map[$middleware])) {
      throw new \Exception("Middleware error", 400);
    }

    $queue = $this;
    $next = function($request) use($queue) {
      return $queue->next($request);
    };

    return (new self::$map[$middleware])->handle($request, $next);
  }
}