<?php
namespace App\Controllers\Auth;

class LogoutController {
  public static function setLogout($request) {
    if (session_status() == PHP_SESSION_ACTIVE) {
      unset($_SESSION['user']);
    }

    if (isset($_COOKIE['user-token'])) {
      setcookie(
        'user-token',
        "",
        time() - 0,
        "/"
      );
    }

    $request->getRouter()->redirect("login");
  }
}