<?php
namespace App\Controllers\Pages;

use \App\Utils\View;
use \App\Models\Entity\LessonEntity;
use \App\Models\Entity\UserEntity;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class HomeController extends GenericPageController {
  public static function getHome() {
    $content = View::renderView('pages/home', [
      'lastWatched' => self::getLastWatchedCard(),
      'cards' => self::getModuleCards()
    ]);

    return parent::getGenericPage('Home', $content, 'pages/home');
  }

  public static function getLastWatchedCard() {
    $JWTToken = $_COOKIE['user-token'];
    $userId = JWT::decode($JWTToken, new Key(JWT_KEY, 'HS256'))->id;
    
    $user = (new UserEntity)->getUser($userId);

    if ($user->last_watched) {
      $lastWatched = json_decode($user->last_watched);
      $lessonUrl = "module/$lastWatched->module_name/$lastWatched->lesson_name";
      $coverPath = $lastWatched->cover_path;
    }

    $lastWatchedCard = View::renderView('pages/components/module-card', [
      'lessonUrl' => $lessonUrl ?? '#',
      'coverPath' => $coverPath ?? URL.'/resources/img/no-last-watched.png',
    ]);

    return $lastWatchedCard;
  }

  public static function getModuleCards() {
    $modules = (new LessonEntity)->getFirstLessonsAndModules();
    $cards = '';

    if ($modules->rowCount() <= 0) {
      $cards = View::renderView('pages/components/module-card', [
        'lessonUrl' => '#',
        'coverPath' => URL.'/resources/img/no-module.png'
      ]);

      return $cards;
    }

    while ($module = $modules->fetchObject()) {
      $cards .= View::renderView('pages/components/module-card', [
        'lessonUrl' => 'module/'.$module->module_sanitized.'/'.$module->lesson_name_sanitized,
        'coverPath' => $module->module_cover
      ]);
    }


    return $cards;
  }
}