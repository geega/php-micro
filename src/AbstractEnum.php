<?php

namespace Geega\Micro;

abstract class AbstractEnum
{
    /**
     * @var array Cached constants list
     */
    static protected $list;

    /**
     * Get list constants
     *
     * @return mixed
     */
    public static function getList()
    {
        $calledClass = get_called_class();
        if (!isset(static::$list[$calledClass])) {
            $refClass = new \ReflectionClass($calledClass);
            $labels   = static::getLabels();
            foreach ($refClass->getConstants() as $constName => $constValue) {
                if (isset($labels[$constValue])) {
                    $label = $labels[$constValue];
                } else {
                    $label = lcfirst(strtolower($constName));
                }
                static::$list[$calledClass][$constValue] = $label;
            }
        }
        return static::$list[$calledClass];
    }

    /**
     * Get list labels
     * Class usage example:
     * class StatusEnum extends Enum
     * {
     *
     *      const ON = 1;
     *      const OFF = 0;
     *
     *
     *      public static function getLabels()
     *       {
     *           return [
     *              self::ON   => 'Turn on',
     *              self::OFF   => 'Turn off',
     *           ];
     *       }
     * }
     *
     * @return array
     */
    abstract public static function getLabels();

    /**
     * Get all values
     *
     * @return array
     */
    public static function getValues()
    {
        return array_keys(static::getList());
    }

    /**
     * Is valid value
     *
     * @param  $value
     * @return bool
     */
    public static function isValid($value)
    {
        return array_key_exists($value, static::getList());
    }

    /**
     * Get label by name
     *
     * @param  $value
     * @return mixed
     */
    public static function getLabel($value)
    {
        if (!static::isValid($value)) {
            throw new \InvalidArgumentException('Unknown constant "' . $value . '".');
        }
        return static::getList()[$value];
    }
}