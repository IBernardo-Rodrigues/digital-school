<?php
namespace App\Controllers\Pages;

use \App\Utils\View;
use \App\Utils\FileManager;
use \App\Utils\ModalManager;
use \App\Utils\FormValidation;
use \App\Models\Entity\UserEntity;

class ProfileController extends GenericPageController {
  public static function getProfile($request) {
    $userId = $_SESSION['user']['id'];

    $user = (new UserEntity)->getUser($userId);

    $profileImg = $user->profile_img;

    $content = View::renderView('pages/profile', [
      'profileImg' => $profileImg ?? URL.'/resources/img/user-profile.png',
      'username' => $user->username,
      'email' => $user->email,
      'modals' => ModalManager::getModal($request)
    ]);

    return parent::getGenericPage('Perfil - DS', $content, 'pages/profile');
  }

  public static function setEditProfile($request) {
    try {
      $files = $_FILES['profile-img'] ?? [];
      $postVars = $request->getPostVars();

      if (!empty($files)) {
        $fileManager = new FileManager($files);

        $upload = $fileManager->upload("profiles");

        if (!$upload) {
          throw new \Exception("Adicione uma imagem válida", 400);
        }

        $userId = $_SESSION['user']['id'];

        $user = (new UserEntity)->getUser($userId);

        if ($user->profile_img) {
          $fileDeleted = FileManager::delete($user->profile_img);
        }

        $user->profile_img = $upload;

        $user->save();

        $request->getRouter()->redirect("profile?status=updated");
        die;
      }

      if (empty($postVars)) {
        throw new \Exception("Não foi possivel concluir a ação", 1);
      }

      $username = FormValidation::validateUsername($postVars['username']);
      $email = FormValidation::validateEmail($postVars['email']);
      $password = $postVars['password'];

      $user = (new UserEntity)->getUserByEmail($email);

      if (!$user) {
        $userId = $_SESSION['user']['id'];

        $user = (new UserEntity)->getUser($userId);
        $user->username = $username;
        $user->email = $email;

        if ($password) {
          $password = FormValidation::validatePassword($password);
          $user->password = password_hash($password, PASSWORD_DEFAULT);
        }

        $user->save();
        $request->getRouter()->redirect("profile?status=updated");  
      }

      if ($password) {
        $password = FormValidation::validatePassword($password);
        $user->password = password_hash($password, PASSWORD_DEFAULT);
      }

      $userId = $_SESSION['user']['id'];

      if ($userId != $user->id) {
        throw new \Exception("Email já está cadastrado", 1);
      }

      $user->username = $username;
      $user->email = $email;
      $user->save();

      $request->getRouter()->redirect("profile?status=updated");  
    } catch (\Exception $e) {
      $encodedUrlMessage = urlencode(rawurlencode($e->getMessage()));

      $request->getRouter()->redirect("profile?status=error&error=$encodedUrlMessage");
      die;
    }
  }
}