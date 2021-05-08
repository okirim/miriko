<?php
namespace App\controllers;

use App\core\Application;
use App\core\Request;

class BaseController
{
public Request $request;

    /**
     * BaseController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

}