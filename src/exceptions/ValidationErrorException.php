<?php

namespace devtek\sdk\exceptions;

use Exception;

/**
 * Validation error
 *
 * @version 1.0.0
 */
class ValidationErrorException extends Exception
{
    /**
     * Field name
     *
     * @var string
     */
    protected $field;

    /**
     * Field value
     *
     * @var mixed|null
     */
    protected $value;

    /**
     * Constructor
     *
     * @param string $field Field name
     * @param mixed|null $value Value
     */
    public function __construct(string $field, $value = null)
    {
        $this->field = $field;
        $this->value = $value;
    }

    /**
     * Returns field name
     *
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Returns field value
     *
     * @return mixed|null
     */
    public function getValue()
    {
        return $this->value;
    }
}
