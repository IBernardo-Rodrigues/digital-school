<?php
namespace App\Controllers\Admin;

use \App\Utils\View;
use \App\Models\Entity\UserEntity;
use \App\Models\Entity\LessonEntity;
use \App\Models\Entity\ModuleEntity;

class HomeController extends GenericPageController {
  public static function getHome() {
   $userAmount = (new UserEntity)->getUsers() ? count((new UserEntity)->getUsers()) : 0;
   $lessonAmount = (new LessonEntity)->getLessons() ? count((new LessonEntity)->getLessons()) : 0;
   $moduleAmount = (new ModuleEntity)->getModules() ? count((new ModuleEntity)->getModules()) : 0;

    $content = View::renderView('admin/home', [
      'lessonsAmount' => $lessonAmount,
      'modulesAmount' => $moduleAmount,
      'usersAmount' => $userAmount
    ]);

    return parent::getGenericPage('Home Admin - DS', $content, 'admin/home');
  }
}