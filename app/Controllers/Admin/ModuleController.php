<?php
namespace App\Controllers\Admin;

use \App\Utils\View;
use \App\Utils\FormValidation;
use \App\Utils\FileManager;
use \App\Utils\ModalManager;
use \App\Models\Entity\ModuleEntity;
use \App\Models\Entity\LessonEntity;

class ModuleController extends GenericPageController {
  public static function getModules($request) {
    $content = View::renderView('admin/modules', [
      'modules' => self::getModuleItems(),
      'modals' => ModalManager::getModal($request, ['confirmation-delete']) ?? '',
    ]);
    
    return parent::getGenericPage('Módulos - DS', $content, 'admin/modules');
  }

  public static function getModuleItems() {
    $moduleItems = '';

    $allModules = (new ModuleEntity)->getModules() ?? [];

    foreach ($allModules as $module) {
      $moduleItems .= View::renderView('admin/components/tr/tr-module', [
        'moduleName' => $module->module,
        'id' => $module->id
      ]);
    }

    return $moduleItems;
  }

  public static function getNewModule($request) {
    $content = View::renderView('admin/components/forms/form-module', [
      'action' => 'Adicionar',
      'currentCover' => '',
      'valueName' => '',
      'modal' => ModalManager::getModal($request) ?? ''
    ]);

    return parent::getGenericPage('Novo módulo - DS', $content, 'admin/form-module');
  }

  public static function setNewModule($request) {
    try {
      $postVars = $request->getPostVars();
      
      $name = FormValidation::validateString($postVars['name']);

      if (empty($_FILES['module-cover']['name'])) {
        throw new \Exception("Envie a capa do módulo");
      }

      $fileManager = new FileManager($_FILES['module-cover']);

      $uploadedFilePath = $fileManager->upload("modules");
      
      if (!$uploadedFilePath) {
        throw new \Exception("Envie uma imagem válida");
      }
      
      $moduleEntity = new ModuleEntity();

      $moduleEntity->module = $name;

      $sanitizedModule = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
      $sanitizedModule = preg_replace('/[^\w\d" "]/', "", $sanitizedModule);
      $sanitizedModule = str_replace(" ", "-", $sanitizedModule);
      $sanitizedModule = strtolower($sanitizedModule);

      $moduleEntity->module_sanitized = $sanitizedModule; 
      $moduleEntity->module_cover = $uploadedFilePath;

      $moduleExists = $moduleEntity->getModuleBySanitizedModule($sanitizedModule);

      if ($moduleExists) {
        throw new \Exception("Módulo já existe", 400);
      }

      $moduleEntity->save();

      $request->getRouter()->redirect("admin/modules?status=created");
    } catch (\Exception $e) {
      $errorMessageEncode = urlencode(rawurlencode($e->getMessage()));

      $request->getRouter()->redirect("admin/modules/new?status=error&error=$errorMessageEncode");
    }
  }

  public static function getEditModule($request, $id) {
    try {
      if (!is_numeric($id)) {
        throw new \Exception("Não foi possivel atualizar o módulo", 400);
      }

      $currentModule = (new ModuleEntity)->getModule($id);

      if (!$currentModule) {
        throw new \Exception("Módulo não existe", 400);
      }

      $currentCoverPath = $currentModule->module_cover;

      $currentCover = View::renderView('admin/components/current-cover', [
        'currentCoverPath' => $currentCoverPath
      ]);

      $content = View::renderView('admin/components/forms/form-module', [
        'action' => 'Editar',
        'valueName' => $currentModule->module,
        'currentCover' => $currentCover,
        'modal' => ModalManager::getModal($request) ? ModalManager::getModal($request) : ''
      ]);

      return parent::getGenericPage('Editar módulo - DS', $content, 'admin/form-module');
    } catch (\Exception $e) {
      $errorMessageEncode = urlencode(rawurlencode($e->getMessage()));

      $request->getRouter()->redirect("admin/modules?status=error&error=$errorMessageEncode");
    }
  }

  public static function setEditModule($request, $id) {
    try {
      if (!is_numeric($id)) {
        throw new \Exception("Não foi possivel atualizar o módulo", 400);
      }

      $postVars = $request->getPostVars();
      $files = !empty($_FILES['module-cover']['name']) ? $_FILES['module-cover'] : [];

      $name = FormValidation::validateString($postVars['name']);
      
      $sanitizedName = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
      $sanitizedName = preg_replace('/[^\w\d" "]/', "", $sanitizedName);
      $sanitizedName = str_replace(" ", "-", $sanitizedName);
      $sanitizedName = strtolower($sanitizedName);

      $module = (new ModuleEntity)->getModule($id);

      if (!$module) {
        throw new \Exception("Módulo não existe", 400);
      }
      
      $module->module = $name;
      $module->module_sanitized = $sanitizedName;

      if (!empty($files)) {
        $fileManager = new FileManager($files);
        $deleteCurrentCover = FileManager::delete($module->module_cover);

        $uploadedFilePath = $fileManager->upload("modules");
        $module->module_cover = $uploadedFilePath;
      }  

      $module->save();
      $request->getRouter()->redirect("admin/modules/$id/edit?status=updated");
    } catch (\Exception $e) {
      $errorMessageEncode = urlencode(rawurlencode($e->getMessage()));

      $request->getRouter()->redirect("admin/modules?status=error&error=$errorMessageEncode");
      die;
    }
  }

  public static function setDeleteModule($request, $id) {
    try {
      if (!is_numeric($id)) {
        throw new \Exception("Não foi possivel remover o módulo", 400);
      }

      $findModule = (new ModuleEntity)->getModule($id);

      if (!$findModule) {
        throw new \Exception("Módulo não encontrado", 404);
      }
    
      $deleModuleLessons = (new LessonEntity)->deleteLessonsByModule($id);

      $deleteCover = FileManager::delete($findModule->module_cover);
      $findModule->destroy();
      $request->getRouter()->redirect("admin/modules?status=deleted");
      die;
    } catch(\Exception $e) {
      $errorMessageEncode = urlencode(rawurlencode($e->getMessage()));

      $request->getRouter()->redirect("admin/modules?status=error&error=$errorMessageEncode");
      die;
    }
  }
}

