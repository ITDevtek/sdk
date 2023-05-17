<?php

namespace devtek\sdk\models;

/**
 * Credit history model
 *
 * @version 1.0.0
 */
class CreditHistory extends Base
{
    /**
     * Credit history: Never took loans
     */
    const NEVER_TOOK_LOANS = 1;

    /**
     * Credit history: Never allowed delays
     */
    const NEVER_ALLOWED_DELAYS = 2;

    /**
     * Credit history: Closed delays
     */
    const CLOSED_DELAYS = 3;

    /**
     * Credit history: Current delays
     */
    const CURRENT_DELAYS = 4;

    /**
     * Credit history: Collectors working
     */
    const COLLECTORS_WORKING = 5;

    /**
     * {@inheritDoc}
     */
    protected $readonly = true;

    /**
     * Returns credit histories list
     *
     * @return string[]
     */
    public static function list(): array
    {
        return [
            static::NEVER_TOOK_LOANS => 'Never took loans',
            static::NEVER_ALLOWED_DELAYS => 'Never allowed delays',
            static::CLOSED_DELAYS => 'Closed delays',
            static::CURRENT_DELAYS => 'Current delays',
            static::COLLECTORS_WORKING => 'Collectors working'
        ];
    }

    /**
     * Returns credit history name
     *
     * @param integer $id Credit history ID
     * @return string|null Name or `null` if credit history with specified ID doesn't exists
     */
    public static function name(int $id): ?string
    {
        $list = static::list();

        if (!isset($list[$id])) {
            return null;
        }
        return $list[$id];
    }
}
