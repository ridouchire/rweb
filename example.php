<?php

use Rweb\App;
use Rweb\Middlewares\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Rweb\IController;

require "vendor/autoload.php";

$app = App::create();

class Controller implements IController
{
    public function __invoke(Request $req, array $vars = []): Response
    {
        return new Response('test', Response::HTTP_OK);
    }
}

$app->addMiddleware((new Router())->addRoute('GET', '/', new Controller()));

$app->run(Request::createFromGlobals());
