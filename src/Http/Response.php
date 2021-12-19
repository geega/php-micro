<?php


namespace Geega\Micro\Http;


class Response implements ResponseInterface
{
    /**
     * Tamplate name and renader as html
     * @param $html
     * @return Response
     */
    public function html($html) {
        $this->headers['Content-Type'] = 'text/html';
        $this->content = $html;
        return $this;
    }

    /**
     * Data as json
     * @param $data
     * @return $this
     */
    public function json($data)
    {
        $this->headers['Content-Type'] = 'application/json';
        $jsonData = [
            'data' => $data,
        ];

        $this->content = json_encode($jsonData);
        return $this;
    }

    /**
     * Render content with headers
     */
    public function render()
    {
        if($this->headers) {
            foreach ($this->headers as $headerName => $headerValue) {
                header($headerName.':'.$headerValue);
            }
        }
        echo $this->content;
    }
}