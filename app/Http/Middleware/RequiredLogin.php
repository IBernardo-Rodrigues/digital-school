<?php
namespace App\Http\Middleware;

use \App\Models\Entity\UserEntity;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class RequiredLogin {

  public function isLogged() {
    try {
      $JWTCookie = $_COOKIE['user-token'] ?? '';

      if (!$JWTCookie) {
        return false;
      }

      $JWTDecoded = JWT::decode($JWTCookie, new Key(JWT_KEY, 'HS256'));

      $user = (new UserEntity)->getUser($JWTDecoded->id);

      $DBAutoLoginToken = $user->auto_login_token ?? '';

      if (!$DBAutoLoginToken) {
        return false;
      }

      $tokenValid = $JWTDecoded->auto_login_token == $DBAutoLoginToken ? true : false;
      
      if ($tokenValid) {
        if (session_status() != PHP_SESSION_ACTIVE) {
          session_start();
        }

        $_SESSION['user'] = [
          'id' => $user->id,
          'role' => $user->role
        ];
        
        return true;
      }

      return false;
    } catch (\Exception $e) {
      return false;
    }
  }

  public function handle($request, $next) {
    if (!$this->isLogged()) {
      $request->getRouter()->redirect("login");
      die;
    }

    return $next($request);
  }
}