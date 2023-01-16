<?php
namespace App\Utils;

class ModalManager {
  public static function getModal($request, $defaultModals = []) {
    $queryParams = $request->getQueryParams();
    $modalName = isset($queryParams['status']) ? $queryParams['status'] : '';
    $modals = '';

    foreach ($defaultModals as $defaultModalName) {
      $modals .= View::renderView("admin/components/modals/$defaultModalName-modal");
    }

    $modals .= View::renderView("admin/components/modals/$modalName-modal");

    return $modals;
  }
}
