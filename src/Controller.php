<?php


namespace Geega\Micro;


use Geega\Micro\Http\Request;
use Geega\Micro\Http\Response;

class Controller
{
    public $request = null;

    public $response;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->response = new Response();
    }

    public function redirect($url)
    {
        header('Location: '.$url);
        exit;
    }
}