<?php

namespace Geega\Micro;

use Geega\Micro\Http\ResponseInterface;

interface ViewInterface
{
    /**
     * @param string $name
     * @param array $data
     * @return ResponseInterface
     */
    public function renderHtml(string $name, array $data=[]): ResponseInterface;
}