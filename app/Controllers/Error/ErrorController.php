<?php
namespace App\Controllers\Error;

use \App\Utils\View;

class ErrorController extends GenericPageController {
  public static function getError($message, $contentType) {
    if ($contentType == "application/json") {
      return [
        "message" => $message
      ];
    }

    $content = View::renderView('error/page-not-found');

    return parent::getGenericPage('Página não encontrada - DS', $content, 'error/page-not-found');
  }
}