<?php
namespace App\Controllers\Admin;

use \App\Utils\View;
use \App\Utils\ModalManager;
use \App\Utils\FormValidation;
use \App\Models\Entity\UserEntity;

class UserController extends GenericPageController {
  public static function getUsers($request) {
    $content = View::renderView('admin/users', [
      'usersItems' => self::getUserItems(),
      'modals' => ModalManager::getModal($request, ['confirmation-delete']) ?? ''
    ]);

    return parent::getGenericPage('Usuários - DS', $content, 'admin/users');
  }

  public static function getUserItems() {
    $usersItems = '';
    $users = (new UserEntity)->getUsers() ?? [];

    foreach ($users as $user) {
      $usersItems .= View::renderView('admin/components/tr/tr-user', [
        'username' => $user->username,
        'email' => $user->email,
        'role' => $user->role,
        'id' => $user->id
      ]);
    }

    return $usersItems;
  }

  public static function getNewUser($request) {
    $content = View::renderView('admin/components/forms/form-user', [
      'action' => 'Adicionar',
      'valueName' => '',
      'valueEmail' => '',
      'valuePassword' => '',
      'roleOptions' => self::getRoleOptions(),
      'modals' => ModalManager::getModal($request) ?? ''
    ]);

    return parent::getGenericPage('Novo usuário - DS', $content, 'admin/form-users');
  }

  public static function setNewUser($request) {
    try {
      $postVars = $request->getPostVars();

      $name = FormValidation::validateUsername($postVars['username']);
      $email = FormValidation::validateEmail($postVars['email']);
      $password = FormValidation::validatePassword($postVars['password']);
      $role = FormValidation::validateString($postVars['role']);

      $userEntity = new UserEntity;
      $user = $userEntity->getUserByEmail($email);

      if ($user) {
        throw new \Exception("Email já cadastrado", 400);
      }

      $userEntity->username = $name;
      $userEntity->email = $email;
      $userEntity->password = password_hash($password, PASSWORD_DEFAULT);
      $userEntity->role = $role;
      $userEntity->auto_login_token = uniqid();

      $userEntity->save();

      $request->getRouter()->redirect("admin/users?status=created");
      die;
    } catch (\Exception $e) {
      $errorMessageEncoded = urlencode(rawurlencode($e->getMessage()));
      
      $request->getRouter()->redirect("admin/users?status=error&error=$errorMessageEncoded");
      die;
    }
  }

  public static function getEditUser($request, $id) {
    try {
      if (!is_numeric($id)) {
        throw new \Exception("Não foi possivel atualizar o usuário", 400);
      }

      $user = (new UserEntity)->getUser($id);

      if (!$user) {
        throw new \Exception("Usuário não existe", 400);
      }

      $content = View::renderView('admin/components/forms/form-user', [
        'action' => 'Editar',
        'valueName' => $user->username,
        'valueEmail' => $user->email,
        'valuePassword' => "",
        'roleOptions' => self::getRoleOptions($user->role),
        'modals' => ModalManager::getModal($request)
      ]);
  
      return parent::getGenericPage('Editar usuário - DS', $content, 'admin/form-users');  

    } catch (\Exception $e) {
      $errorMessageEncoded = urlencode(rawurlencode($e->getMessage()));
      
      $request->getRouter()->redirect("admin/users?status=error&error=$errorMessageEncoded");
      die;
    }
  }

  public static function setEditUser($request, $id) {
    try {
      if (!is_numeric($id)) {
        throw new \Exception("Não foi possivel atualizar o usuário", 400);
      }

      $user = (new UserEntity)->getUser($id);

      if (!$user) {
        throw new \Exception("Usuário não existe", 400);
      }

      $postVars = $request->getPostVars();

      $username = FormValidation::validateUsername($postVars['username']);
      $email = FormValidation::validateEmail($postVars['email']);
      $role = FormValidation::validateString($postVars['role']);
      $password = trim($postVars['password']);
      $password = $password ? password_hash(filter_var($password, FILTER_SANITIZE_SPECIAL_CHARS), PASSWORD_DEFAULT) : $user->password;
      
      $user->username = $username;
      $user->email = $email;
      $user->role = $role;
      $user->password = $password;
      $user->auto_login_token = uniqid();

      $user->save();

      $request->getRouter()->redirect("admin/users/$id/edit?status=updated");
      die;
    } catch (\Exception $e) {
      $errorMessageEncoded = urlencode(rawurlencode($e->getMessage()));
      
      $request->getRouter()->redirect("admin/users?status=error&error=$errorMessageEncoded");
      die;
    }
  }

  public static function setDeleteUser($request, $id) {
    try {
      if (!is_numeric($id)) {
        throw new \Exception("Não foi possivel remover o usuário", 400);
      }

      $user = (new UserEntity)->getUser($id);

      if (!$user) {
        throw new \Exception("Usuário não encontrado", 400);
      }

      $user->destroy();
      $request->getRouter()->redirect("admin/users?status=deleted");
      die;
    } catch (\Exception $e) {
      $errorMessageEncoded = urlencode(rawurlencode($e->getMessage()));
      
      $request->getRouter()->redirect("admin/users?status=error&error=$errorMessageEncoded");
      die;
    }
  } 

  public static function getRoleOptions($currentRole = "user") {
    $role = [
      'usuário',
      'admin',
    ];
    $roleOptions = '';

    foreach ($role as $roleOption) {
      if ($roleOption == $currentRole) {
        $roleOptions .= View::renderView('admin/components/options/option-user', [
          'roleName' => $roleOption,
          'selected' => 'selected'
        ]);
        continue;
      }

      $roleOptions .= View::renderView('admin/components/options/option-user', [
        'roleName' => $roleOption,
        'required' => ''
      ]);
    }

    return $roleOptions;
  }
}