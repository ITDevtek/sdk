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
}
