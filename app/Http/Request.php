<?php
namespace App\Http;

class Request {
  private $router;
  private $httpMethod;
  private $uri;
  private $queryParams = [];
  private $postVars = [];
  private $headers = [];

  public function __construct($router) {
    $this->router = $router;
    $this->httpMethod = $_SERVER['REQUEST_METHOD'];
    $this->queryParams = $_GET ?? [];
    $this->headers = getallheaders();
    $this->setPostVars();
    $this->setUri();
  }

  private function setPostVars() {
    if ($this->httpMethod == 'GET') return false;

    $postVars = $_POST ?? [];

    $inputRaw = file_get_contents('php://input');

    $this->postVars = (strlen($inputRaw) && empty($postVars)) ? json_decode($inputRaw, true) : $postVars;
  }

  private function setUri() {
    $uri = $_SERVER["REQUEST_URI"];

    $uri = explode('?', $uri);
    $this->uri = $uri[0];
  }

  public function getRouter() {
    return $this->router;
  }

  public function getHttpMethod() {
    return $this->httpMethod;
  }

  public function getQueryParams() {
    return $this->queryParams;
  }

  public function getPostVars() {
    return $this->postVars;
  }

  public function getFiles() {
    return $this->files;
  }

  public function getUri() {
    return $this->uri;
  }

}