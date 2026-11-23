<?php

namespace App\Http\Controllers;

use App\Services\Api\ApiClient;

abstract class Controller
{
    public function __construct(protected ApiClient $api) {}
}
