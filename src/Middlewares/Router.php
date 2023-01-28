<?php

namespace Rweb\Middlewares;

use SplObjectStorage;
use FastRoute\RouteParser\Std as Parser;
use FastRoute\DataGenerator\GroupCountBased as Generator;
use FastRoute\RouteCollector as Collector;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Rweb\IMiddleware;

class Router implements IMiddleware
{
    /** @var Collector */
    private $collector;

    /**
     * Router constructor
     *
     * @param Authenticator $authenticator
     */
    public function __construct()
    {
        $this->collector = new Collector(new Parser(), new Generator());
    }

    /**
     * Обрабатывает входящий запрос и запускает его обработчик
     *
     * @param Request $req
     *
     * @return void
     */
    public function process(Request $req): void
    {
        $dispatcher = new Dispatcher($this->collector->getData());

        $path = $req->getPathInfo();
        $path = preg_replace('/\/$/', '', $path);

        $routeInfo = $dispatcher->dispatch($req->getMethod(), $path);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                $res = new Response('not found', Response::HTTP_NOT_FOUND, ['Content-type' => 'text/html']);

                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $res = new Response('method not allowed', Response::HTTP_METHOD_NOT_ALLOWED, ['Content-type' => 'text/html']);

                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                try {
                    $res = call_user_func($handler, $req, $vars);
                } catch (\Throwable $e) {
                    $res = new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, ['Content-type' => 'text/html']);
                }

                break;
            default:
                $res = new Response('internal server error', Response::HTTP_INTERNAL_SERVER_ERROR, ['Content-type' => 'text/html']);

                break;
        }

        $res->send();

        exit(0);
    }

    /**
     * Добавляет обработчик маршрута
     *
     * @param string   $method   HTTP-метод
     * @param string   $path     Маршрут
     * @param callable $callback Обработчик маршрута
     *
     * @throws InvalidArgumentException
     *
     * @return self
     */
    public function addRoute(string $method, string $path, callable $callback): self
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException();
        }

        $this->collector->addRoute($method, $path, $callback);

        return $this;
    }
}
