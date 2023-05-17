<?php

namespace devtek\sdk\helpers;

use DateTimeImmutable;
use devtek\sdk\models\CreditHistory;

/**
 * Validator helper class
 *
 * @version 1.0.0
 */
class Validator
{
    /**
     * Validator: Check name
     */
    const CHECK_NAME = 'check_name';

    /**
     * Validator: Check russian name
     */
    const CHECK_RUSSIAN_NAME = 'check_russian_name';

    /**
     * Validator: Numeric
     */
    const NUMERIC = 'numeric';

    /**
     * Validator: Integer
     */
    const INTEGER = 'integer';

    /**
     * Validator: Email
     */
    const EMAIL = 'email';

    /**
     * Validator: Russian phone
     */
    const PHONE_RUSSIAN = 'russian_phone';

    /**
     * Validator: Date
     */
    const DATE = 'date';

    /**
     * Validator: Region
     */
    const REGION = 'region';

    /**
     * Validator: City
     */
    const CITY = 'city';

    /**
     * Validator: Sub ID
     */
    const SUB_ID = 'sub_id';

    /**
     * Validator: Credit history
     */
    const CREDIT_HISTORY = 'credit_history';

    /**
     * Validator: Passport series
     */
    const PASSPORT_SERIES = 'passport_series';

    /**
     * Validator: Passport number
     */
    const PASSPORT_NUMBER = 'passport_number';

    /**
     * Validators list
     *
     * Keys are validator names and values are validation functions
     *
     * Each validator function accepts two parameters:
     * 1. `$value` - value to be validated;
     * 2. `$error` - reference to variable which will be contain validaton error.
     *
     * When validator finds error it must stop validation,
     * store error message to `$error` variable and return
     * correspond boolean value with validation result
     *
     * __Example validator function__
     * ```
     * function (mixed $value, &$error): bool {}
     * ```
     *
     * Function must return `true` on successful validation
     * or `false` if validation failed
     *
     * @var callable[]
     */
    protected static $validators = [
        /* self::CHECK_NAME => function ($value, &$error): bool {
            if (!is_string($value)) {
                return false;
            }

            $length = mb_strlen($value);
            if ($length < 2) {
                $error = 'Value length must be greater or equal 2';
                return false;
            } else if ($length > 255) {
                $error = 'Value length must be less than 255';
                return false;
            }

            return true;
        }, */
        /* self::CHECK_RUSSIAN_NAME => function ($value, &$error): bool {
            if (!preg_match('/^[а-яА-ЯёЁ\-\s]+$/uis', $value)) {
                $error = 'Value must contain only russian chars, dashes and spaces';
                return false;
            }

            return true;
        }, */
        /* self::NUMERIC => function ($value, &$error): bool {
            if (!is_numeric($value)) {
                $error = 'Value must be numeric';
                return false;
            }

            return true;
        }, */
        self::INTEGER => function ($value, &$error): bool {
            if (!is_integer($value)) {
                $error = 'Value must be integer';
                return false;
            }

            return true;
        },
        self::EMAIL => function ($value, &$error): bool {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid email';
                return false;
            }

            return true;
        },
        self::PHONE_RUSSIAN => function ($value, &$error): bool {
            if (!preg_match('/^([+][0-9\s-\(\)]{11,13})*$/uis', $value)) {
                $error = 'Invalid phone';
                return false;
            }

            return true;
        },
        self::DATE => function ($value, &$error): bool {
            if (DateTimeImmutable::createFromFormat('d.m.Y', $value) === false) {
                $error = 'Invalid date format. Valid format is: "d.m.Y"';
                return false;
            }

            return true;
        },
        self::SUB_ID => function ($value, &$error): bool {
            if (empty($value)) {
                return true;
            }

            if (!is_string($value)) {
                $error = 'Sub ID must be a string';
                return false;
            }
            if (mb_strlen($value) > 255) {
                $error = 'Max length of SUB ID is 255 chars';
                return false;
            }

            return true;
        },
        self::CREDIT_HISTORY => function ($value, &$error): bool {
            if (empty($value)) {
                return true;
            }
            if (!isset(CreditHistory::list()[$value])) {
                $error = 'Invalid credit history';
                return false;
            }

            return true;
        },
        self::PASSPORT_SERIES => function ($value, &$error): bool {
            if (empty($value)) {
                return true;
            }

            if (strlen($value) != 4) {
                $error = 'Passport series length must be equal to 4 numbers';
                return false;
            }
            return true;
        },
        self::PASSPORT_NUMBER => function ($value, &$error): bool {
            if (empty($value)) {
                return true;
            }

            if (strlen($value) != 6) {
                $error = 'Passport number length must be equal to 6 numbers';
                return false;
            }
            return true;
        }
    ];

    /**
     * Returns validator function
     *
     * @param string $name Validator name
     * @return callable|null Validator function or `null` if validator doesn't exists
     */
    public static function get(string $name): ?callable
    {
        if (!isset(static::$validators[$name])) {
            return null;
        }
        return static::$validators[$name];
    }

    /**
     * Adds validator
     *
     * @param string $name Name
     * @param callable $validator Handler
     * @return boolean `true` if validator successfully added or `false` if not _(this may happen if validator with same name already exists)_
     */
    public static function add(string $name, callable $validator): bool
    {
        if (isset(static::$validators[$name])) {
            return false;
        }

        static::$validators[$name] = $validator;
        return true;
    }

    /**
     * Removes validator
     *
     * @param string $name Name
     * @return boolean `true` if validator removed successfully removed or `false` if not _(this may happen if validator with specified name doesn't exists)_
     */
    public static function remove(string $name): bool
    {
        if (!isset(static::$validators[$name])) {
            return false;
        }

        unset(static::$validators[$name]);
        return true;
    }
}
