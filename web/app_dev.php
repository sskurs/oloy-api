<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

require dirname(__DIR__).'/vendor/autoload.php';
Debug::enable();

$kernel = new AppKernel('dev', true);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
