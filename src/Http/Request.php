<?php


namespace Geega\Micro\Http;


use Geega\Micro\Http\Exceptions\NotFoundParamException;

class Request implements RequestInterface
{
    /**
     * @var string
     */
    public $requestMethod;

    /**
     * @var string
     */
    public $uri;

    /**
     * @var array|null
     */
    public $queryParams;

    /**
     * @var array
     */
    public $post;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->requestMethod = 'GET';
        $this->uri = '/';

        if (isset($_SERVER['REQUEST_METHOD'])) {
            $this->requestMethod =  $_SERVER['REQUEST_METHOD'];
        }

        if (isset($_SERVER['REQUEST_URI'])) {
            $this->uri = $_SERVER['REQUEST_URI'];
        }

        $parameters = parse_url($this->uri);

        $query = [];
        if(isset($parameters['query'])) {
            parse_str($parameters['query'], $query);
        }


        $this->uri = $parameters['path'];
        $this->queryParams = $query;
        $this->post = $_POST;
    }


    /**
     * Get param from query
     *
     * @deprecated
     * @param      $name
     * @return     mixed
     * @throws     \Exception
     */
    public function getParam($name)
    {
        return $this->getQueryParamOrFail($name);
    }


    /**
     * @param  $name
     * @return mixed
     * @throws NotFoundParamException
     */
    public function getQueryParamOrFail($name)
    {
        if (isset($this->queryParams[$name])) {
            return $this->queryParams[$name];
        } else {
            throw new NotFoundParamException(sprintf("Params '%s' not found", $name));
        }
    }

    public function getPostParamOrFail($name)
    {
        if (isset($this->post[$name])) {
            return $this->post[$name];
        } else {
            throw new NotFoundParamException(sprintf("Post params '%s' not found", $name));
        }
    }
    /**
     * @param  $name
     * @param  null $default
     * @return mixed
     * @throws \Exception
     */
    public function getQueryParam($name, $default = null)
    {
        if(isset($this->queryParams[$name])) {
            return $this->queryParams[$name];
        }

        return $default;
    }


    /**
     * @param  $name
     * @param  null $default
     * @return mixed
     * @throws \Exception
     */
    public function getPostParam($name, $default = null)
    {
        if(isset($this->post[$name])) {
            return $this->post[$name];
        }

        return $default;
    }
}