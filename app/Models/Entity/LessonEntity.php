<?php
namespace App\Models\Entity;

use CoffeeCode\DataLayer\DataLayer;
use CoffeeCode\DataLayer\Connect;

class LessonEntity extends DataLayer {

  public function __construct() {
    parent::__construct(
      "lessons", 
      [
        "lesson_name",
        "lesson_url", 
        "lesson_module",
        "lesson_name_sanitized",
      ],
      "id",
      false
    );
  }

  public function getLesson($id) {
    return $this->findById($id);
  }
  
  public function getLessons() {
    return $this->find()->fetch(true);
  }

  public function getLessonsByModule($moduleId) {
    return $this->find("lesson_module = :moduleId", "moduleId=$moduleId")->fetch(true);
  }

  // getLesson
  public function getLessonByNameAndModule($name, $module) {
    return $this->find(
      "lesson_name_sanitized = :name AND lesson_module = :module",
      "name=$name&module=$module"
    )->fetch();
  }

  // getModules
  public function getFirstLessonsAndModules() {
    $pdo = Connect::getInstance();

    $query = "SELECT modules.module, modules.module_sanitized, 
    modules.module_cover, lessons.lesson_name, 
    lessons.lesson_name_sanitized
    FROM lessons JOIN modules 
    ON modules.id = lessons.lesson_module GROUP BY modules.module ORDER BY lessons.id ASC";

    $result = $pdo->query($query);
    
    return $result;
  }
  
  public function getLessonsAndModuleNames() {
    $pdo = Connect::getInstance();

    $query = "SELECT lessons.id, lessons.lesson_name,
              lessons.lesson_url,modules.module
              FROM lessons JOIN modules 
              ON modules.id = lessons.lesson_module
              ORDER BY lessons.id ASC";

    $result = $pdo->query($query);

    return $result;
  }
  
  public function deleteLessonsByModule($moduleId) {
    $pdo = Connect::getInstance();
    $query = "DELETE FROM lessons WHERE lesson_module = $moduleId";
    $result = $pdo->query($query);

    return $result;
  }
}