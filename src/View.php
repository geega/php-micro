<?php


namespace Geega\Micro;

use Geega\Micro\Http\ResponseInterface;

class View
{
    /**
     * @var \Geega\Micro\Http\ResponseInterface
     */
    protected $response;

    /**
     * @var string
     */
    protected $templatePath;

    public function __constructor(ResponseInterface $response, ?string $templatePath  = null)
    {
        $this->response = $response;

        if(null === $templatePath) {
            $this->templatePath = dirname(__FILE__).'/../views/';
        } elseif(is_string($templatePath)) {
            $this->templatePath = $templatePath;
        } else {
            throw new \Exception('Incorrect template path');
        }
    }

    /**
     * @return \Geega\Micro\Http\ResponseInterface
     */
    public function renderHtml($name, array $data=[]): ResponseInterface
    {
        $controller_template = $this->templatePath.$name.'.php';
        $layout_template = $this->templatePath.'layout.php';
        ob_start();
        require_once($controller_template);
        $contents = ob_get_contents();
        ob_end_clean();
        ob_start();
        require_once($layout_template);
        $html = ob_get_contents();
        ob_end_clean();

        $this->response->html($html);

        return $this->response;
    }
}