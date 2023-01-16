<?php
namespace App\Http\Middleware;

class RequiredAdmin {

  public function isAdmin() {
    $userData = $_SESSION['user'];
    
    if ($userData['role'] == 'admin') {
      return true;
    }

    return false;
  }

  public function handle($request, $next) {
    if (!$this->isAdmin()) {
      $request->getRouter()->redirect("");
      die;
    }

    return $next($request);
  }
}