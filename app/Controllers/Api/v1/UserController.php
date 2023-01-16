<?php
namespace App\Controllers\Api\v1;

use \App\Models\Entity\UserEntity;
use \App\Utils\FormValidation;

class UserController {
  public static function getUsers() {
    $allUsers = (new UserEntity)->getUsers();
    $usersItems = [];

    foreach ($allUsers as $user) {
      $usersItems[] = [
        'id' => $user->id,        
        'username' => $user->username,
        'email' => $user->email,
        'password' => $user->password,
        'role' => $user->role,
        'autoLoginToken' => $user->auto_login_token,
        'lastWatched' => $user->last_watched,
        'profileImg' => $user->profile_img
      ];
    }
    
    return $usersItems;
  }

  public static function getUser($id) {
    if (!is_numeric($id)) {
      throw new \Exception("O usuário $id não é válido", 400);
    }
    
    $user = (new UserEntity)->getUser($id);

    if (!$user) {
      throw new \Exception("Usuário não encontrado", 404);
    }

    return [
      'id' => $user->id,        
      'username' => $user->username,
      'email' => $user->email,
      'password' => $user->password,
      'role' => $user->role,
      'autoLoginToken' => $user->auto_login_token,
      'lastWatched' => $user->last_watched,
      'profileImg' => $user->profile_img
    ];
  }

  public static function setUser($request) {
    $postVars = $request->getPostVars();

    $username = $postVars['username'] ?? '';
    $email = $postVars['email'] ?? '';
    $password = $postVars['password'] ?? '';
    $role = $postVars['role'] ?? 'usuário';

    $username = FormValidation::validateUsername($username);
    $email = FormValidation::validateEmail($email);
    $password = FormValidation::validatePassword($password);
    $role = FormValidation::validateString($role);
    
    $emailExists = (new UserEntity)->getUserByEmail($email);

    if ($emailExists) {
      throw new \Exception("Esse email já está cadastrado", 400);
    }

    $user = new UserEntity;

    $user->username = $username;
    $user->email = $email;
    $user->password = password_hash($password, PASSWORD_DEFAULT);
    $user->role = $role;
    $user->auto_login_token = uniqid();

    $user->save();

    if ($user->fail) {
      throw new \Exception("Algo deu errado", 400);
    }

    return [
      "message" => "sucesso"
    ];
  }

  public static function editUser($request, $id) {
    if (!is_numeric($id)) {
      throw new \Exception("Use um id válido", 400);    
    }

    $user = (new UserEntity)->getUser($id);

    if (!$user) {
      throw new \Exception("Usuário não foi encontrada", 404);
    }

    $postVars = $request->getPostVars();

    $username = $postVars['username'] ?? '';
    $email = $postVars['email'] ?? '';
    $password = $postVars['password'] ?? '';
    $role = $postVars['role'] ?? 'usuário';

    $username = FormValidation::validateUsername($username);
    $email = FormValidation::validateEmail($email);
    $role = FormValidation::validateString($role);
    $password = $password ? password_hash(FormValidation::validatePassword($password), PASSWORD_DEFAULT) : $user->password;

    $emailExists = (new UserEntity)->getUserByEmail($email);

    if ($emailExists && $emailExists->id != $user->id) {
      throw new \Exception("Esse email já está sendo usado", 400);
    }

    $user->username = $username;
    $user->email = $email;
    $user->password = $password;
    $user->role = $role;

    $user->save();

    return [
      "message" => "sucesso"
    ];
  }

  public static function deleteLesson($id) {
    if (!is_numeric($id)) {
      throw new \Exception("Use um id válido", 1);
    }

    $lesson = (new LessonEntity)->getLesson($id);

    if (!$lesson) {
      throw new \Exception("Aula não foi encontrada", 404);
    }

    $lesson->destroy();
    return [
      'message' => "sucesso"
    ];
  }
}