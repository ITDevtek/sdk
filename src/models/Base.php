<?php

namespace devtek\sdk\models;

use devtek\sdk\interfaces\Model;
use ErrorException;

/**
 * Base model class
 *
 * @version 1.0.0
 */
abstract class Base implements Model
{
    /**
     * Fields
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Readonly mode
     *
     * @var boolean
     */
    protected $readonly = false;

    /**
     * Constructor
     *
     * @param array $fields List of fields and its values _(not required)_
     * @return void
     */
    public function __construct(array $fields = [])
    {
        $this->fields = array_filter($fields, function ($value) {
            return !empty($value);
        });
    }

    /**
     * Magic setter
     *
     * @param string $name Field name
     * @param mixed $value Field value
     * @return void
     * @throws ErrorException If readonly mode enabled for model
     */
    public function __set($name, $value)
    {
        if ($this->readonly) {
            throw new ErrorException('Unable assign value to field. Model ' . static::class . ' is readonly');
        }

        $this->fields[$name] = $value;
    }

    /**
     * Magic getter
     *
     * @param string $name Field name
     * @return mixed Field value
     */
    public function __get($name)
    {
        return $this->fields[$name] ?: null;
    }

    /**
     * {@inheritDoc}
     */
    public function list(): array
    {
        return $this->fields;
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $name): bool
    {
        return isset($this->fields[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function filled(string $name): bool
    {
        return !empty($this->fields[$name]);
    }

    /**
     * Validates model fields
     *
     * @return boolean `true` if all field values is valid or `false` if not
     */
    public function validate(): bool
    {
        return true;
    }
}
