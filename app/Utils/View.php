<?php
namespace App\Utils;

class View {

  public static $vars = [];

  public static function init($vars = []) {
    self::$vars = $vars;
  }

  public static function getViewContent($view) {
    $filePath = __DIR__."/../../resources/view/$view.html";
    return file_exists($filePath) ? file_get_contents($filePath) : '';
  }

  public static function renderView($view, $vars = []) { 
    $viewContent = self::getViewContent($view);
    
    $vars = array_merge(self::$vars, $vars);

    $keys = array_map(function($key) {
      return "{{" .$key. "}}";
    }, array_keys($vars));

    return str_replace($keys, array_values($vars), $viewContent);
  }

}