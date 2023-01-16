<?php
namespace App\Http\Middleware;

use \App\Utils\CacheFile;

class Cache {
  public function isCacheable($request) {
    if (CACHE_TIME <= 0) {
      return false;
    }

    if ($request->getHttpMethod() != 'GET') {
      return false;
    }

    $headers = getallheaders();

    if (isset($headers['Cache-Control']) && $headers['Cache-Control'] == 'no-control') {
      return false;
    }

    return true;
  }

  public function getHash($request) {
    $uri = $request->getRouter()->getUri();
    
    $queryParams = $request->getQueryParams();
    array_shift($queryParams);// <- it removes the the key of friendly url([url])
    
    $uri .= !empty($queryParams) ? '?'.http_build_query($queryParams) : '';
    $uri = 'route-'.preg_replace( '/[^\w\d]/', "-", ltrim($uri, '/'));

    return $uri == 'route-' ? 'route-home' : $uri;
  }

  public function handle($request, $next) {
    if (!$this->isCacheable($request)) return $next($request);

    $hash = $this->getHash($request);
    
    return CacheFile::getCache($hash, CACHE_TIME, function() use($request, $next) {
      return $next($request);
    });
  }
}