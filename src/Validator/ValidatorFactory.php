<?php
// "rakit/validation": "1.0.0"

namespace Geega\Micro\Validator;


class ValidatorFactory
{
    /**
     * @param array $inputs
     * @param array $attributes
     *
     * @return \Rakit\Validation\Validation
     */
    public function createValidation(array $inputs, array $attributes)
    {
        $validator = new Validator;

        $validation = $validator->make($inputs, $this->buildAttributes($attributes));

        $aliases = [];
        foreach ($attributes as $attribute) {
            if ($attribute->hasAlias()) {
                $aliases[$attribute->getName()] = $attribute->getAlias();
            }

        }

        $validation->setAliases($aliases);

        return $validation;
    }

    /**
     * @param array $attributes
     */
    public function buildAttributes(array $attributes)
    {
        $result = [];

        foreach ($attributes as $attribute) {
            $result[$attribute->getName()] = $attribute->build();
        }

        return $result;
    }
}