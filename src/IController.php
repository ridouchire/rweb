<?php

namespace Rweb;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface IController
{
    public function __invoke(Request $req, array $vars = []): Response;
}
