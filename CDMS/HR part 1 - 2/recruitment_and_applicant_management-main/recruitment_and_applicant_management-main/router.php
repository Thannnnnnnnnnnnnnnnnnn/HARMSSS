<?php

$routes = require 'routes.php';
// this separate the query and path 
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

routeToController($uri, $routes);


// class Router
// {
//     public function get() {}

//     public function post() {}

//     public function delete() {}

//     public function patch() {}

//     public function put() {}
// }
