<?php
namespace App\Controllers\Admin;

use \App\Utils\View;
use \App\Utils\FileManager;
use \App\Utils\FormValidation;
use \App\Utils\ModalManager;
use \App\Models\Entity\LessonEntity;
use \App\Models\Entity\ModuleEntity;

class LessonController extends GenericPageController {
  public static function getLessons($request) {
    $content = View::renderView('admin/lessons', [
      'lessons' => self::getLessonsItems(),
      'modals' => ModalManager::getModal($request, ['confirmation-delete']) ?? ''
    ]);

    return parent::getGenericPage('Aulas - DS', $content, 'admin/lessons');
  }

  public static function getLessonsItems() {
    $lessonItems = ''; 

    $allLessons = (new LessonEntity)->getLessonsAndModuleNames();

    while ($lesson = $allLessons->fetchObject(LessonEntity::class)) {

      $lessonItems .= View::renderView('admin/components/tr/tr-lesson', [
        'name' => ucfirst(urldecode($lesson->lesson_name)),
        'LessonURL' => $lesson->lesson_url,
        'module' => ucfirst(urldecode($lesson->module)),
        'id' => $lesson->id
      ]);
    }
    return $lessonItems;
  }

  public static function getNewLesson($request) {
    $content = View::renderView('admin/components/forms/form-lesson', [
      'action' => 'Adicionar',
      'modal' => ModalManager::getModal($request) ? ModalManager::getModal($request) : '',
      'moduleOptions' => self::getModuleOptions(),
      'valueName' => '',
      'valueUrl' => '',
    ]);

    return parent::getGenericPage('Nova aula - DS', $content, 'admin/form-lesson');
  }

  public static function getModuleOptions($moduleId = '') {
    $allModules = (new ModuleEntity)->getModules() ?? [];
    $moduleOptions = '';
    
    foreach ($allModules as $module) {
      if ($module->id == $moduleId) {
        $moduleOptions .= View::renderView('admin/components/options/option-module', [
          'moduleId' => $module->id,
          'moduleName' => $module->module,
          'selected' => 'selected'
        ]);

        continue;
      }
      
      $moduleOptions .= View::renderView('admin/components/options/option-module', [
        'moduleId' => $module->id,
        'moduleName' => $module->module,
        'selected' => ''
      ]);
    }
    
    return $moduleOptions;
  }
  
  public static function setNewLesson($request) {
    try {
      $postVars = $request->getPostVars();
      
      $name = FormValidation::validateString($postVars['name']);
      $URL = FormValidation::validateURL($postVars['lesson-url']);
      $module = FormValidation::validateString($postVars['lesson-module']);  

      $lessonEntity = new LessonEntity();

      $lessonEntity->lesson_name = $name;
      $lessonEntity->lesson_url = $URL;
      $lessonEntity->lesson_module = $module;

      $sanitizedName = FormValidation::sanitizeStringForURL($name);

      $lessonEntity->lesson_name_sanitized = $sanitizedName; 

      $lessonExists = $lessonEntity->getLessonByNameAndModule($sanitizedName, $module);

      if ($lessonExists) {
        throw new \Exception("Aula já existe", 400);
      }

      $module = (new ModuleEntity)->getModule($module);

      if (!$module) {
        throw new \Exception("Módulo não encontrado", 400);
      }

      $lessonEntity->save();

      $request->getRouter()->redirect("admin/lessons?status=created");
      die;
    } catch (\Exception $e) {
      $errorMessageEncoded = urlencode(rawurlencode($e->getMessage()));

      $request->getRouter()->redirect("admin/lessons/new?status=error&error=$errorMessageEncoded");
    }
  }

  public static function getEditLesson($request, $id) {
    try {
      if (!is_numeric($id)) {
        throw new \Exception("Não foi possivel atualizar a aula", 400);
      }

      $currentLesson = (new LessonEntity)->getLesson($id);

      if (!$currentLesson) {
        throw new \Exception("Aula não encontrada", 400);
      }

      $content = View::renderView('admin/components/forms/form-lesson', [
        'action' => 'Editar',
        'valueName' => $currentLesson->lesson_name,
        'valueUrl' => $currentLesson->lesson_url,
        'moduleOptions' => self::getModuleOptions($currentLesson->lesson_module),
        'modal' => ModalManager::getModal($request) ?? ''
      ]);

      return parent::getGenericPage('Editar aula - DS', $content, 'admin/form-lesson');  
    } catch (\Exception $e) {
      $errorMessageEncoded = urlencode(rawurlencode($e->getMessage()));

      $request->getRouter()->redirect("admin/lessons?status=error&error=$errorMessageEncoded");
    }
  }

  public static function setEditLesson($request, $id) {
    try {
      if (!is_numeric($id)) {
        throw new \Exception("Não foi possivel atualizar a aula", 400);
      }

      $postVars = $request->getPostVars();

      $name = FormValidation::validateString($postVars['name']);
      $URL = FormValidation::validateURL($postVars['lesson-url']);
      $module = FormValidation::validateString($postVars['lesson-module']);
      
      $lesson = (new LessonEntity)->findById($id);

      if (!$lesson) {
        throw new \Exception("Aula não existe", 400);
      }

      $module = (new ModuleEntity)->getModule($module);

      if (!$module) {
        throw new \Exception("Módulo não encontrado", 400);
      }

      $lesson->lesson_name = $name;
      $lesson->lesson_url = $URL;
      $lesson->lesson_module = $module;

      $lesson->save();
      $request->getRouter()->redirect("admin/lessons/$id/edit?status=updated");
    } catch (\Exception $e) {
      $errorMessageEncoded = urlencode(rawurlencode($e->getMessage()));

      $request->getRouter()->redirect("admin/lessons?status=error&error=$errorMessageEncoded");
    }
  }

  public static function setDeleteLesson($request, $id) {
    try {
      if (!is_numeric($id)) {
        throw new \Exception("Não foi possivel remover a aula", 400);
      }

      $findLesson = (new LessonEntity)->getlesson($id);
      
      if (!$findLesson) {
        throw new \Exception("Aula não encontrada", 404);
      }

      $findLesson->destroy();
      $request->getRouter()->redirect("admin/lessons?status=deleted");
      die;      
    } catch (\Exception $e) {
      $errorMessageEncoded = urlencode(rawurlencode($e->getMessage()));

      $request->getRouter()->redirect("admin/lessons?status=error&error=$errorMessageEncoded");
      die;
    }
  }
}