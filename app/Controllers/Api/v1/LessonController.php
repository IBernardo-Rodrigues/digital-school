<?php
namespace App\Controllers\Api\v1;

use \App\Models\Entity\LessonEntity;
use \App\Models\Entity\ModuleEntity;
use \App\Utils\FormValidation;

class LessonController {
  public static function getLessons() {
    $allLessons = (new LessonEntity)->getLessons();
    $lessonItems = [];

    foreach ($allLessons as $lesson) {
      $lessonItems[] = [
        'id' => $lesson->id,
        'name' => $lesson->lesson_name,
        'description' => $lesson->lesson_description,
        'url' => $lesson->lesson_url,
        'moduleId' => $lesson->lesson_module,
        'nameSanitized' => $lesson->lesson_name_sanitized
      ];
    }
    
    return $lessonItems;
  }

  public static function getLesson($id) {
    if (!is_numeric($id)) {
      throw new \Exception("A aula $id não é válido", 400);
    }

    $lesson = (new LessonEntity)->getLesson($id);

    if (!$lesson) {
      throw new \Exception("Aula não encontrado", 404);
    }

    return [
      'id' => $lesson->id,        
      'name' => $lesson->lesson_name,
      'description' => $lesson->lesson_description,
      'url' => $lesson->lesson_url,
      'moduleId' => $lesson->lesson_module,
      'nameSanitized' => $lesson->lesson_name_sanitized
    ];
  }

  public static function setLesson($request) {
    $postVars = $request->getPostVars();

    $name = $postVars['name'] ?? '';
    $description = $postVars['description'] ?? '';
    $url = $postVars['url'] ?? '';
    $moduleId = $postVars['moduleId'] ?? '';

    FormValidation::validateString($name);
    FormValidation::validateString($description);
    FormValidation::validateURL($url);
    
    if (!is_numeric($moduleId)) {
      throw new \Exception("Use um id de módulo válido", 400);
    }

    $module = (new ModuleEntity)->getModule($moduleId);

    if (!$module) {
      throw new \Exception("Módulo não foi encontrado", 400);
    }

    $sanitizedName = FormValidation::sanitizeStringForURL($name);

    $lessonExists = (new LessonEntity)->getLessonByNameAndModule($sanitizedName, $moduleId);

    if ($lessonExists) {
      throw new \Exception("Aula já existe", 400);
    }

    $lesson = new LessonEntity;

    $lesson->lesson_name = $name;
    $lesson->lesson_description = $description;
    $lesson->lesson_url = $url;
    $lesson->lesson_module = $moduleId;
    $lesson->lesson_name_sanitized = $sanitizedName;

    $lesson->save();

    return [
      "message" => "sucesso"
    ];
  }

  public static function editLesson($request, $id) {
    if (!is_numeric($id)) {
      throw new \Exception("Use um id válido", 400);    
    }

    $lesson = (new LessonEntity)->getLesson($id);

    if (!$lesson) {
      throw new \Exception("Aula não foi encontrada", 404);
    }

    $postVars = $request->getPostVars();

    $name = $postVars['name'] ?? '';
    $description = $postVars['description'] ?? '';
    $url = $postVars['url'] ?? '';
    $moduleId = $postVars['moduleId'] ?? '';

    FormValidation::validateString($name);
    FormValidation::validateString($description);
    FormValidation::validateURL($url);

    if (!is_numeric($moduleId)) {
      throw new \Exception("Use um id de módulo válido", 400);
    }

    $module = (new ModuleEntity)->getModule($moduleId);

    if (!$module) {
      throw new \Exception("Módulo não foi encontrado", 400);
    }

    $sanitizedName = FormValidation::sanitizeStringForURL($name);

    $lessonExists = (new LessonEntity)->getLessonByNameAndModule($sanitizedName, $moduleId);

    if ($lessonExists && $lessonExists->id != $lesson->id) {
      throw new \Exception("Nome já está sendo usado", 400);
    }

    $lesson->lesson_name = $name;
    $lesson->lesson_description = $description;
    $lesson->lesson_url = $url;
    $lesson->lesson_module = $module;
    $lesson->lesson_name_sanitized = $sanitizedName;

    $lesson->save();

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