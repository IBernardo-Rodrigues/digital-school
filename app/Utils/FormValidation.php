<?php
namespace App\Utils;

class FormValidation {
  public static function validateUsername($username) {
    if (!empty(trim($username))) {
      $namePattern = '/^[a-zA-Zá-úÁ-Ú" "]*$/';
      $isValid = preg_match($namePattern, $username, $matches);

      if ($isValid) {
        return filter_var($username, FILTER_SANITIZE_SPECIAL_CHARS);
      }

      throw new \Exception("Use apenas espaços e letras para seu nome");
      
    }

    throw new \Exception("Preencha seu nome");
  }

  public static function validateString($string) {
    if (!empty(trim($string))) {
      return trim(filter_var($string, FILTER_SANITIZE_SPECIAL_CHARS));
    }

    throw new \Exception("Preencha todos os campos");
  }

  public static function validateURL($url) {
    if (!empty(trim($url))) {
      $url = filter_var($url, FILTER_SANITIZE_URL);
      $url = filter_var($url, FILTER_SANITIZE_SPECIAL_CHARS);

      $url = filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_QUERY_REQUIRED);

      if ($url) return $url;

      throw new \Exception("Digite uma URL válida");
    }

    throw new \Exception("Preencha a URL");
  }

  public static function validateEmail($email) {
    if (!empty(trim($email))) {
      $email = filter_var($email, FILTER_SANITIZE_EMAIL);
      $email = filter_var($email, FILTER_SANITIZE_SPECIAL_CHARS);
      $email = filter_var($email, FILTER_VALIDATE_EMAIL);

      if ($email) return trim($email);

      throw new \Exception("Digite um email válido");
    }

    throw new \Exception("Preencha seu email");
  }

  public static function validatePassword($password) {
    if (!empty(trim($password))) {
      return filter_var($password, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    throw new \Exception("Preencha sua senha");
  }

  public static function sanitizeStringForURL($string) {
    $sanitizedName = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
    $sanitizedName = preg_replace('/[^\w\d" "]/', "", $sanitizedName);
    $sanitizedName = str_replace(" ", "-", $sanitizedName);
    $sanitizedName = strtolower($sanitizedName);

    return $sanitizedName;
  }
}