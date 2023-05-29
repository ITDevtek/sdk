<?php

namespace devtek\sdk\interfaces;

/**
 * Interface for listable models
 *
 * @version 1.0.0
 */
interface Listable
{
    /**
     * Lists available values
     *
     * @return mixed[]
     */
    public static function list(): array;
}
