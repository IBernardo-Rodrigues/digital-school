<?php
namespace App\Http\Middleware;

class Maintenance {
  public function handle($request, $next) {
    if (MAINTENANCE) {
      throw new \Exception("Server em manutenção", 200);
    }

    return $next($request);
  }
}