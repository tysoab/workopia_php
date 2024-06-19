<?php

// import autoloader
require __DIR__ . '/../vendor/autoload.php';

use Framework\Router;
use Framework\Session;

// start session
Session::start();

require '../helpers.php';


// require(basePath('Framework/Router.php'));
// require basePath('Framework/Database.php'); //done with autoloader

// // custom autoloader function
// spl_autoload_register(function ($class) {

//   $path = basePath('Framework/' . $class . '.php');

//   if ($path) {
//     require $path;
//   }
// });


// loadView('home');

// // create basic routes array
// $routes = [
//   '/' => 'controllers/home.php',
//   '/listings' => 'controllers/listings/index.php',
//   '/listings/create' => 'controllers/listings/create.php',
//   '404' => 'controllers/error/404.php',
// ];

// moved to routes.php


// instantiate the Router
$router = new Router();
// get route
$routes = require basePath('routes.php');
// get uri
// $uri = $_SERVER['REQUEST_URI'];
//refactor uri to avoid query string e.g index?id=12
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// get the method
// $method = $_SERVER['REQUEST_METHOD']; //move to Router

// $router->route($uri, $method);
$router->route($uri);

// moved to router
// if (array_key_exists($uri, $routes)) {
//   require(basePath($routes[$uri]));
// } else {
//   // check if routes doesn't exist
//   require(basePath($routes['404']));
// }