<?php

namespace Framework;

use App\Controllers\ErrorController;

// use authorization
use Framework\Middleware\Authorize;

// $routes = require basePath('routes.php');

// if (array_key_exists($uri, $routes)) {
//   require basePath($routes[$uri]);
// } else {
//   // check if routes doesn't exist
//   http_response_code(404);
//   require basePath($routes['404']);
// }

// create Router class

class Router
{
  protected $routes = [];

  /**
   * add new route
   *
   * @param string $method
   * @param string $uri
   * @param string $action
   * @param array $middleware
   * @return void
   */
  // refactor
  public function registerRoute($method, $uri, $action, $middleware = [])
  {
    //list is used as array destructor
    list($controller, $controllerMethod) = explode('@', $action);

    $this->routes[] = [
      'method' => $method,
      'uri' => $uri,
      'controller' => $controller,
      'controllerMethod' => $controllerMethod,
      'middleware' => $middleware,
    ];
  }
  // public function registerRoute($method, $uri, $controller)
  // {
  //   $this->routes[] = [
  //     'method' => $method,
  //     'uri' => $uri,
  //     'controller' => $controller,
  //   ];
  // }

  /**
   * Add a GET route
   *
   * @param string $uri
   * @param string $controller
   * @param array middleware
   * @return void
   */
  public function get($uri, $controller, $middleware = [])
  {
    $this->registerRoute('GET', $uri, $controller, $middleware);
  }

  /**
   * Add a POST route
   *
   * @param string $uri
   * @param string $controller
   * @return void
   */
  public function post($uri, $controller, $middleware = [])
  {
    $this->registerRoute('POST', $uri, $controller, $middleware);
  }

  /**
   * Add a PUT route
   *
   * @param string $uri
   * @param string $controller
   * @return void
   */
  public function put($uri, $controller, $middleware = [])
  {
    $this->registerRoute('PUT', $uri, $controller, $middleware);
  }

  /**
   * Add a DELETE route
   *
   * @param string $uri
   * @param string $controller
   * @return void
   */
  public function delete($uri, $controller, $middleware = [])
  {
    $this->registerRoute('DELETE', $uri, $controller, $middleware);
  }

  // public function error($httpCode = 404)
  // {
  //   http_response_code($httpCode);
  //   loadView("error/{$httpCode}");
  //   exit;
  // }

  /**
   * create route
   *
   * @param string $uri
   * @param string $method
   * @return void
   */
  //refactor route
  public function route($uri)
  {
    // get the method
    $requestMethod = $_SERVER['REQUEST_METHOD'];

    // check for _method input
    if ($requestMethod === 'POST' && isset($_POST['_method'])) {
      $requestMethod = strtoupper($_POST['_method']);
    }

    foreach ($this->routes as $route) {


      // split current uri into segments
      $uriSegments = explode('/', trim($uri, '/'));

      // split the route URI into segments
      $routeSegments = explode('/', trim($route['uri'], '/'));

      $match = true;

      // check if number of segments matches
      if (count($uriSegments) === count($routeSegments) && strtoupper($route['method'] === $requestMethod)) {
        $params = [];

        $match = true;

        for ($i = 0; $i < count($uriSegments); $i++) {

          // if uri do not match and there is no param
          if ($routeSegments[$i] !== $uriSegments[$i] && !preg_match('/\{(.+?)\}/', $routeSegments[$i])) {
            $match = false;
            break;
          }

          if (preg_match('/\{(.+?)\}/', $routeSegments[$i], $matches)) {
            $params[$matches[1]] = $uriSegments[$i];
          }
        }

        if ($match) {

          // middleware
          foreach ($route['middleware'] as $middleware) {
            (new Authorize())->handle($middleware);
          }

          //extract controller and controller method
          $controller = 'App\\Controllers\\' . $route['controller'];
          $controllerMethod = $route['controllerMethod'];

          //instantiate the controller and call the method

          $controllerInstance = new $controller();
          $controllerInstance->$controllerMethod($params);
          return;
        }
      }

      // if ($route['uri'] === $uri && $route['method'] === $method) {
      //   // require basePath('App/' . $route['controller']);

      //   //extract controller and controller method
      //   $controller = 'App\\Controllers\\' . $route['controller'];
      //   $controllerMethod = $route['controllerMethod'];

      //   //instantiate the controller and call the method

      //   $controllerInstance = new $controller();
      //   $controllerInstance->$controllerMethod();
      //   return;
      // }
    }

    // $this->error(403);
    ErrorController::notFound();
  }
  //refactor route
  // public function route($uri, $method)
  // {
  //   foreach ($this->routes as $route) {
  //     if ($route['uri'] === $uri && $route['method'] === $method) {
  //       // require basePath('App/' . $route['controller']);

  //       //extract controller and controller method
  //       $controller = 'App\\Controllers\\' . $route['controller'];
  //       $controllerMethod = $route['controllerMethod'];

  //       //instantiate the controller and call the method

  //       $controllerInstance = new $controller();
  //       $controllerInstance->$controllerMethod();
  //       return;
  //     }
  //   }

  //   // $this->error(403);
  //   ErrorController::notFound();
  // }
  // public function route($uri, $method)
  // {
  //   foreach ($this->routes as $route) {
  //     if ($route['uri'] === $uri && $route['method'] === $method) {
  //       require basePath('App/' . $route['controller']);
  //       return;
  //     }
  //   }

  //   $this->error(403);
  // }
}
