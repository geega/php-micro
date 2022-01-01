<?php


namespace Geega\Micro;


use Geega\Micro\Http\Request;
use Geega\Micro\Http\Response;

class Controller
{
    public $request = null;

    public $response;

    public $view;

    public function __construct(Request $request, Response $response, View $view)
    {
        $this->request = $request;
        $this->response = $response;
        $this->view = $view;
    }

    public function redirect($url)
    {
        header('Location: '.$url);
        exit;
    }

    public function getView()
    {
        return $this->view;
    }
}