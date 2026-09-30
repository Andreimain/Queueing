<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrustProxies as Middleware;
use Illumate\Http\Request;

class TrustProxies extends Middleware
{
protected $proxies = '*';
protected $headers =
\Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
\Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
\Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
\Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO;

}
