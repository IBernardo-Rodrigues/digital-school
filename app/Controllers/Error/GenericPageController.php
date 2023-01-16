<?php
namespace App\Controllers\Error;

use \App\Utils\View;

class GenericPageController {
  private static function getHeader() {
    return View::renderView('error/header');
  }

  private static function getFooter() {
    return View::renderView('error/footer');
  }
  
  public static function getGenericPage($title, $content, $cssJsFileName) {
    return View::renderView('error/genericPage', [
      'title' => $title,
      'cssFileName' => $cssJsFileName,
      'jsFileName' => $cssJsFileName,
      'content' => $content
    ]);
  }
}
