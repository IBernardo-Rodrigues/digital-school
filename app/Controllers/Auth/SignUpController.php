<?php
namespace App\Controllers\Auth;

use \App\Utils\View;
use \App\Utils\FormValidation;
use \App\Utils\ModalManager;
use \App\Models\Entity\UserEntity;
use Firebase\JWT\JWT;

class SignUpController extends GenericPageController {
  public static function getSignUp($request) {
    $content = View::renderView("auth/signup", [
      'modals' => ModalManager::getModal($request) ?? ''
    ]);

    return parent::getGenericPage("SignUp - DS", $content, "auth/signup");
  }

  public static function setSignUp($request) {

    try {
      $postVars = $request->getPostVars();

      $username = FormValidation::validateUsername($postVars['username']);
      $email = FormValidation::validateEmail($postVars['email']);
      $password = FormValidation::validatePassword($postVars['password']);

      $user = (new UserEntity)->getUserByEmail($email);

      if ($user) {
        throw new \Exception("O email já está cadastrado!", 400);
      }

      $user = new UserEntity;
      $autoLoginToken = uniqid().time().uniqid();

      $user->username = $username;
      $user->email = $email;
      $user->password = password_hash($password, PASSWORD_DEFAULT);
      $user->role = "usuário";
      $user->auto_login_token = $autoLoginToken;

      $user->save();

      $payload = [
        'id' => $user->id,
        'auto_login_token' => $autoLoginToken,
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

      $request->getRouter()->redirect("");

    } catch(\Exception $e) {
      $errorEncoded = urlencode(rawurlencode($e->getMessage()));

      $request->getRouter()->redirect("signup?status=error&error=$errorEncoded");
      die;
    }

  }
}