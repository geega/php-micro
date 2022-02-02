<?php

namespace Geega\Micro\Validator\Rules;

class Required extends AbstractRule
{
    /**
     * @return string
     */
    public function getTpl()
    {
        return 'required';
    }

    /**
     * @return string
     */
    public function build()
    {
        return $this->getTpl();
    }

}