<?php
namespace App\Utils;

class CacheFile {

  public static function getFilePath($hash) {
    $cacheDir = CACHE_DIR;

    if (!file_exists($cacheDir)) {
      mkdir($cacheDir, 0777, true);
    }

    return "$cacheDir/$hash";
  }

  public static function getContentCache($hash, $expiration) {
    $cacheFilePath = self::getFilePath($hash);

    if (!file_exists($cacheFilePath)) {
      return false;
    }

    $lastUpdateFileTime = filemtime($cacheFilePath);
    $diffTime = time() - $lastUpdateFileTime;

    if ($diffTime > $expiration) {
      return false;
    }

    $serializedContent = file_get_contents($cacheFilePath);

    return unserialize($serializedContent);
  }

  public static function storageCache($hash, $content) {
    $serializedContent = serialize($content);

    $cacheFilePath = self::getFilePath($hash);

    return file_put_contents($cacheFilePath, $serializedContent);
  }

  public static function getCache($hash, $expiration, $function) {
    if ($content = self::getContentCache($hash, $expiration)) {
      return $content;
    }

    $content = $function();

    self::storageCache($hash, $content);

    return $content;
  }
}