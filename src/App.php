<?php

namespace Rweb;

use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Rweb\IMiddleware;

class App extends Container
{
    private static $instance;

    private array $middlewares = [];

    public static function create(): App
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function run(Request $req): void
    {
        if (empty($this->middlewares)) {
            $res = new Response('500: no middlewares', Response::HTTP_INTERNAL_SERVER_ERROR);

            $res->send();

            exit(-1);
        }

        foreach ($this->middlewares as $middleware) {
            $middleware->process($req);
        }
    }

    public function addMiddleware(IMiddleware $middleware): self
    {
        array_push($this->middlewares, $middleware);

        return $this;
    }

    private function __construct()
    {
    }
}
