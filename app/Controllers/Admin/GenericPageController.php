<?php
namespace App\Controllers\Admin;

use \App\Utils\View;

class GenericPageController {
  private static function getHeader() {
    return View::renderView('admin/header');
  }

  private static function getFooter() {
    return View::renderView('admin/footer');
  }
  
  public static function getGenericPage($title, $content, $cssJsFileName) {
    return View::renderView('admin/genericPage', [
      'title' => $title,
      'cssFileName' => $cssJsFileName,
      'jsFileName' => $cssJsFileName,
      'header' => self::getHeader(),
      'content' => $content,
    ]);
  }
}
