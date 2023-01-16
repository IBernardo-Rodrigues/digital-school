<?php
namespace App\Models\Entity;

use CoffeeCode\DataLayer\DataLayer;

class ModuleEntity extends DataLayer {

  public function __construct() {
    parent::__construct(
      "modules",
      [
        "module",
        "module_sanitized"
      ],
      "id",
      false
    );
  }

  
  public function getModule($id) {
    return $this->find("id = :id", "id=$id")->fetch();
  }

  public function getModules() {
    return $this->find()->order("id ASC")->fetch(true);
  }
  
  public function getModuleBySanitizedName($module) {
    return $this->find("module_sanitized = :module", "module=$module")->fetch();
  }

  public function getModuleBySanitizedModule($module) {
    return $this->find("module_sanitized = :module", "module=$module")->fetch();
  }

  public function getFirstModule() {
    $firstModule = $this->getModules();
    return reset($firstModule);
  }
}