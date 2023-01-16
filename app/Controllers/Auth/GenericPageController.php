<?php
namespace App\Controllers\Auth;

use \App\Utils\View;

class GenericPageController {
  public static function getGenericPage($title, $content, $cssJsFileName) {
    return View::renderView('auth/genericPage', [
      'title' => $title,
      'cssFileName' => $cssJsFileName,
      'jsFileName' => $cssJsFileName,
      'content' => $content,
    ]);
  }
}
