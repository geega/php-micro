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
     * App constructor.
     */
    public function __construct(Router $route)
    {
        $this->route = $route;
    }
    
    public function run ()
    {
        $current_request = $this->route->getCurrent();
        $controller = new $current_request->controller($this->route->getRequest());
        $response = $controller->{$current_request->method}();

        if(is_object($response) && $response instanceof Response) {
            $response->render();
        } elseif(is_string($response)) {
            echo $response;
        }
    }
}