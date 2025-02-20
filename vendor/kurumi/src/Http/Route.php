<?php

namespace Kurumi\Http;

/** ---------------
 *  new Route
 *  sebuah class yang di buat untuk menangani
 *  sebuah routing, mulai dengan http method sampai routing segmen
 */

class Route
{

  private static array $routes;

  private static $param;

  public static function get(string $path, $handler)
  {
    self::addRoute("GET", self::segmen($path), $handler);
  }

  public static function post(string $path, $handler)
  {
    self::addRoute("POST", $path, $handler);
  }

  private static function addRoute(string $method, string $path, $handler)
  {
    self::$routes[$method . $path] = [
      "method" => $method,
      "path" => $path,
      "handler" => $handler
    ];
  }

  private static function segmen($path)
  {
    if (preg_match('/{.*}/i', $path)) {
      $p = explode("/", $path);
      $q = explode('/', parse_url($_SERVER["REQUEST_URI"])["path"]);
      $d = array_search('{:segmen}', $p);
    }

    $path = @preg_replace('/{.*}/i', @$q[$d], $path);
    self::$param =  @$q[$d];
    return $path;
  }

  public static function run()
  {
    $RequestUri = parse_url($_SERVER["REQUEST_URI"]);
    $RequestPath = $RequestUri['path'];
    $method = $_SERVER["REQUEST_METHOD"];
    $handler = null;

    foreach (self::$routes as $route) {
      if ($route["path"] === $RequestPath xor $route["path"] . "/" === $RequestPath) {
        if ($route["method"] === $method) $handler = $route["handler"];
      }
    }

    if (!$handler) {
      if (explode('/', $RequestPath)[1] === 'public') {
        $handler = fn () => require '.' . $RequestPath;
      } else {
        $handler = fn () => include './vendor/kurumi/src/Handling/Web/404.phtml';
      }
    }

    call_user_func_array($handler, [
      self::$param,
      array_merge($_GET, $_POST)
    ]);
  }
}
