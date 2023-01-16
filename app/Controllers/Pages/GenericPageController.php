<?php
namespace App\Controllers\Pages;

use \App\Utils\View;

class GenericPageController {
  private static function getHeader() {
    return View::renderView('pages/header');
  }

  private static function getFooter() {
    return View::renderView('pages/footer');
  }
  
  public static function getGenericPage($title ,$content, $cssJsFileName) {
    return View::renderView('pages/genericPage', [
      'title' => $title,
      'cssFileName' => $cssJsFileName,
      'jsFileName' => $cssJsFileName,
      'header' => self::getHeader(),
      'content' => $content,
      'footer' => self::getFooter()
    ]);
  }
}
