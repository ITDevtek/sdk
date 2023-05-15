<?php

namespace devtek\sdk\interfaces;

/**
 * Model interface
 *
 * @version 1.0.0
 */
interface Model
{
    /**
     * Returns fields list as key-value pairs
     *
     * @return array
     */
    public function list(): array;

    /**
     * Checks if field exists
     *
     * @param string $name Field name
     * @return boolean `true` if field exists or `false` if not
     */
    public function has(string $name): bool;

    /**
     * Checks if field filled
     *
     * Field considered as filled if its value isn't empty
     *
     * @param string $name
     * @return boolean
     */
    public function filled(string $name): bool;

    /**
     * Validates field values
     *
     * @return boolean `true` if all field values is valid or `false` is not
     */
    public function validate(): bool;
}
