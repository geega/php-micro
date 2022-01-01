<?php


namespace Geega\Micro;


use Geega\Micro\Http\Response;

class App
{
    /**
     * @var Router
     */
    private $route;

    /**
     * @var string
     */
    private $viewsPath;

    /**
     * App constructor.
     */
    public function __construct(Router $route, string $viewsPath)
    {
        $this->route = $route;
        $this->viewsPath = $viewsPath;
    }
    
    public function run ()
    {
        $current_request = $this->route->getCurrent();
        $controller = new $current_request->controller($this->route->getRequest(), $this->createResponse(), $this->createView());
        $response = $controller->{$current_request->method}();

        if(is_object($response) && $response instanceof Response) {
            $response->render();
        } elseif(is_string($response)) {
            echo $response;
        }
    }

    public function createResponse()
    {
        return new Response();
    }

    public function createView()
    {
        return new View($this->createResponse(), $this->viewsPath);
    }


}