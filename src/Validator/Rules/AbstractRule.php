<?php

namespace Geega\Micro\Validator\Rules;

abstract class AbstractRule implements RuleInterface
{
    /**
     * @return string
     */
    abstract public function getTpl();

    /**
     * @return string
     */
    abstract public function build();

    /**
     * @return string
     */
    public function getRuleAsString()
    {
        return $this->build();
    }
}