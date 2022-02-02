<?php

namespace Geega\Micro\Validator\Rules;

class Numeric extends AbstractRule
{
    /**
     * @return string
     */
    public function getTpl()
    {
        return 'numeric';
    }

    /**
     * @return string
     */
    public function build()
    {
        return $this->getTpl();
    }

}