<?php

use Rweb\App;
use Rweb\Middlewares\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require "vendor/autoload.php";

$app = App::create();

$app->addMiddleware((new Router())->addRoute('GET', '/', function (Request $req) {
    return new Response('test', Response::HTTP_OK);
}));

$app->run(Request::createFromGlobals());
