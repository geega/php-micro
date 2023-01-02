<?php


namespace Geega\Micro\Validator;


use Geega\Micro\Validator\Rules\RuleInterface;

class Attribute
{
    /**
     * @var string 
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $alias = null;

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @var array
     */
    protected $rawRules = [];

    /**
     * @param string      $name
     * @param null|string $alias
     */
    public function __construct($name, $alias = null)
    {
        $this->name = $name;
        $this->alias = $alias;
    }

    /**
     * @param RuleInterface $rule
     *
     * @return $this
     */
    public function addRule(RuleInterface $rule)
    {
        $this->rules[] = $rule;

        return $this;
    }

    /**
     * @param array $ruels
     *
     * @return $this
     */
    public function setRules(array $ruels)
    {
        $this->rules = $ruels;

        return $this;
    }

    /**
     * @param string $rule
     *
     * @return $this
     */
    public function addRawRule($rule)
    {
        $this->rawRules[] = $rule;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasAlias()
    {
        return !is_null($this->alias);
    }

    /**
     * @return string|null
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function build()
    {
        $rawRules = [];

        foreach ($this->rules as $rule) {
            $rawRules[] = $rule->getRuleAsString();
        }

        $rawRules = $rawRules + $this->rawRules;

        return join('|', $rawRules);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}