<?php
namespace App\Http;

use \Exception;
use \App\Http\Middleware\Queue;
use \App\Controllers\Error\ErrorController;

class Router {
  private $routes = [];
  private $request;  
  private $url;  
  private $prefix;  
  private $contentType = "text/html";  

  public function __construct($url) {
    $this->request = new Request($this);
    $this->url = $url;
    $this->setPrefix();
  }

  public function get($route, $params) {
    $this->addRoute("GET", $route, $params);
  }
  
  public function post($route, $params) {
    $this->addRoute("POST", $route, $params);
  }
  
  public function put($route, $params) {
    $this->addRoute("PUT", $route, $params);
  }
  
  public function delete($route, $params) {
    $this->addRoute("DELETE", $route, $params);
  }

  public function redirect($path) {
    header("location: {$this->url}/$path");
    die;
  }

  public function getUri() {
    $uri = $this->request->getUri();

    $explodedUri = explode($this->prefix, $uri);
    
    $uriWithoutPrefix = end($explodedUri);

    return $uriWithoutPrefix != '/' ? rtrim(end($explodedUri), '/') : '/' ;
  }

  public function setPrefix() {
    $parseUrl = parse_url($this->url);
    $prefix = $parseUrl['path'] ?? '';

    $this->prefix = $prefix;
  }

  public function setContentType($contentType) {
    $this->contentType = $contentType;
  }

  private function addRoute($httpMethod, $route, $params) {
    foreach ($params as $key => $value) {
      if ($value instanceof \Closure) {
        $params['controller'] = $value;

        unset($params[$key]);
      }
    }

    $params['middlewares'] = $params['middlewares'] ?? []; 

    $params['variables'] = [];
    $patternVariable = '/{(.*?)}/';
    if (preg_match_all($patternVariable, $route, $matches)) {
      $route = preg_replace($patternVariable, '(.*?)', $route);
      $params['variables'] = $matches[1];
    }

    $patternRoute = str_replace("/", "\/", $route);
    $patternRoute = "/^$patternRoute$/";
    $this->routes[$patternRoute][$httpMethod] = $params;
  }

  private function getRoute() {
    $uri = $this->getUri();
    $httpMethod = $this->request->getHttpMethod();

    foreach ($this->routes as $patternRoute => $method) {
      if (preg_match($patternRoute, $uri, $matches)) {
        if (isset($method[$httpMethod])) {
          
          unset($matches[0]);
          $keys = $method[$httpMethod]['variables'];
          $method[$httpMethod]['variables'] = array_combine($keys, $matches);
          $method[$httpMethod]['variables']['request'] = $this->request;

          return $method[$httpMethod];
        }

        throw new Exception("Metódo não existe", 404);
      }  
    }

    throw new Exception("Página não existe", 404);
  }

  public function run() {
    try {
      $route = $this->getRoute();

      if (!isset($route['controller'])) {
        throw new Exception("Controlador não existe!", 400);
      }

      $args = [];
      $reflection = new \ReflectionFunction($route['controller']);
      foreach ($reflection->getParameters() as $parameter) {
        $parameterName = $parameter->getName();
        $args[$parameterName] = $route['variables'][$parameterName] ?? '';
      }
      return (new Queue($route['middlewares'], $route['controller'], $args))->next($this->request);
    } catch (Exception $e) {
      
      $errorCode = $e->getCode();
      $errorMessage = $e->getMessage();
      $contentType = $this->contentType;

      return new Response($errorCode, ErrorController::getError($errorMessage, $contentType), $contentType);
    }
  }
}