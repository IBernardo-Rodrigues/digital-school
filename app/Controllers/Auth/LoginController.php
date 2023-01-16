<?php
namespace App\Controllers\Auth;

use \App\Utils\View;
use \App\Utils\FormValidation;
use \App\Utils\ModalManager;
use \App\Models\Entity\UserEntity;
use Firebase\JWT\JWT;

class LoginController extends GenericPageController {
  public static function getLogin($request) {
    $content = View::renderView('auth/login', [
      'modals' => ModalManager::getModal($request)
    ]);

    return parent::getGenericPage('Login -  DS', $content, 'auth/login');
  }

  public static function setLogin($request) {
    try {
      $postVars = $request->getPostVars();

      $email = FormValidation::validateEmail($postVars['email']);
      $password = FormValidation::validatePassword($postVars['password']);

      $user = (new UserEntity)->getUserByEmail($email);

      if (!$user) {
        throw new \Exception("Email ou senha inválidos", 400);
      }

      $passwordVerify = password_verify($password, $user->password);
      
      if (!$passwordVerify) {
        throw new \Exception("Email ou senha inválidos", 400);
      }

      $autoLoginToken = uniqid().$user->id.time();
      $user->auto_login_token = $autoLoginToken;

      $user->save();

      $payload = [
        'id' => $user->id,
        'auto_login_token' => $autoLoginToken
      ];

      $jwt = JWT::encode($payload, JWT_KEY, 'HS256');

      setcookie(
        "user-token",
        $jwt,
        time() + (60 * 60 * 24 * 7),
        "/"
      );

      $_SESSION['user'] = [
        'id' => $user->id,
        'role' => $user->role
      ];
      
      if ($user->role == 'admin') {
        $request->getRouter()->redirect("admin");
        die;
      }
      
      $request->getRouter()->redirect("");
      die;
    } catch (\Exception $e) {
      $errorMessageEncoded = urlencode(rawurlencode($e->getMessage()));
      $request->getRouter()->redirect("login?status=error&error=$errorMessageEncoded");
    }
  }
}