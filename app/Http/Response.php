<?php
namespace App\Http;

class Response {
  private $httpCode = 200;
  private $content;
  private $contentType = "text/html";
  private $headers = [];

  public function __construct($httpCode, $content, $contentType = "text/html") {
    $this->httpCode = $httpCode;
    $this->content = $content;
    $this->setContentType($contentType);
  }

  private function setContentType($contentType) {
    $this->contentType = $contentType;
    $this->addHeader('Content-Type', $contentType);
  }

  private function addHeader($key, $value) {
    $this->headers[$key] = $value;
  }

  private function sendHeaders() {
    http_response_code($this->httpCode);

    foreach ($this->headers as $key => $value) {
      header("$key: $value");
    }
  }

  public function sendResponse() {
    $this->sendHeaders();

    switch ($this->contentType) {
      case 'text/html':
        echo $this->content;
        break;
      case 'application/json':
        echo json_encode($this->content, JSON_UNESCAPED_UNICODE);
        break;
    }
  }
}
