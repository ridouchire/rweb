<?php

namespace Rweb;

use Symfony\Component\HttpFoundation\Request;

interface IMiddleware
{
    public function process(Request $req): void;
}
