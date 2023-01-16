<?php
namespace App\Controllers\Pages;

use \App\Utils\View;
use \App\Models\Entity\LessonEntity;
use \App\Models\Entity\ModuleEntity;
use \App\Models\Entity\UserEntity;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class LessonController extends GenericPageController {
  public static function getLesson($request, $moduleName, $lessonName) {
    $module = (new ModuleEntity)->getModuleBySanitizedName($moduleName);
    
    if (!$module) {
      $firstModule  = (new ModuleEntity)->getModules() ?? [];
      $firstModule  = reset($firstModule);

      if (!$firstModule) {
        $request->getRouter()->redirect("");
        die;
      } 

      $firstModuleLesson = (new LessonEntity)->getLessonsByModule($firstModule->id) ?? []; 
      $firstModuleLesson = reset($firstModuleLesson);

      if (!$firstModuleLesson) {
        $request->getRouter()->redirect("");
        die;
      }
      
      $firstModuleName = $firstModule->module_sanitized;
      $firstModuleLessonName = $firstModuleLesson->lesson_name_sanitized;
      
      $request->getRouter()->redirect("module/$firstModuleName/$firstModuleLessonName");
      die;
    }

    $lesson = (new LessonEntity)->getLessonByNameAndModule($lessonName, $module->id);

    if (!$lesson) {
      $allModuleLessons = (new LessonEntity)->getLessonsByModule($module->id) ?? [];

      if (empty($allModuleLessons)) {
        $request->getRouter()->redirect("");
        die;
      }

      $firstLessonModule = $allModuleLessons[0]->lesson_name_sanitized;

      $request->getRouter()->redirect("module/$moduleName/$firstLessonModule");
      die;
    }

    $lessonsLinks = self::getLessonsLinks($module);
    $prefixYoutube = "https://www.youtube.com/watch?";
    $videoId = $lesson->lesson_url;

    $videoId = explode($prefixYoutube, $videoId);
    $videoId = implode("", $videoId);
    $videoId = explode("&", $videoId);

    foreach ($videoId as $key => $value) {
      if (str_contains($value, "v=")) {
        $videoId = str_replace("v=", "", $value);
      }
    }

    $lastWatchedData = json_encode([
      "module_name" => $module->module_sanitized,
      "lesson_name" => $lesson->lesson_name_sanitized,
      "cover_path" => $module->module_cover
    ]);

    $JWTToken = $_COOKIE['user-token'];
    $userId = JWT::decode($JWTToken, new Key(JWT_KEY, 'HS256'))->id;
    
    $user = (new UserEntity)->getUser($userId);

    $user->last_watched = $lastWatchedData;
    
    $user->save();    

    $content = View::renderView('pages/lesson', [
      'lessonTitle' => ucfirst($lesson->lesson_name),
      'moduleName' => ucfirst($module->module),
      'lessonsLinks' => $lessonsLinks,
      'videoId' => $videoId
    ]);

    return parent::getGenericPage('Aula', $content, 'pages/lesson');
  }

  public static function getLessonsLinks($module) {
    $links = '';
    
    $allModuleLessons = (new LessonEntity)->getLessonsByModule($module->id) ?? [];

    foreach ($allModuleLessons as $value) {
      $links .= View::renderView('pages/components/lesson-link', [
        'moduleName' => $module->module_sanitized,
        'lessonNameURL' => $value->lesson_name_sanitized,
        'lessonName' => $value->lesson_name
      ]);
    }
    
    return $links;
  }
}