<?php
namespace App\Controllers\Api\v1;

use \App\Models\Entity\LessonEntity;
use \App\Models\Entity\ModuleEntity;
use \App\Utils\FormValidation;
use \App\Utils\FileManager;

class ModuleController {
  public static function getModules() {
    $allModules = (new ModuleEntity)->getModules() ?? [];
    $moduleItems = [];

    foreach ($allModules as $module) {
      $moduleItems[] = [
        'id' => $module->id,
        'module' => $module->module,
        'moduleSanitized' => $module->module_sanitized,
        'moduleCover' => $module->module_cover
      ];
    }
    
    return $moduleItems;
  }

  public static function getModule($id) {
    if (!is_numeric($id)) {
      throw new \Exception("O módulo $id não é válido", 400);
    }

    $module = (new ModuleEntity)->getModule($id);

    if (!$module) {
      throw new \Exception("Módulo não encontrado", 404);
    }

    return [
      'id' => $module->id,        
      'module' => $module->module,
      'moduleSanitized' => $module->module_sanitized,
      'moduleCover' => $module->module_cover
    ];
  }

  public static function setModule($request) {
    $postVars = $request->getPostVars();

    $name = $postVars['moduleName'] ?? '';
    $base64Img = $postVars['base64Img'] ?? '';
    $sanitizedName = FormValidation::sanitizeStringForURL($name);

    FormValidation::validateString($name);

    $lessonExists = (new ModuleEntity)->getModuleBySanitizedName($sanitizedName);

    if ($lessonExists) {
      throw new \Exception("Módulo com este nome já existe", 400);
    }

    $pathHTML = FileManager::uploadBase64Image($base64Img, "modules");

    if (!$pathHTML) {
      throw new \Exception("Não foi possivel adicionar a imagem", 1);
    }

    $module = new ModuleEntity;

    $module->module = $name;
    $module->module_sanitized = $sanitizedName;
    $module->module_cover = $pathHTML;

    $module->save();

    return [
      "message" => "sucesso"
    ];
  }

  public static function editModule($request, $id) {
    if (!is_numeric($id)) {
      throw new \Exception("Use um id válido", 400);    
    }

    $module = (new ModuleEntity)->getModule($id);

    if (!$module) {
      throw new \Exception("O módulo não foi encontrada", 404);
    }

    $postVars = $request->getPostVars();

    $name = $postVars['moduleName'] ?? '';
    $base64Img = $postVars['base64Img'] ?? '';
    $sanitizedName = FormValidation::sanitizeStringForURL($name);

    FormValidation::validateString($name);

    $moduleNameExists = (new ModuleEntity)->getModuleBySanitizedName($sanitizedName);

    if ($moduleNameExists && $moduleNameExists->id != $module->id) {
      throw new \Exception("Nome já está sendo usado", 400);
    }

    if ($base64Img) {
      $pathHTML = FileManager::uploadBase64Image($base64Img, "modules");
      
      if ($pathHTML) {
        $deleteLastCover = FileManager::delete($module->module_cover);
        $module->module_cover = $pathHTML;
      }
    }

    $module->module = $name;
    $module->module_sanitized = $sanitizedName;

    $module->save();

    return [
      "message" => "sucesso"
    ];
  }

  public static function deleteModule($id) {
    if (!is_numeric($id)) {
      throw new \Exception("Use um id válido", 1);
    }

    $module = (new ModuleEntity)->getModule($id);

    if (!$module) {
      throw new \Exception("O módulo não foi encontrada", 404);
    }

    $module->destroy();
    return [
      'message' => "sucesso"
    ];
  }
}