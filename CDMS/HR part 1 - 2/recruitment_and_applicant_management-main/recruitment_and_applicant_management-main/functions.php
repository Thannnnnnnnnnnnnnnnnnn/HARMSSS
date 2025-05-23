<?php

function dd($value)
{
    echo '<pre>';
    var_dump($value);
    echo '</pre>';

    die();
}

function abort($code = 404)
{
    http_response_code($code);

    require "views/error/{$code}.php";

    die();
}

function routeToController($uri, $routes)
{
    if (array_key_exists($uri, $routes)) {
        require $routes[$uri];
    } else {
        abort();
    }
}

function validate($value, &$errors)
{
    if (empty(trim($_POST[$value] ?? ''))) {
        $errors[$value] = "{$value} field is required.";
    }
}
